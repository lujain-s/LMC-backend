<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Repositories\InvoiceRepository;
use Illuminate\Http\Request;
use Exception;

class InvoiceService
{
    protected $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function createInvoice(Request $request, $creatorId)
    {
        DB::beginTransaction();

        try {
            $task = $this->invoiceRepository->findTask($request->TaskId);
           /* if (!$task) {
                throw new Exception('Task not found', 404);
            }*/

            if (!$task) {
                throw new Exception('Task not found', 404); // Note: 404 is integer
            }

           /* if (!$request->hasFile('Image')) {
                throw new Exception('Image file is required', 400);
            }*/

            if (!$request->hasFile('Image')) {
                throw new Exception('Image file is required', 400); // 400 is integer
            }

            $image = $request->file('Image');
            $new_name = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $new_name);
            $imageUrl = url('images/' . $new_name);

            if (!file_exists(public_path('images/' . $new_name))) {
                throw new Exception('Failed to upload image', 500);
            }

            $invoice = $this->invoiceRepository->createInvoice([
                'TaskId' => $request->TaskId,
                'Amount' => $request->Amount,
                'Image' => $imageUrl,
                'Status' => 'Sent',
                'CreatorId' => $creatorId,
            ]);

            $secretaries = $this->invoiceRepository->getSecretaries();
            if ($secretaries->isEmpty()) {
                throw new Exception('No Secretarya users found', 400);
            }

            foreach ($secretaries as $secretary) {
                $this->invoiceRepository->createRecipient($invoice->id, $secretary->id);
            }

            DB::commit();

            return [
                'invoice' => $invoice,
                'recipients' => $secretaries->pluck('id'),
                'image_url' => $imageUrl
            ];

        } catch (Exception $e) {
            DB::rollBack();

            if (isset($new_name) && file_exists(public_path('images/' . $new_name))) {
                unlink(public_path('images/' . $new_name));
            }

            throw $e;
        }
    }

    public function showInvoice($invoiceId, $userId)
    {
        DB::beginTransaction();

        try {
            $invoice = $this->invoiceRepository->findInvoiceWithRelations($invoiceId);
            $recipientRecord = $this->invoiceRepository->findRecipient($invoiceId, $userId);

            if ($recipientRecord && $recipientRecord->Status === 'Pending') {
                $recipientRecord->update(['Status' => 'Approved']);
            }

            $pendingRecipients = $this->invoiceRepository->countPendingRecipients($invoiceId);

            if ($pendingRecipients === 0 && $invoice->Status === 'Sent') {
                $this->invoiceRepository->updateInvoiceStatus($invoice, 'Seen');
            }

            $approvalStatus = $invoice->recipients->map(function ($recipient) {
                return [
                    'user_id' => $recipient->user->id,
                    'user_name' => $recipient->user->name,
                    'status' => $recipient->Status,
                    'approved_at' => $recipient->Status === 'Approved' ? $recipient->updated_at : null
                ];
            });

            DB::commit();

            return [
                'invoice' => $invoice,
                'approval_status' => $approvalStatus,
                'all_approved' => $pendingRecipients === 0,
                'current_user_approved' => $recipientRecord ? $recipientRecord->Status === 'Approved' : false
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function checkApproval($invoiceId, $userId)
    {
        $invoice = $this->invoiceRepository->findInvoiceByIdWithRecipients($invoiceId);

        if ($invoice->CreatorId !== $userId) {
            throw new Exception('Unauthorized to view this invoice', 403);
        }

        $approvalStatus = $invoice->recipients->map(function ($recipient) {
            return [
                'user_id' => $recipient->user->id,
                'user_name' => $recipient->user->name,
                'status' => $recipient->Status,
                'approved_at' => $recipient->Status === 'Approved' ? $recipient->updated_at : null
            ];
        });

        $pendingCount = $invoice->recipients->where('Status', '!=', 'Approved')->count();

        return [
            'invoice_id' => $invoice->id,
            'invoice_status' => $invoice->Status,
            'approval_status' => $approvalStatus,
            'pending_approvals' => $pendingCount,
            'all_approved' => $pendingCount === 0
        ];
    }
}
