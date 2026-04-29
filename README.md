# Incident Report Form + Storage of User Request in DB

Módulo personalizado para Drupal que renderiza un formulario de notificación de incidencias en el sitio y las almacena en una base de datos. Adicionalmente, provee una lista de solicitudes eenviadas por usuario.

## 📂 Estructura de Archivos

```text
└── incident_report_form/
    ├── src/
    │   ├── Controller/
    │   │   └── ListSubmissionsController.php
    │   ├── Form/
    │   │   └── IncidentReportForm.php
    │   └── Service/
    │       └── DataService.php
    ├── templates/
    │   └── submissions-list.html.twig
    ├── incident_report_form.info.yml
    ├── incident_report_form.install
    ├── incident_report_form.links.menu.yml
    ├── incident_report_form.module
    ├── incident_report_form.routing.yml
    └── incident_report_form.services.yml
```

<br>

## 💻 Setup e Instalación

Este proyecto ha sido desarrollado usando **DDEV**, pero es totalmente compatible con otros entornos locales (Lando, XAMPP, Docker puro).

### Requisitos del Sistema
- **PHP**: 8.1 o superior.
- **Drupal**: 9.4+ o 10.x.
- **Extensiones PHP**: `php-curl` (obligatoria para conectar con la API).

### Opción A: Con DDEV (Recomendado)
1. Asegúrate de tener el módulo en `web/modules/custom/pokeapi_integration`.
2. Activa el módulo y limpia caché:
   ```bash
   ddev drush en pokeapi_integration -y
   ddev drush cr
   ```

### Opción B: Sin DDEV (Entornos estándar)
Si utilizas un servidor local como XAMPP, Laragon o Apache:
1. Copia el módulo en la carpeta de módulos personalizados de tu instalación de Drupal.
2. Abre tu terminal en la raíz del proyecto.
3. Activa el módulo usando Drush global o la interfaz de Drupal (`/admin/modules`):
   ```bash
   drush en pokeapi_integration -y
   drush cr
   ```
4. **Importante**: Asegúrate de que tu PHP tenga habilitada la extensión `curl` y `openssl` para poder realizar peticiones externas por HTTPS.

<br>

## 🔐 Configuración de Accesos y Seguridad
El acceso al módulo está restringido por defecto para proteger la privacidad de los datos. Según la configuración en incident_report_form.routing.yml:
* **Restricción de Acceso**: Solo los usuarios autenticados (_user_is_logged_in: TRUE) pueden acceder tanto al formulario como al listado.
* **Rutas Protegidas**:
  - Notificar un problema: /form/incident-report-form/report-incident
  - Mis solicitudes: /incident_report/my-submissions
  > Nota: Si un usuario anónimo intenta acceder a estas URLs, Drupal lo redirigirá automáticamente a la página de inicio de sesión.

<br>

## 🛠️ Decisiones Técnicas
### 1. Arquitectura basada en Servicios
Se ha implementado un DataService (incident_report_form.subs_list) que encapsula toda la lógica de base de datos.

* Utiliza la API select de Drupal para mayor seguridad contra inyecciones SQL.
* Implementa un bloque try-catch que registra cualquier fallo en el log del sistema (Watchdog) mediante el servicio logger.

### 2. Inyección de Dependencias
El ListSubmissionsController no instancia clases directamente, sino que utiliza el contenedor de servicios de Drupal (ContainerInterface) para inyectar el DataService. Esto facilita el mantenimiento y la realización de pruebas unitarias.
### 3. Seguridad y Privacidad

* Identificación por UUID: En lugar de usar IDs numéricos incrementales, se utiliza el UUID del usuario.
* Filtrado en Frontend: El template Twig recibe el listado completo pero realiza una validación (sub.user == current_user) para asegurar que los usuarios solo visualicen sus propias incidencias enviadas.

### 4. Capa de Presentación (Twig)
El módulo define un hook de tema personalizado (hook_theme) para manejar la visualización:

* Template: submissions-list.html.twig.
* Dinamicidad: Muestra la fecha de envío formateada, la prioridad y la descripción del incidente.
* Caché: Se ha configurado '#cache' => ['max-age' => 0] en el controlador para garantizar que el listado se actualice inmediatamente después de un nuevo envío.


## ⚠️ Observaciones y Notas Importantes
### Gestión de Errores de Conexión
Si la base de datos no es accesible o la tabla no existe, el DataService capturará la excepción y devolverá un array vacío, evitando que el sitio web muestre un error fatal (White Screen of Death). El error será visible únicamente para los administradores en los informes de estado de Drupal.
### Personalización Visual
El template utiliza estilos en línea para asegurar una visualización básica consistente (flexbox para el título y la fecha), pero se recomienda extenderlo mediante el sistema de librerías CSS de Drupal si se desea un diseño más avanzado.

## 🎥 Demo de Previsualización
Para comprobar el flujo de envío del formulario y la actualización en tiempo real del listado de usuario, consulta el vídeo adjunto:


![Ver el vídeo aquí](https://github.com/bdiopdio/EJ2-Incident_Report_Form/raw/refs/heads/develop/demo.mp4)
