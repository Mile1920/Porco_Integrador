<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use App\Models\Area;
use App\Models\Ingreso;
use App\Models\Incidente;
use App\Models\Cargo;
use App\Models\Alerta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class EstadisticasController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getData(auth()->user(), $request);

        return view('estadisticas.index', $data);
    }

    public function pdf(Request $request)
    {
        $data = $this->getData(auth()->user(), $request);

        $pdf = Pdf::loadView('estadisticas.pdf', $data);
        return $pdf->download('estadisticas-mina-porco-' . now()->format('Y-m-d') . '.pdf');
    }

    private function getData($user, Request $request = null)
    {
        $filtrando = $request && ($request->filled('desde') || $request->filled('hasta'));
        $desde = $request && $request->filled('desde') ? \Carbon\Carbon::parse($request->desde)->startOfDay() : today()->subDays(30);
        $hasta = $request && $request->filled('hasta') ? \Carbon\Carbon::parse($request->hasta)->endOfDay() : today()->endOfDay();

        $areaRestringida = null;
        if ($user->hasRole('administrador-area') && $user->trabajador && $user->trabajador->area_id) {
            $areaRestringida = $user->trabajador->area_id;
        }

        $totalTrabajadores = $areaRestringida
            ? Trabajador::where('activo', true)->where('area_id', $areaRestringida)->count()
            : Trabajador::where('activo', true)->count();
        $totalAreas = Area::where('activo', true)->count();
        $totalCargos = Cargo::where('activo', true)->count();

        // Usar el rango de fechas para todas las consultas cuando hay filtro
        $fechaRefInicio = $filtrando ? $desde : today()->startOfDay();
        $fechaRefFin = $filtrando ? $hasta : today()->endOfDay();

        $ingresosHoy = Ingreso::whereBetween('registrado_en', [$fechaRefInicio, $fechaRefFin])->where('tipo', 'ingreso')->when($areaRestringida, fn($q) => $q->whereHas('trabajador', fn($qq) => $qq->where('area_id', $areaRestringida)))->count();
        $salidasHoy = Ingreso::whereBetween('registrado_en', [$fechaRefInicio, $fechaRefFin])->where('tipo', 'salida')->when($areaRestringida, fn($q) => $q->whereHas('trabajador', fn($qq) => $qq->where('area_id', $areaRestringida)))->count();
        $ingresosMes = $ingresosHoy;
        $salidasMes = $salidasHoy;

        $incidentesAbiertos = Incidente::where('estado', 'pendiente')->whereBetween('created_at', [$fechaRefInicio, $fechaRefFin])->when($areaRestringida, fn($q) => $q->where('area_id', $areaRestringida))->count();
        $incidentesPendientes = $incidentesAbiertos;
        $incidentesCerrados = Incidente::where('estado', 'completado')->whereBetween('created_at', [$fechaRefInicio, $fechaRefFin])->when($areaRestringida, fn($q) => $q->where('area_id', $areaRestringida))->count();
        $incidentesCriticos = Incidente::where('gravedad', 'alta')->whereBetween('created_at', [$fechaRefInicio, $fechaRefFin])->when($areaRestringida, fn($q) => $q->where('area_id', $areaRestringida))->count();

        // Alertas del período
        $alertasPeriodo = Alerta::whereBetween('fecha_alerta', [$fechaRefInicio, $fechaRefFin])
            ->when($areaRestringida, fn($q) => $q->where('area', $areaRestringida))
            ->count();
        $alertasCriticas = Alerta::whereBetween('fecha_alerta', [$fechaRefInicio, $fechaRefFin])
            ->where('nivel', 'critico')
            ->when($areaRestringida, fn($q) => $q->where('area', $areaRestringida))
            ->count();
        $alertasPorTipo = Alerta::whereBetween('fecha_alerta', [$fechaRefInicio, $fechaRefFin])
            ->when($areaRestringida, fn($q) => $q->where('area', $areaRestringida))
            ->select('tipo', DB::raw('count(*) as total'))
            ->groupBy('tipo')
            ->pluck('total', 'tipo');

        $turnos = Trabajador::where('activo', true)
            ->select('turno', DB::raw('count(*) as total'))
            ->groupBy('turno')
            ->pluck('total', 'turno');

        $trabajadoresPorArea = Area::withCount(['trabajadores' => function ($q) {
            $q->where('activo', true);
        }])->get()->map(function ($a) {
            return ['label' => $a->nombre, 'value' => $a->trabajadores_count];
        });
        if ($areaRestringida && $user->trabajador?->area) {
            $trabajadoresPorArea = $trabajadoresPorArea->filter(fn($a) => $a['label'] === $user->trabajador->area->nombre)->values();
        }

        // Gráfico de ingresos/salidas: por día dentro del rango (o últimos 7 si no hay filtro)
        $ingresos7d = collect();
        $salidas7d = collect();
        $labels7d = collect();
        $diasAtras = $filtrando ? min(30, (int) $desde->diffInDays($hasta)) : 6;
        for ($i = $diasAtras; $i >= 0; $i--) {
            $dia = $hasta->copy()->subDays($i);
            $labels7d->push($dia->format('d/m'));
            $ingresos7d->push(Ingreso::whereDate('registrado_en', $dia)->where('tipo', 'ingreso')
                ->when($areaRestringida, fn($q, $aid) => $q->whereHas('trabajador', fn($qq) => $qq->where('area_id', $aid)))
                ->count());
            $salidas7d->push(Ingreso::whereDate('registrado_en', $dia)->where('tipo', 'salida')
                ->when($areaRestringida, fn($q, $aid) => $q->whereHas('trabajador', fn($qq) => $qq->where('area_id', $aid)))
                ->count());
        }

        $horasPico = Ingreso::whereBetween('registrado_en', [$fechaRefInicio, $fechaRefFin])
            ->when($areaRestringida, fn($q) => $q->whereHas('trabajador', fn($qq) => $qq->where('area_id', $areaRestringida)))
            ->select(DB::raw("EXTRACT(HOUR FROM registrado_en) as hora"), DB::raw('count(*) as total'))
            ->groupBy('hora')
            ->orderBy('hora')
            ->pluck('total', 'hora');

        $incidentesPorGravedad = Incidente::whereBetween('created_at', [$fechaRefInicio, $fechaRefFin])
            ->when($areaRestringida, fn($q) => $q->where('area_id', $areaRestringida))
            ->select('gravedad', DB::raw('count(*) as total'))
            ->groupBy('gravedad')
            ->pluck('total', 'gravedad');

        // Incidentes por mes: dentro del rango (o últimos 6 si no hay filtro)
        $incidentes6m = collect();
        $labels6m = collect();
        $mesesAtras = $filtrando ? max(0, (int) $desde->diffInMonths($hasta)) : 5;
        for ($i = $mesesAtras; $i >= 0; $i--) {
            $mes = $hasta->copy()->subMonths($i);
            $labels6m->push($mes->format('M Y'));
            $incidentes6m->push(Incidente::whereYear('created_at', $mes->year)->whereMonth('created_at', $mes->month)
                ->when($areaRestringida, fn($q, $aid) => $q->where('area_id', $aid))
                ->count());
        }

        // Tabla de datos del período filtrado
        $incidentesQuery = Incidente::whereBetween('created_at', [$desde, $hasta]);
        $ingresosQuery = Ingreso::whereBetween('registrado_en', [$desde, $hasta])->with('trabajador');
        if ($areaRestringida) {
            $incidentesQuery->where('area_id', $areaRestringida);
            $ingresosQuery->whereHas('trabajador', fn($q) => $q->where('area_id', $areaRestringida));
        }
        $incidentesPeriodo = $incidentesQuery->get();
        $ingresosPeriodo = $ingresosQuery->get();
        $dataTable = collect();

        foreach ($incidentesPeriodo as $inc) {
            $dataTable->push([
                'fecha' => $inc->created_at ? $inc->created_at->format('d/m/Y H:i') : '-',
                'tipo' => 'Incidente',
                'tipo_badge' => 'danger',
                'descripcion' => Str::limit($inc->descripcion ?? 'Incidente reportado', 80),
                'origen' => $inc->trabajador?->nombre_completo ?? 'Sistema',
                'estado' => ucfirst($inc->estado ?? 'pendiente'),
            ]);
        }
        foreach ($ingresosPeriodo as $ing) {
            $dataTable->push([
                'fecha' => $ing->registrado_en ? $ing->registrado_en->format('d/m/Y H:i') : '-',
                'tipo' => $ing->tipo === 'ingreso' ? 'Ingreso' : 'Salida',
                'tipo_badge' => $ing->tipo === 'ingreso' ? 'success' : 'warning',
                'descripcion' => $ing->tipo === 'ingreso' ? 'Registro de ingreso' : 'Registro de salida',
                'origen' => $ing->trabajador?->nombre_completo ?? '-',
                'estado' => 'Completado',
            ]);
        }
        $dataTable = $dataTable->sortByDesc('fecha')->values();

        return compact(
            'totalTrabajadores', 'totalAreas', 'totalCargos',
            'ingresosHoy', 'salidasHoy', 'ingresosMes', 'salidasMes',
            'incidentesAbiertos', 'incidentesPendientes', 'incidentesCerrados', 'incidentesCriticos',
            'turnos', 'trabajadoresPorArea',
            'ingresos7d', 'salidas7d', 'labels7d', 'horasPico',
            'incidentesPorGravedad', 'incidentes6m', 'labels6m',
            'areaRestringida', 'dataTable',
            'alertasPeriodo', 'alertasCriticas', 'alertasPorTipo'
        );
    }
}
