<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Services\ApiService;

new #[Layout('components.layout')] class extends Component {
    public $slug;
    public $contract;

    public function mount($slug, ApiService $apiService)
    {
        $this->slug = $slug;
        $this->contract = $apiService->getContract($slug);

        if (!$this->contract) {
            abort(404);
        }
    }
};

?>

<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-10">
            <h1 class="text-3xl font-extrabold text-white">{{ $contract['title'] }}</h1>
            <p class="text-blue-100 mt-2">Son güncelleme: {{ \Carbon\Carbon::parse($contract['updated_at'])->format('d.m.Y') }}</p>
        </div>
        
        <div class="p-8 md:p-12 prose prose-blue max-w-none text-gray-700 leading-relaxed">
            {!! nl2br(e($contract['content'])) !!}
        </div>
        
        <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Ana Sayfaya Dön
            </a>
            <button onclick="window.print()" class="text-gray-500 hover:text-gray-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Yazdır
            </button>
        </div>
    </div>
</div>
