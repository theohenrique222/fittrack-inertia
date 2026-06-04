<?php

namespace App\Http\Controllers\BodyMeasurements;

use App\Actions\BodyMeasurements\DestroyBodyMeasurementAction;
use App\Http\Controllers\Controller;
use App\Models\BodyMeasurement;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;

class DestroyBodyMeasurementController extends Controller
{
    public function __invoke(
        Client $student,
        BodyMeasurement $measurement,
        DestroyBodyMeasurementAction $action,
    ): RedirectResponse {
        if ($measurement->client_id !== $student->id) {
            abort(404);
        }

        $user = request()->user();

        if (! $user->isAdmin()) {
            if ($user->isStudent() && $student->user_id !== $user->id) {
                abort(403);
            }

            if ($user->isPersonal() && $student->user->trainer_id !== $user->id) {
                abort(403);
            }
        }

        $action->execute($measurement);

        return redirect()
            ->route('students.measurements', $student)
            ->with('success', 'Medida removida com sucesso.');
    }
}
