<?php

namespace App\Http\Controllers\Students;

use App\Actions\BodyMeasurements\ListMeasurementsOverviewAction;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ListMeasurementsOverviewController extends Controller
{
    public function __invoke(ListMeasurementsOverviewAction $action): Response
    {
        return Inertia::render('students/MeasurementsOverview', [
            'title' => 'Visão Geral de Medidas',
            'students' => $action->execute(),
        ]);
    }
}
