<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\CarbonInterface;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Usuario;
use App\Models\Tema;
use App\Models\IntentoTest;
use App\Models\Pregunta;

class Test extends Model {

    use HasUuids, SoftDeletes;

    protected $table = "tests";

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'creado_por', 
        'temario_id', 
        'titulo',
        'descripcion',
    ];
    
    public $timestamps = true;
    public const UPDATED_AT = null;

    
    // Momento en que se borró el test. 
    // Se guarda en "restoring", porque en "restored" deleted_at ya vale null.
    private ?CarbonInterface $borradoEn = null;

    protected static function booted(): void 
    {
        // Prevención: restaurar solo lo eliminado en cascada y no 
        // eliminaciones premeditadas anteriores.
        
        // Al borrar el test, borrar también hijos en evento deleted: 
        // así los hijos siempre tienen un deleted_at igual o posterior al del test.
        static::deleted(function (Test $test) {
            if ($test->isForceDeleting()) { return; }

            $test->preguntas()->get()->each->delete();
            $test->intentosTest()->get()->each->delete();
        });

        static::restoring(function (Test $test) {
            $test->borradoEn = $test->deleted_at;
        });

        // Al restaurar el test, restaurar solo los hijos borrados con él,
        // no los que se borraron antes por su cuenta.
        static::restored(function (Test $test) {
            // uno a uno: dispara la cascada de cada Pregunta (sus opciones)
            $test->preguntas()->onlyTrashed()
                ->where('deleted_at', '>=', $test->borradoEn)
                ->get()->each->restore();

            // en bloque: los intentos no tienen hijos
            $test->intentosTest()->onlyTrashed()
                ->where('deleted_at', '>=', $test->borradoEn)
                ->whereHas('usuario')
                ->restore();
        });
    }


    // Relaciones

    public function preguntas(): HasMany{
        return $this->hasMany(Pregunta::class, 'test_id');
    }

    public function intentosTest(): HasMany{
        return $this->hasMany(IntentoTest::class, 'test_id');
    }
    
    public function temario(): BelongsTo{
        return $this->belongsTo(Tema::class, 'temario_id');
    }
    
    public function usuario(): BelongsTo{
        return $this->belongsTo(Usuario::class, 'creado_por');
    }

}
