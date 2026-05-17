<?php

namespace App\Http\Controllers\Categories;

use App\Actions\Categories\ListCategoriesAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;

class ListCategoriesController extends Controller
{
    public function __invoke(ListCategoriesAction $action): JsonResponse
    {
        $categories = $action->execute();

        return response()->json([
            'data' => CategoryResource::collection($categories),
        ]);
    }
}
