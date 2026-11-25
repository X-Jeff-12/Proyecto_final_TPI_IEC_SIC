# Matriz funcional — versión inicial de migración

| Área | Estado de esta versión | Pendiente de validación / ampliación |
|---|---|---|
| Autenticación | Login/logout, sesiones, comando de alta de usuario | Prueba de navegador y roles si TPI los exige |
| Diario y partida doble | Asientos con 2+ líneas y control Debe=Haber, borrado restringido | Pruebas CRUD y conciliación contable |
| Mayor, balanza, hoja | Lectura desde asientos y ajustes | Comprobación con cálculo manual |
| Estados financieros | Estado de resultados y balance reconstruido | Confirmar patrimonio de apertura, ingreso real y saldos |
| Costos | Distribución MD/MOD/CIF desde cuentas y volumen estimado | Sustentar factores de ponderación |
| Activos | CRUD, 3 métodos de depreciación, ajuste automático mensual | Fecha real/vida útil y clasificación contable |
| Alternativas | CRUD, campos originales y relación con fuentes | Cotizaciones reales y comparación contra conservar el equipo |
| Ingeniería Económica | Factores, VPN/TIR/CAUE/B-C, flujos y vida económica | Impuestos, equivalencia de alternativas y supuestos de beneficios |
| Verificación cruzada | Carga manual de indicadores con tolerancias | Procedimientos manuales independientes y anexos |
| Evidencias | Fuentes y PDF/fotos privados, trazas por campo | Al menos 2 fuentes por dato clave, carta firmada |
| Ventas | CRUD, contabilización por lote explícita | Evitar duplicar asientos reconstruidos, enlace al lote |
| Inventario/personal/pendientes/mantenimiento | CRUD operativo | Decidir si contabilizar transacciones desde estos módulos |
| Reporte e historial | Informe imprimible, log de acciones, export JSON | Informe académico de 15–25 páginas y controles de auditoría |

## Datos heredados

Las 16 tablas iniciales provienen directamente de `ProyectoSIC/carwash.db`, y se exportaron a `database/seeders/data/origen_sqlite.json` para las migraciones y seeders. No se transformó un dato estimado en confirmado. El SQLite original se conserva solamente como referencia local.

## Cuidados de presentación

- La **paridad numérica** del motor económico se comprobó frente a los cálculos originales de Python para las dos alternativas existentes. Esto no verifica por sí solo la validez de los supuestos ni el cumplimiento de la rúbrica.
- La aplicación **no se ejecutó end-to-end** en el entorno de generación, ya que no hay un daemon Docker ni dependencias Composer disponibles aquí.
- No registrar nueva venta, nómina ni mantenimiento sobre el mismo período sin comprobar si el asiento de reconstrucción ya incluye ese importe.
- Ninguna cifra de ingreso es validada automáticamente por hacer que el balance cuadre. Consultar los documentos y conservar diferencias visibles.
