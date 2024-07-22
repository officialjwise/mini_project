<?php

namespace App\Http\Controllers\Auth;

use App\Events\UsersActivityEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Jobs\SendConfirmationEmail;
use App\Models\Setting;
use App\Models\User;
use Google2FA;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('panel.authentication.login', [
            'plan' => request('plan'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {

        $settings = Setting::first();

        if ($settings->recaptcha_login && ($settings->recaptcha_sitekey || $settings->recaptcha_secretkey)) {
            $client = new Client();
            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret' => config('services.recaptcha.secret'),
                    'response' => $request->input('g-recaptcha-response'),
                ],
            ])->getBody()->getContents();

            if (! json_decode($response, true)['success']) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('Invalid Recaptcha.'),
                ], 401);
            }
        }

        if ((bool) $settings->login_without_confirmation == false) {
            $user = User::where('email', $request->email)->first();
            if (! $user) {
                $data = [
                    'errors' => [trans('auth.failed')],
                ];

                return response()->json($data, 401);
            }
            if ($user and $user->email_confirmed != 1 and $user->type != 'admin') {
                dispatch(new SendConfirmationEmail($user));
                $data = [
                    'errors' => [__('We have sent you an email for account confirmation. Please confirm your account to continue. Please also check your spam folder')],
                    'type' => 'confirmation',
                ];

                return response()->json($data, 401);
            }

        }

        $request->authenticate();

        $request->session()->regenerate();

        if (Auth::check()) {
            $user = Auth::user();
            if (Google2FA::isActivated()) {
                $user_id = Auth::id();

                Auth::logout();

                session()->put('user_id', $user_id);
                session()->save();

                return response()->json([
                    'link' => '2fa/login',
                ]);
            }

            $user = Auth::user();
            $ip = $request->ip();
            $connection = $request->header('User-Agent');
            event(new UsersActivityEvent($user->email, $user->type, $ip, $connection));
        }

        if ($plan = $request->get('plan')) {
            return response()->json([
                'link' => '/dashboard/user/payment?plan='.$plan,
            ]);
        }

        return response()->json([
            'link' => '/dashboard',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
