<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Usuario;
use App\Models\Tema;

class Recurso extends Model {

    use HasUuids, SoftDeletes;

    protected $table = "recursos";

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'subido_por', 
        'temario_id', 
        'titulo',
        'recurso_url',
    ];
    
    public $timestamps = true;
    public const UPDATED_AT = null;


    // Relaciones

    public function temario(): BelongsTo{
        return $this->belongsTo(Tema::class, 'temario_id');
    }
    
    public function usuario(): BelongsTo{
        return $this->belongsTo(Usuario::class, 'subido_por');
    }

}
