# GeoAuditor GIS - Geographic Territorial Spatial Service Outage Mapping Auditor

Plataforma Web de Inteligencia Comunitaria y Auditoría Geoespacial para la gestión, registro y visualización en tiempo real de fallas en servicios públicos e incidentes territoriales.

![GIS Banner](https://img.shields.io/badge/GIS-Leaflet-0284c7?style=for-the-badge&logo=leaflet)
![PHP Version](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/Database-MySQL%20%2F%20SQLite-4479A1?style=for-the-badge&logo=mysql)
![License](https://img.shields.io/badge/Software-Libre-059669?style=for-the-badge)

---

## 🚀 Características Principales

- 🗺️ **Mapa Geoespacial Interactivo:** Basado en **Leaflet.js** y **OpenStreetMap** con soporte para agrupamiento de marcadores (`MarkerCluster`).
- 📍 **Captura de Coordenadas de Incidente:** Geolocalización vía GPS o selección directa mediante clic en el mapa.
- ⚡ **Clasificación por Tipo de Incidente:**
  - ⚡ **Eléctrico:** Apagones, postes dañados, fallas de transformadores.
  - 🛣️ **Asfaltado:** Baches, grietas en la vía, deterioro del pavimento.
  - 💥 **Accidente:** Siniestros viales y emergencias de tránsito.
  - 💧 **Agua:** Tuberías rotas, botes de aguas blancas o servidas, falta de suministro.
  - 🚧 **Vialidad:** Semáforos averiados, problemas de señalización.
  - 📌 **Otros:** Incidentes diversos comunitarios.
- 🔍 **Buscador Geoespacial:** Búsqueda rápida de direcciones y sectores con la API de Nominatim.
- 📊 **Dashboard Analítico:** Tablero estadístico con resumen de reportes por estado, densidad por sectores y tráfico reciente.
- 🛡️ **Backend Resiliente e Híbrido:** Soporte multi-puerto para MySQL (3307 / 3306), respaldo automático en SQLite y archivo JSON (`reportes_guardados.json`), garantizando tolerancia total a fallos.

---

## 🛠️ Requisitos del Sistema

- **Servidor Web:** Apache (vía Laragon, XAMPP o WampServer).
- **PHP:** Versión 7.4 o superior (Recomendado PHP 8.x).
- **Base de Datos:** MySQL / MariaDB o SQLite.
- **Navegador:** Cualquier navegador moderno (Chrome, Edge, Firefox, Safari).

---

## 💻 Instalación y Configuración Local

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/RodrigoMarval1/proyecto-de-software-libre-Geographic-Territorial-Spatial-Service-Outage-Mapping-Auditor.git
   cd proyecto-de-software-libre-Geographic-Territorial-Spatial-Service-Outage-Mapping-Auditor
   ```

2. **Copiar la carpeta del proyecto a la raíz de Laragon/XAMPP:**
   Copiar la carpeta `proyecto Geographic Territorial Spatial Service Outage Mapping Auditor` dentro del directorio servidor:
   - En Laragon: `C:\laragon\www\geoauditor`
   - En XAMPP: `C:\xampp\htdocs\geoauditor`

3. **Base de Datos (Opcional):**
   - Importa el archivo `outage_mapping2.sql` en phpMyAdmin o MySQL Workbench.
   - *Nota:* La aplicación crea e integra automáticamente la base de datos `outage_mapping` y la tabla `reports` al conectarse por primera vez.

4. **Acceder a la Aplicación:**
   Abre tu navegador e ingresa a:
   ```text
   http://localhost/geoauditor/index.html
   ```

---

## 📁 Estructura del Proyecto

```text
├── outage_mapping2.sql           # Esquema de base de datos MySQL actualizado
├── README.md                     # Documentación general del proyecto
└── proyecto Geographic Territorial Spatial Service Outage Mapping Auditor/
    ├── index.html                # Interfaz principal con mapa interactivo y filtros
    ├── style.css                 # Estilos Glassmorphism y diseño responsive
    ├── app.js                    # Lógica del mapa Leaflet, marcadores y eventos
    ├── dashboard.html            # Tablero analítico y métricas de incidentes
    ├── dashboard.js              # Carga de estadísticas en vivo para el dashboard
    ├── database.php              # Módulo de conexión y tolerancia a fallos
    ├── save_report.php           # API para guardar nuevos reportes e imágenes
    ├── get_reports.php           # API GeoJSON para consultar y filtrar incidentes
    └── get_dashboard_stats.php   # API de estadísticas analíticas
```

---

## 👥 Contribuciones y Pull Requests

1. Crea tu rama de características: `git checkout -b feature/mi-nueva-funcionalidad`
2. Realiza tus cambios y confirma los commits: `git commit -m "feat: descripción del cambio"`
3. Envía tus cambios: `git push origin feature/mi-nueva-funcionalidad`
4. Abre un **Pull Request** en GitHub para revisión.

---

## 📄 Licencia

Proyecto desarrollado bajo la filosofía de **Software Libre** para apoyo a la gestión pública y comunitaria.