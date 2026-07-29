GeoAuditor GIS - Geographic Territorial Spatial Service Outage Mapping Auditor

Plataforma Web de Inteligencia Comunitaria y Auditoría Geoespacial para la gestión, registro y visualización en tiempo real de fallas en servicios públicos e incidentes territoriales.

Características Principales

- 🗺️ Mapa Geoespacial Interactivo: Basado en Leaflet.js y OpenStreetMap con soporte para agrupamiento de marcadores (`MarkerCluster`).
- 📍 Captura de Coordenadas de Incidente: Geolocalización vía GPS o selección directa mediante clic en el mapa.
- ⚡ Clasificación por Tipo de Incidente:
  - ⚡ Eléctrico:** Apagones, postes dañados, fallas de transformadores.
  - 🛣️ Asfaltado:** Baches, grietas en la vía, deterioro del pavimento.
  - 💥 Accidente:** Siniestros viales y emergencias de tránsito.
  - 💧 Agua:** Tuberías rotas, botes de aguas blancas o servidas, falta de suministro.
  - 🚧 Vialidad:** Semáforos averiados, problemas de señalización.
  - 📌 Otros:** Incidentes diversos comunitarios.
- 🔍 Buscador Geoespacial:** Búsqueda rápida de direcciones y sectores con la API de Nominatim.
- 📊 Dashboard Analítico:** Tablero estadístico con resumen de reportes por estado, densidad por sectores y tráfico reciente.
- 🛡️ Backend Resiliente e Híbrido:** Soporte multi-puerto para MySQL (3307 / 3306), respaldo automático en SQLite y archivo JSON (`reportes_guardados.json`), garantizando tolerancia total a fallos.
