<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="text('auth.login.email', 'Email')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full text-left"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                inputmode="email"
                dir="ltr"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="text('auth.login.password', 'Password')" />

            <x-text-input
                id="password"
                class="block mt-1 w-full text-left"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                dir="ltr"
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 shadow-sm" style="accent-color: var(--color-primary);" name="remember">
                <span class="ms-2 text-sm text-muted">{{ text('auth.login.remember', 'Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-muted hover:text-theme rounded-md focus:outline-none" href="{{ route('password.request') }}">
                    {{ text('auth.login.forgot', 'Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ text('auth.login.submit', 'Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
