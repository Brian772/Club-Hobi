<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Exception;
use Throwable;

class SocialiteController extends Controller
{
    public function redirectToProvider($provider)
    {
        if (!in_array($provider, ['google'])) {
            return redirect()->route('login')->withErrors(['error' => 'Unsupported provider']);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialiteDriver = Socialite::driver($provider);

            if (!$socialiteDriver instanceof \Laravel\Socialite\Two\AbstractProvider) {
                return redirect()->route('login')->withErrors(['error' => 'Unsupported provider']);
            }

            $socialUser = $socialiteDriver->stateless()->user();

            $user = User::where('provider_name', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();

            if (!$user) {
                $user = User::where('email', $socialUser->getEmail())->first();

                if ($user) {
                    $user->update([
                        'provider_name' => $provider,
                        'provider_id' => $socialUser->getId(),
                    ]);
                } else {
                    $user = User::create(
                        [
                            'id' => (string) Str::uuid(),
                            'name' => $socialUser->getName(),
                            'email' => $socialUser->getEmail(),
                            'provider_name' => $provider,
                            'provider_id' => $socialUser->getId(),
                            'avatar_url' => $socialUser->getAvatar(),
                            'role_global' => 'member',
                            'status' => 'active',
                        ]
                    );
                }
            }

            Auth::login($user, true);

            return redirect()->intended('/dashboard');
        } catch (Throwable $th) {
            return redirect()->route('login')->with('error', 'Gagal login menggunakan ' . $provider);
        }
    }
}
