<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Services\ApiService;

new #[Layout('components.layout')] class extends Component {
    use WithPagination;

    public $search = '';
    public $category = '';
    public $categories = [];
    public $posts = [];

    public function mount(ApiService $apiService)
    {
        $this->search = request('search', '');
        $this->category = request('category', '');
        $this->categories = $apiService->getCategories()['data'] ?? [];
        $this->loadPosts();
    }

    public function updatedSearch()
    {
        $this->loadPosts();
    }

    public function updatedCategory()
    {
        $this->loadPosts();
    }

    public function loadPosts()
    {
        $apiService = app(ApiService::class);
        $filters = [];
        if ($this->search)
            $filters['search'] = $this->search;
        if ($this->category)
            $filters['category'] = $this->category;

        $response = $apiService->getPosts($filters);
        $this->posts = $response['data'] ?? [];
        
        // Load comment counts for each post
        foreach ($this->posts as &$post) {
            $commentsResponse = $apiService->getComments($post['id']);
            $commentCount = count($commentsResponse['data'] ?? []);
            $post['comments_count'] = $commentCount;
        }
    }
};


?>


<div class="max-w-[1024px] mx-auto py-6">
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Layout Container -->
    <div class="flex flex-col md:flex-row gap-6 justify-center">

        <!-- Feed Column -->
        <div class="w-full md:w-2/3 lg:w-[640px] space-y-4">

            <!-- Filters -->
            <div class="bg-white p-4 rounded border border-gray-300 flex flex-col sm:flex-row gap-2 mb-4 items-start sm:items-center justify-between">
                <div class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V19a1 1 0 01-1.447.894l-4-2A1 1 0 018 17.999v-4.585L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    Filtreler
                    @if($this->search)
                        <span class="ml-2 inline-flex items-center gap-1 text-xs font-semibold bg-gray-100 border border-gray-200 rounded-full px-2 py-1">
                            "{{ $this->search }}"
                            <button wire:click="$set('search', '')" class="text-gray-500 hover:text-gray-800" title="Aramayı temizle">✕</button>
                        </span>
                    @endif
                </div>

                <div class="w-full sm:w-auto flex gap-2">
                    <input wire:model.live.debounce.300ms="search" 
                        type="text" 
                        placeholder="Ara..." 
                        class="w-full sm:w-64 bg-gray-100 border border-gray-200 rounded px-4 py-2 focus:outline-none focus:border-blue-500 hover:bg-white">
                    <select wire:model.change="category"
                        class="w-full sm:w-64 bg-gray-100 border border-gray-200 rounded px-4 py-2 focus:outline-none focus:border-blue-500 hover:bg-white cursor-pointer">
                        <option value="">Tüm Topluluklar</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat['slug'] ?? $cat['id'] }}">r/{{ $cat['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @forelse($posts as $post)
                <!-- Reddit Post Card -->
                <div class="bg-white border border-gray-300 rounded hover:border-gray-400 cursor-pointer transition-colors flex overflow-hidden">
                    <!-- Vote Sidebar -->
                    <div class="w-10 bg-gray-50/50 flex flex-col items-center pt-3 gap-1 border-r border-gray-100/50">
                        <svg class="w-6 h-6 text-gray-400 hover:text-[#FF4500]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                        <span class="text-xs font-bold text-gray-700">0</span>
                        <svg class="w-6 h-6 text-gray-400 hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>

                    <!-- Post Content -->
                    <a href="{{ route('posts.show', $post['slug']) }}" wire:navigate class="flex-grow p-3 hover:bg-gray-50/30">
                        <!-- Header -->
                        <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-2">
                            @if(isset($post['category']))
                                <span class="font-bold text-gray-900 hover:underline z-10 relative">r/{{ $post['category']['name'] }}</span>
                                <span class="text-gray-400">•</span>
                            @endif
                            <span>Posted by <span class="hover:underline">u/{{ $post['user']['name'] ?? 'Anonim' }}</span></span>
                            <span class="text-gray-400">•</span>
                            <span>{{ \Carbon\Carbon::parse($post['created_at'])->diffForHumans() }}</span>
                        </div>

                        <!-- Title -->
                        <h3 class="text-lg font-medium text-gray-900 leading-snug mb-3">{{ $post['title'] }}</h3>

                        <!-- Preview (Image/Text) -->
                        <div class="mb-3">
                            @if(isset($post['image']))
                                <div class="max-h-[512px] overflow-hidden rounded-md border border-gray-200 flex justify-center bg-black/5 relative group">
                                    <div class="absolute inset-0 bg-cover bg-center blur-xl opacity-50" style="background-image: url('{{ $post['image'] }}')"></div>
                                    <img src="{{ $post['image'] }}" class="relative max-w-full max-h-[512px] object-contain">
                                </div>
                            @elseif(isset($post['excerpt']) && $post['excerpt'])
                                <p class="text-sm text-gray-600 font-normal leading-relaxed">{{ $post['excerpt'] }}</p>
                            @endif
                        </div>

                        <!-- Footer Actions -->
                        <div class="flex items-center gap-4 text-gray-500 font-bold text-xs">
                            <div class="flex items-center gap-2 p-1.5 hover:bg-gray-200 rounded px-2 -ml-2 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span>{{ $post['comments_count'] ?? '0' }} Yorum</span>
                            </div>

                            <div class="flex items-center gap-2 p-1.5 hover:bg-gray-200 rounded px-2 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                </svg>
                                <span>Paylaş</span>
                            </div>

                            <div class="flex items-center gap-2 p-1.5 hover:bg-gray-200 rounded px-2 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                </svg>
                                <span>Kaydet</span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="bg-white border border-gray-300 rounded p-8 text-center flex flex-col items-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Burada hiç bir şey yok!</h3>
                    <p class="text-gray-500 mt-2 text-sm">Aradığınız kriterlere uygun yazı bulunamadı.</p>
                </div>
            @endforelse
        </div>

        <!-- Sidebar -->
        <div class="hidden md:block w-80 space-y-4">

            <!-- About Community -->
            <div class="bg-white border border-gray-300 rounded overflow-hidden">
                <div class="bg-blue-500 h-10 pl-4 flex items-center">
                    <span class="text-white font-bold text-sm">Hakkında</span>
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-gray-200 rounded-full overflow-hidden">
                            <!-- Logo placeholder -->
                            <span
                                class="w-full h-full flex items-center justify-center text-xl font-bold text-gray-500">K</span>
                        </div>
                        <h2 class="font-bold text-gray-900 text-lg">Kle Blog</h2>
                    </div>
                    <p class="text-sm text-gray-600 mb-4 leading-relaxed">Topluluk tarafından oluşturulan
                        içeriklerin
                        paylaşıldığı modern bir blog platformu.</p>

                    <div class="border-t border-gray-200 pt-4 grid grid-cols-2 gap-4 text-center mb-4">
                        <div>
                            <span class="block font-bold text-lg text-gray-900">{{ count($posts) }}</span>
                            <span class="text-xs text-gray-500">Yazı</span>
                        </div>
                        <div>
                            <span class="block font-bold text-lg text-gray-900 flex items-center justify-center gap-1">
                                <span class="w-2 h-2 bg-green-500 rounded-full inline-block"></span>
                                {{ count($categories) }}
                            </span>
                            <span class="text-xs text-gray-500">Kategori</span>
                        </div>
                    </div>

                    <a href="{{ route('dashboard') }}"
                        class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-full text-center text-sm transition-colors">Yazı
                        Oluştur</a>
                </div>
            </div>

            <!-- Rules / Footer -->
            <div class="bg-white border border-gray-300 rounded p-4 text-xs text-gray-500">
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <a href="{{ route('home') }}" class="hover:underline">Ana Sayfa</a>
                    <a href="{{ route('categories') }}" class="hover:underline">Kategoriler</a>
                    @if(session('api_token'))
                        <a href="{{ route('profile') }}" class="hover:underline">Profil</a>
                    @else
                        <a href="{{ route('login') }}" class="hover:underline">Giriş</a>
                    @endif
                </div>
                <p>&copy; 2026 Kle Blog Inc. Tüm hakları saklıdır.</p>
            </div>

        </div>
    </div>
</div>