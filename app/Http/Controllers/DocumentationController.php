<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    public function index($page = 'index.html')
    {
        try{
            $htmlContent = file_get_contents(public_path('docs/'.$page));
            return response($htmlContent, 200)->header('Content-Type', 'text/html');
        } catch(Exception $e){
            abort(404);
        }
    }
}
