<?php

namespace DLDelivery\Infrastructure\Persistence;

use DLDelivery\Application\DTO\UploadImageDTO;
use DLDelivery\Exception\Client\LocationImageInvalidSizeException;
use DLDelivery\Exception\Client\LocationImageInvalidTypeException;

class ImageRepository
{
    private const IMAGE_DIR = __DIR__ . '/../../../public/pictures/';
    private static array $acceptedTypes = ['image/png', 'image/webp', 'image/jpg', 'image/jpeg'];

    /**
     * Persist image uploaded
     * 
     * @param UploadImageDTO $dto
     * @return string The file name, otherwise return FALSE if cannot save
     * @throws LocationImageInvalidTypeException If file is bigger than 5Mb
     * @throws LocationImageInvalidTypeException Case file type is not acceptable.
     */
    public static function saveImage(UploadImageDTO $dto): string
    {
        if ($dto->size > 5242880) {
            throw new LocationImageInvalidSizeException;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realType = finfo_file($finfo, $dto->temp_name);
        finfo_close($finfo);

        if (!in_array($realType, self::$acceptedTypes, true)) {
            throw new LocationImageInvalidTypeException;
        }

        $extensionMap = [
            'image/jpeg' => 'jpg',
            'image/jpg'  => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];
        $fileExtension = $extensionMap[$dto->type] ?? 'bin';

        $fileName = uniqid('housepicture_') . time() . '.' . $fileExtension;
        $destinationPath = self::IMAGE_DIR . $fileName;

        if (!is_dir(self::IMAGE_DIR)) {
            mkdir(self::IMAGE_DIR, 0775, true);
        }

        if (move_uploaded_file($dto->temp_name, $destinationPath)) {
            return $fileName;
        } else {
            return false;
        }
        
    }

    /**
     * Delete house image
     * 
     * @param string $fileName
     * @return bool TRUE if successfully deleted, FALSE if file doesn't exists or deletion fail
     */
    public static function deleteImage(string $fileName): bool
    {
        $filePath = self::IMAGE_DIR . $fileName;

        if (!file_exists($filePath)) {
            return false;
        }
        
        return unlink($filePath);
    }
}