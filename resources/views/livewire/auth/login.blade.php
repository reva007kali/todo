<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input name="email" :label="__('Email address')" :value="old('email')" type="email" required
                autofocus autocomplete="email" placeholder="email@example.com" />

            <!-- Password -->
            <div class="relative">
                <flux:input name="password" :label="__('Password')" type="password" required
                    autocomplete="current-password" :placeholder="__('Password')" viewable />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __('Don\'t have an account?') }}</span>
                <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
            </div>
        @endif

        <!-- Separator -->
        <div class="flex items-center justify-between mt-4">
            <span class="w-1/5 border-b dark:border-gray-600 lg:w-1/4"></span>
            <a href="#" class="text-xs text-center text-gray-500 uppercase dark:text-gray-400 hover:underline">or
                login with</a>
            <span class="w-1/5 border-b dark:border-gray-400 lg:w-1/4"></span>
        </div>

        <!-- Google Button -->
        <div class="mt-4">
            <a href="{{ route('google.login') }}"
                class="flex items-center justify-center w-full px-4 py-2 transition-colors duration-300 transform border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">

                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M23.5 12.2857C23.5 11.3786 23.4143 10.6143 23.25 9.9H12.25V14.2286H18.6643C18.4214 15.6857 17.5 17.15 16.0357 18.15L16.0214 18.2857L19.5071 20.9429L19.75 20.9714C21.9214 19 23.5 15.9357 23.5 12.2857Z"
                        fill="#4285F4" />
                    <path
                        d="M12.25 23.5C15.4143 23.5 18.0714 22.4714 20.0143 20.7214L16.2714 17.8714C15.2214 18.5714 13.8714 19 12.25 19C9.20714 19 6.62143 16.9857 5.7 14.2714L5.56429 14.2857L1.92143 17.0571L1.87143 17.1857C3.80714 20.9714 7.75 23.5 12.25 23.5Z"
                        fill="#34A853" />
                    <path
                        d="M5.7 14.2714C5.46429 13.5714 5.33571 12.8214 5.33571 12.05C5.33571 11.2786 5.46429 10.5286 5.7 9.82857L5.69286 9.68571L2.01429 6.89286L1.97143 6.95C0.714286 9.4 0 12.15 0 15C0 17.85 0.714286 20.6 1.97143 23.05L5.7 14.2714Z"
                        fill="#FBBC05" />
                    <path
                        d="M12.25 5.1C13.9714 5.1 15.5 5.67857 16.7143 6.82143L20.0857 3.45C18.0714 1.57143 15.4143 0.5 12.25 0.5C7.75 0.5 3.80714 3.02857 1.87143 6.80714L5.59286 9.67857C6.51429 7.02857 9.1 5.1 12.25 5.1Z"
                        fill="#EB4335" />
                </svg>

                <span class="ml-2 text-sm font-medium text-gray-600 dark:text-gray-200">Sign in with Google</span>
            </a>
        </div>
    </div>
</x-layouts.auth>
