<!-- Contenedor de notificaciones - siempre presente para permitir notificaciones programáticas -->
<div id="notification-container" class="notification-container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show notification-alert" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <i class="fas fa-check-circle"></i>
            <strong>¡Éxito!</strong> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show notification-alert" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Error:</strong> {{ session('error') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show notification-alert" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <i class="fas fa-exclamation-circle"></i>
            <strong>Advertencia:</strong> {{ session('warning') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show notification-alert" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <i class="fas fa-info-circle"></i>
            <strong>Información:</strong> {{ session('info') }}
        </div>
    @endif
</div>

<style>
.notification-container {
    position: fixed;
    top: 16px;
    right: 16px;
    z-index: 1050;
    max-width: 320px;
    width: 100%;
}

.notification-alert {
    margin-bottom: 8px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12);
    border: none;
    border-radius: 8px;
    animation: slideInRight 0.35s ease-out;
    padding: 10px 14px;
    font-size: 13px;
    line-height: 1.3;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-left: 4px solid var(--porco-normal);
    color: #155724;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border-left: 4px solid var(--porco-critical);
    color: #721c24;
}

.alert-warning {
    background: linear-gradient(135deg, #cfe2ff 0%, #9ec5fe 100%);
    border-left: 4px solid #3c7dd9;
    color: #0b2243;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border-left: 4px solid var(--porco-info);
    color: #0c5460;
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    opacity: 0.6;
    cursor: pointer;
    padding: 0;
    margin-left: 10px;
    transition: opacity 0.2s ease;
}

.btn-close:hover {
    opacity: 1;
}

.alert i {
    margin-right: 6px;
    font-size: 1.05rem;
}

.alert strong {
    font-weight: 600;
}

/* Responsive */
@media (max-width: 768px) {
    .notification-container {
        left: 10px;
        right: 10px;
        max-width: none;
    }

    .notification-alert {
        font-size: 12px;
        padding: 10px 12px;
    }
}
</style>

<script>
// Sistema de notificaciones mejorado
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.notification-alert');

    alerts.forEach(function(alert, index) {
        // Auto-hide con delay escalonado
        const delay = 4000 + (index * 500); // 4s, 4.5s, 5s, etc.

        setTimeout(function() {
            if (alert && alert.parentNode) {
                // Animación de salida
                alert.style.animation = 'slideOutRight 0.5s ease-in forwards';

                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 450);
            }
        }, delay);

        // Efecto hover
        alert.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
            this.style.boxShadow = '0 6px 20px rgba(0, 0, 0, 0.2)';
        });

        alert.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
        });
    });
});

// Animación de salida
const style = document.createElement('style');
style.textContent = `
@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}
`;
document.head.appendChild(style);
</script>
