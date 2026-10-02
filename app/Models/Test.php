<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    ];
    
    public $timestamps = true;
    public const UPDATED_AT = null;


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
