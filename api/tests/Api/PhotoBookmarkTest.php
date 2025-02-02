<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class PhotoBookmarkTest extends ApiTestCase
{
    public function testCreatePhotoBookmark(): void
    {
        $response = static::createClient()->request('POST', '/photo_bookmarks', [
            'json' => [
                'url' => 'https://example.com/photo.jpg',
                'title' => 'Example Photo',
                'author' => 'John Doe',
                'width' => 1920,
                'height' => 1080,
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@context' => '/contexts/PhotoBookmark',
            '@type' => 'PhotoBookmark',
            'url' => 'https://example.com/photo.jpg',
            'title' => 'Example Photo',
            'author' => 'John Doe',
            'width' => 1920,
            'height' => 1080,
        ]);
    }

    public function testGetPhotoBookmark(): void
{
    $client = static::createClient();

    $client->request('POST', '/photo_bookmarks', [
        'json' => [
            'url' => 'https://example.com/photo.jpg',
            'title' => 'Example Photo',
            'author' => 'John Doe',
            'width' => 1920,
            'height' => 1080,
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
        'url' => 'https://example.com/photo.jpg',
    ]);
}

public function testDeletePhotoBookmark(): void
{
    $client = static::createClient();

    $client->request('POST', '/photo_bookmarks', [
        'json' => [
            'url' => 'https://example.com/photo.jpg',
            'title' => 'Example Photo',
            'author' => 'John Doe',
            'width' => 1920,
            'height' => 1080,
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
