<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Appointment extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'title',
        'creator_id',
        'start',
        'end',
    ];

    // Relasi ke creator (pembuat appointment)
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    // Relasi ke user yang diundang
    public function users()
    {
        return $this->belongsToMany(User::class, 'appointment_user');
    }

}
