<?php

namespace App\Http\Controllers\Students;

use App\Actions\Students\ListStudentsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use Inertia\Inertia;
use Inertia\Response;

class ListStudentsController extends Controller
{
    public function __invoke(ListStudentsAction $action): Response
    {
        $students = $action->execute();

        return Inertia::render('students/ListStudents', [
            'title' => 'Lista de Alunos',
            'students' => StudentResource::collection($students),
        ]);
    }
}
