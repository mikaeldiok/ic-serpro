<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status'; // Explicit table name

    protected $fillable = [
        'status_id', 'name', 'color', 'description'
    ];

    // Relationships
    public function lsp()
    {
        return $this->hasMany(Lsp::class);
    }

    public function getTableColumns()
    {
        $table_name = DB::getTablePrefix().$this->getTable();

        switch (config('database.default')) {
            case 'sqlite':
                $columns = DB::select("PRAGMA table_info({$table_name});");
                break;
            case 'mysql':
            case 'mariadb':
                $columns = DB::select('SHOW COLUMNS FROM '.$table_name);
                $columns = array_map(function ($column) {
                    return [
                        'name' => $column->Field,
                        'type' => $column->Type,
                        'notnull' => $column->Null,
                        'key' => $column->Key,
                        'default' => $column->Default,
                        'extra' => $column->Extra,
                    ];
                }, $columns);
                break;
            case 'pgsql':
                $columns = DB::select("SELECT column_name as `Field`, data_type as `Type` FROM information_schema.columns WHERE table_name = '{$table_name}';");
                break;

            default:
                // code...
                break;
        }

        return json_decode(json_encode($columns));
    }
}
