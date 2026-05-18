<?php

namespace App\Http\Controllers\Students;

use App\Actions\Students\ListStudentsAction;
use App\Actions\Students\UpdateStudentAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UpdateStudentController extends Controller
{
    public function __invoke(
        Request $request,
        Client $student,
        UpdateStudentAction $action
    ): Response {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'nickname' => ['nullable', 'string', 'max:255'],
        ]);

        $updatedStudent = $action->execute($student, $validated);
        $students = (new ListStudentsAction)->execute();

        return Inertia::render('students/ListStudents', [
            'title' => 'Lista de Alunos',
            'students' => StudentResource::collection($students),
        ]);
    }
}
