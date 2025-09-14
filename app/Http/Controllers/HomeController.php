<?php

namespace App\Http\Controllers;

use App\Services\HomeStatisticsService;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __construct(
        protected HomeStatisticsService $homeStatistics
    ) {}

    public function show()
    {
        $stats = $this->homeStatistics->getStatistics();

        return Inertia::render('Home',
            $stats
        );
    }
}
