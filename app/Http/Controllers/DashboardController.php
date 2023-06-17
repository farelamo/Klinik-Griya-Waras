<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(DashboardService $service) {
        $this->service = $service;
    }

    public function index() {
        return $this->service->index();
    }
}
