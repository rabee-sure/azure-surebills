<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Report extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;

    protected $fillable = ['name', 'params', 'emails'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('reports_file')->singleFile();
    }
}
