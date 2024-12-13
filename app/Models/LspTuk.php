<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LspTuk extends Model
{
    use HasFactory;

    protected $table = 'lsp_tuks'; // Explicit table name

    protected $fillable = [
        'lsp_id', 'order', 'tuk_code', 'type', 'name', 'address',
    ];

    // Relationships
    public function lsp()
    {
        return $this->belongsTo(Lsp::class, 'lsp_id');
    }
}
