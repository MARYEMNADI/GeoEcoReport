<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentImage extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للتعبئة بشكل جماعي (Mass Assignment)
     */
    protected $fillable = [
        'incident_id', // معرف البلاغ المرتبط بهذه الصورة (Foreign Key)
        'image_path',  // مسار تخزين ملف الصورة داخل السيرفر (Storage)
    ];

    /**
     * العلاقة العكسية: الصورة تنتمي إلى بلاغ واحد (Incident)
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }
}