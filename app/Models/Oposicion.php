<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; 

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Usuario;
use App\Models\Publicacion;
use App\Models\Tema;


class Oposicion extends Model {
    use HasUuid;

    protected $table = "oposiciones";

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nombre', 
        'descripcion',
    ];
    
    public $timestamps = false;


    // Relaciones
    
    public function usuarios(): HasMany {
        return $this->hasMany(Usuario::class, 'oposicion_id');
    }

    public function publicaciones(): HasMany {
        return $this->hasMany(Publicacion::class, 'oposicion_id');
    }

    public function temarios(): HasMany {
        return $this->hasMany(Tema::class, 'oposicion_id');
    }

}
