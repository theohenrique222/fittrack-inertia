<?php

namespace App\Http\Controllers\Students;

use App\Actions\Students\ListAllStudentsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use Inertia\Inertia;
use Inertia\Response;

class ListAllStudentsController extends Controller
{
    public function __invoke(ListAllStudentsAction $action): Response
    {
        $students = $action->execute();
        $archivedStudents = $action->execute(trashed: true);

        return Inertia::render('students/AdminAllStudents', [
            'title' => 'Todos os Alunos',
            'students' => StudentResource::collection($students),
            'archivedStudents' => StudentResource::collection($archivedStudents),
        ]);
    }
}
