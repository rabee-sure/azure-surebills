<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\MerchantImpersonation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function login(Request $request)
    {
        $payload = MerchantImpersonation::validatePayload(
            $request->query('payload'),
            $request->query('signature')
        );

        abort_unless($payload, 403);

        $user = User::query()->findOrFail($payload['user_id']);

        abort_unless($user->canBeImpersonated(), 403);

        Auth::login($user);
        $request->session()->regenerate();

        session([
            'impersonated_by_admin' => [
                'id' => $payload['admin_id'] ?? null,
                'name' => $payload['admin_name'] ?? null,
            ],
        ]);

        return redirect()->route('home');
    }

    public function leave(Request $request)
    {
        if (! session()->has('impersonated_by_admin')) {
            return redirect()->route('home');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $adminUrl = config('app.admin_app_url');
        if ($adminUrl) {
            return redirect()->away(rtrim($adminUrl, '/').'/merchants');
        }

        return redirect()->route('login');
    }
}
