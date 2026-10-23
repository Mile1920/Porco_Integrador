<div id="emergencyOverlay" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.85); z-index:99999; justify-content:center; align-items:center;">
    <div id="emergencyBox" style="background:#1a0000; border:3px solid #ff0000; border-radius:12px; max-width:440px; width:90%; box-shadow:0 0 50px rgba(255,0,0,0.5);">
        <div style="background:#cc0000; color:#fff; padding:10px 14px; border-radius:9px 9px 0 0; text-align:center;">
            <div style="font-size:1.8rem;">⚠️</div>
            <div style="font-weight:900; text-transform:uppercase; font-size:1rem;">Alerta de Emergencia</div>
        </div>
        <div style="padding:12px 14px; color:#fff; max-height:320px; overflow-y:auto;" id="emergencyContent"></div>
        <div style="padding:8px 14px; border-top:2px solid #cc0000; display:flex; justify-content:space-between; align-items:center;">
            <small style="color:#aaa;" id="emergencyCount"></small>
            <button onclick="cerrarOverlay()" style="background:#cc0000; color:#fff; border:none; padding:4px 16px; border-radius:4px; font-weight:bold; font-size:0.85rem; cursor:pointer;">Entendido</button>
        </div>
    </div>
</div>

<audio id="alertSound" preload="auto" loop style="display:none;">
    <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACAf39/f4B/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AgH9/f3+AAAA" type="audio/wav">
</audio>

<script>
(function() {
    var ultimoId = {{ $ultimoIdAlerta ?? 0 }};
    var pollTimer = null;
    var silenciado = false;
    var idsMostrados = {};  // IDs ya mostrados para evitar bucle

    var overlay = document.getElementById('emergencyOverlay');
    var content = document.getElementById('emergencyContent');
    var countEl = document.getElementById('emergencyCount');

    if (!overlay) { console.warn('[Alertas] overlay no encontrado'); return; }

    function init() {
        fetch('/api/alertas/ultimas?desde_id=0&solo_no_leidas=1&solo_criticas=1', {
            headers: { 'Accept': 'application/json' }
        })
        .then(function(r) { 
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json(); 
        })
        .then(function(data) {
            if (data.ultimo_id > ultimoId) ultimoId = data.ultimo_id;
            if (data.hay_nuevas && data.alertas.length > 0) {
                var nuevas = filtrarNoMostradas(data.alertas);
                if (nuevas.length > 0) mostrar(nuevas);
                else badge(0);
            } else {
                badge(0);
            }
        })
        .catch(function(e) { console.warn('[Alertas] init:', e); });

        pollTimer = setInterval(poll, 15000);
    }

    function poll() {
        fetch('/api/alertas/ultimas?desde_id=' + ultimoId, {
            headers: { 'Accept': 'application/json' }
        })
        .then(function(r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function(data) {
            if (data.hay_nuevas && data.alertas.length > 0) {
                var criticas = data.alertas.filter(function(a) {
                    return a.nivel === 'critico' || a.nivel === 'alto';
                });
                var nuevas = filtrarNoMostradas(criticas);
                if (nuevas.length > 0) {
                    ultimoId = data.ultimo_id;
                    mostrar(nuevas);
                } else if (data.ultimo_id > ultimoId) {
                    ultimoId = data.ultimo_id;
                }
            }
        })
        .catch(function(e) { console.warn('[Alertas] poll:', e); });
    }

    function filtrarNoMostradas(lista) {
        return lista.filter(function(a) {
            var key = String(a.id);
            if (idsMostrados[key]) return false;
            idsMostrados[key] = true;
            return true;
        });
    }

    function mostrar(alertas) {
        badge(alertas.length);
        var html = '';
        alertas.forEach(function(a) {
            html += '<div style="background:rgba(255,0,0,0.1); border-left:4px solid #ff0000; padding:8px 10px; margin-bottom:6px; border-radius:4px; font-size:0.85rem;">';
            html += '  <div style="display:flex; align-items:flex-start;">';
            html += '    <span style="font-size:1.3rem; margin-right:8px;">⚠️</span>';
            html += '    <div style="flex:1;">';
            html += '      <strong>' + escaparHtml(a.mensaje) + '</strong>';
            if (a.area) html += '<br><small style="color:#aaa;">Area: ' + escaparHtml(a.area) + '</small>';
            html += '      <br><small style="color:#888;">' + escaparHtml(fecha(a.fecha)) + '</small>';
            html += '    </div>';
            html += '    <span style="background:#cc0000; color:#fff; font-size:0.7rem; padding:2px 8px; border-radius:3px;">' + escaparHtml(String(a.nivel || 'desconocido').toUpperCase()) + '</span>';
            html += '  </div>';
            if (a.tipo === 'gases_toxicos') {
                html += '  <div style="margin-top:4px; font-size:0.75rem; color:#ff8888; border-top:1px solid rgba(255,255,255,0.1); padding-top:4px;">Use equipo de respiración. Evacúe si es necesario.</div>';
            }
            html += '</div>';
        });
        content.innerHTML = html;
        countEl.textContent = alertas.length + ' alerta(s) activa(s)';
        overlay.style.display = 'flex';
        if (!silenciado) sonido();
    }

    window.cerrarOverlay = function() {
        overlay.style.display = 'none';
        pararSonido();
        fetch('/api/alertas/marcar-leidas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]') || {}).getAttribute('content') || ''
            }
        }).catch(function() {});
    };

    function sonido() {
        try { var a = document.getElementById('alertSound'); if (a) { a.currentTime = 0; a.loop = true; a.play().catch(function() {}); } } catch(e) {}
    }
    function pararSonido() {
        try { var a = document.getElementById('alertSound'); if (a) { a.pause(); a.currentTime = 0; } } catch(e) {}
    }
    function badge(n) {
        var b = document.getElementById('alertBadge');
        if (!b) return;
        b.textContent = n > 0 ? n : '';
        b.style.display = n > 0 ? 'inline' : 'none';
    }
    function fecha(f) {
        try { var d = new Date(f); return ('0'+d.getDate()).slice(-2)+'/'+('0'+(d.getMonth()+1)).slice(-2)+'/'+d.getFullYear()+' '+('0'+d.getHours()).slice(-2)+':'+('0'+d.getMinutes()).slice(-2); } catch(e) { return f; }
    }
    function escaparHtml(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function(character) {
            return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[character];
        });
    }

    window.addEventListener('beforeunload', function() { if (pollTimer) clearInterval(pollTimer); pararSonido(); });

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
</script>
