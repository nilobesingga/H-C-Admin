<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponser;
    public function doLogin(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required'], // Accepts either email or username
            'password' => ['required'],
        ]);

        // Determine if the input is an email or username
        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';

        if (Auth::attempt([$loginField => $credentials['login'], 'password' => $credentials['password']])) {

            $user = Auth::user();

            if ($user->bitrix_active == 1) {
                $user->update([
                    'last_login' => Carbon::now(),
                    'last_ip' => $request->getClientIp(),
                    'status' => 'online'
                ]);
            }

            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        } else {
            Auth::logout();
            return redirect()->back()->withErrors(['error' => 'No login access for this user']);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
    public function loginByAccessToken($accessToken, Request $request)
    {
        try{
            $user = User::where('access_token', $accessToken)->first();

            if ($user && $user->bitrix_active == 1) {
                Auth::login($user);
                $request->session()->regenerate();

                // Update user's last login details
                $user->update([
                    'last_login' => Carbon::now(),
                    'last_ip' => request()->getClientIp(),
                    'status' => 'online',
                ]);

                return redirect()->intended('dashboard');
            }
            else{
                Auth::logout();
                return redirect()->back()->withErrors(['error' => 'No login access for this user']);
            }
        } catch (\Exception $e){
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    public function logout()
    {
        if (Auth::check()) {
            User::whereId(Auth::id())->update(['status' => 'offline']);
            Auth::logout();

            return redirect()->route('login')->with('message', 'Logout Successfully');
        }
    }
    public function updatePassword(Request $request, $userId)
    {
        try {
            // Find the user
            $user = User::findOrFail($userId);

            $user->update([
                'password' => Hash::make($request->password),
                'is_default_password' => false,
                'updated_by' => Auth::id()
            ]);

            return $this->successResponse('User password updated successfully');

        } catch (\Exception $e){
            return $this->errorResponse('Oops! An error occurred. Please refresh the page or contact support', config('app.debug') === true ? $e->getMessage() : null, 500 );
        }
    }
}
