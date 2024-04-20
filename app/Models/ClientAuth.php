<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Support\Str;

class ClientAuth extends Authenticatable implements JWTSubject
{
    protected $table = 'clients'; // Указываем, что модель должна использовать таблицу 'clients'
    public $incrementing = false;  // Указывает Laravel, что ID не автоинкрементное
    protected $keyType = 'string';  // Тип ключа - строка
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

