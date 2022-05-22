<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SystemAction;

class ActionLog extends Model
{
    use HasFactory;

    protected $table = 'actions_logs';
    protected $fillable = [
        'action_id',
        'user_id',
        'model_class',
        'model_id',
        'message',
        'payload'
    ];

    public function systemAction()
    {
        return $this->belongsTo(SystemAction::class, 'action_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createActionLog($action_name, $user_id, array $payload, $modelClass = null, $modelId = null)
    {
        $action = SystemAction::where('action_name', $action_name)->first();
        $message = __('actions_logs.'.$action_name, $payload['message'], 'en');

        self::create([
            'action_id' => $action->id,
            'user_id' => $user_id,
            'model_class' => $modelClass,
            'model_id' => $modelId,
            'message' => $message,
            'payload' => (isset($payload['changes'])) ? json_encode($payload['changes']) : null,
        ]);
    }
}
