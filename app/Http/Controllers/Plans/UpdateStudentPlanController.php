<?php

namespace App\Http\Controllers\Plans;

use App\Actions\Plans\UpdateStudentPlanAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Plans\UpdateStudentPlanRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;

class UpdateStudentPlanController extends Controller
{
    public function __invoke(
        UpdateStudentPlanRequest $request,
        Client $student,
        UpdateStudentPlanAction $action,
    ): RedirectResponse {
        $validated = $request->validated();

        $action->execute($student, $validated['plan_id'] ?? null);

        return redirect()
            ->route('students.show', $student)
            ->with('success', 'Plano do aluno atualizado com sucesso');
    }
}
