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
        return view('coming-soon', [
            'title' => 'Alur Wisuda',
            'description' => 'Halaman Alur Wisuda sedang dalam pengembangan. Kami akan menampilkan panduan lengkap proses wisuda dengan detail tahapan demi tahapan.',
            'message' => 'Fitur ini akan segera tersedia. Terima kasih atas kesabaran Anda.'
        ]);
    }

    /**
     * Display the help desk page.
     */
    public function helpDesk(): View
    {
        return view('coming-soon', [
            'title' => 'QnA',
            'description' => 'Halaman QnA sedang dalam pengembangan. Kami akan menampilkan pertanyaan dan jawaban yang sering diajukan untuk membantu Anda mengenai wisuda.',
            'message' => 'Fitur ini akan segera tersedia. Terima kasih atas kesabaran Anda.'
        ]);
    }
}
