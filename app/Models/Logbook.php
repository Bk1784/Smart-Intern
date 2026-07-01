<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Logbook extends Model
{
    use SoftDeletes;

    protected $table = 'logbooks';

    protected $fillable = [
        'user_id',
        'tanggal',
        'deskripsi',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}