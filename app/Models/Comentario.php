<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Usuario;
use App\Models\Publicacion;
use App\Models\Reporte;

class Comentario extends Model {

    use HasUuids, SoftDeletes;

    protected $table = "comentarios";

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'usuario_id', 
        'publicacion_id', 
        'contenido',
    ];
    
    public $timestamps = true;
    public const UPDATED_AT = null;


    // Relaciones

    public function reportesRecibidos(): MorphMany {
        // morphMany($related, $relationName, $type, $id, [$localKey])
        return $this->morphMany(
            Reporte::class,
            'reportable',
            'reportable_tipo',
            'reportable_id'
        );
    }

    public function usuario(): BelongsTo {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function publicacion(): BelongsTo {
        return $this->belongsTo(Publicacion::class, 'publicacion_id');
    }

}
