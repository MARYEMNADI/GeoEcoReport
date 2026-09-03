<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'user_id',
        'content',
    ];

    /**
     * البلاغ المرتبط بهذا التعليق
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    /**
     * المستخدم صاحب التعليق
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}