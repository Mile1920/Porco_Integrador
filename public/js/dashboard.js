document.addEventListener('DOMContentLoaded', function () {
    // Detail modal behavior
    document.querySelectorAll('.detail-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
            var type = this.dataset.type;
            var modalBody = document.getElementById('detailModalBody');
            if (!modalBody) return;
            if (type === 'incidente'){
                var desc = this.dataset.desc || '';
                var area = this.dataset.area || '';
                var gravedad = this.dataset.gravedad || '';
                mostrarDetalle(modalBody, [['Área', area], ['Gravedad', gravedad]], desc);
            } else if (type === 'trabajador'){
                var nombre = this.dataset.nombre || '';
                var ci = this.dataset.ci || '';
                var area = this.dataset.area || '';
                mostrarDetalle(modalBody, [['Nombre', nombre], ['CI', ci], ['Área', area]]);
            }
            if (window.jQuery && $('#detailModal').modal) {
                $('#detailModal').modal('show');
            }
        });
    });

    // Render sparkline using Chart.js and window.DASHBOARD_DATA
    var ctxEl = document.getElementById('sensorSparkline');
    if (ctxEl && window.Chart && window.DASHBOARD_DATA) {
        try {
            var labels = window.DASHBOARD_DATA.labels || [];
            var data = window.DASHBOARD_DATA.counts || [];
            new Chart(ctxEl.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        borderColor: '#c52d59',
                        backgroundColor: 'rgba(197,45,89,0.15)',
                        tension: 0.3,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {legend: {display: false}},
                    scales: {x: {display: false}, y: {display: false}}
                }
            });
        } catch (e) {
            console.error('Error inicializando sparkline:', e);
        }
    }
});

function mostrarDetalle(contenedor, campos, descripcion) {
    var fragmento = document.createDocumentFragment();
    campos.forEach(function (campo) {
        var p = document.createElement('p');
        var strong = document.createElement('strong');
        strong.textContent = campo[0] + ': ';
        p.append(strong, document.createTextNode(campo[1] || '—'));
        fragmento.appendChild(p);
    });
    if (descripcion !== undefined) {
        var hr = document.createElement('hr');
        var p = document.createElement('p');
        p.textContent = descripcion;
        fragmento.append(hr, p);
    }
    contenedor.replaceChildren(fragmento);
}
