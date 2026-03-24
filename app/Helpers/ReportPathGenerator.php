<?php

namespace App\Helpers;

use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ReportPathGenerator implements PathGenerator
{
  public function getPath(Media $media): string
  {
    return 'reports/' . $media->model->name . '/';
  }

  public function getPathForConversions(Media $media): string
  {
    return $this->getPath($media) . 'conversions/';
  }

  public function getPathForResponsiveImages(Media $media): string
  {
    return $this->getPath($media) . 'responsive/';
  }
}
