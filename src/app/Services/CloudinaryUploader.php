<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class CloudinaryUploader
{
    public function uploadImage(UploadedFile $file): string
    {
        $config = $this->config();
        $timestamp = time();
        $params = [
            'folder' => $config['folder'],
            'timestamp' => $timestamp,
        ];

        $signature = $this->signature($params, $config['api_secret']);
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            throw ValidationException::withMessages([
                'image' => 'File gambar tidak bisa dibaca.',
            ]);
        }

        $response = Http::attach('file', $handle, $file->getClientOriginalName())
            ->asMultipart()
            ->post("https://api.cloudinary.com/v1_1/{$config['cloud_name']}/image/upload", [
                'api_key' => $config['api_key'],
                'timestamp' => $timestamp,
                'folder' => $config['folder'],
                'signature' => $signature,
            ]);

        if (is_resource($handle)) {
            fclose($handle);
        }

        if (! $response->successful()) {
            throw ValidationException::withMessages([
                'image' => 'Upload ke Cloudinary gagal: '.$response->json('error.message', 'Periksa konfigurasi Cloudinary.'),
            ]);
        }

        $secureUrl = $response->json('secure_url');

        if (! is_string($secureUrl) || $secureUrl === '') {
            throw ValidationException::withMessages([
                'image' => 'Cloudinary tidak mengembalikan URL gambar.',
            ]);
        }

        return $secureUrl;
    }

    /**
     * @return array{cloud_name: string, api_key: string, api_secret: string, folder: string}
     */
    private function config(): array
    {
        $cloudinaryUrl = config('services.cloudinary.url');
        $cloudName = config('services.cloudinary.cloud_name');
        $apiKey = config('services.cloudinary.api_key');
        $apiSecret = config('services.cloudinary.api_secret');

        if (is_string($cloudinaryUrl) && str_starts_with($cloudinaryUrl, 'cloudinary://')) {
            $parsed = parse_url($cloudinaryUrl);
            $apiKey = $apiKey ?: ($parsed['user'] ?? null);
            $apiSecret = $apiSecret ?: ($parsed['pass'] ?? null);
            $cloudName = $cloudName ?: (isset($parsed['host']) ? ltrim($parsed['host'], '/') : null);
        }

        if (
            ! is_string($cloudName) || $cloudName === ''
            || ! is_string($apiKey) || $apiKey === ''
            || ! is_string($apiSecret) || $apiSecret === ''
            || in_array($cloudName, ['CLOUD_NAME', 'your_cloud_name'], true)
            || in_array($apiKey, ['API_KEY', 'your_api_key'], true)
            || in_array($apiSecret, ['API_SECRET', 'your_api_secret'], true)
        ) {
            throw ValidationException::withMessages([
                'image' => 'Konfigurasi Cloudinary belum lengkap. Isi CLOUDINARY_URL atau CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, dan CLOUDINARY_API_SECRET.',
            ]);
        }

        return [
            'cloud_name' => $cloudName,
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
            'folder' => (string) config('services.cloudinary.folder', 'official-merchandise/products'),
        ];
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function signature(array $params, string $apiSecret): string
    {
        ksort($params);

        $payload = collect($params)
            ->map(fn (mixed $value, string $key): string => $key.'='.$value)
            ->implode('&');

        return sha1($payload.$apiSecret);
    }
}
