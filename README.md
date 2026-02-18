# Kle Blog Frontend

Modern bir blog platformu frontend uygulaması. Laravel tabanlı backend API ile entegre çalışır.

## Özellikler

- Modern ve responsive tasarım
- Reddit tarzı post arayüzü
- Kullanıcı kimlik doğrulama (Login/Register)
- Post oluşturma ve yönetimi
- Kategori sistemi
- Yorum sistemi
- Livewire ile interaktif bileşenler
- Tailwind CSS ile modern stil

## Teknolojiler

- **Backend**: Laravel 10.x
- **Frontend**: Livewire + Blade
- **CSS Framework**: Tailwind CSS
- **JavaScript**: Alpine.js
- **Build Tool**: Vite

## Kurulum

1. Repository'yi klonlayın:
```bash
git clone <repository-url>
cd laravel-blog-frontend
```

2. Dependencies'leri yükleyin:
```bash
composer install
npm install
```

3. Environment dosyasını yapılandırın:
```bash
cp .env.example .env
php artisan key:generate
```

4. Backend API URL'ini ayarlayın:
```env
BACKEND_API_URL=http://localhost:8000/api
```

5. Uygulamayı başlatın:
```bash
npm run dev
php artisan serve
```

## Kullanım

Uygulama `http://127.0.0.1:8001` adresinde çalışacaktır.

### Ana Sayfa
- Tüm post'ları listeler
- Arama ve filtreleme özellikleri
- Kategori tabanlı filtreleme

### Kullanıcı İşlemleri
- Kayıt olma ve giriş yapma
- Post oluşturma
- Yorum yapma

### Admin Paneli
- Post onaylama sistemi
- Kategori yönetimi

## API Entegrasyonu

Frontend uygulaması Laravel backend API ile iletişim kurar:

- **Authentication**: `/api/login`, `/api/register`
- **Posts**: `/api/posts`, `/api/posts/{id}`
- **Categories**: `/api/categories`
- **Comments**: `/api/posts/{id}/comments`

## Proje Yapısı

```
├── app/
│   ├── Http/
│   ├── Models/
│   ├── Services/
│   └── Providers/
├── resources/
│   ├── views/
│   │   ├── livewire/
│   │   └── components/
│   ├── css/
│   └── js/
├── routes/
└── database/
```

## Lisans

MIT License
