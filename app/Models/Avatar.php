<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; 
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Usuario;

class Avatar extends Model {
    use HasUuids;

    protected $table = "avatares";

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nombre', 
        'ruta_archivo',
    ];

    public $timestamps = false;
    

    public function usuarios(): HasMany {
        return $this->hasMany(Usuario::class, 'avatar_id');
    }

}
