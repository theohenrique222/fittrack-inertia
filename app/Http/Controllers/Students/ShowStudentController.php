<?php

namespace App\Http\Controllers\Students;

use App\Actions\Students\ShowStudentAction;
use App\Http\Controllers\Controller;
use App\Models\Client;
use Inertia\Inertia;
use Inertia\Response;

class ShowStudentController extends Controller
{
    public function __invoke(Client $student, ShowStudentAction $action): Response
    {
        $data = $action->execute($student);

        return Inertia::render('students/ShowStudent', [
            'title' => 'Aluno - '.$data['student']['name'],
            'student' => $data['student'],
            'workout' => $data['workout'],
            'stats' => $data['stats'],
        ]);
    }
}
