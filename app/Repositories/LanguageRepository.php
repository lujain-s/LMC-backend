<?php

namespace App\Repositories;

use App\Models\Language;
use Carbon\Carbon;

class LanguageRepository
{


    public function createLanguage(array $data): Language
    {
        return Language::create($data);
    }

    public function updateLanguage(int $id, array $data): ?Language
    {
        $language = Language::find($id);
        if ($language) {
            $language->update($data);
        }
        return $language;
    }

    public function findLanguageById($id)
    { 
     return Language::find($id);
    }

    public function getAllLanguage()
    {
     return Language::all();
    }

    public function deleteLanguage($language)
    {
     return $language->delete();
    }

}