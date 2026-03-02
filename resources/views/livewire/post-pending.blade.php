<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layout')] class extends Component {
};

?>

<div class="max-w-2xl mx-auto py-16 px-4">
    <div class="bg-white rounded-lg border border-gray-300 p-8 text-center">
        <div class="mb-6">
            <svg class="w-20 h-20 mx-auto text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Gönderiniz Alındı!</h1>
        
        <p class="text-lg text-gray-600 mb-6">
            Yazınız başarıyla oluşturuldu ve admin onayı bekliyor.
        </p>
        
        <p class="text-sm text-gray-500 mb-8">
            Gönderiniz yöneticiler tarafından incelendikten sonra yayınlanacaktır. Bu işlem genellikle birkaç saat sürmektedir.
        </p>
        
        <div class="flex gap-4 justify-center">
            <a href="{{ route('home') }}" 
               class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-full hover:bg-blue-700 transition">
                Ana Sayfaya Dön
            </a>
            <a href="{{ route('dashboard') }}" 
               class="px-6 py-2.5 bg-gray-200 text-gray-700 font-semibold rounded-full hover:bg-gray-300 transition">
                Yeni Gönderi Oluştur
            </a>
        </div>
    </div>
</div>
