<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Carbon\CarbonInterface;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Oposicion;
use App\Models\Usuario;
use App\Models\Comentario;
use App\Models\Reporte;


class Publicacion extends Model {

    use HasUuids, SoftDeletes;
    
    protected $table = "publicaciones";

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'usuario_id',
        'oposicion_id', 
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


    private ?CarbonInterface $borradoEn = null;

    protected static function booted(): void
    {
        static::deleted(function (Publicacion $publicacion) {
            if ($publicacion->isForceDeleting()) { return; }

            $publicacion->comentarios()->delete();
        });

        static::restoring(function (Publicacion $publicacion) {
            $publicacion->borradoEn = $publicacion->deleted_at;
        });

        static::restored(function (Publicacion $publicacion) {
            $publicacion->comentarios()->onlyTrashed()
                ->where('deleted_at', '>=', $publicacion->borradoEn)
                ->whereHas('usuario')
                ->restore();
        });
    }
    
    
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

    // TODO. El admin, si quiere ver los comentarios de una publicación eliminada, 
    // que los vea por una ruta de admin específica con su propia consulta withTrashed(), 
    // no por la página pública del foro.
    public function comentarios(): HasMany {
        return $this->hasMany(Comentario::class, 'publicacion_id');
        // ->withTrashed();
    }

    public function meSirvio(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'me_sirvio', 'publicacion_id', 'usuario_id');
    }
}
