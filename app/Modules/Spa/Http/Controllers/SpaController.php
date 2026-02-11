<?php

namespace App\Modules\Spa\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class SpaController
{
    public function index(): Response
    {
        $spaIndex = public_path('spa/index.html');

        // If SPA build exists, serve it. Otherwise fall back to the Laravel welcome page.
        if (File::exists($spaIndex)) {
            return response(File::get($spaIndex), 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
            ]);
        }

        return response(view('welcome')->render(), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}

