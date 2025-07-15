<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

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
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'last_login' => Carbon::now(),
                        'last_ip' => $request->getClientIp(),
                        'status' => 'online'
                    ]);
            }

            $request->session()->regenerate();
            if($user->is_admin) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('dashboard');
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
                if($user->is_admin) {
                    return redirect()->route('admin.dashboard');
                }
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
    public function verify2FA(Request $request)
    {
        $request->validate([
            'text' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $code = $request->input('text');

        // Here you should implement your 2FA verification logic
        // For example, checking against authenticator app code
        if ($this->verifyAuthenticatorCode($user, $code)) {
            // Mark as verified and redirect to dashboard
            session(['2fa_verified' => true]);
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'text' => 'Invalid verification code.',
        ]);
    }

    protected function verifyAuthenticatorCode($user, $code)
    {
        // Implement your verification logic here
        // This is just a placeholder - you should implement actual 2FA verification
        // using Google Authenticator or similar service
        return true; // For testing purposes
    }
    public function register(Request $request)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                // 'email' => 'required|string|email|unique:users',
                'user_name' => 'required|string|email|unique:users',
                'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
                'bitrix_contact_id' => 'required|string|unique:users',
            ]);

            // Create a new user
            $user = User::updateOrCreate(
                [
                    'bitrix_contact_id' => $request['bitrix_contact_id'],
                ],
                [
                'email' => $request['user_name'],
                'user_name' => $request['user_name'],
                'password' => Hash::make($request['password']),
                'bitrix_contact_id' => $request['bitrix_contact_id'] ?? 0,
                'bitrix_user_id' => $request['bitrix_user_id'] ?? 0,
                'access_token' => Str::random(64), // Legacy token for backward compatibility
                'bitrix_active' => 1,
                'is_default_password' => false,
                'status' => 'offline',
                'created_by' => 0, // System created
                'type' => 'client', // Default to active
            ]);

            // Create a personal access token that expires in 7 days
            $tokenResult = $user->createToken(
                'auth_token',
                ['*'],
                now()->addDays(7)
            );
            $expiresAt = now()->addDays(7)->toDateTimeString();
            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'data' => [
                    'user' => $user->only(['id', 'email', 'user_name', 'bitrix_contact_id', 'bitrix_user_id']),
                    'access_token' => $tokenResult->plainTextToken,
                    'token_type' => 'Bearer',
                    'expires_at' => $expiresAt
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 200);
        }
    }

    /**
     * API login method that returns a token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiLogin(Request $request)
    {
        try {
            $credentials = $request->validate([
                'login' => ['required'], // Accepts either email or username
                'password' => ['required'],
            ]);

            // Determine if the input is an email or username
            $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';

            if (Auth::attempt([$loginField => $credentials['login'], 'password' => $credentials['password']])) {
                $user = Auth::user();

                if ($user->bitrix_active != 1) {
                    Auth::logout();
                    return $this->errorResponse('No login access for this user', null, 401);
                }

                // Update user status
                $user->update([
                    'last_login' => Carbon::now(),
                    'last_ip' => $request->getClientIp(),
                    'status' => 'online'
                ]);

                // Revoke all existing tokens
                $user->tokens()->delete();

                // Create a new token
                $tokenResult = $user->createToken(
                    'auth_token',
                    ['*'],
                    now()->addDays(7)
                );

                return $this->successResponse('Login successful', [
                    'user' => $user->only(['id', 'email', 'user_name', 'bitrix_contact_id', 'bitrix_user_id']),
                    'access_token' => $tokenResult->plainTextToken,
                    'token_type' => 'Bearer',
                    'expires_at' => $tokenResult->accessToken->expires_at
                ]);
            }

            return $this->errorResponse('Invalid credentials', null, 401);
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Login failed',
                config('app.debug') === true ? $e->getMessage() : null,
                500
            );
        }
    }

    /**
     * Refresh the user's token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request)
    {
        try {
            // Revoke the current token
            $request->user()->tokens()->where('id', $request->user()->currentAccessToken()->id)->delete();

            // Create a new token
            $tokenResult = $request->user()->createToken(
                'auth_token',
                ['*'],
                now()->addDays(7)
            );

            return $this->successResponse('Token refreshed', [
                'access_token' => $tokenResult->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $tokenResult->accessToken->expires_at
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to refresh token',
                config('app.debug') === true ? $e->getMessage() : null,
                500
            );
        }
    }

    /**
     * Logout the user (API method)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiLogout(Request $request)
    {
        try {
            // Update user status
            $request->user()->update(['status' => 'offline']);

            // Revoke the token that was used to authenticate the current request
            if ($request->user()) {
                if ($request->user()->currentAccessToken()) {
                    $request->user()->currentAccessToken()->delete();
                }

                // You can also revoke all tokens if needed
                // $request->user()->tokens()->delete();
            }

            return $this->successResponse('Logged out successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to logout',
                config('app.debug') === true ? $e->getMessage() : null,
                500
            );
        }
    }

    /**
     * Generate an admin token for system integration
     * Only admin users can access this endpoint
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateAdminToken(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'expires_in_days' => 'nullable|integer|min:1',
                'abilities' => 'nullable|array',
                'abilities.*' => 'string',
            ]);

            // Default token expiration (can be longer for system integrations)
            $expiresAt = $validated['expires_in_days']
                ? now()->addDays($validated['expires_in_days'])
                : now()->addYear(); // Default to 1 year for admin tokens

            // Default abilities for admin tokens
            $abilities = $validated['abilities'] ?? ['*'];

            // Create a new token with admin privileges
            $tokenResult = $request->user()->createToken(
                $validated['name'],
                $abilities,
                $expiresAt
            );

            // Log the creation of an admin token
            Log::info('Admin token created', [
                'user_id' => $request->user()->id,
                'token_name' => $validated['name'],
                'expires_at' => $expiresAt,
            ]);

            return $this->successResponse('Admin token generated successfully', [
                'token_name' => $validated['name'],
                'access_token' => $tokenResult->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $tokenResult->accessToken->expires_at,
                'abilities' => $abilities
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to generate admin token',
                config('app.debug') === true ? $e->getMessage() : null,
                500
            );
        }
    }

    /**
     * List all admin tokens for the current user
     * Only admin users can access this endpoint
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listAdminTokens(Request $request)
    {
        try {
            // Get all tokens for the user
            $tokens = $request->user()->tokens()->get()->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'abilities' => $token->abilities,
                    'last_used_at' => $token->last_used_at,
                    'expires_at' => $token->expires_at,
                    'created_at' => $token->created_at
                ];
            });

            return $this->successResponse('Admin tokens retrieved successfully', [
                'tokens' => $tokens
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve admin tokens',
                config('app.debug') === true ? $e->getMessage() : null,
                500
            );
        }
    }

    /**
     * Revoke a specific admin token
     * Only admin users can access this endpoint
     *
     * @param Request $request
     * @param int $tokenId
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokeAdminToken(Request $request, $tokenId)
    {
        try {
            // Find the token
            $token = $request->user()->tokens()->where('id', $tokenId)->first();

            if (!$token) {
                return $this->errorResponse('Token not found', null, 404);
            }

            // Revoke the token
            $token->delete();

            // Log the revocation
            Log::info('Admin token revoked', [
                'user_id' => $request->user()->id,
                'token_id' => $tokenId,
            ]);

            return $this->successResponse('Admin token revoked successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to revoke admin token',
                config('app.debug') === true ? $e->getMessage() : null,
                500
            );
        }
    }

    /**
     * Verify the validity of a token
     * This endpoint is useful for other systems to check if a token is valid
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyToken(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return $this->errorResponse('No token provided', null, 401);
            }

            $accessToken = \App\Models\PersonalAccessToken::findToken($token);

            if (!$accessToken) {
                return $this->errorResponse('Invalid token', null, 401);
            }

            // Check if token is expired
            if ($accessToken->expires_at && now()->greaterThan($accessToken->expires_at)) {
                return $this->errorResponse('Token has expired', null, 401);
            }

            // Get the user associated with the token
            $user = $accessToken->tokenable;

            // Update last used timestamp
            $accessToken->last_used_at = now();
            $accessToken->save();

            return $this->successResponse('Token is valid', [
                'token_id' => $accessToken->id,
                'token_name' => $accessToken->name,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'is_admin' => (bool) $user->is_admin
                ],
                'expires_at' => $accessToken->expires_at,
                'abilities' => $accessToken->abilities
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Token verification failed',
                config('app.debug') === true ? $e->getMessage() : null,
                500
            );
        }
    }

    public function accountSetting()
    {
        $user = User::with([
            'modules' => function ($q) {
                $q->where('parent_id', 0)->orderBy('order', 'ASC');
            },
            'modules.children' => function ($q) {
                // Inner join to include only user-assigned child modules
                $q->join('user_module_permission as ump', function ($join) {
                    $join->on('modules.id', '=', 'ump.module_id')
                        ->where('ump.user_id', Auth::id());
                })
                ->orderBy('modules.order', 'ASC'); // Fallback to module order
            },
            'categories'
        ])->whereId(Auth::id())->first();
        $contact = Contact::where('contact_id', $user->bitrix_contact_id)->first();
        if($user){
            $page = (Object) [
                    'title' => 'Account Settings',
                    'description' => 'Manage your account settings and personal information.',
                    'user' => $user,
            ];

            return view('settings.account', compact('page', 'user', 'contact'));
        }
    }

    /**
     * Update the authenticated user's username and/or password
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAccount(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $validated = $request->validate([
            'username' => 'required|string|email|unique:users,user_name,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        try {
            $user->user_name = $validated['username'];
            $user->email = $validated['username'];
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
                $user->is_default_password = false;
            }
            $user->updated_by = $user->id;
            $user->save();

            return $this->successResponse('Account updated successfully', [
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'user_name' => $user->user_name
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update account', config('app.debug') === true ? $e->getMessage() : null, 500);
        }
    }
}
