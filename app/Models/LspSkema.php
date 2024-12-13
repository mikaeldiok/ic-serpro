<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LspSkema extends Model
{
    use HasFactory;

    protected $fillable = [
        'lsp_id', 'name', 'order',
    ];

    // Relationships
    public function lsp()
    {
        return $this->belongsTo(Lsp::class, 'lsp_id');
    }

    public function units()
    {
        return $this->hasMany(LspSkemaUnit::class, 'skema_id');
    }
}
