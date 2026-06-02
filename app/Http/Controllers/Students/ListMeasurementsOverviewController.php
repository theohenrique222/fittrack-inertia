<?php

namespace App\Http\Controllers\Students;

use App\Actions\BodyMeasurements\ListMeasurementsOverviewAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ListMeasurementsOverviewController extends Controller
{
    public function __invoke(ListMeasurementsOverviewAction $action): Response|RedirectResponse
    {
        $user = request()->user();

        if ($user->isStudent()) {
            $client = $user->client;

            if ($client) {
                return redirect()->route('students.measurements', $client);
            }
        }

        return Inertia::render('students/MeasurementsOverview', [
            'title' => 'Visão Geral de Medidas',
            'students' => $action->execute(),
        ]);
    }
}
