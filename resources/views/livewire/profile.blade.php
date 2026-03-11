<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Services\ApiService;

new #[Layout('components.layout')] class extends Component {
    public array|null $me = null;
    public array|null $user = null;

    public $name = '';
    public $email = '';
    public $password = '';

    public function mount(ApiService $apiService)
    {
        $this->me = $apiService->getCurrentUser();
        $this->user = data_get($this->me, 'user')
            ?? data_get($this->me, 'data')
            ?? (is_array($this->me) ? $this->me : null);

        if ($this->user) {
            $this->name = data_get($this->user, 'name', '');
            $this->email = data_get($this->user, 'email', '');
        }
    }

    public function updateProfile(ApiService $apiService)
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if (!empty($this->password)) {
            $data['password'] = $this->password;
        }

        $response = $apiService->updateProfile($data);

        if ($response && isset($response['success'])) {
            session()->flash('success', 'Profil başarıyla güncellendi.');
            $this->user = $response['user'];
        } elseif ($response && isset($response['errors'])) {
            foreach ($response['errors'] as $field => $messages) {
                if ($field === 'system') {
                    session()->flash('error', is_array($messages) ? $messages[0] : $messages);
                } else {
                    $this->addError($field, is_array($messages) ? $messages[0] : $messages);
                }
            }
        } else {
            session()->flash('error', 'Profil güncellenirken bir sorun oluştu.');
        }
    }
};

?>

<div class="max-w-[1024px] mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row gap-6 justify-center items-start">
        <div class="w-full md:w-[640px] flex-shrink-0 space-y-4">
            <div class="bg-white border border-gray-300 rounded overflow-hidden">
                <div class="h-24 bg-gradient-to-r from-blue-600 to-blue-500"></div>
                <div class="p-4 -mt-10">
                    <div class="flex items-end gap-4">
                        <div class="w-20 h-20 rounded-full ring-4 ring-white overflow-hidden bg-gray-200 flex items-center justify-center">
                            <img
                                src="https://ui-avatars.com/api/?name={{ urlencode(data_get($user, 'name', session('user_name', 'U'))) }}&background=random&size=128"
                                alt="Avatar"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <h1 class="text-xl font-extrabold text-gray-900 leading-tight">
                                {{ data_get($user, 'name', session('user_name', 'Kullanıcı')) }}
                            </h1>
                            <p class="text-sm text-gray-500">
                                u/{{ data_get($user, 'name', session('user_name', 'user')) }}
                            </p>
                        </div>
                        <div class="hidden sm:flex gap-2">
                            <a href="{{ route('dashboard') }}"
                               class="px-4 py-2 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold transition-colors">
                                Yazı Oluştur
                            </a>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="bg-gray-50 border border-gray-200 rounded p-3">
                            <p class="text-xs text-gray-500">E-posta</p>
                            <p class="text-sm font-semibold text-gray-900 truncate">
                                {{ data_get($user, 'email', session('user_email', '-')) }}
                            </p>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded p-3">
                            <p class="text-xs text-gray-500">Durum</p>
                            <p class="text-sm font-semibold text-gray-900">
                                Aktif
                            </p>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded p-3">
                            <p class="text-xs text-gray-500">Karma</p>
                            <p class="text-sm font-semibold text-gray-900">
                                1
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-300 rounded p-4">
                <h2 class="font-bold text-gray-900 mb-2">Hızlı İşlemler</h2>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('categories') }}"
                       class="px-4 py-2 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold transition-colors">
                        Kategoriler
                    </a>
                    <a href="{{ route('dashboard') }}"
                       class="px-4 py-2 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold transition-colors">
                        Dashboard
                    </a>
                </div>
            </div>

            <!-- Profile Edit Form -->
            <div class="bg-white border border-gray-300 rounded p-4 mt-6">
                <h2 class="font-bold text-gray-900 mb-4">Profil Bilgilerini Düzenle</h2>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded relative mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded relative mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit="updateProfile" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">İsim Soyisim</label>
                        <input wire:model="name" type="text"
                            class="w-full p-2 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:outline-none text-sm">
                        @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">E-posta</label>
                        <input wire:model="email" type="email"
                            class="w-full p-2 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:outline-none text-sm">
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Yeni Şifre (İsteğe bağlı)</label>
                        <input wire:model="password" type="password" placeholder="Şifrenizi değiştirmek istemiyorsanız boş bırakın"
                            class="w-full p-2 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:outline-none text-sm">
                        @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full text-sm transition-colors">
                            Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="hidden md:block w-80 flex-shrink-0 space-y-4">
            <div class="bg-white border border-gray-300 rounded overflow-hidden">
                <div class="bg-blue-500 h-10 pl-4 flex items-center">
                    <span class="text-white font-bold text-sm">Hakkında</span>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Profil bilgilerinizi burada görebilir, içerik oluşturma işlemlerine hızlıca erişebilirsiniz.
                    </p>
                </div>
            </div>

            <div class="bg-white border border-gray-300 rounded p-4 text-xs text-gray-500">
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <a href="{{ route('home') }}" class="hover:underline">Ana Sayfa</a>
                    <a href="{{ route('categories') }}" class="hover:underline">Kategoriler</a>
                    <a href="{{ route('dashboard') }}" class="hover:underline">Dashboard</a>
                </div>
                <p>&copy; {{ date('Y') }} Kle Blog Inc. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </div>
</div>


