<?php

namespace App\Http\Controllers\BodyMeasurements;

use App\Actions\BodyMeasurements\LatestBodyMeasurementAction;
use App\Actions\BodyMeasurements\ListBodyMeasurementsAction;
use App\Http\Controllers\Controller;
use App\Models\Client;

class ListBodyMeasurementsController extends Controller
{
    public function __invoke(
        Client $student,
        ListBodyMeasurementsAction $action,
        LatestBodyMeasurementAction $latestAction,
    ) {
        $user = request()->user();

        $measurements = $action->execute($student);
        $latest = $latestAction->execute($student);

        return inertia('students/BodyMeasurements', [
            'title' => 'Medidas Corporais - '.($student->user?->name ?? 'Aluno'),
            'student' => [
                'id' => $student->id,
                'name' => $student->user?->name ?? 'Sem nome',
            ],
            'measurements' => $measurements,
            'latest' => $latest,
            'user_has_profile' => (bool) ($user->gender && $user->birthdate),
            'user_gender' => $user->gender?->value ?? null,
            'user_birthdate' => $user->birthdate?->format('Y-m-d') ?? null,
        ]);
    }
}
