<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Services\LibraryService;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    protected $service;

    public function __construct(LibraryService $service)
    {
        $this->service = $service;
    }

    public function getLanguages()
    {
        return response()->json($this->service->getLanguages());
    }

    public function getFilesByLanguage($languageId)
    {
        try {
            $result = $this->service->getFilesByLanguage($languageId);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function uploadFile(Request $request)
    {
        $validated = $request->validate([
            'LibraryId' => 'required|exists:libraries,id',
            'file' => 'required|file|mimes:pdf,doc,docx,txt,mp4,mp3,jpg,png',
            'Description' => 'required|string'
        ]);

        try {
            $result = $this->service->uploadFile($validated, $request->file('file'));

            return response()->json([
                'message' => 'File is uploaded successfully',
                'item' => $result['item'],
                'file_url' => $result['file_url'],
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function addLanguageToLibrary(Request $request)
    {
        $request->validate([
            'language_id' => 'required|exists:languages,id',
        ]);

        try {
            $library = $this->service->addLanguageToLibrary($request->language_id);

            return response()->json([
                'message' => 'Library was created for this language',
                'Library' => $library
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function editFile(Request $request, $id)
    {
        $validated = $request->validate([
            'Description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt,mp4,mp3,jpg,png|max:10240'
        ]);

        try {
            $result = $this->service->editFile($id, $validated, $request->file('file'));

            return response()->json([
                'message' => 'File was edited successfully',
                'item' => $result['item'],
                'file_url' => $result['file_url'],
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function deleteFile($id)
    {
        try {
            $this->service->deleteFile($id);
            return response()->json(['message' => 'File was deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function downloadFile($id)
    {
        return $this->service->downloadFile($id);
    }

   /* public function downloadFile($id)
    {
     $item = Item::find($id);

     if (!$item) {
        return response()->json(['message' => 'العنصر غير موجود'], 404);
     }

     $filePath = storage_path('app/public/' . $item->File);

     if (!file_exists($filePath)) {
        return response()->json(['message' => 'الملف غير موجود'], 404);
     }

     // return response()->download($filePath);

     return response()->download($filePath, basename($filePath), [
     'Content-Type' => mime_content_type($filePath),
     'Content-Disposition' => 'attachment; filename="' . basename($filePath) . '"',
     ]);
    }*/

}

/*
namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Language;
use App\Models\Library;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class LibraryController extends Controller
{

    //نفذ الأمر التالي: php artisan storage:link

    //واجعل FILESYSTEM_DRIVER=public في ملف .env. ,,,,FILESYSTEM_DRIVER=public


    //composer require --dev barryvdh/laravel-ide-helper


    //GET http://localhost:8000/api/files/1/download




    // استرجاع كل اللغات التي لها مكتبة مرتبطة
    public function getLanguages()
    {
        return Language::whereHas('Library')
        ->select('id', 'Name', 'Description')
        ->get();
    }

    // استرجاع الملفات الخاصة بلغة محددة
    public function getFilesByLanguage($languageId)
    {
        // Load the language with its library and items in one query
        $language = Language::with('library.items')->find($languageId);

        // Check if language exists
        if (!$language) {
            return response()->json([
                'message' => 'This language does not found',
            ], 404);
        }

        // Check if language has a library
        if (!$language->library) {
            return response()->json([
                'message' => 'This language does not have a library',
                'language' => $language->Name,
                'files' => []
            ], 200);
        }

        // Return files if library exists
        return response()->json([
            'language' => $language->Name,
            'files' => $language->library->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'file_name' => basename($item->File),
                    'url' => url('storage/' . $item->File),
                    'description' => $item->Description,
                ];
            })
        ], 200);
    }

     // رفع ملف جديد إلى مكتبة معينة
     /*public function uploadFile(Request $request)
    {
         $request->validate([
             'LibraryId' => 'required|exists:libraries,id',
             'file' => 'required|file|mimes:pdf,doc,docx,txt,mp4,mp3,jpg,png',
             'Description' => 'required|string' // Changed from nullable to required
         ]);

         $path = $request->file('file')->store('library_files', 'public');

         $item = Item::create([
             'LibraryId' => $request->LibraryId,
             'File' => $path,
             'Description' => $request->Description,
         ]);

         return response()->json([
             'message' => 'تم رفع الملف بنجاح',
             'item' => $item,
             'file_url' => asset('storage/' . $path),
         ]);
    }*/


  /*lll  public function uploadFile(Request $request)
    {
     $validated = $request->validate([
        'LibraryId' => 'required|exists:libraries,id',
        'file' => 'required|file|mimes:pdf,doc,docx,txt,mp4,mp3,jpg,png',
        'Description' => 'required|string'
     ]);

     // حفظ الملف في مجلد ضمن معرف المكتبة لسهولة الترتيب لاحقاً
     $path = $request->file('file')->store('library_files/' . $validated['LibraryId'], 'public');

     $item = Item::create([
        'LibraryId' => $validated['LibraryId'],
        'File' => $path,
        'Description' => $validated['Description'],
     ]);

     return response()->json([
        'message' => 'تم رفع الملف بنجاح',
        'item' => $item,
        'file_url' => asset('storage/' . $path),
     ], 201); // 201 = Created
    }


     public function addLanguageToLibrary(Request $request)
    {
     $request->validate([
        'language_id' => 'required|exists:languages,id',
     ]);

     // التأكد أنه لا توجد مكتبة مضافة مسبقًا لهذه اللغة
     $existing = Library::where('LanguageId', $request->language_id)->first();
     if ($existing) {
        return response()->json(['message' => 'هذه اللغة لديها مكتبة بالفعل'], 409);
     }

     $library = Library::create([
        'LanguageId' => $request->language_id,
     ]);

     return response()->json([
        'message' => 'تم إنشاء المكتبة لهذه اللغة بنجاح',
        'library' => $library
     ]);
    }


    public function editFile(Request $request, $id)
    {
        $item = Item::find($id);

        // Check if item exists
        if (!$item) {
            return response()->json([
                'message' => 'الملف غير موجود'
            ], 404);
        }

        $validated = $request->validate([
            'Description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt,mp4,mp3,jpg,png|max:10240'
        ]);

        // إذا تم رفع ملف جديد، نحذف القديم ونرفع الجديد
        if ($request->hasFile('file')) {
            // حذف الملف القديم
            if (Storage::disk('public')->exists($item->File)) {
                Storage::disk('public')->delete($item->File);
            }

            // رفع الملف الجديد بنفس مجلد المكتبة
            $path = $request->file('file')->store('library_files/' . $item->LibraryId, 'public');
            $item->File = $path;
        }

        // تحديث الوصف إذا تم توفيره
        if (isset($validated['Description'])) {
            $item->Description = $validated['Description'];
        }

        $item->save();

        return response()->json([
            'message' => 'تم تعديل الملف بنجاح',
            'item' => $item,
            'file_url' => asset('storage/' . $item->File),
        ]);
    }

    public function deleteFile($id)
    {
        $item = Item::find($id); // Use find() instead of findOrFail()

        // Check if item exists
        if (!$item) {
            return response()->json([
                'message' => 'الملف غير موجود'
            ], 404);
        }

        // Delete file from storage if exists
        if ($item->File && Storage::disk('public')->exists($item->File)) {
            Storage::disk('public')->delete($item->File);
        }

        $item->delete();

        return response()->json([
            'message' => 'تم حذف الملف بنجاح'
        ]);
    }


///////////////////////////


    // تحميل ملف
    /*public function downloadFile($itemId)
    {
        $item = Item::findOrFail($itemId);
        $path = storage_path('app/public/' . $item->File);

        if (!file_exists($path)) {
            return response()->json(['message' => 'الملف غير موجود'], 404);
        }

        return response()->download($path);
    }*/


   /* public function downloadFile($itemId)
    {
     $item = Item::findOrFail($itemId);

     $filePath = storage_path('app/public/' . $item->File);

     if (!file_exists($filePath)) {
        abort(404);
     }

     return response()->download($filePath, basename($item->File));
    }*/

    /*public function downloadFile($id)
{
    $item = Item::findOrFail($id);
    $filePath = storage_path('app/public/' . $item->File);

    if (!file_exists($filePath)) {
        return response()->json(['message' => 'الملف غير موجود'], 404);
    }

    return response()->download($filePath);
}*/

/*llllllllll
public function downloadFile($id)
{
    $item = Item::findOrFail($id);

    $filePath = $item->File;

    if (!Storage::disk('public')->exists($filePath)) {
        return response()->json(['message' => 'الملف غير موجود'], 404);
    }

    return response()->download(storage_path('app/public/' . $filePath));
}

*/

