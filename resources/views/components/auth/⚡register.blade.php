<?php

use Livewire\Component;
use App\Services\ApiService;
use Livewire\Attributes\{Layout, Title, Validate};

new #[Layout('components.layout')] #[Title('Sign Up - Kle Blog')] class extends Component {
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $terms = false;
    public $errorMessage = '';

    public function updated($field)
    {
        // Clear validation errors when user starts typing
        $this->resetErrorBag($field);
    }

    public function register(ApiService $api)
    {
        $this->resetErrorBag();
        $this->errorMessage = '';

        if (empty($this->name)) {
            $this->addError('name', 'Name is required.');
        } elseif (strlen($this->name) < 3) {
            $this->addError('name', 'Name must be at least 3 characters.');
        }

        if (empty($this->email)) {
            $this->addError('email', 'Email is required.');
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->addError('email', 'Email must be a valid email address.');
        }

        if (empty($this->password)) {
            $this->addError('password', 'Password is required.');
        } elseif (strlen($this->password) < 6) {
            $this->addError('password', 'Password must be at least 6 characters.');
        }

        if (empty($this->password_confirmation)) {
            $this->addError('password_confirmation', 'Password confirmation is required.');
        } elseif ($this->password !== $this->password_confirmation) {
            $this->addError('password_confirmation', 'The password confirmation does not match.');
        }

        if (!$this->terms) {
            $this->errorMessage = 'You must accept the terms and conditions.';
        }

        if ($this->getErrorBag()->any() || $this->errorMessage) {
            return;
        }

        try {
            $result = $api->register($this->name, $this->email, $this->password, $this->password_confirmation);

            if ($result && isset($result['token'])) {
                session()->flash('success', 'Registration successful! Welcome.');
                return redirect()->route('dashboard');
            }

            if ($result && isset($result['errors'])) {
                $errors = $result['errors'];
                
                if (isset($errors['email'])) {
                    $this->addError('email', is_array($errors['email']) ? $errors['email'][0] : $errors['email']);
                }
                if (isset($errors['name'])) {
                    $this->addError('name', is_array($errors['name']) ? $errors['name'][0] : $errors['name']);
                }
                if (isset($errors['password'])) {
                    $this->addError('password', is_array($errors['password']) ? $errors['password'][0] : $errors['password']);
                }
            } else {
                $this->errorMessage = 'An error occurred during registration. Please try again.';
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Connection error. Please check if the backend is running.';
        }
    }
};
?>

<div class="container-custom py-12">
    <div class="max-w-md mx-auto">
        <div class="card p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Create Account</h1>
                <p class="text-sm text-gray-600">Enter your information to create a new account</p>
                

            </div>

            <!-- Form -->
            <form wire:submit="register" class="space-y-6">
                <!-- Error Message -->
                @if($errorMessage)
                    <div class="p-3 text-sm text-red-600 bg-red-50 border border-red-200 rounded">
                        {{ $errorMessage }}
                    </div>
                @endif

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-900 mb-2">
                        Full Name
                    </label>
                    <div class="relative">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            id="name" 
                            wire:model.debounce.300ms="name"
                            class="input-field pl-10 @error('name') border-red-500 @enderror"
                            placeholder="Your Name"
                        >
                    </div>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

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
                            wire:model.debounce.300ms="email"
                            class="input-field pl-10 @error('email') border-red-500 @enderror"
                            placeholder="email@gmail.com"
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
                            wire:model.debounce.300ms="password"
                            class="input-field pl-10 @error('password') border-red-500 @enderror"
                            placeholder="Create a strong password"
                        >
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Must be at least 6 characters</p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-900 mb-2">
                        Confirm Password
                    </label>
                    <div class="relative">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            wire:model.debounce.300ms="password_confirmation"
                            class="input-field pl-10 @error('password_confirmation') border-red-500 @enderror"
                            placeholder="Confirm your password"
                        >
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Terms -->
                <div class="flex items-start">
                    <input 
                        type="checkbox" 
                        id="terms" 
                        wire:model="terms"
                        class="mt-1 w-4 h-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900"
                    >
                    <label for="terms" class="ml-2 text-sm text-gray-600">
                        I agree to the <a href="#" class="text-gray-900 underline">Terms and Conditions</a> and <a href="#" class="text-gray-900 underline">Privacy Policy</a>
                    </label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="btn-primary w-full" 
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Create Account</span>
                    <span wire:loading class="flex items-center justify-center">
                        <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating Account...
                    </span>
                </button>

                <!-- Login Link -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account? 
                        <a href="/login" class="text-gray-900 font-medium hover:underline">Login</a>
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
