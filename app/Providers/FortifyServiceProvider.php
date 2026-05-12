<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use App\Models\User; // Pastikan ini di-import di paling atas
use Illuminate\Support\Facades\Hash; // Pastikan ini juga di-import
class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();

        Fortify::authenticateUsing(function (Request $request) {
            // Ambil input dari field 'email' (atau ganti jadi 'login' jika kamu sudah ubah di view)
            $login = $request->input('email'); 
    
            $user = User::where('email', $login)
                        ->orWhere('username', $login)
                        ->first();
    
            if ($user && Hash::check($request->password, $user->password)) {
                // Cek juga apakah user aktif (opsional)
                if (!$user->is_active) {
                    return null; 
                }
                return $user;
            }
            
            return null;
        });
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        Fortify::loginView(fn () => view('pages::auth.login'));
        Fortify::verifyEmailView(fn () => view('pages::auth.verify-email'));
        Fortify::twoFactorChallengeView(fn () => view('pages::auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn () => view('pages::auth.confirm-password'));
        Fortify::registerView(fn () => view('pages::auth.register'));
        Fortify::resetPasswordView(fn () => view('pages::auth.reset-password'));
        Fortify::requestPasswordResetLinkView(fn () => view('pages::auth.forgot-password'));
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            // Gunakan input 'email' (atau 'login') sebagai kunci pembatas
            $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());
    
            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
