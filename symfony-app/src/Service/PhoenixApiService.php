<?php
declare(strict_types=1);
namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PhoenixApiService
{
    public function __construct(
        private HttpClientInterface $client,
         #[Autowire('%env(PHOENIX_BASE_URL)%')]
        private string $phoenixBaseUrl,
    ) {}

    public function getPhotos(string $token): array
    {
        $response = $this->client->request('GET', $this->phoenixBaseUrl.'/api/photos', [
            'headers' => [
                'access-token' => $token,
            ],
        ]);

        if ($response->getStatusCode() === 401) {
            return array(
                'authorized' => false
            );
        }

        return $response->toArray();
    }
}
