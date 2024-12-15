<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lsp extends Model
{
    use HasFactory;

    protected $fillable = [
        'encrypted_id', 'name', 'sk_lisensi', 'no_lisensi', 'jenis',
        'no_telp', 'no_hp', 'no_fax', 'email', 'website',
        'masa_berlaku_sert', 'status_lisensi', 'alamat', 'logo_image',
        'notes','subtype','pimpinan','asesi_per_tahun','status_id'
    ];

    // Relationships
    public function skemas()
    {
        return $this->hasMany(LspSkema::class, 'lsp_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class,"status_id");
    }

    public function tuks()
    {
        return $this->hasMany(LspTuk::class, 'lsp_id');
    }

    public function asesors()
    {
        return $this->hasMany(LspAsesor::class, 'lsp_id');
    }
}
