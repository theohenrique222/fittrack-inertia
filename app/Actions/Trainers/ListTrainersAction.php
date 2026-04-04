<?php

namespace App\Actions\Trainers;

use App\Models\Trainer;
use Illuminate\Database\Eloquent\Collection;

class ListTrainersAction
{
    public function execute(): Collection
    {
        return Trainer::with('user')->get();
    }
}
