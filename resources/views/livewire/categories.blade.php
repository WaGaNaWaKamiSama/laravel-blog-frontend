<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Services\ApiService;

new #[Layout('components.layout')] class extends Component {
    public $categories = [];

    public function mount(ApiService $apiService)
    {
        $this->categories = $apiService->getCategories()['data'] ?? [];
        
        // Load post counts for each category
        foreach ($this->categories as &$category) {
            $filters = ['category' => $category['slug'] ?? $category['id']];
            $response = $apiService->getPosts($filters);
            $category['posts_count'] = count($response['data'] ?? []);
        }
    }
};

?>

<div class="max-w-[1024px] mx-auto py-6">
    <div class="flex flex-col md:flex-row gap-6">
        <div class="w-full md:w-2/3 lg:w-[640px] space-y-4">
            <div class="bg-white border border-gray-300 rounded p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-extrabold text-gray-900">Topluluklar</h1>
                        <p class="text-sm text-gray-500">Kategorilere göz at ve yazıları görüntüle.</p>
                    </div>
                    <span class="text-xs font-bold text-gray-500">{{ count($categories) }} kategori</span>
                </div>
            </div>

            @forelse($categories as $category)
                <a href="{{ route('posts.index', ['category' => $category['slug']]) }}" wire:navigate
                   class="bg-white border border-gray-300 rounded hover:border-gray-400 transition-colors flex items-center gap-4 p-4">
                    <div class="w-12 h-12 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 font-extrabold">
                        {{ mb_strtoupper(mb_substr($category['name'] ?? 'K', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-gray-900 truncate">r/{{ $category['name'] }}</h3>
                            <span class="text-xs text-gray-400">•</span>
                            <span class="text-xs text-gray-500">{{ $category['posts_count'] ?? 0 }} yazı</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 truncate">
                            Bu toplulukta en yeni gönderilere göz at.
                        </p>
                    </div>
                    <div class="text-sm font-bold text-blue-600">İncele →</div>
                </a>
            @empty
                <div class="bg-white border border-gray-300 rounded p-8 text-center">
                    <h3 class="text-lg font-bold text-gray-900">Kategori bulunamadı</h3>
                    <p class="text-sm text-gray-500 mt-1">Henüz kategori eklenmemiş.</p>
                </div>
            @endforelse
        </div>

        <div class="hidden md:block w-80 space-y-4">
            <div class="bg-white border border-gray-300 rounded overflow-hidden">
                <div class="bg-blue-500 h-10 pl-4 flex items-center">
                    <span class="text-white font-bold text-sm">Hakkında</span>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Topluluklar; içerikleri kategorilere ayırır. Bir topluluk seçip gönderilere göz atabilirsin.
                    </p>

                    <div class="border-t border-gray-200 mt-4 pt-4 grid grid-cols-2 gap-4 text-center">
                        <div>
                            <span class="block font-bold text-lg text-gray-900">{{ count($categories) }}</span>
                            <span class="text-xs text-gray-500">Kategori</span>
                        </div>
                        <div>
                            <span class="block font-bold text-lg text-gray-900">{{ array_sum(array_column($categories, 'posts_count')) }}</span>
                            <span class="text-xs text-gray-500">Toplam yazı</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-300 rounded p-4 text-xs text-gray-500">
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <a href="{{ route('home') }}" class="hover:underline">Ana Sayfa</a>
                    <a href="{{ route('dashboard') }}" class="hover:underline">Yazı Oluştur</a>
                    @if(session('api_token'))
                        <a href="{{ route('profile') }}" class="hover:underline">Profil</a>
                    @endif
                </div>
                <a href="{{ route('login') }}" class="hover:underline">Giriş</a>
                <p>&copy; {{ date('Y') }} Kle Blog Inc. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </div>
</div>