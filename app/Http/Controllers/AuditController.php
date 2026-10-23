<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'accion' => 'nullable|string|max:100',
            'tabla' => 'nullable|string|max:50',
            'busqueda' => 'nullable|string|max:200',
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date|after_or_equal:desde',
        ]);

        $query = Auditoria::with('usuario')->orderBy('Fecha', 'desc');

        if ($request->filled('accion')) {
            $query->where('Accion', $request->accion);
        }
        if ($request->filled('tabla')) {
            $query->where('Tabla_Afectada', 'like', '%' . $request->tabla . '%');
        }
        if ($request->filled('busqueda')) {
            $q = $request->busqueda;
            $query->where(function ($w) use ($q) {
                $w->where('Detalle', 'like', "%{$q}%")
                  ->orWhere('IP_Origen', 'like', "%{$q}%");
            });
        }
        if ($request->filled('desde')) {
            $query->whereDate('Fecha', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('Fecha', '<=', $request->hasta);
        }

        $logs = $query->paginate(50);

        $acciones = Auditoria::select('Accion')->distinct()->orderBy('Accion')->pluck('Accion');
        $tablas = Auditoria::select('Tabla_Afectada')->distinct()->orderBy('Tabla_Afectada')->pluck('Tabla_Afectada');

        return view('auditoria.index', compact('logs', 'acciones', 'tablas'));
    }

    public function show($id)
    {
        $log = Auditoria::with('usuario')->findOrFail($id);
        return view('auditoria.show', compact('log'));
    }

    public function limpiar()
    {
        $limite = now()->subMonths(3);
        $eliminados = Auditoria::where('Fecha', '<', $limite)->delete();

        return redirect()->route('auditoria.index')
            ->with('success', "Logs anteriores a 3 meses eliminados: {$eliminados} registros");
    }
}
