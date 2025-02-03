<?php

declare(strict_types=1);

namespace App\Service;

use Embed\Embed;
use Embed\Http\Crawler;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Symfony\Component\HttpClient\Psr18Client;

class OEmbedService
{
    private Embed $embed;

    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private UriFactoryInterface $uriFactory,
    ) {
        $psr18Client = new Psr18Client();
        $crawler = new Crawler($psr18Client, $this->requestFactory, $this->uriFactory);
        $this->embed = new Embed($crawler);
    }

    /**
     * @return array<string, mixed>
     */
    public function fetchMetadata(string $url): array
    {
        try {
            $info = $this->embed->get($url);
            $oembed = $info->getOEmbed();

            return [
                'title' => $info->title ?? $oembed->get('title') ?? null,
                'author_name' => $info->authorName ?? $oembed->get('author_name') ?? null,
                'width' => $info->code?->width ?? $oembed->get('width') ?? null,
                'height' => $info->code?->height ?? $oembed->get('height') ?? null,
                'duration' => $info->code?->duration ?? $oembed->get('duration') ?? null,
                'provider' => $info->providerName ?? $oembed->get('provider_name') ?? null,
            ];
        } catch (\Throwable $e) {
            throw new \RuntimeException("Failed to fetch oEmbed metadata for URL: {$url}", 0, $e);
        }
    }
}
