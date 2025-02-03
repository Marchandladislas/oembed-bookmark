<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Bookmark;
use App\Entity\PhotoBookmark;
use App\Entity\VideoBookmark;
use App\Service\OEmbedService;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @implements ProcessorInterface<Bookmark, Bookmark>
 */
class BookmarkProcessor implements ProcessorInterface
{
    public function __construct(
        private OEmbedService $oEmbedService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param Bookmark $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Bookmark
    {
        if (!$data instanceof Bookmark) {
            throw new \InvalidArgumentException('Data must be an instance of Bookmark.');
        }

        $metadata = $this->oEmbedService->fetchMetadata($data->getUrl());

        $isVideo = null !== $metadata['duration'];

        if ($isVideo && !$data instanceof VideoBookmark) {
            throw new \LogicException('This URL corresponds to a video but was submitted as a photo.');
        }

        if (!$isVideo && !$data instanceof PhotoBookmark) {
            throw new \LogicException('This URL corresponds to a photo but was submitted as a video.');
        }

        $this->hydrateBookmark($data, $metadata);

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }

    /**
     * @param array<string, mixed> $metadata
     */
    private function hydrateBookmark(Bookmark $bookmark, array $metadata): void
    {
        $bookmark->setTitle($metadata['title'] ?? 'Unknown Title');
        $bookmark->setAuthor($metadata['author_name'] ?? 'Unknown Author');

        if ($bookmark instanceof VideoBookmark) {
            $bookmark->setWidth($metadata['width'] ?? 0);
            $bookmark->setHeight($metadata['height'] ?? 0);
            $bookmark->setDuration($metadata['duration'] ?? 0);
        } elseif ($bookmark instanceof PhotoBookmark) {
            $bookmark->setWidth($metadata['width'] ?? 0);
            $bookmark->setHeight($metadata['height'] ?? 0);
        }
    }
}
