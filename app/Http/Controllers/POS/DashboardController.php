<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index()
    {
        return view(
            'POS.Dashboard',
            $this->dashboardService->getDashboardData()
        );
    }
}
