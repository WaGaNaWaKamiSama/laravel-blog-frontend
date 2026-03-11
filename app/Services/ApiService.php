<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ApiService
{
    protected string $baseUrl;
    protected ?string $token;

    public function __construct()
    {
        $this->baseUrl = env('BACKEND_API_URL', 'http://localhost:8000/api');
        $this->token = session('api_token');
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;
        return $this;
    }

    protected function client()
    {
        $client = Http::baseUrl($this->baseUrl)->timeout(30)->acceptJson();
        
        if ($this->token) {
            $client->withToken($this->token);
        }

        return $client;
    }

    public function login(string $email, string $password)
    {
        try {
            $response = $this->client()->post('/login', compact('email', 'password'));

            if (!$response->successful()) {
                if ($response->status() === 422) {
                    $errors = $response->json();
                    return ['errors' => $errors['errors'] ?? $errors];
                }
                $data = $response->json();
                $message = isset($data['message']) && str_word_count($data['message']) < 10 && !str_contains($data['message'], 'SQLSTATE') ? $data['message'] : 'Login failed.';
                return ['errors' => ['system' => $message]];
            }

            $data = $response->json()['data'] ?? $response->json();
            $token = $data['access_token'] ?? $data['token'] ?? null;
            $user = $data['user'] ?? null;

            if ($token) {
                session(['api_token' => $token]);
                if ($user) {
                    session([
                        'user_name' => $user['name'] ?? null,
                        'user_email' => $user['email'] ?? null,
                    ]);
                }
                $this->token = $token;
                return compact('token', 'user');
            }
        } catch (\Exception $e) {
            return ['errors' => ['system' => 'Sunucuya bağlanılamadı. Lütfen daha sonra tekrar deneyiniz.']];
        }

        return null;
    }

    public function register(string $name, string $email, string $password, ?string $password_confirmation = null)
    {
        try {
            $response = $this->client()->post('/register', [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password_confirmation ?? $password,
            ]);

            if (!$response->successful()) {
                if ($response->status() === 422) {
                    $errors = $response->json();
                    return ['errors' => $errors['errors'] ?? $errors];
                }
                $data = $response->json();
                $message = isset($data['message']) && str_word_count($data['message']) < 10 && !str_contains($data['message'], 'SQLSTATE') ? $data['message'] : 'Registration failed.';
                return ['errors' => ['system' => $message]];
            }

            $data = $response->json()['data'] ?? $response->json();
            $token = $data['access_token'] ?? $data['token'] ?? null;
            $user = $data['user'] ?? null;

            if ($token) {
                session(['api_token' => $token]);
                if ($user) {
                    session([
                        'user_name' => $user['name'] ?? null,
                        'user_email' => $user['email'] ?? null,
                    ]);
                }
                $this->token = $token;
                return compact('token', 'user');
            }
        } catch (\Exception $e) {
            return ['errors' => ['system' => 'Connection to server failed. Please try again later.']];
        }

        return null;
    }

    public function logout()
    {
        try {
            $response = $this->client()->post('/logout');
            if ($response->successful()) {
                session()->forget(['api_token', 'user_name', 'user_email']);
                $this->token = null;
                return true;
            }
        } catch (\Exception $e) {
            // Ignore logout errors
        }
        session()->forget(['api_token', 'user_name', 'user_email']);
        $this->token = null;
        return true;
    }

    public function me()
    {
        $response = $this->client()->get('/me');
        return $response->successful() ? $response->json() : null;
    }

    public function getPosts(array $filters = [])
    {
        $response = $this->client()->get('/posts', $filters);
        
        if (!$response->successful()) {
            return ['data' => []];
        }

        $data = $response->json();
        return isset($data['data']) ? $data : ['data' => $data];
    }

    public function getPost(string $slug)
    {
        $response = $this->client()->get("/posts/{$slug}");
        return $response->successful() ? $response->json() : null;
    }

    public function createPost(array $data)
    {
        $data['status'] = 'pending_review';
        try {
            $response = $this->client()->post('/posts', $data);

            if (!$response->successful()) {
                $message = isset($response->json()['message']) && str_word_count($response->json()['message']) < 10 && !str_contains($response->json()['message'], 'SQLSTATE') ? $response->json()['message'] : 'Post creation failed.';
                return $response->status() === 422 
                    ? ['errors' => $response->json()['errors'] ?? $response->json()] 
                    : ['errors' => ['system' => $message]];
            }

            Cache::forget('categories');
            $result = $response->json();
            return isset($result['data']) ? $result : ['data' => $result];
        } catch (\Exception $e) {
            return ['errors' => ['system' => 'Connection to server failed.']];
        }
    }

    public function getCategories()
    {
        $response = $this->client()->get('/categories');
        return $response->successful() ? $response->json() : ['data' => []];
    }

    public function getComments(int $postId)
    {
        $response = $this->client()->get("/posts/{$postId}/comments");
        
        if (!$response->successful()) {
            return ['data' => []];
        }

        $data = $response->json();
        return isset($data['data']) ? $data : ['data' => $data];
    }

    public function createComment(int $postId, string $content)
    {
        $response = $this->client()->post("/posts/{$postId}/comments", ['content' => $content]);
        return $response->successful() ? $response->json() : null;
    }

    public function isAuthenticated(): bool
    {
        return !empty($this->token);
    }

    public function getCurrentUser()
    {
        if (!$this->token) return null;
        
        return $this->me();
    }

    public function updateProfile(array $data)
    {
        try {
            $response = $this->client()->put('/profile', $data); // Assuming backend has /profile PUT route

            if (!$response->successful()) {
                return $response->status() === 422 
                    ? ['errors' => $response->json()['errors'] ?? $response->json()] 
                    : ['errors' => ['system' => $response->json()['message'] ?? 'Profil güncellenemedi.']];
            }

            $user = $response->json()['data'] ?? $response->json();
            session([
                'user_name' => $user['name'] ?? session('user_name'),
                'user_email' => $user['email'] ?? session('user_email'),
            ]);
            Cache::forget('user_' . $this->token);

            return ['success' => true, 'user' => $user];
        } catch (\Exception $e) {
            return ['errors' => ['system' => 'Sunucuya bağlanılamadı.']];
        }
    }
}
