<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\Usuario;
use App\Enums\EstadoReporte;
// use App\Enums\Reportable;


class Reporte extends Model {
    use HasUuids;

    protected $table = "reportes";

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'usuario_reporta_id', 
        'reportable_id', 
        'reportable_tipo',
        'motivo',
        'estado',
        'resuelto_por'
    ];
    
    public $timestamps = true;
    public const UPDATED_AT = null;

    protected $casts = [ 
        /**
         * Nota: Columna negocio corriente.
         *  
         * El único camino de lectura es el método getAttribute() estándar de Eloquent. 
         * El cast intercepta, convierte el string de la BD ('pendiente') en el objeto 
         * EstadoReporte::PENDIENTE, y lo devuelve.
         */
        'estado' => EstadoReporte::class
        
        /**
         * Nota: Columna de control.
         *  
         * Usa internamente el mecanismo de relaciones polimórficas. 
         * Cuando se llama a $reporte->reportable, internamente se lee el valor crudo de reportable_tipo, 
         * y se usa ese valor como clave de un array ($morphMap[$valor]) para decidir qué clase de modelo 
         * instanciar (Publicacion, Comentario, Usuario).
         * 
         * En PHP, las claves de array solo pueden ser string o int. Si el cast ya transformó 
         * reportable_tipo en un objeto Reportable::PUBLICACION antes de que morphTo() lo use, Laravel 
         * intenta hacer $morphMap[$objetoEnum]
         */

        // 'reportable_tipo' => Reportable::class,
    ];


    // Relaciones

    /**
     * Polymorphic Many to Many Relationships
     * https://laravel.com/framework/docs/12.x/eloquent-relationships#one-to-many-polymorphic-relations
     */
    
    // morphTo($relationName, $type, $id, [$ownerKey]) es el destino - dado un reporte, te devuelve el objeto reportado
    // morphMany es el lado inverso - dada una publicación, te devuelve todos sus reportes.
    public function reportable(): MorphTo {
        return $this->morphTo(
            'reportable', 
            'reportable_tipo', 
            'reportable_id'
        );
    }

    public function usuarioReporta(): BelongsTo {
        return $this->belongsTo(Usuario::class, 'usuario_reporta_id');
    }

    public function resueltoPor(): BelongsTo {
        return $this->belongsTo(Usuario::class, 'resuelto_por');
    }
}
