<?php

namespace App\Http\Controllers\Students;

use App\Actions\Students\ListStudentsAction;
use App\Actions\Students\RestoreStudentAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RestoreStudentController extends Controller
{
    public function __invoke(
        Client $student,
        RestoreStudentAction $action,
        Request $request,
    ): Response {
        $action->execute($student);
        $students = (new ListStudentsAction)->execute();

        $request->session()->flash('success', 'Aluno restaurado com sucesso.');

        return Inertia::render('students/ListStudents', [
            'title' => 'Lista de Alunos',
            'students' => StudentResource::collection($students),
        ]);
    }
}
