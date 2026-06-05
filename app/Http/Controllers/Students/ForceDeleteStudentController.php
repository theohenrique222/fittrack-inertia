<?php

namespace App\Http\Controllers\Students;

use App\Actions\Students\ForceDeleteStudentAction;
use App\Actions\Students\ListStudentsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Client;
use Inertia\Inertia;
use Inertia\Response;

class ForceDeleteStudentController extends Controller
{
    public function __invoke(
        Client $student,
        ForceDeleteStudentAction $action,
    ): Response {
        $action->execute($student);
        $students = (new ListStudentsAction)->execute();

        return Inertia::render('students/ListStudents', [
            'title' => 'Lista de Alunos',
            'students' => StudentResource::collection($students),
        ]);
    }
}
