<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class EmailVerificationNoticeController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        if ($user->hasVerifiedEmail()) {
            return $user->isAdmin()
                ? redirect()->route('admin.attendance.list')
                : redirect()->route('attendance');
        }

        return view('auth.verify-email', [
            'verificationUrl' => URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
            ),
        ]);
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        if ($user->hasVerifiedEmail()) {
            return $user->isAdmin()
                ? redirect()->route('admin.attendance.list')
                : redirect()->route('attendance');
        }

        $user->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')->with('status', '認証メールを再送信しました。');
    }
}
