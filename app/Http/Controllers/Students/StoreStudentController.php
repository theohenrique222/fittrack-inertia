<?php

namespace App\Http\Controllers\Students;

use App\Actions\Students\ListStudentsAction;
use App\Actions\Students\StoreStudentAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class StoreStudentController extends Controller
{
    public function __invoke(
        Request $request,
        StoreStudentAction $action
    ): Response {
        Gate::authorize('create-student');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'gender' => ['required', 'string', 'in:male,female'],
            'birthdate' => ['required', 'date', 'before:today'],
        ]);

        $action->execute($validated);
        $students = (new ListStudentsAction)->execute();

        return Inertia::render('students/ListStudents', [
            'title' => 'Lista de Alunos',
            'students' => StudentResource::collection($students),
        ])->with('success', 'Student created successfully');
    }
}
