# Car Wash Hernández — Proyecto Integrador (Laravel + PostgreSQL)

**Universidad de El Salvador · TPI + Sistemas Contables + Ingeniería Económica.**

Esta versión traslada la lógica y datos iniciales de la aplicación **ProyectoSIC (Flask + SQLite)** a Laravel 12 + PHP + PostgreSQL. Conserva las 16 tablas, el registro inicial de octubre 2026 y las categorías **confirmado / estimado / supuesto / pendiente**. Los datos de entrevista y las cotizaciones hipotéticas **no han sido validados**. El código Laravel debe probarse funcionalmente en tu equipo; en el entorno de generación se realizaron análisis estáticos y pruebas del motor financiero, pero no Docker end-to-end.

## Requisitos mínimos

- Windows 10/11 con **Docker Desktop** + WSL 2 y Docker integrado a Ubuntu; o Docker Engine + Compose en Linux.
- Conexión a Internet para que Docker descargue las imágenes y Composer instale las dependencias en el primer `build`.
- No necesitas instalar PostgreSQL ni PHP localmente si usas Docker.

## Arranque desde cero (RECOMENDADO)

1. Descomprime el ZIP en tu carpeta de proyectos de Ubuntu/WSL; por ejemplo `~/carwash-laravel`.
2. Abre Ubuntu (WSL) y entra a la carpeta:

   ```bash
   cd ~/carwash-laravel
   cp .env.example .env
   docker compose up -d --build
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate --seed
   docker compose exec app php artisan sic:usuario admin@carwash.local Administrador
   docker compose exec app php artisan optimize:clear
   ```

   Al ejecutar `sic:usuario` escribe tu contraseña **cuando te la pida** (mínimo 8 caracteres). No hay una contraseña predeterminada ni registro público. Las contraseñas se guardan con hash. Si deseas otro usuario, repite el comando con otro correo.

3. Abre **http://localhost:8090** e inicia sesión con el correo y contraseña que creaste.
4. Comprueba datos: panel, Libro diario, Costos, Alternativas, Evaluación, Fuente, Diagnóstico.
5. Si ves un error de conexión al arrancar el primer segundo, espera a que Docker indique `healthy` a PostgreSQL y repite `docker compose exec app php artisan migrate --seed`.

**Puertos elegidos para evitar conflictos con el proyecto anterior:** `8090` (web), `5436` (PostgreSQL desde Windows). Dentro de la red Docker la base usa `postgres:5432`, tal como está en `.env.example`.

Si ya usas `8090` o `5436`, cambia **solo** el lado izquierdo de los puertos publicados en `compose.yaml`. No cambies `DB_PORT=5432` para la conexión interna de Docker.

## Si no tienes el comando `docker` dentro de Ubuntu

En Docker Desktop activa **Settings → Resources → WSL Integration → Ubuntu → Apply & Restart**. Abre otra terminal Ubuntu y ejecuta `docker version` y `docker compose version`. No ejecutes el proyecto desde la distribución interna `docker-desktop`.

## Modificar y compartir

Los archivos están organizados de manera similar al mini proyecto de Semana de Sistemas:

```text
app/Models/                    Modelos Eloquent de las 16 tablas
app/Http/Controllers/         Autenticación, catálogos, diario, informes y economía
app/Services/                 ContabilidadService y FinanzasService (lógica)
config/sic.php                Campos y validaciones de los CRUD
routes/web.php                Rutas web y middleware auth
resources/views/              Plantillas Blade
public/css/style.css          Interfaz, adaptada del proyecto Flask
 database/migrations/         Tablas y usuarios
 database/seeders/            Importación inicial NO destructiva
 database/seeders/data/       Copia JSON de las 16 tablas de SQLite
compose.yaml                  App PHP/Apache y PostgreSQL
Dockerfile                    PHP 8.3 + extensiones + Composer
```

Los cambios en PHP/Blade/CSS normalmente se ven al recargar por el volumen compartido de Docker. Si editas `composer.json`, ejecuta `docker compose exec app composer install`. Si agregas una migración, ejecuta `docker compose exec app php artisan migrate`.

**GitHub:**

```bash
git init
git add .
git commit -m "Base Laravel y PostgreSQL - Proyecto SIC"
git branch -M main
git remote add origin https://github.com/TU_USUARIO/TU_REPOSITORIO.git
git push -u origin main
```

Si trabajas en un repositorio ya creado, omite `git init`, `git branch -M` y `git remote add` y usa las ramas del equipo. **Nunca subas `.env`, una copia de producción, contraseñas, documentación sensible ni archivos de evidencia privados.** `.env` y `*.db` están excluidos en `.gitignore`.

El compañero clona el proyecto, copia `.env.example` a `.env`, arranca Docker, ejecuta las migraciones y crea su propio usuario. Los seeders importan los datos académicos iniciales automáticamente; **no hacen falta scripts SQL manuales ni importar la base SQLite directamente a PostgreSQL**.

## Base de datos (PostgreSQL)

