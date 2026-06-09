<?php

namespace App\Http\Controllers\Financial;

use App\Actions\Financial\GetFinancialDashboardAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Financial\FinancialFilterRequest;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class FinancialDashboardController extends Controller
{
    public function __invoke(
        FinancialFilterRequest $request,
        GetFinancialDashboardAction $action,
    ): Response {
        $user = $request->user();
        $period = $request->validated('period') ?? '30d';
        $startDate = $request->validated('start_date') ? Carbon::parse($request->validated('start_date')) : null;
        $endDate = $request->validated('end_date') ? Carbon::parse($request->validated('end_date')) : null;
        $studentId = $request->validated('student_id');
        $status = $request->validated('status');
        $paymentMethod = $request->validated('payment_method');

        $data = $action->execute(
            user: $user,
            period: $period,
            startDate: $startDate,
            endDate: $endDate,
            studentId: $studentId ? (int) $studentId : null,
            status: $status,
            paymentMethod: $paymentMethod,
        );

        return Inertia::render('financial/Index', [
            ...$data,
            'title' => 'Financeiro',
        ]);
    }
}
