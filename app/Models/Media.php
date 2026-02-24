<?php

namespace App\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    public function getUrl(string $conversionName = ''): string
    {
        if ($this->disk !== 'oci') {
            return parent::getUrl($conversionName);
        }
        return Storage::disk($this->disk)->temporaryUrl(
            $this->getPath($conversionName),
            now()->addMinutes(10)
        );
    }
}