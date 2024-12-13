<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LspSkemaUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'skema_id', 'unit_code', 'name', 'order'
    ];

    // Relationships
    public function skema()
    {
        return $this->belongsTo(LspSkema::class, 'skema_id');
    }
}