- Base: `carwash`
- Usuario local de desarrollo: `carwash`
- Contraseña local de desarrollo: `carwash_dev_local` (solo contenedor local; cambiar para desplegar)
- Host desde **Laravel en Docker**: `postgres`
- Host desde **Windows/pgAdmin externo**: `localhost`, puerto `5436`
- Se crean las 16 tablas académicas, usuarios y migraciones. El Seeder importa las 16 tablas desde `database/seeders/data/origen_sqlite.json`. El SQLite original se incluye como referencia y no se usa en ejecución.
- Puedes conectar pgAdmin con localhost:5436. No necesitas importar SQL; las migraciones y seeders crean todo.
- El seeder es **no destructivo**: en tablas con registros evita reinsertar, para no duplicar datos.

**Precaución:** `php artisan migrate:fresh --seed` borra las tablas actuales y **todos** los cambios. Úsalo solo en una base de datos descartable, nunca sobre el trabajo real de tus compañeros.

### Backup PostgreSQL

Desde Ubuntu, en la carpeta del proyecto:

```bash
# Copia binaria completa: NO subirla a GitHub
docker compose exec -T postgres pg_dump -U carwash -Fc carwash > carwash_respaldo.dump

# Respaldo descriptivo de las 16 tablas desde la aplicación:
# Inicia sesión y abre http://localhost:8090/respaldo/exportar
```

El exportador JSON contiene las tablas pero **no incluye el contenido de los archivos PDF/fotos**; para recuperar todo, conserva también las evidencias de `storage/app/private/evidencias` y el respaldo de PostgreSQL. El backup `.dump` se restaura con `pg_restore` en una base vacía, no sobre tablas con datos actuales.

## Funcionalidades

| Área | Funcionalidad |
|---|---|
| Autenticación | Login, cierre de sesión, protección de rutas, comando para crear usuarios |
| Contabilidad | Libro diario partida doble, libro mayor, balanza, hoja de trabajo, estados, caja |
| Costos | Costeo ponderado por servicio, MD/MOD/CIF, clasificación fijo/variable |
| Operación | CRUD ventas, personal, inventario, activos, pendientes, mantenimientos |
| Economía | Alternativas CRUD, VPN, TIR, CAUE, B/C, equivalencias, 3 métodos de depreciación, vida económica, comparación y verificación manual |
| Trazabilidad | Evidencias con PDF/fotos privados, fuentes asociadas a cada campo clave, diagnóstico de respaldos |
| Gestión | Configuración de empresa y tasas, registro de actividades, reporte imprimible y exportación JSON |

**Límite intencional:** los módulos de personal, pendientes, inventario y mantenimiento son registros operativos; **no crean automáticamente asientos**. Deben registrarse en Libro diario para que afecten estados financieros. Esto evita contabilizar dos veces cifras reconstruidas. El módulo de ventas sí tiene una acción explícita para contabilizar, con confirmación, y bloquea la edición/eliminación posterior de las ventas contabilizadas. El informe en pantalla es un resumen, no el documento académico completo.

## Advertencias contables y económicas pendientes

1. Los asientos heredados de octubre son reconstrucciones de entrevista y **no** evidencia de depósitos, caja inicial o facturas reales. Debe conciliarse el ingreso por 74 lavados desglosados, el promedio diario y la cifra de $1,200.
2. En el proyecto original aparecen **dos alternativas de hidrolavadora** con costos **supuestos** de $400 y $500, ahorro anual supuesto de $25 y cero lavados adicionales. Deben sustentarse con dos cotizaciones y con la decisión de conservar o reemplazar la actual, si corresponde.
3. El estudio original usa **TMAR 12% e ISR 30% como supuestos**. No los interpretes como tasas tributarias verificadas para una empresa informal.
4. El modelo tributario migrado preserva el comportamiento de `finanzas.py`: impuesto = tasa × (ingreso − costo − depreciación), incluso negativo. Esa deducción ante pérdidas no debe asumirse automáticamente aplicable sin revisión académica/tributaria.
5. La cuenta patrimonial de apertura (3103) es un **cuadre explícito** de activos conocidos, no una prueba de capital aportado.
6. Solo hay **una fuente documental registrada** en los datos iniciales (la entrevista sin fecha). Agrega documentos, fecha, carta firmada y verificaciones manuales.
7. Los costos unitarios recalculados emplean las ponderaciones iniciales y no representan tiempos medidos de lavado.
8. Se incorporaron redondeos a cuatro decimales en ciertas columnas de costos. Contrasta contra cálculos manuales antes de la defensa.

## Comprobación de cálculos

Sin instalar Laravel, con PHP local:

```bash
php tests/manual/comprobar_calculos.php
```

Tras instalar dependencias con Composer:

```bash
docker compose exec app php artisan test
```

Asegúrate de comprobar **tus** indicadores con cálculo manual independiente y revisar los resultados con el docente. La construcción del proyecto en este entorno no pudo ejecutar una prueba end-to-end de Laravel/PostgreSQL/Docker; ejecuta los pasos de arranque en tu PC antes de compartir el repositorio como versión estable.
