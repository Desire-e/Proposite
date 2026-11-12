<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; 
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Usuario;
use App\Models\Publicacion;
use App\Models\Tema;
use Carbon\CarbonInterface;


class Oposicion extends Model {
    use HasUuids, SoftDeletes;

    protected $table = "oposiciones";

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nombre', 
        'descripcion',
    ];
    
    public $timestamps = false;


    private ?CarbonInterface $borradoEn = null;

    protected static function booted(): void 
    {
        // evento de Eloquent disparado despues de que un modelo sea 
        // eliminado de la BD
        static::deleting(function (Oposicion $oposicion) {
            // si contiene usuarios no baneados, impide eliminado lógico y físico
            if($oposicion->usuarios()->exists()) { 
                throw new \LogicException('No se puede eliminar una oposición con usuarios.');
            }

            // si hay solo baneados, se borran junto la oposición
            if ($oposicion->isForceDeleting()) {
                $oposicion->usuarios()->onlyTrashed()->get()->each->forceDelete();
            }
        });

        static::deleted(function (Oposicion $oposicion) {            
            if ($oposicion->isForceDeleting()) { return; }
            
            $oposicion->temario()->get()->each->delete();
            $oposicion->publicaciones()->get()->each->delete();
        });

        static::restoring(function (Oposicion $oposicion) {
            $oposicion->borradoEn = $oposicion->deleted_at;
        });

        static::restored(function (Oposicion $oposicion) {
            $oposicion->temario()->onlyTrashed()
                ->where('deleted_at', '>=', $oposicion->borradoEn)
                ->get()->each->restore();
            
            $oposicion->publicaciones()->onlyTrashed()
                ->where('deleted_at', '>=', $oposicion->borradoEn)
                ->get()->each->restore();
        });
    }


    // Relaciones
    
    public function usuarios(): HasMany {
        return $this->hasMany(Usuario::class, 'oposicion_id');
    }

    public function publicaciones(): HasMany {
        return $this->hasMany(Publicacion::class, 'oposicion_id');
    }

    public function temario(): HasMany {
        return $this->hasMany(Tema::class, 'oposicion_id');
    }

}
