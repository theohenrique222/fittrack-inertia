<?php

namespace App\Http\Controllers\BodyMeasurements;

use App\Actions\BodyMeasurements\UpdateBodyMeasurementAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\BodyMeasurements\UpdateBodyMeasurementRequest;
use App\Models\BodyMeasurement;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;

class UpdateBodyMeasurementController extends Controller
{
    public function __invoke(
        Client $student,
        BodyMeasurement $measurement,
        UpdateBodyMeasurementRequest $request,
        UpdateBodyMeasurementAction $action,
    ): RedirectResponse {
        if ($measurement->client_id !== $student->id) {
            abort(404);
        }

        $validated = $request->validated();
        $warnings = $request->getCautionWarnings();

        $result = $action->execute($measurement, $student, $validated);

        $metrics = $result['metrics'];

        $message = 'Medidas atualizadas com sucesso! '
            ."IMC: {$metrics['bmi']['value']} | "
            ."Gordura: {$metrics['body_fat']['value']}% | "
            ."TMB: {$metrics['bmr']} kcal";

        $redirect = redirect()
            ->route('students.measurements', $student)
            ->with('success', $message);

        if (! empty($warnings)) {
            $redirect->with('measurement_warnings', $warnings);
        }

        return $redirect;
    }
}
