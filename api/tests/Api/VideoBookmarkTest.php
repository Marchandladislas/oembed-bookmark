<?php

declare(strict_types = 1);

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class VideoBookmarkTest extends ApiTestCase
{
    public function testCreateVideoBookmark(): void
    {
        $response = static::createClient()->request('POST', '/video_bookmarks', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'url' => 'https://vimeo.com/76979871',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $jsonResponse = $response->toArray();

        $this->assertSame('VideoBookmark', $jsonResponse['@type']);
        $this->assertSame('https://vimeo.com/76979871', $jsonResponse['url']);
        $this->assertNotEmpty($jsonResponse['title']);
        $this->assertNotEmpty($jsonResponse['author']);

        $this->assertArrayHasKey('width', $jsonResponse);
        $this->assertArrayHasKey('height', $jsonResponse);
        $this->assertArrayHasKey('duration', $jsonResponse);
    }

    public function testGetVideoBookmark(): void
    {
        $client = static::createClient();

        $client->request('POST', '/video_bookmarks', [
            'json' => [
                'url' => 'https://vimeo.com/76979871',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $iri = $client->getResponse()->toArray()['@id'];

        $response = $client->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@type' => 'VideoBookmark',
            'url' => 'https://vimeo.com/76979871',
        ]);
    }

    public function testDeleteVideoBookmark(): void
    {
        $client = static::createClient();

        $client->request('POST', '/video_bookmarks', [
            'json' => [
                'url' => 'https://vimeo.com/76979871',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $iri = $client->getResponse()->toArray()['@id'];

        $client->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(204);
    }
}
