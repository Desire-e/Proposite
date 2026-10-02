<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Test;
use App\Models\Recurso;
use App\Models\Oposicion;


class Tema extends Model {
    use HasUuids;

    protected $table = "temarios";

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'oposicion_id',
        'numero_tema', 
        'titulo', 
    ];

    public $timestamps = false;
    
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
