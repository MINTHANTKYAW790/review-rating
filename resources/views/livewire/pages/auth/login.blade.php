<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-3">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="form.password" id="password"
                type="password"
                name="password"
                required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="d-flex justify-content-between mt-3">
    <div class="form-check">
        <input wire:model="form.remember" id="remember" type="checkbox" class="form-check-input" name="remember">
        <label for="remember" class="form-check-label text-muted">
            {{ __('Remember me') }}
        </label>
    </div>
    @if (Route::has('password.request'))
    <a class="text-decoration-none text-muted me-3" href="{{ route('password.request') }}" wire:navigate>
        {{ __('Forgot your password?') }}
    </a>
    @endif
</div>

<x-primary-button class="w-100 mt-4">
    {{ __('Log in') }}
</x-primary-button>



</form>

<br>

<div class="d-grid mb-4">
<a href="{{ route('auth.google') }}" class="btn btn-light btn-lg border shadow-sm d-flex align-items-center justify-content-center py-2" style="border-radius: 12px; font-size: 0.95rem;">
    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="me-2" style="width: 18px;">
    Continue with Google
</a>
</div>
<div class="mt-4 text-center">
<a class="text-decoration-none text-muted" href="{{ route('register') }}" wire:navigate>
    Don't you have an account? {{ __('Register') }}
</a>
</div>
</div>