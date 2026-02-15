<?php

namespace App\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
        public function getUrl(string $conversionName = ''): string
    {
        $disk = $this->disk;
        $path = $this->getPathRelativeToRoot($conversionName);

        if ($disk !== 'oci') {
            return parent::getUrl($conversionName);
        }

        return Storage::disk($disk)
            ->temporaryUrl($path, now()->addMinutes(10));
    }
}