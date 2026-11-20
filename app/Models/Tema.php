<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\CarbonInterface;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Test;
use App\Models\Recurso;
use App\Models\Oposicion;


class Tema extends Model {
    use HasUuids, SoftDeletes;

    protected $table = "temarios";

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'oposicion_id',
        'bloque',
        'numero_tema', 
        'titulo', 
    ];

    public $timestamps = false;

    
    private ?CarbonInterface $borradoEn = null;

    protected static function booted(): void 
    {
        static::deleted(function (Tema $tema) {
            if ($tema->isForceDeleting()) { return; }
            
            // Pone a null el numero del tema para poder insertar otros 
            // sin error de duplicados
            if ($tema->trashed()) {
                $tema->forceFill(['numero_tema' => null])->saveQuietly();
            }

            $tema->recursos()->get()->each->delete();
            $tema->tests()->get()->each->delete();
        });

        static::restoring(function (Tema $tema) {
            $tema->borradoEn = $tema->deleted_at;
        });

        static::restored(function (Tema $tema) {
            $tema->recursos()->onlyTrashed()
                ->where('deleted_at', '>=', $tema->borradoEn)
                ->restore();

            $tema->tests()->onlyTrashed()
                ->where('deleted_at', '>=', $tema->borradoEn)
                ->get()->each->restore();
        });
    }
    

    // Relaciones

    public function oposicion(): BelongsTo {
        return $this->belongsTo(Oposicion::class, 'oposicion_id');
    }

    public function recursos(): HasMany {
        return $this->hasMany(Recurso::class, 'temario_id');
    }

    public function tests(): HasMany {
        return $this->hasMany(Test::class, 'temario_id');
    }


}
