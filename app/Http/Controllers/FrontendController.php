<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class FrontendController extends Controller
{
    public function index(): Response
    {
        $templateFile = base_path('mmtech-website/index.html');

        abort_unless(is_file($templateFile), 404, 'Template not found.');

        $html = file_get_contents($templateFile);

        $html = str_replace(
            ['href="css/', 'src="js/', 'src="images/'],
            ['href="/assets/css/', 'src="/assets/js/', 'src="/assets/images/'],
            $html
        );

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function asset(string $type, string $file)
    {
        $allowedTypes = ['css', 'js', 'images'];
        abort_unless(in_array($type, $allowedTypes, true), 404);

        // Prevent path traversal; template assets are flat files.
        if (str_contains($file, '/') || str_contains($file, '\\')) {
            abort(404);
        }

        $path = base_path("mmtech-website/{$type}/{$file}");
        abort_unless(is_file($path), 404);

        return response()->file($path, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
