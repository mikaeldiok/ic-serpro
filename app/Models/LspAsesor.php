<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LspAsesor extends Model
{
    use HasFactory;

    protected $table = 'lsp_asesors'; // Explicit table name

    protected $fillable = [
        'lsp_id', 'order', 'name', 'registration_id', 'address',
    ];

    // Relationships
    public function lsp()
    {
        return $this->belongsTo(Lsp::class, 'lsp_id');
    }
}
