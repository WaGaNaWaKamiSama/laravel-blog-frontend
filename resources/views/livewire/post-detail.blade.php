<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;

new #[Layout('components.layout')] class extends Component {
    public $post;
    public $comments = [];
    public $newComment = '';
    public $slug;

    public function mount($slug, ApiService $apiService)
    {
        $this->slug = $slug;
        $response = $apiService->getPost($slug);

        if (!$response || !isset($response['data'])) {
            abort(404);
        }

        $this->post = $response['data'];
        $this->loadComments($apiService);
    }

    public function loadComments(ApiService $apiService)
    {
        if (isset($this->post['id'])) {
            $this->comments = $apiService->getComments($this->post['id'])['data'] ?? [];
        }
    }

    public function submitComment(ApiService $apiService)
    {
        if (!session('api_token')) {
            session()->flash('error', 'Yorum yapmak için giriş yapmalısınız.');
            return redirect()->route('login');
        }

        $this->validate(['newComment' => 'required|min:3|max:500']);

        $result = $apiService->createComment($this->post['id'], $this->newComment);

        if ($result) {
            $this->newComment = '';
            $this->loadComments($apiService);
            session()->flash('message', 'Yorumunuz başarıyla gönderildi.');
        } else {
            session()->flash('error', 'Yorum gönderilirken bir hata oluştu.');
        }
    }
};

?>

