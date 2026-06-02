<?php

namespace App\Http\Controllers\BodyMeasurements;

use App\Actions\BodyMeasurements\StoreBodyMeasurementAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\BodyMeasurements\StoreBodyMeasurementRequest;
use App\Models\Client;

class StoreBodyMeasurementController extends Controller
{
    public function __invoke(
        Client $student,
        StoreBodyMeasurementRequest $request,
        StoreBodyMeasurementAction $action,
    ) {
        $validated = $request->validated();

        $result = $action->execute($student, $validated);

        $metrics = $result['metrics'];

        $message = 'Medidas registradas com sucesso! '
            ."IMC: {$metrics['bmi']['value']} | "
            ."Gordura: {$metrics['body_fat']['value']}% | "
            ."TMB: {$metrics['bmr']} kcal";

        return redirect()
            ->route('students.measurements', $student)
            ->with('success', $message);
    }
}
