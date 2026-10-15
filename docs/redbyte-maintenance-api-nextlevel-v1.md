# API privada de mantenimiento — Next Level / Gestión Redes (REV0)

## Propósito

Permitir que el **recolector central Red-Byte**, y no Dot ni un cliente final,
obtenga estadísticas agregadas de la plataforma sin acceso SSH. Dot redacta
posteriormente el informe a partir de todas las fuentes consolidadas por Red-Byte.

## Endpoint y autenticación

`GET /api/internal/maintenance-evidence/v1?from=YYYY-MM-DD&to=YYYY-MM-DD`

- HTTPS obligatorio desde el recolector. Header `X-RedByte-Evidence-Token`.
- Deshabilitado por defecto y fail-closed: 404 sin flag, secreto mínimo 32 caracteres o token incorrecto.
- Restringido a consulta agregada; el endpoint no publica, modifica datos, envía mensajes ni crea archivos.
- Límite 6 peticiones/minuto (Laravel throttle).
- Permite ventanas explícitas de máximo 63 días, con fecha final no futura.
- Fechas interpretadas en `America/Argentina/Buenos_Aires`; consulta períodos según timestamps UTC.
- Respuesta JSON `redbyte.maintenance-evidence.v1`, `Cache-Control: no-store`.

Activación posterior a QA y aprobación, mediante configuración protegida en el servidor
**sin insertar tokens en Git, logs ni chats**:

```dotenv
REDBYTE_MAINTENANCE_API_ENABLED=false
REDBYTE_MAINTENANCE_API_TOKEN=<generar-secreto-aleatorio-minimo-32-caracteres>
```

## Campos (agregados solamente)

- `snapshot`: conectividad DB, operadores activos actualmente, jugadores registrados
  actualmente, operaciones registradas actualmente.
- `activity`: altas de operadores, altas de jugadores, operaciones registradas dentro del
  período solicitado. Las cifras se calculan por fecha de creación, no miden sesiones.
- `collected_at_utc` y `window`: procedencia temporal explícita.
- `external_sources`: UptimeRobot, incidencias y respaldos **pendientes de ser aportados
  por fuentes separadas**, no se simulan con el endpoint `/up`.
- `limitations`: advierte de la diferencia entre una foto actual y el histórico completo.

No se devuelven nombres, e-mails, teléfonos, transacciones individuales,
montos, saldo, logs brutos, respuestas HTTP privadas ni secretos.

No se crean JSON persistentes en Next Level; Red-Byte decide la retención de
sus evidencias y de los archivos temporales de informes.

## Próximos pasos

1. QA local con SQLite aislado y revisión de seguridad.
2. Aprobación de despliegue; no merge ni deploy por defecto.
3. Configuración del token en el cliente y en el recolector central de Red-Byte.
4. Prueba autenticada en un entorno controlado.
5. Red-Byte combina este JSON con UptimeRobot, tickets y otros controles
   disponibles y entrega evidencias a Dot para redactar.

Esta API no pretende reemplazar los informes anteriores ni `ReportDraftService`.
