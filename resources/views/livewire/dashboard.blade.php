<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Services\ApiService;

new #[Layout('components.layout')] class extends Component {
    public $title = '';
    public $content = '';
    public $category_id = '';
    public $categories = [];
    public $user = [];

    public function mount(ApiService $apiService)
    {
        $this->user = $apiService->getCurrentUser();
        $this->categories = $apiService->getCategories()['data'] ?? [];
    }

    public function submitProp(ApiService $apiService)
    {
        $response = $apiService->createPost([
            'title' => $this->title,
            'content' => $this->content,
            'category_id' => $this->category_id,
        ]);

        if ($response && isset($response['data'])) {
            return redirect()->route('post.pending');
        }
        
        if ($response && isset($response['errors'])) {
            foreach ($response['errors'] as $field => $messages) {
                if ($field === 'system') {
                    session()->flash('error', is_array($messages) ? $messages[0] : $messages);
                } else {
                    $this->addError($field, is_array($messages) ? $messages[0] : $messages);
                }
            }
            return;
        }

        session()->flash('error', 'Yazı gönderilirken bir sorun oluştu.');
    }
};

?>

<div class="max-w-[1024px] mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row gap-6 justify-center items-start">

        <!-- Main Content -->
        <div class="w-full md:w-[640px] flex-shrink-0 space-y-4">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h1 class="text-xl font-extrabold text-gray-900 leading-tight">Yeni Gönderi</h1>
                    <p class="text-sm text-gray-500">Başlık, topluluk ve içeriğini girip paylaş.</p>
                </div>
                <a href="{{ route('home') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 hover:underline">
                    Geri dön
                </a>
            </div>

            <div class="bg-white rounded border border-gray-300 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-sm font-bold text-gray-800">
                        <svg class="w-5 h-5 text-[#FF4500]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Gönderi oluştur
                    </div>
                    <span class="text-xs text-gray-500">Zorunlu alanlar: Topluluk, Başlık, İçerik</span>
                </div>

                <form wire:submit="submitProp" class="p-4 space-y-4">
                    @if(session('error'))
                        <div class="text-red-500 text-sm p-2 bg-red-50 rounded mb-2">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Community -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">Topluluk</label>
                        <select wire:model="category_id"
                            class="w-full bg-white border border-gray-200 rounded text-sm p-2 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                            <option value="">Bir topluluk seçin</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat['id'] }}">r/{{ $cat['name'] }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Title -->
                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-800 mb-1">Başlık</label>
                        <input wire:model="title" type="text" maxlength="300"
                            class="w-full text-sm p-2 border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-black focus:border-black placeholder-gray-400"
                            placeholder="Başlık">
                        <div class="absolute right-2 top-9 text-xs text-gray-400 font-bold">{{ mb_strlen($title ?? '') }}/300</div>
                        @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Content -->
                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-800 mb-1">İçerik</label>
                        <textarea wire:model="content" rows="10"
                            class="w-full text-sm p-3 border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-black focus:border-black placeholder-gray-400 min-h-[220px]"
                            placeholder="Ne paylaşmak istiyorsun?"></textarea>
                        @error('content') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="border-t border-gray-200 pt-4 flex justify-end gap-2 mt-2">
                        <button type="submit"
                            class="px-6 py-1.5 bg-blue-600 text-white font-bold rounded-full text-sm hover:bg-blue-700 transition disabled:opacity-50">Gönder</button>
                    </div>

                </form>
            </div>
        </div>

        <!-- Sidebar (for consistency) -->
        <div class="hidden md:block w-80 flex-shrink-0 space-y-4">
            <div class="bg-white border border-gray-300 rounded overflow-hidden">
                <div class="bg-blue-500 h-10 pl-4 flex items-center">
                    <span class="text-white font-bold text-sm">Kurallar</span>
                </div>
                <div class="p-4">
                    <ol class="text-xs text-gray-600 space-y-2 list-decimal list-inside">
                        <li>İçeriği uygun kategoride paylaşın.</li>
                        <li>Başkalarına saygılı olun.</li>
                        <li>Spam yapmaktan kaçının.</li>
                        <li>Orijinal içerik paylaşmaya özen gösterin.</li>
                    </ol>
                </div>
            </div>

            <div class="bg-white border border-gray-300 rounded p-4 text-xs text-gray-500">
                <p>Yeni bir paylaşım yaparak topluluğa katkıda bulunuyorsunuz. Teşekkürler!</p>
            </div>
        </div>

    </div>
</div>