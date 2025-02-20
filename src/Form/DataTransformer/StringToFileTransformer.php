<?php
// src/Form/DataTransformer/StringToFileTransformer.php
namespace App\Form\DataTransformer;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class StringToFileTransformer implements DataTransformerInterface
{
    private $uploadDirectory;

    public function __construct(string $uploadDirectory)
    {
        $this->uploadDirectory = $uploadDirectory;
    }

    /**
     * Transforms a string (file path) into a File object.
     */
    public function transform($value): ?File
    {
        if (null === $value || '' === $value) {
            return null;
        }

        // Convert the file path to a File object
        return new File($this->uploadDirectory . '/' . $value);
    }

    /**
     * Transforms a File object into a string (file path).
     */
    public function reverseTransform($value): ?string
    {
        if (null === $value) {
            return null;
        }

        // If the value is already a string (file path), return it
        if (is_string($value)) {
            return $value;
        }

        // Handle the file upload and return the file path
        if ($value instanceof File) {
            return $value->getFilename();
        }

        throw new TransformationFailedException('Expected a File object or a string.');
    }
}