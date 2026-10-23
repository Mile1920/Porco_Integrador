<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mina Porco | @yield('title', 'Dashboard')</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">

    <!-- Material Dashboard CSS -->
    <link href="{{ asset('assets/css/material-dashboard.css?v=2.1.0') }}" rel="stylesheet">

    <!-- Tu CSS personalizado -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/porco-theme.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body class="{{ session('theme', 'dark-edition') }}" id="appBody">
    <div class="wrapper">

        <!-- SIDEBAR MATERIAL DARK -->
        @include('layouts.material-sidebar')

        <div class="main-panel main-app">

            <!-- NAVBAR SUPERIOR -->
            <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top">
                <div class="container-fluid">
                    <div class="navbar-wrapper">
                        <a class="navbar-brand" href="javascript:;">@yield('title')</a>
                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                        <span class="navbar-toggler-icon icon-bar"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end">
                        <ul class="navbar-nav">
                            <!-- Theme Toggle -->
                            <li class="nav-item d-flex align-items-center me-3">
                                <!-- Indicador de alertas activas -->
                                <a class="nav-link" href="{{ route('alerts.index') }}" title="Alertas activas" style="position:relative; cursor:pointer;">
                                    <i class="material-icons" style="font-size: 24px; vertical-align: middle;">notifications</i>
                                    <span id="alertBadge" class="badge badge-danger" style="position:absolute; top:2px; right:2px; font-size:0.65rem; min-width:18px; height:18px; line-height:12px; border-radius:50%; display:none;">0</span>
                                </a>
                            </li>
                            <li class="nav-item d-flex align-items-center me-3">
                                <a class="nav-link" href="#" onclick="toggleTheme(); return false;" title="Cambiar tema" style="cursor:pointer;">
                                    <i class="material-icons" id="themeIcon" style="font-size: 24px; vertical-align: middle;">dark_mode</i>
                                    <span id="themeText" class="d-none d-md-inline" style="font-size:0.85rem;vertical-align:middle;">Modo Oscuro</span>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="{{ asset('img/Minero.png') }}" alt="Perfil" class="icon-img"> 
                                    <span class="d-none d-lg-inline">{{ Auth::user()->name }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                                    <div class="dropdown-header">
                                        {{ Auth::user()->name }}<br>
                                        <small class="text-muted">{{ Auth::user()->getRoleNames()->first() ?? 'Usuario' }}</small>
                                    </div>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <img src="{{ asset('img/Minero.png') }}" alt="Perfil" class="icon-img"> Mi Perfil
                                    </a>
                                    <a class="dropdown-item" href="{{ route('profile.change-password') }}">
                                        <img src="{{ asset('img/Minero.png') }}" alt="Contraseña" class="icon-img"> Cambiar Contraseña
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
                                        @csrf
                                    </form>
                                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <img src="{{ asset('img/Logo.png') }}" alt="Cerrar" class="icon-img"> Cerrar Sesión
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- FIN NAVBAR -->

            <div class="content">
                <div class="container-fluid">
                    <!-- Componente de notificaciones -->
                    <x-notification />
                    
                    <!-- Componente de confirmación -->
                    <x-confirm-modal />

                    @yield('content')
                </div>
            </div>

        </div>
    </div>

    <!-- Scripts Material Dashboard -->
    <script src="{{ asset('assets/js/core/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap-material-design.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/material-dashboard.js?v=2.1.0') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')

    <!-- Modal de Emergencia Global -->
    @auth
        @include('partials.emergency-alert-modal')
    @endauth

    <!-- Theme Toggle -->
    <script>
        function toggleTheme() {
            const body = document.getElementById('appBody');
            const icon = document.getElementById('themeIcon');
            const text = document.getElementById('themeText');
            const isDark = body.classList.contains('dark-edition');

            if (isDark) {
                body.classList.remove('dark-edition');
                body.classList.add('light-edition');
                icon.textContent = 'light_mode';
                text.textContent = 'Modo Claro';
                localStorage.setItem('theme', 'light-edition');
            } else {
                body.classList.remove('light-edition');
                body.classList.add('dark-edition');
                icon.textContent = 'dark_mode';
                text.textContent = 'Modo Oscuro';
                localStorage.setItem('theme', 'dark-edition');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'dark-edition';
            const body = document.getElementById('appBody');
            const icon = document.getElementById('themeIcon');
            const text = document.getElementById('themeText');

            body.className = savedTheme;
            if (icon && text) {
                icon.textContent = savedTheme === 'dark-edition' ? 'dark_mode' : 'light_mode';
                text.textContent = savedTheme === 'dark-edition' ? 'Modo Oscuro' : 'Modo Claro';
            }
        });
    </script>

    <!-- Script global de utilidades -->
    <script>
        // Función global para mostrar notificaciones
        window.showNotification = function(type, message, duration = 5000) {
            const container = document.getElementById('notification-container');
            if (!container) {
                console.warn('Notification container not found');
                return;
            }

            const alertClass = type === 'success' ? 'alert-success' :
                              type === 'error' ? 'alert-danger' :
                              type === 'warning' ? 'alert-warning' : 'alert-info';

            const iconClass = type === 'success' ? 'fa-check-circle' :
                             type === 'error' ? 'fa-exclamation-triangle' :
                             type === 'warning' ? 'fa-exclamation-circle' : 'fa-info-circle';

            const title = type === 'success' ? '¡Éxito!' :
                         type === 'error' ? 'Error:' :
                         type === 'warning' ? 'Advertencia:' : 'Información:';

            const alertId = 'alert-' + Date.now();

            const alertHtml = `
                <div id="${alertId}" class="alert ${alertClass} alert-dismissible fade show notification-alert" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <i class="fas ${iconClass}"></i>
                    <strong>${title}</strong> ${message}
                </div>
            `;

            container.insertAdjacentHTML('beforeend', alertHtml);

            // Aplicar eventos al nuevo alert
            const newAlert = document.getElementById(alertId);
            newAlert.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.02)';
                this.style.boxShadow = '0 6px 20px rgba(0, 0, 0, 0.2)';
            });

            newAlert.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
                this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
            });

            // Auto-hide
            setTimeout(function() {
                if (newAlert && newAlert.parentNode) {
                    newAlert.style.animation = 'slideOutRight 0.5s ease-in forwards';
                    setTimeout(function() {
                        const bsAlert = new bootstrap.Alert(newAlert);
                        bsAlert.close();
                    }, 450);
                }
            }, duration);
        };

        // Función para mostrar notificación de éxito
        window.showSuccess = function(message, duration) {
            showNotification('success', message, duration);
        };

        // Función para mostrar notificación de error
        window.showError = function(message, duration) {
            showNotification('error', message, duration);
        };

        // Función para mostrar notificación de advertencia
        window.showWarning = function(message, duration) {
            showNotification('warning', message, duration);
        };

        // Función para mostrar notificación de información
        window.showInfo = function(message, duration) {
            showNotification('info', message, duration);
        };

        // Prevent form flash on page load + validaciones ligeras de formulario
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('loaded');
            
            // Inicializar todos los tooltips de Bootstrap
            if (typeof $ !== 'undefined' && $.fn.tooltip) {
                $('[data-toggle="tooltip"]').tooltip();
            }

            // Validación rápida: solo letras para nombres/apellidos
            const lettersPattern = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;
            const phonePattern = /^[0-9+\s-]{6,20}$/;

            function markInvalid(input, messageKey, messageText) {
                input.classList.add('is-invalid');
                const flag = 'validationShown_' + messageKey;
                if (!input.dataset[flag] && typeof window.showError === 'function') {
                    window.showError(messageText, 5000);
                    input.dataset[flag] = '1';
                }
            }

            function clearInvalid(input) {
                input.classList.remove('is-invalid');
            }

            document.querySelectorAll('input[data-letters-only]').forEach(function(input) {
                input.addEventListener('input', function (e) {
                    const value = e.target.value;
                    if (value && !lettersPattern.test(value)) {
                        markInvalid(e.target, 'letters', 'Este campo solo permite letras y espacios.');
                    } else {
                        clearInvalid(e.target);
                    }
                });
            });

            document.querySelectorAll('input[data-phone]').forEach(function(input) {
                input.addEventListener('input', function (e) {
                    const value = e.target.value;
                    if (value && !phonePattern.test(value)) {
                        markInvalid(e.target, 'phone', 'El celular solo puede tener números, espacios, + y -.');
                    } else {
                        clearInvalid(e.target);
                    }
                });
            });
        });
    </script>
</body>
</html>
