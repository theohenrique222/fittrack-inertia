<?php

namespace App\Http\Controllers\Dashboard;

use App\Actions\Dashboard\GetClientDashboardStatsAction;
use App\Actions\Dashboard\GetTrainerDashboardStatsAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request, GetTrainerDashboardStatsAction $trainerStats, GetClientDashboardStatsAction $clientStats)
    {
        $user = $request->user();

        if ($user->isPersonal() || $user->isAdmin() || $user->isSelf()) {
            $data = $trainerStats->execute($user);
        } else {
            $data = $clientStats->execute($user);
        }

        return Inertia::render('Dashboard', $data);
    }
}
