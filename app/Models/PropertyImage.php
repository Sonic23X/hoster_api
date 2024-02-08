<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PropertyImage extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'property_id',
        'name',
        'storage',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'property_id',
        'created_at',
        'updated_at',
    ];
}
