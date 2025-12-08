<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">

<head>
    @include('partials.head')

    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #334155;
        }
    </style>
</head>

<body class="h-full bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 overflow-hidden">

    <div class="flex min-h-full">

        <!-- BAGIAN KIRI: Visual & Branding (Desktop Only) -->
        <div class="relative hidden w-0 flex-1 lg:block">
            <!-- Animated Background -->
            <div class="absolute inset-0 h-full w-full bg-zinc-900 overflow-hidden">
                <!-- Glowing Orbs -->
                <div
                    class="absolute top-0 -left-4 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob">
                </div>
                <div
                    class="absolute top-0 -right-4 w-72 h-72 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000">
                </div>
                <div
                    class="absolute -bottom-8 left-20 w-72 h-72 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000">
                </div>

                <!-- Grid Overlay -->
                <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-zinc-900 via-zinc-900/50 to-transparent"></div>
            </div>

            <!-- Content Overlay -->
            <div class="absolute inset-0 flex flex-col justify-between p-12 z-10">
                <!-- Logo Area -->
                <div class="animate-fade-in">
                    <img class="size-18" src="favicon.png" alt="">
                </div>

                <!-- Testimonial / Quote Area -->
                <div class="space-y-6 max-w-lg animate-slide-up" style="animation-delay: 0.2s;">
                    <blockquote class="text-2xl font-medium text-white">
                        “This isn’t just another to-do app — it’s your AI-backed productivity sidekick. It predicts, prioritizes, organizes, and keeps you on track even when life gets messy. You handle the vision, it handles the workload.”
                    </blockquote>
                    <div class="flex items-center gap-4">
                        <img class="h-12 w-12 rounded-full ring-2 ring-white/20"
                            src="https://ui-avatars.com/api/?name=Reva+Sahabu&background=6366f1&color=fff"
                            alt="">
                        <div>
                            <div class="font-bold text-white">Revaldy Sahabu</div>
                            <div class="text-sm text-zinc-400">Creator of Todo by Reva</div>
                        </div>
                    </div>
                </div>

                <div class="text-xs text-zinc-500">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </div>
            </div>
        </div>

        <!-- BAGIAN KANAN: Login Form -->
        <div
            class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-white dark:bg-zinc-950 relative z-20">
            <div class="mx-auto w-full max-w-sm lg:w-96 animate-slide-up">

                <!-- Mobile Logo (Visible only on small screens) -->
                <div class="lg:hidden mb-8 text-center">
                    <svg class="mx-auto h-12 w-auto text-indigo-600 dark:text-indigo-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>

                <div>
                    <h2 class="mt-6 text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                        Welcome back
                    </h2>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                        Please enter your details to sign in.
                    </p>
                </div>

                <div class="mt-8">
                    <!-- Session Status -->
                    @if (session('status'))
                        <div
                            class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-700 dark:bg-green-900/30 dark:text-green-400">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="mt-6">
                        <form action="{{ route('login.store') }}" method="POST" class="space-y-6">
                            @csrf

                            <!-- Email Input -->
                            <div>
                                <label for="email"
                                    class="block text-sm font-medium leading-6 text-zinc-900 dark:text-zinc-200">
                                    Email address
                                </label>
                                <div class="mt-2 relative">
                                    <input id="email" name="email" type="email" autocomplete="email" required
                                        value="{{ old('email') }}"
                                        class="block w-full rounded-lg border-0 p-3 text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:bg-zinc-900 dark:text-white dark:ring-zinc-700 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6 transition-all duration-200"
                                        placeholder="name@example.com">
                                    <!-- Icon -->
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                        </svg>
                                    </div>
                                </div>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password Input -->
                            <div x-data="{ show: false }">
                                <label for="password"
                                    class="block text-sm font-medium leading-6 text-zinc-900 dark:text-zinc-200">
                                    Password
                                </label>
                                <div class="mt-2 relative">
                                    <input id="password" name="password" :type="show ? 'text' : 'password'"
                                        autocomplete="current-password" required
                                        class="block w-full rounded-lg border-0 p-3 text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:bg-zinc-900 dark:text-white dark:ring-zinc-700 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6 transition-all duration-200">

                                    <!-- Toggle Eye Icon -->
                                    <button type="button" @click="show = !show"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer focus:outline-none">
                                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input id="remember-me" name="remember" type="checkbox"
                                        class="h-4 w-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-600 dark:border-zinc-700 dark:bg-zinc-900 dark:checked:bg-indigo-500">
                                    <label for="remember-me"
                                        class="ml-2 block text-sm text-zinc-900 dark:text-zinc-300">
                                        {{ __('Remember me') }}
                                    </label>
                                </div>

                                @if (Route::has('password.request'))
                                    <div class="text-sm">
                                        <a href="{{ route('password.request') }}" wire:navigate
                                            class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                                            {{ __('Forgot password?') }}
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <button type="submit"
                                    class="flex w-full justify-center rounded-lg bg-indigo-600 px-3 py-3 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-200 transform hover:scale-[1.01] active:scale-[0.98]">
                                    {{ __('Sign in') }}
                                </button>
                            </div>
                        </form>

                        <!-- Google / Social Login -->
                        <div class="mt-8">
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                    <div class="w-full border-t border-zinc-200 dark:border-zinc-800"></div>
                                </div>
                                <div class="relative flex justify-center text-sm font-medium leading-6">
                                    <span class="bg-white px-6 text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
                                        Or continue with
                                    </span>
                                </div>
                            </div>

                            <div class="mt-6">
                                <a href="{{ route('google.login') }}"
                                    class="flex w-full items-center justify-center gap-3 rounded-lg bg-white dark:bg-zinc-900 px-3 py-3 text-sm font-semibold text-zinc-900 dark:text-white shadow-sm ring-1 ring-inset ring-zinc-300 dark:ring-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 focus-visible:ring-transparent transition-all duration-200 transform hover:scale-[1.01]">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
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
                                    <span class="text-[#1f1f1f] dark:text-[#e3e3e3]">Google</span>
                                </a>
                            </div>
                        </div>

                        <!-- Register Link -->
                        @if (Route::has('register'))
                            <p class="mt-8 text-center text-sm text-zinc-600 dark:text-zinc-400">
                                Not a member?
                                <a href="{{ route('register') }}" wire:navigate
                                    class="font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                                     Sign up now
                                </a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
        @fluxScripts

</body>

</html>
