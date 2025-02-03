<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class PhotoBookmarkTest extends ApiTestCase
{
    public function testCreatePhotoBookmark(): void
    {
        $response = static::createClient()->request('POST', '/photo_bookmarks', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'url' => 'https://www.flickr.com/photos/bees/2341623661',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $jsonResponse = $response->toArray();

        $this->assertSame('PhotoBookmark', $jsonResponse['@type']);
        $this->assertSame('https://www.flickr.com/photos/bees/2341623661', $jsonResponse['url']);
        $this->assertNotEmpty($jsonResponse['title']);
        $this->assertNotEmpty($jsonResponse['author']);

        $this->assertArrayHasKey('width', $jsonResponse);
        $this->assertArrayHasKey('height', $jsonResponse);
    }

    public function testGetPhotoBookmark(): void
    {
        $client = static::createClient();

        $client->request('POST', '/photo_bookmarks', [
            'json' => [
                'url' => 'https://www.flickr.com/photos/bees/2341623661',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $iri = $client->getResponse()->toArray()['@id'];

        $response = $client->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@type' => 'PhotoBookmark',
            'url' => 'https://www.flickr.com/photos/bees/2341623661',
        ]);
    }

    public function testDeletePhotoBookmark(): void
    {
        $client = static::createClient();

        $client->request('POST', '/photo_bookmarks', [
            'json' => [
                'url' => 'https://www.flickr.com/photos/bees/2341623661',
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
