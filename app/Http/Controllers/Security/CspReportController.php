<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class CspReportController extends Controller
{
    public function store(Request $request): Response
    {
        $payload = $request->json()->all();

        // Browsers may post either `csp-report` or a top-level report object.
        $report = $payload['csp-report'] ?? $payload;

        Log::channel('csp_violations')->warning('csp_violation', [
            'report' => $report,
            'headers' => [
                'user-agent' => $request->userAgent(),
                'referer' => $request->headers->get('referer'),
                'origin' => $request->headers->get('origin'),
                'x-forwarded-for' => $request->headers->get('x-forwarded-for'),
            ],
            'ip' => $request->ip(),
            'received_at' => now()->toIso8601String(),
        ]);

        return response()->noContent();
    }
}
