<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the welcome page.
     */
    public function welcome(): View
    {
        return view('welcome');
    }

    /**
     * Display the alur wisuda page.
     */
    public function alurWisuda(): View
    {
        return view('alur-wisuda');
    }

    /**
     * Display the help desk page.
     */
    public function helpDesk(): View
    {
        return view('help-desk');
    }
}
