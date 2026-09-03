<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
    ];

    /**
     * البلاغات المرتبطة بهذا التصنيف
     */
    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }
}
