<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'total_rows',
        'success_rows',
        'failed_rows',
        'error_details',
        'user_id',
    ];

    protected $casts = [
        'error_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}