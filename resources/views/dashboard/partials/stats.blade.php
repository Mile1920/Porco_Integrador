<div class="row">
    <div class="col-md-4">
        <div class="card card-stats" style="min-height: 200px;">
            <div class="card-body text-center">
                <div class="icon-big text-center icon-warning" style="font-size: 3rem; margin-bottom: 15px;">
                    <i class="fas fa-users"></i>
                </div>
                <p class="card-category" style="font-size: 1.2rem; font-weight: bold; color: #c5d0dc;">Trabajadores Activos</p>
                <h2 class="card-title" style="font-size: 3rem; font-weight: bold; color: var(--porco-normal);">{{ $trabajadoresActivos ?? 0 }}</h2>
            </div>
            <div class="card-footer text-center">
                <div class="stats">
                    <small style="color: #9ca3af;">Información actualizada</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-stats" style="min-height: 200px;">
            <div class="card-body text-center">
                <div class="icon-big text-center icon-danger" style="font-size: 3rem; margin-bottom: 15px;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <p class="card-category" style="font-size: 1.2rem; font-weight: bold; color: #c5d0dc;">Incidentes Abiertos</p>
                <h2 class="card-title" style="font-size: 3rem; font-weight: bold; color: var(--porco-critical);">{{ $incidentesAbiertos ?? 0 }}</h2>
            </div>
            <div class="card-footer text-center">
                <div class="stats">
                    <small style="color: #9ca3af;">Incidentes pendientes</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-stats" style="min-height: 200px;">
            <div class="card-body text-center">
                <div class="icon-big text-center icon-info" style="font-size: 3rem; margin-bottom: 15px;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <p class="card-category" style="font-size: 1.2rem; font-weight: bold; color: #c5d0dc;">Datos Sensores (24h)</p>
                <h2 class="card-title" style="font-size: 3rem; font-weight: bold; color: var(--porco-info);">{{ $sensorRecientes ?? 0 }}</h2>
                <div class="mt-3">
                    <canvas id="sensorSparkline" width="200" height="60"></canvas>
                </div>
            </div>
            <div class="card-footer text-center">
                <div class="stats">
                    <small style="color: #9ca3af;">Datos en tiempo real</small>
                </div>
            </div>
        </div>
    </div>
</div>
