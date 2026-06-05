<?php

namespace App\Actions\Students;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

class ListAllStudentsAction
{
    public function execute(bool $trashed = false): Collection
    {
        $query = Client::with(['user', 'user.trainer']);

        if ($trashed) {
            $query->onlyTrashed();
        }

        return $query->get();
    }
}
