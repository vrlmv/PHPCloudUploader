<?php

namespace Vrlmv\PhpCloudUploader\Collection;

use Vrlmv\PhpCloudUploader\DTO\FileDTO;

class FileDTOCollection
{
    /**
     * @var FileDTO[]
     */
    public array $items;

    public function __construct(array $items)
    {
        $this->items = array_filter($items, fn($value) => $value instanceof FileDTO);
    }

    public function items(): array
    {
        return $this->items;
    }
}
