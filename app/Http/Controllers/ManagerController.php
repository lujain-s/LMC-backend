<?php

namespace App\Http\Controllers;

use App\Models\LMCInfo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ManagerController extends Controller
{
    public function editEmployee(Request $request) {

    }

    public function deleteEmployee() {

    }

    public function editLMCInfo(Request $request)
    {
        $info = LMCInfo::findOrFail(1);

        $data = $request->validate([
            'Title' => 'sometimes|string',
            'Descriptions' => 'sometimes|array',
            'Descriptions.*.Title' => 'required_with:Descriptions|string',
            'Descriptions.*.Explanation' => 'required_with:Descriptions|string',
            'Photo' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('Photo')) {
            if ($info->photo) {
                Storage::disk('public')->delete($info->photo);
            }
            $image = $request->file('Photo');
            $new_name = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $new_name);
            $imageUrl = url('images/' . $new_name);

            if (!file_exists(public_path('images/' . $new_name))) {
                throw new Exception('Failed to upload image', 500);
            }
            $data['Photo'] = $imageUrl;
        }

        if (isset($data['Descriptions'])){
            $data['Descriptions'] = json_encode($data['Descriptions']);
        }

        $info->update($data);

        return response()->json($info);
    }

    public function reviewFinalGrades() {

    }

    public function addHolidays(Request $request) {

    }

    public function addTasks(Request $request) {

    }

    public function viewStatistics() {

    }
}
