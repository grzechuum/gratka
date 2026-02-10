<?php
declare(strict_types=1);
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class PhoenixApiService
{
    public function __construct(
        private HttpClientInterface $client,
        private string $phoenixBaseUrl,
    ) {}

    public function getPhotos(string $token): array
    {
        $response = $this->client->request('GET', $this->phoenixBaseUrl.'/api/photos', [
            'headers' => [
                'access-token' => $token,
            ],
        ]);

        return $response->toArray();
    }
}
