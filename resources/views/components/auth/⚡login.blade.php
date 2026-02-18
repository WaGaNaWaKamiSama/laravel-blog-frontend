<?php

use Livewire\Component;
use App\Services\ApiService;
use Livewire\Attributes\{Layout, Title, Validate};

new #[Layout('components.layout')] #[Title('Login - Kle Blog')] class extends Component {
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|min:6')]
    public string $password = '';

    public bool $remember = false;
    public string $errorMessage = '';

    public function login(ApiService $api)
    {
        $this->validate();

        try {
            $result = $api->login($this->email, $this->password);

            if ($result && isset($result['token'])) {
                session()->flash('success', 'Successfully logged in!');
                return redirect()->route('dashboard');
            }

            $this->errorMessage = 'Email or password is incorrect.';
        } catch (\Exception $e) {
            $this->errorMessage = 'An error occurred. Please try again.';
        }
    }
};
?>

<div class="container-custom py-12">
    <div class="max-w-md mx-auto">
        <div class="card p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Welcome Back</h1>
                <p class="text-sm text-gray-600">Login to your account</p>
            </div>

            <!-- Form -->
            <form wire:submit="login" class="space-y-6">
                <!-- Error Message -->
                @if($errorMessage)
                    <div class="p-3 text-sm text-red-600 bg-red-50 border border-red-200 rounded">
                        {{ $errorMessage }}
                    </div>
                @endif

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-900 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input 
                            type="email" 
                            id="email" 
                            wire:model="email"
                            class="input-field pl-10 @error('email') border-red-500 @enderror"
                            placeholder="example@email.com"
                        >
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-900 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input 
                            type="password" 
                            id="password" 
                            wire:model="password"
                            class="input-field pl-10 pr-10 @error('password') border-red-500 @enderror"
                            placeholder="••••••••"
                        >
                        </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember & Forgot -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            wire:model="remember"
                            class="w-4 h-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900"
                        >
                        <span class="ml-2 text-sm text-gray-600">Remember Me</span>
                    </label>
                    <a href="#" class="text-sm text-gray-900 hover:underline">Forgot Password?</a>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="btn-primary w-full" 
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Login</span>
                    <span wire:loading class="flex items-center justify-center">
                        <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Logging in...
                    </span>
                </button>

                <!-- Divider -->
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">or</span>
                    </div>
                </div>

                <!-- Register Link -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account? 
                        <a href="/register" class="text-gray-900 font-medium hover:underline">Sign Up</a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-6">
            <a href="/" class="text-sm text-gray-600 hover:text-gray-900">← Back to Home</a>
        </div>
    </div>
</div>
