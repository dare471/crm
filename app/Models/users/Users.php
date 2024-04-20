<?php

namespace App\Models\users;  // Рекомендуемое пространство имён

use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

class Users extends Authenticatable implements JWTSubject // Используется единственное число
{
    public $incrementing = false; // ID не автоинкрементное
    protected $keyType = 'string'; // Тип ключа - строка
    protected $fillable = ['name', 'email', 'phone', 'bin', 'password'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}