<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    protected $table = 'alertas';

    protected $fillable = [
        'tipo', 'nivel', 'mensaje', 'origen', 'area', 'leida', 'fecha_alerta',
    ];

    protected $casts = [
        'leida' => 'boolean',
        'fecha_alerta' => 'datetime',
    ];

    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    public function scopeRecientes($query, $minutos = 10)
    {
        return $query->where('fecha_alerta', '>=', now()->subMinutes($minutos));
    }

    public function scopeCriticas($query)
    {
        return $query->where('nivel', 'critico');
    }
}
