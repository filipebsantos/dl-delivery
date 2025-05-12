<?php

namespace DLDelivery\Application\DTO;

class UploadImageDTO
{
    private string $fileName;
    
    public function __construct(
        public readonly string $temp_name,
        public readonly string $type,
        public readonly int $size,
    ) {}

    public function setFileName(string $name): void
    {
        $this->fileName = $name;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}