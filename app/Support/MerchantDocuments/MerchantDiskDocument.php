<?php

namespace App\Support\MerchantDocuments;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Storage;

/**
 * Merchant KYC file stored on the public disk (admin-aligned keys), not Spatie media.
 */
final class MerchantDiskDocument implements Arrayable, \JsonSerializable
{
    /** @var int Stable handle for API / UI (crc32 of relative path). */
    public $id;

    public $name;

    public $file_name;

    public $size;

    public $mime_type;

    /** Relative path on the public disk (may include OCI bucket prefix segment). */
    public $disk_relative_path;

    public $download_url;

    public $thumbnail_url;

    /** @var string business_documents|bank_documents */
    private $collection;

    private function __construct()
    {
    }

    public static function fromRelativePath(string $relativePath, string $collection): self
    {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        $disk = Storage::disk('public');

        $doc = new self;
        $doc->collection = $collection;
        $doc->disk_relative_path = $relativePath;
        $doc->file_name = basename($relativePath);
        $doc->name = $doc->file_name;
        $doc->size = (int) $disk->size($relativePath);
        $doc->mime_type = (string) ($disk->mimeType($relativePath) ?: '');
        $doc->id = (int) sprintf('%u', crc32($relativePath));
        $doc->thumbnail_url = $doc->thumbnailUrl();
        $doc->download_url = route('download.merchant_document', [
            'collection' => $collection,
            'file' => rawurlencode($doc->file_name),
        ]);

        return $doc;
    }

    public function getFullUrl(): string
    {
        $url = public_storage_url($this->disk_relative_path);

        return $url !== null && $url !== '' ? $url : url('media/'.$this->encodeMediaPathSegments($this->disk_relative_path));
    }

    public function thumbnailUrl(): ?string
    {
        if ($this->mime_type === '' || strpos($this->mime_type, 'image/') !== 0) {
            return null;
        }

        return url('media/'.$this->encodeMediaPathSegments($this->disk_relative_path));
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'file_name' => $this->file_name,
            'size' => $this->size,
            'mime_type' => $this->mime_type,
            'disk_relative_path' => $this->disk_relative_path,
            'download_url' => $this->download_url,
            'thumbnail_url' => $this->thumbnail_url,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    private function encodeMediaPathSegments(string $relativePath): string
    {
        $segments = array_values(array_filter(explode('/', $relativePath)));

        return implode('/', array_map('rawurlencode', $segments));
    }
}
