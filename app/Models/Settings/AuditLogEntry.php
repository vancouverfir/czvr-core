<?php

namespace App\Models\Settings;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLogEntry extends Model
{
    protected $fillable = [
        'user_id', 'action', 'affected_id', 'time', 'private',
    ];

    public static function insert(User $user, $message, User $affected_user, $private)
    {
        $log = new self;
        $log->action = $message;
        $log->user_id = $user->id;
        $log->affected_id = $affected_user->id;
        $log->time = date('Y-m-d H:i:s');
        $log->private = $private;
        $log->save();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function affectedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affected_id');
    }
}
