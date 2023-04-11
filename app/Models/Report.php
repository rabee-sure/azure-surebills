<?php

namespace App\Models;

use App\Events\GenerateBillReport;
use App\Events\GenerateReport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\MediaRepository;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Events\AddActionLogEvent;
use App\Jobs\GenerateBillsReport;
use Illuminate\Support\Facades\Auth;

class Report extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;

    protected $fillable = ['name', 'params', 'emails', 'type'];

    public static function boot() {
        parent::boot();
        static::creating(function (Report $report) {
            if($report->type == 'merchants outstanding')
            {
                $report->name = 'merchants-outstanding';
                $report->params = json_encode(['merchants' => implode('","', json_decode($report->merchants, true)), 'from' => $report->from, 'to' => $report->to ?? $report->from]);
            }
            else if($report->type == 'bill')
            {
                $report->name = 'bill';
                $report->params = json_encode(['paid_from' => $report->from, 'paid_to' => $report->to ?? $report->from, 'merchants' => implode('","', json_decode($report->merchants, true)), 'channels' => implode('","', json_decode($report->channels, true))]);
            }

            unset($report->merchants);
            unset($report->channels);
            unset($report->from);
            unset($report->to);
        });
        static::created(function (Report $report) {
            if($report->type == 'merchants outstanding')
            {
                GenerateReport::dispatch($report->id);
                event(new AddActionLogEvent(
                    'create_merchants_outstanding_report',
                    Auth::id(),
                    [
                        'message' => [
                            'name' => $report->name,
                            'adminname' => Auth::user()->name,
                            'type' => $report->type,
                            'time' => $report->created_at,
                        ],
                        'changes' => [],
                    ],
                    $report->id,
                    Report::class
                ));
            }
            else if($report->type == 'bill')
            {
                $report_emails = explode(",", $report->emails);
                $report_filters = json_decode($report->params, true) ;

                GenerateBillsReport::dispatch($report_filters, $report_emails, $report->name, $report->id);
                
                event(new AddActionLogEvent(
                    'create_bill_report',
                    Auth::id(),
                    [
                        'message' => [
                            'name' => $report->name,
                            'adminname' => Auth::user()->name,
                            'type' => $report->type,
                            'time' => $report->created_at,
                        ],
                        'changes' => [],
                    ],
                    $report->id,
                    Report::class
                ));
            }
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
