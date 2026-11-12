<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Test;
use App\Models\Usuario;

class IntentoTest extends Model {
    
    use HasUuids, SoftDeletes;

    protected $table = "intentos_test";

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'usuario_id', 
        'test_id', 
        'nota',
    ];
    
    public $timestamps = true;
    public const UPDATED_AT = null;

    // Accesor
    // public const NOTA_APROBADO = 5;

    // public function getAprobado(): bool
    // {
    //     return $this->nota >= self::NOTA_APROBADO;
    // }


    // Relaciones
    public function test(): BelongsTo{
        return $this->belongsTo(Test::class, 'test_id');
    }

    public function usuario(): BelongsTo{
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
