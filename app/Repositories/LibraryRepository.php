<?php

namespace App\Repositories;

use App\Models\Item;
use App\Models\Language;
use App\Models\Library;

class LibraryRepository
{
    public function getLanguagesWithLibrary()
    {
        return Language::whereHas('Library')->select('id', 'Name', 'Description')->get();
    }

    public function findLanguageWithLibraryAndItems($languageId)
    {
        return Language::with('library.items')->find($languageId);
    }

    public function createLibrary($languageId)
    {
        return Library::create(['LanguageId' => $languageId]);
    }

    public function getLibraryByLanguage($languageId)
    {
        return Library::where('LanguageId', $languageId)->first();
    }

    public function createItem($data)
    {
        return Item::create($data);
    }

    public function findItemById($id)
    {
        return Item::find($id);
    }

    public function deleteItem($item)
    {
        return $item->delete();
    }
}
