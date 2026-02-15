<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    public function index($page = 'index.html')
    {
        $page = basename($page);

        if (!str_ends_with($page, '.html')) {
            abort(403);
        }

        $file = public_path("docs/{$page}");

        abort_unless(file_exists($file), 404);

        return response()->file($file);
    }
}
