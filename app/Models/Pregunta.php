<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Test;
use App\Models\OpcionRespuesta;


class Pregunta extends Model {

    use HasUuids;

    protected $table = "preguntas";

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'test_id', 
        'enunciado', 
    ];
    
    public $timestamps = false;


    // Relaciones

    public function opcionesRespuesta(): HasMany{
        return $this->hasMany(OpcionRespuesta::class, 'pregunta_id');
    }
    
    public function test(): BelongsTo{
        return $this->belongsTo(Test::class, 'test_id');
    }
}
