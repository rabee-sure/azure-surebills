<?php

namespace App\Models;

use App\Events\GenerateReport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\MediaRepository;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Report extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;

    protected $fillable = ['name', 'params', 'emails'];

    public static function boot() {
        parent::boot();
        static::creating(function (Report $report) {
            $report->params = json_encode(['merchants' => implode('","', json_decode($report->merchants, true)), 'from' => $report->from, 'to' => $report->to]);
            $report->name = 'merchants-outstanding';
            unset($report->merchants);
            unset($report->from);
            unset($report->to);

        });
        static::created(function (Report $report) {
            GenerateReport::dispatch($report->id);
        });
    }


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('reports_file')->singleFile();
    }

    public function getParametersAttribute()
    {
        return json_decode($this->params ,true);
    }
}
