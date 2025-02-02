<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class VideoBookmarkTest extends ApiTestCase
{
    public function testCreateVideoBookmark(): string
    {
        $response = static::createClient()->request('POST', '/video_bookmarks', [
            'json' => [
                'url' => 'https://vimeo.com/123456789',
                'title' => 'Example Video',
                'author' => 'Jane Doe',
                'width' => 1280,
                'height' => 720,
                'duration' => 300,
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@type' => 'VideoBookmark',
            'title' => 'Example Video',
            'duration' => 300,
        ]);

        $data = $response->toArray();
        $this->assertArrayHasKey('@id', $data);

        return $data['@id'];
    }


    /**
     * @depends testCreateVideoBookmark
     */
    public function testGetVideoBookmark(string $id): void
    {
        $response = static::createClient()->request('GET', $id, [
            'headers' => [
                'Accept' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@type' => 'VideoBookmark',
            'title' => 'Example Video',
            'duration' => 300,
        ]);
    }

    /**
     * @depends testCreateVideoBookmark
     */
    public function testDeleteVideoBookmark(string $id): void
    {
        $response = static::createClient()->request('DELETE', $id, [
            'headers' => [
                'Accept' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(204);

        static::createClient()->request('GET', $id);
        $this->assertResponseStatusCodeSame(404);
    }
}
