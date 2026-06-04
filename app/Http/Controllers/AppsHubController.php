<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class AppsHubController extends Controller
{
    /**
     * Organization-wide web apps launcher (icons / short descriptions).
     */
    public function __invoke(): Response
    {
        return Inertia::render('AppsHub');
    }
}
