<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\HasRolesAndPermissions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRolesAndPermissions;

    /**
     * الحقول القابلة للتعبئة.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * الحقول المخفية.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * تحويل أنواع البيانات.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * البلاغات التي أنشأها المستخدم.
     */
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    /**
     * التعليقات التي كتبها المستخدم.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * التكليفات التي استلمها المستخدم بصفته تقنياً.
     */
    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'technicien_id');
    }
}