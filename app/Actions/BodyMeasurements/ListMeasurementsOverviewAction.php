<?php

namespace App\Actions\BodyMeasurements;

use App\Models\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ListMeasurementsOverviewAction
{
    public function execute(): Collection
    {
        $students = Client::with('user')
            ->whereHas('user', fn ($q) => $q->where('trainer_id', Auth::id()))
            ->get();

        $latestAction = app(LatestBodyMeasurementAction::class);

        return $students->map(fn (Client $student) => [
            'id' => $student->id,
            'name' => $student->user?->name ?? 'Sem nome',
            'nickname' => $student->nickname,
            'latest_measurement' => $latestAction->execute($student),
        ]);
    }
}
