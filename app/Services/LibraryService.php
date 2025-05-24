<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Library;
use App\Repositories\LibraryRepository;
use Illuminate\Support\Facades\Storage;

class LibraryService
{
    protected $repository;

    public function __construct(LibraryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getLanguages()
    {
        return $this->repository->getLanguagesWithLibrary();
    }

    public function getFilesByLanguage($languageId)
    {
        $language = $this->repository->findLanguageWithLibraryAndItems($languageId);

        if (!$language) {
            throw new \Exception('This language is not found', 404);
        }

        // Check if language has a library
        if (!$language->library) {
            return [
                'message' => 'This language does not have a library',
                'language' => $language->Name,
            ];
        }

        return [
            'language' => $language->Name,
            'files' => $language->library->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'file_name' => basename($item->File),
                    'url' => url('storage/' . $item->File),
                    'description' => $item->Description,
                ];
            })
        ];
    }

    public function uploadFile($data, $file)
    {
        $path = $file->store('library_files/' . $data['LibraryId'], 'public');

        $item = $this->repository->createItem([
            'LibraryId' => $data['LibraryId'],
            'File' => $path,
            'Description' => $data['Description'],
        ]);

        return [
            'item' => $item,
            'file_url' => asset('storage/' . $path)
        ];
    }

    public function addLanguageToLibrary($languageId)
    {
        $existing = $this->repository->getLibraryByLanguage($languageId);

        if ($existing) {
            throw new \Exception('This language already has a library', 409);
        }

        return $this->repository->createLibrary($languageId);
    }

    public function deleteLibraryWithFiles($libraryId)
    {
        $library = Library::with('language')->where('id', $libraryId)->first();

        if (!$library) {
            return 'not_found';
        }

        // Get all related items
        $items = Item::where('LibraryId', $library->id)->get();

        if ($items->isEmpty()) {
            $folderPath = "library_files/{$library->id}";
            if (Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->deleteDirectory($folderPath);
            }

            $library->delete();
            return 'no_items';
        }

        // حذف كل العناصر والملفات المرتبطة
        foreach ($items as $item) {
            if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
                Storage::disk('public')->delete($item->file_path);
            }
            $item->delete();
        }

        $folderPath = "library_files/{$library->id}";
        if (Storage::disk('public')->exists($folderPath)) {
            Storage::disk('public')->deleteDirectory($folderPath);
        }

        $library->delete();
        return 'deleted';
    }

    public function editFile($id, $data, $file = null)
    {
        $item = $this->repository->findItemById($id);

        if (!$item) {
            throw new \Exception('File not found', 404);
        }

        if ($file) {
            if (Storage::disk('public')->exists($item->File)) {
                Storage::disk('public')->delete($item->File);
            }

            $path = $file->store('library_files/' . $item->LibraryId, 'public');
            $item->File = $path;
        }

        if (isset($data['Description'])) {
            $item->Description = $data['Description'];
        }

        $item->save();

        return [
            'item' => $item,
            'file_url' => asset('storage/' . $item->File)
        ];
    }

    public function deleteFile($id)
    {
        $item = $this->repository->findItemById($id);

        if (!$item) {
            throw new \Exception('File not found', 404);
        }

        if ($item->File && Storage::disk('public')->exists($item->File)) {
            Storage::disk('public')->delete($item->File);
        }

        $this->repository->deleteItem($item);
    }

    public function downloadFile($id)
    {
        $item = $this->repository->findItemById($id);

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $filePath = storage_path('app/public/' . $item->File);

        if (!file_exists($filePath)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->download($filePath, basename($filePath), [
            'Content-Type' => mime_content_type($filePath),
            'Content-Disposition' => 'attachment; filename="' . basename($filePath) . '"',
        ]);
    }
}
