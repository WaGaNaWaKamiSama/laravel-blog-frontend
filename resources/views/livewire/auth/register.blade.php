<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Services\ApiService;

new #[Layout('components.layout')] class extends Component {
    public $kullaniciAdi = '';
    public $epostaMail = '';
    public $girisSifresi = '';
    public $sifreTekrari = '';

    public function hesapOlustur(ApiService $apiService)
    {
        $sonuc = $apiService->register($this->kullaniciAdi, $this->epostaMail, $this->girisSifresi, $this->sifreTekrari);

        if ($sonuc && isset($sonuc['token'])) {
            return redirect()->route('dashboard');
        }

        if ($sonuc && isset($sonuc['errors'])) {
            $hatalar = $sonuc['errors'];
            
            foreach ($hatalar as $alan => $mesajlar) {
                $mesaj = is_array($mesajlar) ? $mesajlar[0] : $mesajlar;
                $this->addError($alan, $mesaj);
            }
        } else {
            $this->addError('epostaMail', 'Üyelik oluşturulurken sorun yaşandı. Tekrar deneyiniz.');
        }
    }
};

?>

<div class="max-w-md mx-auto py-12">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <div class="text-center">
                <div class="flex items-center justify-center gap-3 mb-2">
                    <img src="{{ asset('klelogo.svg') }}" alt="Kle Logo" class="w-10 h-10">
                    <h2 class="text-3xl font-bold text-white">Kayıt Ol</h2>
                </div>
                <p class="text-blue-100 mt-2">Kle Blog'a katılın - Her şeye dalın</p>
            </div>
        </div>

        <!-- Form Content -->
        <div class="p-8">
            <form wire:submit="hesapOlustur" class="space-y-6">
                <!-- Name Field -->
                <div>
                    <label for="kullaniciAdi" class="block text-sm font-semibold text-gray-700 mb-2">İsim Soyisim</label>
                    <div class="relative">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 100-14 7 7 0 000 14z"></path>
                            </svg>
                        </div>
                        <input wire:model="kullaniciAdi" type="text" id="kullaniciAdi" 
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200 placeholder-gray-400"
                            placeholder="Mehmet Yılmaz">
                    </div>
                    @error('kullaniciAdi') 
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label for="epostaMail" class="block text-sm font-semibold text-gray-700 mb-2">E-posta Hesabı</label>
                    <div class="relative">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input wire:model="epostaMail" type="email" id="epostaMail" 
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200 placeholder-gray-400"
                            placeholder="mehmet@gmail.com">
                    </div>
                    @error('epostaMail') 
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="girisSifresi" class="block text-sm font-semibold text-gray-700 mb-2">Gizli Şifre</label>
                    <div class="relative">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input wire:model="girisSifresi" type="password" id="girisSifresi" 
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200 placeholder-gray-400"
                            placeholder="••••••••">
                    </div>
                    @error('girisSifresi') 
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Password Confirmation Field -->
                <div>
                    <label for="sifreTekrari" class="block text-sm font-semibold text-gray-700 mb-2">Şifre Doğrulama</label>
                    <div class="relative">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4 6 6m-6-6v6m0 0V6"></path>
                            </svg>
                        </div>
                        <input wire:model="sifreTekrari" type="password" id="sifreTekrari" 
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-none transition-all duration-200 placeholder-gray-400"
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold py-3 px-4 rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-[1.02]">
                        Üyeliği Tamamla
                    </button>
                </div>
            </form>

            <!-- Login Link -->
            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Zaten hesabınız var mı?
                    <a href="{{ route('login') }}" class="font-semibold text-green-600 hover:text-green-500 transition-colors">Giriş Yapın</a>
                </p>
            </div>
        </div>
    </div>
</div>