<div class="max-w-[1024px] mx-auto py-6">
    <div class="flex flex-col md:flex-row gap-6 justify-center">

    <div class="w-full md:w-2/3 lg:w-[740px]">

            <div class="bg-white border border-gray-300 rounded overflow-hidden flex mb-4">

            <div class="w-10 bg-white flex flex-col items-center pt-3 gap-1 border-r border-gray-100">
                    <button class="text-gray-400 hover:text-[#FF4500] focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7">
                            </path>
                        </svg>
                    </button>
                    <span class="text-xs font-bold text-gray-700">0</span>
                    <button class="text-gray-400 hover:text-blue-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                </div>


                <div class="flex-grow p-3">

                <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-2">
                        @if(isset($post['category']))
                            <span
                                class="font-bold text-gray-900 hover:underline cursor-pointer">r/{{ $post['category']['name'] }}</span>
                            <span class="text-gray-400">•</span>
                        @endif
                        <span>Posted by <span
                                class="hover:underline cursor-pointer">u/{{ $post['user']['name'] ?? 'Anonim' }}</span></span>
                        <span class="text-gray-400">•</span>
                        <span>{{ \Carbon\Carbon::parse($post['created_at'])->diffForHumans() }}</span>
                    </div>


                    <h1 class="text-xl font-medium text-gray-900 leading-snug mb-4 break-all"
                        style="word-break: break-all !important; overflow-wrap: anywhere !important;">
                        {{ $post['title'] }}
                    </h1>


                    <div class="mb-6">
                        @if(isset($post['image']))
                            <div
                                class="mb-6 flex justify-center bg-gray-50 border border-gray-200 rounded-md overflow-hidden">
                                <img src="{{ $post['image'] }}" alt="{{ $post['title'] }}"
                                    class="max-w-full max-h-[700px] object-contain">
                            </div>
                        @endif

                        <div class="prose prose-sm sm:prose max-w-none text-gray-800 break-all"
                            style="word-break: break-all !important; overflow-wrap: anywhere !important;">
                            {!! nl2br(e($post['content'] ?? '')) !!}
                        </div>
                    </div>


                    <div
                        class="flex items-center gap-4 text-gray-500 font-bold text-xs border-b border-gray-200 pb-4 mb-6">
                        <div
                            class="flex items-center gap-2 p-1.5 hover:bg-gray-200 rounded px-2 -ml-2 transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                </path>
                            </svg>
                            <span>{{ count($comments) }} Yorum</span>
                        </div>

                        <div
                            class="flex items-center gap-2 p-1.5 hover:bg-gray-200 rounded px-2 transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z">
                                </path>
                            </svg>
                            <span>Paylaş</span>
                        </div>

                        <div
                            class="flex items-center gap-2 p-1.5 hover:bg-gray-200 rounded px-2 transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                            <span>Kaydet</span>
                        </div>
                    </div>


                    <div class="">
                        @if(session('message'))
                            <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded relative mb-4"
                                role="alert">
                                <span class="block sm:inline">{{ session('message') }}</span>
                            </div>
                        @endif


                        @if(session('api_token'))
                            <div class="mb-8">
                                <p class="text-sm mb-1 text-gray-600">Comment as <span
                                        class="text-blue-500">u/{{ session('user_name') ?? 'User' }}</span></p>

                                        <form wire:submit="submitComment" class="relative">
                                    <textarea wire:model="newComment" rows="4"
                                        class="w-full border border-gray-300 rounded p-3 focus:outline-none focus:border-gray-500 focus:ring-0 resize-y min-h-[120px]"
                                        placeholder="What are your thoughts?"></textarea>
                                    <div
                                        class="bg-gray-100 flex justify-end p-2 border-x border-b border-gray-300 rounded-b -mt-1">
                                        <button type="submit" wire:loading.attr="disabled" wire:target="submitComment"
                                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-4 rounded-full text-sm disabled:opacity-50">
                                            <span wire:loading.remove wire:target="submitComment">Yorum Yap</span>
                                            <span wire:loading wire:target="submitComment">Gönderiliyor...</span>
                                        </button>
                                    </div>
                                    @error('newComment') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </form>
                            </div>
                        @else
                            <div
                                class="flex items-center justify-between border border-gray-300 rounded p-4 mb-8 bg-gray-50">
                                <span class="text-gray-600 font-medium">Yorum yapmak için giriş yapmalısın</span>
                                <div class="space-x-2">
                                    <a href="{{ route('login') }}"
                                        class="text-blue-600 font-bold border border-blue-600 px-4 py-1 rounded-full hover:bg-blue-50 transition">Giriş</a>
                                    <a href="{{ route('register') }}"
                                        class="bg-blue-600 text-white font-bold px-4 py-1 rounded-full hover:bg-blue-700 transition">Kayıt
                                        Ol</a>
                                </div>
                            </div>
                        @endif


                        <div class="space-y-6">
                            @forelse($comments as $comment)
                                <div class="flex gap-3 group">

                                <div class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($comment['user']['name'] ?? 'U') }}&background=EBF4FF&color=2563EB"
                                                class="w-full h-full">
                                        </div>
                                    </div>


                                    <div class="flex-grow">
                                        <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                                            <span
                                                class="font-bold text-gray-900">u/{{ $comment['user']['name'] ?? 'Anonim' }}</span>
                                            <span>•</span>
                                            <span>{{ \Carbon\Carbon::parse($comment['created_at'])->diffForHumans() }}</span>
                                        </div>
                                        <div class="text-sm text-gray-800 leading-relaxed break-all"
                                            style="word-break: break-all !important; overflow-wrap: anywhere !important;">
                                            {{ $comment['content'] }}
                                        </div>


                                        <div class="flex items-center gap-2 mt-1 -ml-2">
                                            <button
                                                class="flex items-center gap-1 p-1 rounded hover:bg-gray-100 text-gray-500 text-xs font-bold">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 15l7-7 7 7"></path>
                                                </svg>
                                                <span>1</span>
                                            </button>
                                            <button
                                                class="flex items-center gap-1 p-1 rounded hover:bg-gray-200 text-gray-500 text-xs font-bold">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                            <button
                                                class="flex items-center gap-1 p-1 rounded hover:bg-gray-200 text-gray-500 text-xs font-bold px-2">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                                    </path>
                                                </svg>
                                                Reply
                                            </button>
                                            <button
                                                class="text-xs text-gray-500 font-bold hover:bg-gray-200 p-1 rounded px-2">Share</button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10 text-gray-500 text-sm">
                                    Henüz yorum yok. İlk yorumu sen yap!
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="hidden md:block w-80 space-y-4">


        <div class="bg-white border border-gray-300 rounded overflow-hidden">
                <div class="bg-blue-500 h-10 pl-4 flex items-center">
                    <span class="text-white font-bold text-sm">Hakkında</span>
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-gray-200 rounded-full overflow-hidden">

                        <span
                                class="w-full h-full flex items-center justify-center text-xl font-bold text-gray-500">K</span>
                        </div>
                        <h2 class="font-bold text-gray-900 text-lg">Kle Blog</h2>
                    </div>
                    <p class="text-sm text-gray-600 mb-4 leading-relaxed">Topluluk tarafından oluşturulan içeriklerin
                        paylaşıldığı modern bir blog platformu.</p>

                    <div class="border-t border-gray-200 pt-4 grid grid-cols-2 gap-4 text-center mb-4">
                        <div>
                            <span class="block font-bold text-lg text-gray-900">{{ (int) data_get($post, 'comments_count', 0) }}</span>
                            <span class="text-xs text-gray-500">Yorum</span>
                        </div>
                        <div>
                            <span class="block font-bold text-lg text-gray-900 flex items-center justify-center gap-1">
                                <span class="w-2 h-2 bg-green-500 rounded-full inline-block"></span>
                                {{ \Carbon\Carbon::parse(data_get($post, 'created_at'))->diffForHumans() }}
                            </span>
                            <span class="text-xs text-gray-500">Yayın</span>
                        </div>
                    </div>

                    <a href="{{ route('dashboard') }}"
                        class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-full text-center text-sm transition-colors">Yazı
                        Oluştur</a>
                </div>
            </div>


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