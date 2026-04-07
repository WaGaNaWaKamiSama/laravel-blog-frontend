<?php

use Livewire\Volt\Component;
use App\Services\ApiService;

new class extends Component {
    public $contracts = [];

    public function mount(ApiService $apiService)
    {
        $this->contracts = $apiService->getContracts();
    }
};

?>

<footer class="border-t border-gray-300 bg-white mt-12">
    <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <img src="{{ asset('klelogo.svg') }}" alt="Kle Logo" class="w-6 h-6">
                <h3 class="font-bold text-gray-900 mb-1">Kle</h3>
            </div>
            <p class="text-sm text-gray-600">Her şeye dalın</p>

            <div class="flex flex-wrap justify-center gap-6 text-sm">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">Ana Sayfa</a>
                <a href="{{ route('categories') }}" class="text-gray-600 hover:text-gray-900">Topluluklar</a>
                
                @foreach($contracts as $contract)
                    <a href="{{ route('contracts.show', $contract['slug']) }}" class="text-gray-600 hover:text-gray-900">
                        {{ $contract['title'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="border-t border-gray-200 mt-6 pt-6 text-center text-sm text-gray-600">
            <p>&copy; {{ date('Y') }} Kle Blog Inc. Tüm hakları saklıdır.</p>
        </div>
    </div>
</footer>
