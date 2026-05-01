<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
            'password_changed_at' => now(),
        ]);

        return back()->with('status', 'password-updated');
    }

    public function editForcedChange(): View
    {
        return view('auth.change-password');
    }

    public function updateForcedChange(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'CHANGE_PASSWORD',
            'description' => 'Changed temporary password after admin reset',
        ]);

        return redirect()->route('dashboard')->with('success', 'Password changed successfully.');
    }
}
