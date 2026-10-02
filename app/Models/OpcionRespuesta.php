<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Pregunta;


class OpcionRespuesta extends Model {

    use HasUuids;

    protected $table = "opciones_respuesta";

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pregunta_id', 
        'contenido', 
        'es_correcta',
    ];
    
    public $timestamps = false;

    // Cast de atributos
    protected function casts(): array {
        return [ 'es_correcta' => 'boolean', ];
    }


    // Relaciones
    public function pregunta(): BelongsTo{
        return $this->belongsTo(Pregunta::class, 'pregunta_id');
    }
}
