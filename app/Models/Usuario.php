<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\Rol;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Oposicion;
use App\Models\Avatar;
use App\Models\Reporte;
use App\Models\Publicacion;
use App\Models\Comentario;
use App\Models\Test;
use App\Models\IntentoTest;
use App\Models\Recurso;


class Usuario extends Authenticatable {
    use HasUuids, SoftDeletes;

    protected $table = "usuarios";

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'oposicion_id', 
        'name', 
        'email',
        'password',
        'rol',
        'fecha_convocatoria',
        'biografia',
        'suspendido',
        'suspendido_por',
        'avatar_id',
    ];
    
    public $timestamps = true;

    // Evita que aparezca el valor en serializaciones
    protected $hidden = ['password'];

    // Cast de atributos
    protected function casts(): array {
        return [
            'rol'=> Rol::class, // enum de roles
            'fecha_convocatoria' => 'date',
            'suspendido' => 'boolean',
        ];
    }


    // Relaciones
    
    public function avatar(): BelongsTo{
        return $this->belongsTo(Avatar::class, 'avatar_id');
    }
    
    public function intentosTests(): HasMany {
        return $this->hasMany(IntentoTest::class, 'usuario_id');
    }

    // ================
    // OPOSITOR
    // ================
    
    public function oposicion(): BelongsTo{
        // Usuario pertenece a una Oposicion
        // La relación se guarda en "oposicion_id" de la tabla Usuario
        return $this->belongsTo(Oposicion::class, 'oposicion_id');
    }

    // Reportes que este usuario ha enviado
    public function reportesMandados(): HasMany {
        return $this->hasMany(Reporte::class, 'usuario_reporta_id');
    }

    // Reportes que otros usuarios han hecho sobre ESTE usuario
    public function reportesRecibidos(): MorphMany {
        // morphMany($related, $relationName, $type, $id, [$localKey])
        return $this->morphMany(
            Reporte::class,
            'reportable',
            'reportable_tipo',
            'reportable_id'
        );
    }
    
    public function suspendidoPor(): BelongsTo{
        return $this->belongsTo(Usuario::class, 'suspendido_por');
    }

    public function publicaciones(): HasMany {
        return $this->hasMany(Publicacion::class, 'usuario_id');
    }

    public function comentarios(): HasMany {
        return $this->hasMany(Comentario::class, 'usuario_id');
    }

    public function temarioMarcado(): BelongsToMany {
        return $this->belongsToMany(Tema::class, 'check_temario')
                ->withPivot('checked'); 
    }

    // ================
    // ADMIN
    // ================

    public function usuariosSuspendidos(): HasMany {
        return $this->hasMany(Usuario::class, 'suspendido_por');
    }

    public function testsCreados(): HasMany {
        return $this->hasMany(Test::class, 'creado_por');
    }

    public function recursos(): HasMany {
        return $this->hasMany(Recurso::class, 'subido_por');
    }

    public function reportesResueltos(): HasMany {
        return $this->hasMany(Reporte::class, 'resuelto_por');
    }

}
