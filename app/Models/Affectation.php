<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Affectation extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'technicien_id',
        'date_affectation',
        'instructions',
    ];

    protected $casts = [
        'date_affectation' => 'datetime',
    ];

    /**
     * البلاغ المعني بهذه الإحالة
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    /**²²
     * التقني المكلف بمعالجة البلاغ
     */
    public function technicien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }
}