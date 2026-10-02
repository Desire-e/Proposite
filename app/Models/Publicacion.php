<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Oposicion;
use App\Models\Usuario;
use App\Models\Comentario;
use App\Models\Reporte;


class Publicacion extends Model {

    /**
     * SoftDeletes. Gestión de deleted_at
     * 
     * $publicacion->delete(); No elimina físicamente, establece deleted_at=fecha/hora actual.
     * Publicacion::withTrashed()->get(); Para consultas incluyendo eliminadas.
     * Publicacion::onlyTrashed()->get(); Para consultas eliminadas.
     * $publicacion->restore(); Restaurar.
     * $publicacion->forceDelete(); Eliminarla físicamente.
     */

    use HasUuids, SoftDeletes;
    // NOTA. No necesitas declarar manualmente deleted_at en $fillable, 
    // ni añadir un cast específico para que SoftDeletes funcione.
    
    protected $table = "publicaciones";

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'usuario_id',
        'oposicion_id', 
        'titulo', 
        'contenido',
    ];
    
    /**
     * Timestamps.
     * If you need to customize the names of the columns used to store the timestamps, 
     * you may define CREATED_AT and UPDATED_AT constants on your model
     * https://laravel.com/framework/docs/12.x/eloquent#timestamps
     */

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

    public function oposicion(): BelongsTo {
        return $this->belongsTo(Oposicion::class, 'oposicion_id');
    }

    public function comentarios(): HasMany {
        return $this->hasMany(Comentario::class, 'publicacion_id');
    }

    // colección de Usuario, cada uno con
    // $publicacion->usuariosMeSirvio()->wherePivot('checked', true)
    public function usuariosMeSirvio(): BelongsToMany {
        return $this->belongsToMany(Usuario::class, 'me_sirvio', 'publicacion_id', 'usuario_id')
                ->withPivot('checked'); 
    }

}
