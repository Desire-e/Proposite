<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Test;
use App\Models\OpcionRespuesta;
use Carbon\CarbonInterface;


class Pregunta extends Model {

    use HasUuids, SoftDeletes;

    protected $table = "preguntas";

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'test_id', 
        'enunciado', 
    ];
    
    public $timestamps = false;


    private ?CarbonInterface $borradoEn = null;

    protected static function booted(): void 
    {
        static::deleted(function (Pregunta $pregunta) {
            if ($pregunta->isForceDeleting()) { return; }

            $pregunta->opcionesRespuesta()->delete();
        });

        static::restoring(function (Pregunta $pregunta) {
            $pregunta->borradoEn = $pregunta->deleted_at;
        });

        static::restored(function (Pregunta $pregunta) {
            $pregunta->opcionesRespuesta()->onlyTrashed()
                ->where('deleted_at', '>=', $pregunta->borradoEn)
                ->restore();
        });
    }

    
    // Relaciones

    public function opcionesRespuesta(): HasMany{
        return $this->hasMany(OpcionRespuesta::class, 'pregunta_id');
    }
    
    public function test(): BelongsTo{
        return $this->belongsTo(Test::class, 'test_id');
    }
}
