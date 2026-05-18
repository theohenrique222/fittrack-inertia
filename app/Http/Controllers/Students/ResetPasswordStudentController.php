<?php

namespace App\Http\Controllers\Students;

use App\Actions\Students\ResetPasswordStudentAction;
use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;

class ResetPasswordStudentController extends Controller
{
    public function __invoke(
        Client $student,
        ResetPasswordStudentAction $action
    ): RedirectResponse {
        $action->execute($student);

        return to_route('students')
            ->with('success', 'Senha redefinida com sucesso!');
    }
}
