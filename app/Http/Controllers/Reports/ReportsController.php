<?php

namespace App\Http\Controllers\Reports;

use App\Actions\Reports\GetAdminReportsAction;
use App\Actions\Reports\GetTrainerReportsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\ReportsFilterRequest;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class ReportsController extends Controller
{
    public function __invoke(
        ReportsFilterRequest $request,
        GetAdminReportsAction $adminReports,
        GetTrainerReportsAction $trainerReports,
    ) {
        $user = $request->user();
        $period = $request->validated('period') ?? '30d';
        $startDate = $request->validated('start_date') ? Carbon::parse($request->validated('start_date')) : null;
        $endDate = $request->validated('end_date') ? Carbon::parse($request->validated('end_date')) : null;

        if ($user->isAdmin()) {
            $data = $adminReports->execute($period, $startDate, $endDate);
        } elseif ($user->isPersonal() || $user->isSelf()) {
            $data = $trainerReports->execute($user, $period, $startDate, $endDate);
        } else {
            abort(403);
        }

        return Inertia::render('reports/Index', [
            ...$data,
            'selectedPeriod' => $period,
        ]);
    }
}
