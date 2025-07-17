<x-layouts.auth :title="__('Login')">
    <!-- Login Card -->
    <div class="w-full max-w-md mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-xl">
            <div class="p-8">
                <!-- Logo & Header -->
                <div class="text-center mb-8">
                    <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                        </svg> --}}
                        <img src="{{ asset('images/logo.png') }}" alt="Company Logo" class="h-10 w-10 rounded-full">
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ __('Welcome back') }}</h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">Sign in to your account to continue</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Email Input -->
                    <div>
                        <x-forms.input 
                            label="Email address" 
                            name="login" 
                            type="text" 
                            placeholder="email@escardastrading.com"
                            icon="envelope"
                            autocomplete="username"
                            required
                            autofocus
                        />
                    </div>

                    <!-- Password Input -->
                    <div>
                        <x-forms.input 
                            label="Password" 
                            name="password" 
                            type="password" 
                            placeholder="••••••••" 
                            icon="lock"
                            autocomplete="current-password"
                            required
                        />
                        <div class="mt-1 flex items-center justify-between">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 transition-colors duration-200">
                                    {{ __('Forgot password?') }}
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <x-forms.checkbox label="Remember me" name="remember" />
                    </div>

                    <!-- Login Button -->
                    <div>
                        <x-button type="primary" class="w-full py-3" rounded="lg">
                            {{ __('Sign In') }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </x-button>
                    </div>
                </form>

                {{-- @if (Route::has('register'))
                    <!-- Register Link -->
                    <div class="mt-8 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Don\'t have an account?') }}
                            <a href="{{ route('register') }}" class="font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 transition-colors duration-200">
                                {{ __('Sign up') }}
                            </a>
                        </p>
                    </div>
                @endif --}}
            </div>
        </div>
    </div>
</x-layouts.auth>