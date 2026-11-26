<?php
/** Esquema del CRUD: exclusivamente estos módulos/campos son editables desde el navegador. */
return [
 'niveles'=>['confirmado'=>'Confirmado con evidencia','estimado'=>'Estimado desde entrevista','supuesto'=>'Supuesto de trabajo','pendiente'=>'Pendiente de verificar'],
 'modulos'=>[
  'servicios'=>['titulo'=>'Servicios y costeo','descripcion'=>'Precios, volumen y factores del lavado; MD/MOD/CIF se calculan desde el libro diario.',
   'campos'=>['nombre'=>['Nombre','text'],'precio'=>['Precio unitario ($)','number'],'lavados_mes'=>['Cantidad del periodo','number'],'peso'=>['Peso de equivalencia','number'],'es_adicional'=>['Es adicional','boolean'],'nota'=>['Justificación / nota','textarea'],'nivel'=>['Nivel de evidencia','nivel']]],
  'productos'=>['titulo'=>'Productos e inventario','descripcion'=>'Inventario referencial. La existencia no se descuenta automáticamente por registrar ventas.',
   'campos'=>['nombre'=>['Nombre','text'],'categoria'=>['Categoría','text'],'unidad'=>['Unidad','text'],'costo'=>['Costo unitario ($)','number'],'frecuencia_dias'=>['Frecuencia de compra (días)','number'],'precio_venta'=>['Precio venta ($)','number'],'comprado_mes'=>['Comprado en el período','number'],'vendido_mes'=>['Vendido en el período','number'],'nota'=>['Observaciones','textarea'],'nivel'=>['Evidencia','nivel']]],
  'activos'=>['titulo'=>'Activos y depreciación','descripcion'=>'Al guardar un activo se regenera el ajuste mensual de depreciación y el costeo.',
   'campos'=>['nombre'=>['Nombre','text'],'fecha_adq'=>['Fecha de adquisición (si se conoce)','date'],'costo'=>['Costo histórico ($)','number'],'vida_util'=>['Vida útil (años)','integer'],'valor_salvamento'=>['Salvamento ($)','number'],'valor_mercado'=>['Valor de mercado actual ($)','number'],'vida_restante'=>['Vida útil restante (años)','integer'],'cao_anual'=>['CAO anual — Costo Anual de Operación ($)','number'],'dep_acum_previa'=>['Depreciación previa ($)','number'],'nota'=>['Notas de origen','textarea'],'nivel'=>['Evidencia del costo','nivel'],'nivel_vida'=>['Evidencia de vida útil','nivel']]],
  'alternativas'=>['titulo'=>'Alternativas de inversión','descripcion'=>'Cifras aún provisionales. Compare sobre una misma base de beneficios y costos.',
   'campos'=>['nombre'=>['Alternativa','text'],'descripcion'=>['Descripción','textarea'],'costo_inicial'=>['Inversión inicial ($)','number'],'vida_util'=>['Vida útil (años)','integer'],'valor_salvamento'=>['Salvamento ($)','number'],'lavados_mes'=>['Lavados adicionales por mes','number'],'servicio_id'=>['Servicio base','servicio'],'energia_anual'=>['Energía adicional anual ($)','number'],'mantenimiento_anual'=>['Mantenimiento anual ($)','number'],'ahorro_anual'=>['Ahorro anual ($)','number'],'dep_mercado'=>['Caída anual del valor de mercado (0.15 = 15%)','number'],'marca_modelo'=>['Marca/modelo','text'],'voltaje'=>['Voltaje','text'],'presion_psi'=>['Presión (PSI)','text'],'garantia'=>['Garantía','text'],'notas'=>['Notas / cotizaciones pendientes','textarea'],'nivel'=>['Evidencia','nivel']]],
  'ventas'=>['titulo'=>'Ventas individuales','descripcion'=>'No están contabilizadas hasta ejecutar la acción Contabilizar pendientes.',
   'campos'=>['fecha'=>['Fecha','date'],'servicio_id'=>['Servicio','servicio'],'cantidad'=>['Cantidad','number'],'precio_unit'=>['Precio unitario ($)','number'],'domicilio'=>['A domicilio','boolean'],'nota'=>['Nota','textarea'],'nivel'=>['Evidencia','nivel']]],
  'pagos_personal'=>['titulo'=>'Personal y pagos','descripcion'=>'Registro operativo: al guardar no se crea automáticamente un asiento. Registre el pago en Libro diario para evitar duplicaciones.',
   'campos'=>['fecha'=>['Fecha','date'],'trabajador'=>['Trabajador','text'],'dias'=>['Días trabajados','number'],'tarifa'=>['Tarifa diaria ($)','number'],'extra'=>['Bonificación ($)','number'],'nota'=>['Observación','textarea'],'nivel'=>['Evidencia','nivel']]],
  'pendientes'=>['titulo'=>'Cuentas por cobrar/pagar','descripcion'=>'Registro operativo. Los saldos son informativos; contabilice manualmente para mantener la partida doble.',
   'campos'=>['tipo'=>['Tipo','tipo_pendiente'],'tercero'=>['Cliente o proveedor','text'],'fecha'=>['Fecha','date'],'concepto'=>['Concepto','textarea'],'monto'=>['Monto ($)','number'],'abonado'=>['Total abonado ($)','number'],'cuenta_contra'=>['Cuenta contrapartida','cuenta'],'nivel'=>['Evidencia','nivel']]],
  'mantenimientos'=>['titulo'=>'Mantenimiento de equipos','descripcion'=>'Registro operativo; para los costos registre el asiento correspondiente en el Libro diario.',
   'campos'=>['fecha'=>['Fecha','date'],'activo_id'=>['Equipo','activo'],'tipo'=>['Tipo','text'],'costo'=>['Costo ($)','number'],'descripcion'=>['Descripción','textarea'],'nivel'=>['Evidencia','nivel']]],
 ],
 'campos_traza'=>['alternativas'=>['costo_inicial','vida_util','valor_salvamento','lavados_mes','energia_anual','mantenimiento_anual','ahorro_anual'],
                   'servicios'=>['precio']],
];
