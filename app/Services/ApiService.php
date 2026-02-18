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

    /**
     * Set authentication token
     */
    public function setToken(?string $token): self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Get HTTP client with optional authentication
     */
    protected function client()
    {
        $client = Http::baseUrl($this->baseUrl)
            ->timeout(30)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]);

        if ($this->token) {
            $client->withToken($this->token);
        }

        return $client;
    }

    /**
     * Authentication Methods
     */
    public function login(string $email, string $password)
    {
        try {
            $response = $this->client()->post('/login', [
                'email' => $email,
                'password' => $password,
            ]);
            
            // Debug: Log login attempt
            \Log::info('Login attempt:', ['email' => $email]);
            \Log::info('Login response status:', ['status' => $response->status()]);
            \Log::info('Login response body:', ['body' => $response->body()]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? $data['token'] ?? null;
                if ($token) {
                    session(['api_token' => $token]);
                    if (!empty($data['user']) && is_array($data['user'])) {
                        session([
                            'user_name' => $data['user']['name'] ?? null,
                            'user_email' => $data['user']['email'] ?? null,
                        ]);
                    }
                    $this->setToken($token);
                    \Log::info('Login successful, token set');
                    return ['token' => $token, 'user' => $data['user'] ?? null];
                }
            }

            // Handle validation errors (422)
            if ($response->status() === 422) {
                $errors = $response->json();
                \Log::error('Login validation failed', ['errors' => $errors]);
                return ['errors' => $errors];
            }

            \Log::error('Login failed', ['response' => $response->json(), 'status' => $response->status()]);
        } catch (\Exception $e) {
            \Log::error('Login exception', ['message' => $e->getMessage()]);
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

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? $data['token'] ?? null;
                if ($token) {
                    session(['api_token' => $token]);
                    if (!empty($data['user']) && is_array($data['user'])) {
                        session([
                            'user_name' => $data['user']['name'] ?? null,
                            'user_email' => $data['user']['email'] ?? null,
                        ]);
                    }
                    $this->setToken($token);
                    return ['token' => $token, 'user' => $data['user'] ?? null];
                }
            }

            // Handle validation errors (422)
            if ($response->status() === 422) {
                $errors = $response->json();
                \Log::error('Register validation failed', ['errors' => $errors]);
                return ['errors' => $errors];
            }

            \Log::error('Register failed', ['response' => $response->json(), 'status' => $response->status()]);
        } catch (\Exception $e) {
            \Log::error('Register exception', ['message' => $e->getMessage()]);
        }

        return null;
    }

    public function logout()
    {
        $response = $this->client()->post('/logout');

        session()->forget('api_token');
        $this->token = null;

        return $response->successful();
    }

    public function me()
    {
        $response = $this->client()->get('/me');

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Posts Methods
     */
    public function getPosts(array $filters = [])
    {
        // $cacheKey = 'posts_' . md5(json_encode($filters));
        // $ttl = app()->environment('local') ? 1 : 300;

        try {
            $response = $this->client()->get('/posts', $filters);
            
            // Debug: Log the filters and response
            \Log::info('API Request - Filters:', $filters);
            \Log::info('API Response Status:', ['status' => $response->status()]);
            \Log::info('API Response Data:', $response->json());

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            \Log::error('API Exception:', ['message' => $e->getMessage()]);
        }

        return ['data' => []];
    }

    public function getPost(string $slug)
    {
        $cacheKey = 'post_' . $slug;

        try {
            $response = $this->client()->get("/posts/{$slug}");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            //
        }

        return null;
    }

    public function createPost(array $data)
    {
        try {
            // Ensure status is published
            $data['status'] = 'published';
            
            $response = $this->client()->post('/posts', $data);

            if ($response->successful()) {
                Cache::flush(); // Clear posts cache
                return $response->json();
            }
        } catch (\Exception $e) {
            //
        }

        return null;
    }

    public function getCategories()
    {
        return Cache::remember('categories', 3600, function () {
            try {
                $response = $this->client()->get('/categories');

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                //
            }

            return ['data' => []];
        });
    }

    /**
     * Comments Methods
     */
    public function getComments(int $postId)
    {
        try {
            $response = $this->client()->get("/posts/{$postId}/comments");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            //
        }

        return ['data' => []];
    }

    public function createComment(int $postId, string $content)
    {
        try {
            $response = $this->client()->post('/comments', [
                'post_id' => $postId,
                'content' => $content,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            \Log::error('Create comment failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            \Log::error('Create comment exception', ['message' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool
    {
        return !empty($this->token);
    }

    /**
     * Get current user
     */
    public function getCurrentUser()
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return Cache::remember('current_user_' . $this->token, 600, function () {
            return $this->me();
        });
    }
}
