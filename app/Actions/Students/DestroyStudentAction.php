<?php

namespace App\Actions\Students;

use App\Models\Client;
use Illuminate\Support\Facades\DB;

class DestroyStudentAction
{
    public function execute(Client $client): void
    {
        DB::transaction(function () use ($client) {
            $client->user()->delete();
            $client->delete();
        });
    }
}
