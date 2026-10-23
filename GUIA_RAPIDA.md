# 🚀 GUÍA RÁPIDA DE INICIO - MINA PORCO

## Estado Actual del Proyecto ✅

El proyecto ha sido completamente revisado y mejorado sin dañar funcionalidad existente.

---

## 🎯 Inicio Rápido (5 minutos)

### 1️⃣ Configurar Base de Datos
```bash
# Editar .env con tus credenciales PostgreSQL
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=IntegradorMinaSW
DB_USERNAME=postgres
DB_PASSWORD=tu_contraseña
```

### 2️⃣ Instalar Dependencias
```bash
composer install
npm install
```

### 3️⃣ Ejecutar Migraciones
```bash
php artisan migrate --fresh --seed
```

### 4️⃣ Compilar Assets
```bash
npm run build
```

### 5️⃣ Iniciar Servidor
```bash
php artisan serve
# Acceder a: http://localhost:8000
```

---

## 🔐 Credenciales de Prueba

| Usuario | Contraseña | Rol | Acceso |
|---------|-----------|-----|--------|
| admin@minaporco.com | 12345678 | Administrador Principal | Todo |
| adminarea@minaporco.com | 12345678 | Administrador Área | Áreas y Cargos |
| tecnico@minaporco.com | 12345678 | Técnico | Limitado |
| trabajador@minaporco.com | 12345678 | Trabajador | Básico |

Estas cuentas se crean con `php artisan migrate --seed`; son solo para desarrollo. Cambia las contraseñas antes de poner el sistema en producción.

---

## 📧 Configurar Correos

Para enviar credenciales de trabajadores automáticamente:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_app_password_16_caracteres
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu_email@gmail.com
MAIL_FROM_NAME="Mina Porco - Sistema"
```

**Nota:** Usar contraseña de aplicación de Gmail (16 caracteres), no la contraseña normal.

---

## 🛠️ Comandos Útiles

### Desarrollo
```bash
php artisan serve                    # Iniciar servidor
npm run dev                          # Compilar assets en modo desarrollo
php artisan tinker                   # Consola interactiva
```

### Base de Datos
```bash
php artisan migrate --fresh --seed   # Resetear base de datos
php artisan migrate:refresh          # Refrescar migraciones
php artisan db:seed --class=AdminUserSeeder  # Ejecutar seeder específico
```

### Código
```bash
composer pint                        # Formater PHP (Laravel Pint)
php artisan test                     # Ejecutar tests
composer lint                        # Verificar sintaxis
```

### Limpieza
```bash
php artisan cache:clear             # Limpiar cache
php artisan config:clear            # Limpiar configuración
php artisan view:clear              # Limpiar vistas compiladas
```

---

## 📁 Estructura Importante

```
app/
├── Models/              ← Modelos Eloquent (Trabajador, Area, Cargo, User)
├── Http/
│   └── Controllers/     ← Controladores (TrabajadorController, etc.)
└── Mail/                ← Clases de correos

resources/
├── views/               ← Plantillas Blade
├── css/                 ← Estilos CSS
└── js/                  ← JavaScript

database/
├── migrations/          ← Cambios de base de datos
└── seeders/            ← Datos iniciales

storage/
└── logs/               ← Archivos de log (verificar aquí los errores)

routes/
├── web.php             ← Rutas principales
└── auth.php            ← Rutas de autenticación
```

---

## 🐛 Solucionar Problemas

### Error: "SQLSTATE[HY000]: General error"
```bash
# Resetear y migrar de nuevo
php artisan migrate:fresh --seed
```

### Error: "No existe la tabla..."
```bash
# Ejecutar todas las migraciones
php artisan migrate
```

### Correos no se envían
```bash
# Verificar configuración .env
# Revisar logs: tail -f storage/logs/laravel.log
```

### Assets no cargan
```bash
# Compilar nuevamente
npm run build
php artisan config:clear
```

---

## 📚 Documentación Completa

Para más detalles, revisar:
- 📄 `IMPROVEMENTS.md` - Cambios técnicos detallados
- 📄 `RESUMEN_MEJORAS.md` - Resumen ejecutivo
- 📄 `CHECKLIST.md` - Verificación completa

---

## 🎓 Roles y Permisos

### Administrador Principal
- ✅ Ver/Crear/Editar/Eliminar Trabajadores
- ✅ Ver/Crear/Editar/Eliminar Áreas
- ✅ Ver/Crear/Editar/Eliminar Cargos

### Administrador de Área
- ✅ Ver/Crear/Editar Áreas
- ✅ Ver/Crear/Editar Cargos
- ❌ No puede gestionar Trabajadores

### Técnico
- ✅ Ver información (lectura limitada)
- ❌ No puede crear o editar

### Trabajador
- ✅ Ver su perfil
- ✅ Cambiar contraseña

---

## 🔒 Mejoras de Seguridad

- ✅ Validación CSRF en todos los formularios
- ✅ Encriptación de contraseñas con bcrypt
- ✅ Control de acceso basado en roles (Spatie Permission)
- ✅ Logging de todas las operaciones críticas
- ✅ Manejo de excepciones completo

---

## 📞 Soporte

Para problemas o dudas, revisar:
1. Archivos de log: `storage/logs/laravel.log`
2. Documentación: `IMPROVEMENTS.md`
3. Código fuente bien comentado en controladores

---

**Última actualización:** 25 de noviembre de 2025
**Versión:** 1.0
**Estado:** ✅ Listo para Producción
