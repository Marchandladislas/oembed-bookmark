<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\State\BookmarkProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            processor: BookmarkProcessor::class
        ),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['bookmark:read']],
    denormalizationContext: ['groups' => ['bookmark:write']]
)]
class VideoBookmark extends Bookmark
{
    #[ApiProperty(
        description: 'Width of the video (extracted from oEmbed)',
        openapiContext: ['example' => 1280]
    )]
    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(['bookmark:read'])]
    private ?int $width = null;

    #[ApiProperty(
        description: 'Height of the video (extracted from oEmbed)',
        openapiContext: ['example' => 720]
    )]
    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(['bookmark:read'])]
    private ?int $height = null;

    #[ApiProperty(
        description: 'Duration of the video in seconds (extracted from oEmbed)',
        openapiContext: ['example' => 120]
    )]
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['bookmark:read'])]
    private ?int $duration = null;

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }
}
