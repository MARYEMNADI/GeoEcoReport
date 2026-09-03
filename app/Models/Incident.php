<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'latitude',
        'longitude',
        'status',
        'priority',
        'user_id',
        'category_id',
        'ai_summary',
        'ai_suggested_category',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * المستخدم الذي أنشأ البلاغ.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * التصنيف الخاص بالبلاغ.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * الصور المرفقة بالبلاغ.
     */
    public function images(): HasMany
    {
        return $this->hasMany(IncidentImage::class);
    }

    /**
     * التعليقات المرتبطة بالبلاغ.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * التكليفات الخاصة بالبلاغ.
     */
    public function affectations(): HasMany
    {
        return $this->hasMany(Affectation::class);
    }
}
