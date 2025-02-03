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
class PhotoBookmark extends Bookmark
{
    #[ApiProperty(
        description: 'Width of the photo (extracted from oEmbed)',
        openapiContext: ['example' => 800]
    )]
    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(['bookmark:read'])]
    private ?int $width = null;

    #[ApiProperty(
        description: 'Height of the photo (extracted from oEmbed)',
        openapiContext: ['example' => 600]
    )]
    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(['bookmark:read'])]
    private ?int $height = null;

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
}
