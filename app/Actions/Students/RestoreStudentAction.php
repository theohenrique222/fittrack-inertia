<?php

namespace App\Actions\Students;

use App\Models\Client;
use Illuminate\Support\Facades\DB;

class RestoreStudentAction
{
    public function execute(Client $student): void
    {
        DB::transaction(function () use ($student) {
            $student->user()->restore();
            $student->restore();
        });
    }
}
