<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = 'user/login';

    /**
     * Show the reset password form.
     */
    public function showResetForm(Request $request, $token = null)
    {
        $email = $request->email;

        /*
         * If token or email is missing,
         * send user back to forgot password page.
         */
        if (!$token || !$email) {
            return redirect()->route('password.request');
        }

        /*
         * Find the user associated with this email.
         */
        $user = Password::broker()->getUser([
            'email' => $email
        ]);

        /*
         * Check whether the reset token is still valid.
         */
        if (!$user || !Password::broker()->tokenExists($user, $token)) {
            return view('auth.passwords.reset', [
                'expired' => true
            ]);
        }

        /*
         * Token is valid, so show the normal reset form.
         */
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $email,
            'expired' => false
        ]);
    }
}