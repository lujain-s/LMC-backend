<?php

namespace App\Repositories;

use App\Models\Lesson;
use App\Models\Notes;

class StudentRepository
{
    //Note
    public function createNote($data) {
        return Notes::create($data);
    }

    public function updateNote($note, $content) {
        $note->Content = $content;
        $note->save();
        return $note;
    }

    public function deleteNote($note) {
        $note->delete();
        return true;
    }
}
