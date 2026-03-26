// public/js/app.js

document.addEventListener('DOMContentLoaded', () => {
    // ---- 1. INICIALIZACIÓN DE LEAFLET & MARKERCLUSTER ----
    // Coordenadas base (Ejemplo: Centro de Caracas, VE)
    const map = L.map('map').setView([10.4806, -66.9036], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> | GeoAuditor GIS',
        maxZoom: 19
    }).addTo(map);

    let markersCluster = L.markerClusterGroup({
        chunkedLoading: true,
        maxClusterRadius: 50,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false
    });
    map.addLayer(markersCluster);


    // ---- 2. CARGA DE REPORTES (GEOJSON DESDE LA API) ----
    const loadReports = async (category = 'todos') => {
        try {
            const response = await fetch(`get_reports.php?category=${encodeURIComponent(category)}`);
            if (!response.ok) throw new Error("Error en la conexión a la API");
            
            const geojsonData = await response.json();
            renderMarkers(geojsonData);
        } catch (error) {
            console.error("No se pudieron cargar los reportes:", error);
            // Puede no haber tabla o conexión si no se ejecuta Laragon
        }
    };

    const renderMarkers = (geojsonData) => {
        markersCluster.clearLayers(); // Limpiar el cluster para el redibujado (Filtros)
        
        const geoJsonLayer = L.geoJSON(geojsonData, {
            onEachFeature: (feature, layer) => {
                const props = feature.properties;
                const statusClass = `status-${props.current_state.replace(' ', '-')}`;
                const statusBadge = `<span class="status-badge ${statusClass}">${props.current_state}</span>`;
                
                let imgHtml = '';
                if (props.image_path) {
                    imgHtml = `<img src="${props.image_path}" class="popup-img" alt="Foto referencia de la falla">`;
                }
                
                const popupContent = `
                    <div style="min-width: 220px;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 8px;">
                            <h3 style="margin:0; font-size:1.05rem; line-height:1.2; padding-right:10px;">${props.title}</h3>
                            ${statusBadge}
                        </div>
                        <p style="font-size:0.85rem; margin:6px 0;"><strong>Servicio:</strong> <span style="text-transform:capitalize">${props.category}</span></p>
                        <p style="font-size:0.85rem; margin:6px 0; opacity:0.85;">${props.description || 'Sin descripción detallada.'}</p>
                        <p style="font-size:0.75rem; color:gray; font-weight:600; margin-top:8px;">Reportado el: ${new Date(props.created_at).toLocaleString('es-ES')}</p>
                        ${imgHtml}
                    </div>
                `;
                layer.bindPopup(popupContent);
            }
        });

        markersCluster.addLayer(geoJsonLayer);
    };

    // Carga inicial
    loadReports();


    // ---- 3. LOGICA DE FILTROS POR CATEGORÍA ----
    const chips = document.querySelectorAll('.chip');
    chips.forEach(chip => {
        chip.addEventListener('click', (e) => {
            // UI Update
            chips.forEach(c => c.classList.remove('active'));
            const target = e.target;
            target.classList.add('active');
            
            // Refetch data
            const category = target.getAttribute('data-category');
            loadReports(category);
        });
    });


    // ---- 4. GEOCODING CON NOMINATIM (Buscador de Direcciones) ----
    const searchInput = document.getElementById('searchInput');
    const btnSearch = document.getElementById('btnSearch');

    const doSearch = async () => {
        const query = searchInput.value.trim();
        if (!query) return;

        btnSearch.innerHTML = "⏳";
        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ', Venezuela')}`);
            const data = await res.json();
            
            if (data && data.length > 0) {
                const { lat, lon } = data[0];
                map.flyTo([lat, lon], 16, { duration: 1.5 });
            } else {
                alert("No se encontró la dirección. Intenta ser más específico.");
            }
        } catch (error) {
            console.error("Geocoding error:", error);
            alert("Error consultando el buscador de ubicaciones.");
        } finally {
            btnSearch.innerHTML = "🔎";
        }
    };

    btnSearch.addEventListener('click', doSearch);
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') doSearch();
    });


    // ---- 5. FORMULARIO MODAL Y CAPTURA DE DATOS (Geolocator API) ----
    const btnAddReport = document.getElementById('btnAddReport');
    const btnCloseModal = document.getElementById('btnCloseModal');
    const reportModal = document.getElementById('reportModal');
    const reportForm = document.getElementById('reportForm');
    
    // Inputs del form
    const inputLat = document.getElementById('lat');
    const inputLng = document.getElementById('lng');
    const btnGeolocate = document.getElementById('btnGeolocate');
    const btnSubmitForm = document.getElementById('btnSubmitForm');

    // Manejo de Modal
    btnAddReport.addEventListener('click', () => reportModal.classList.add('active'));
    btnCloseModal.addEventListener('click', () => {
        reportModal.classList.remove('active');
        reportForm.reset();
    });
    
    // Cerrar si se da click fuera del panel principal del modal
    reportModal.addEventListener('click', (e) => {
        if(e.target === reportModal) {
            reportModal.classList.remove('active');
            reportForm.reset();
        }
    });

    // Device Geolocation (GPS)
    btnGeolocate.addEventListener('click', () => {
        if ('geolocation' in navigator) {
            btnGeolocate.textContent = "⏳";
            btnGeolocate.disabled = true;
            
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    inputLat.value = lat.toFixed(6);
                    inputLng.value = lng.toFixed(6);
                    
                    btnGeolocate.textContent = "📍";
                    btnGeolocate.disabled = false;
                    
                    // Solo volar si el modal NO está tapando todo (o visualmente útil)
                    map.flyTo([lat, lng], 17);
                }, 
                (error) => {
                    console.warn("Location error:", error);
                    alert("No se pudo obtener la ubicación automáticamente. Por favor, haz clic directamente sobre el mapa.");
                    btnGeolocate.textContent = "📍";
                    btnGeolocate.disabled = false;
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        } else {
            alert("Tu explorador no soporta geolocalización.");
        }
    });

    // Captura manual de POI haciendo clic en el mapa de Leaflet
    let tempMarker = null;

    map.on('click', (e) => {
        const lat = e.latlng.lat.toFixed(6);
        const lng = e.latlng.lng.toFixed(6);
        
        inputLat.value = lat;
        inputLng.value = lng;
        
        // Poner un marcador temporal rojo para que el usuario visualice su click
        if(tempMarker) {
            map.removeLayer(tempMarker);
        }
        
        const customIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background-color:#e11d48; width:15px; height:15px; border-radius:50%; border:2px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.5);'></div>",
            iconSize: [15, 15],
            iconAnchor: [7.5, 7.5]
        });
        
        tempMarker = L.marker([lat, lng], {icon: customIcon}).addTo(map);

        // Si el panel no estaba abierto de antemano, lo abrimos automáticamente
        if (!reportModal.classList.contains('active')) {
             reportModal.classList.add('active');
        }
    });

    // ---- 6. ENVÍO DE FORMULARIO (POST MULTIPART) ----
    reportForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        if(!inputLat.value || !inputLng.value) {
            alert("Debes proveer la ubicación geográfica.Usa el botón 📍 o haz clic en el mapa.");
            return;
        }

        btnSubmitForm.disabled = true;
        btnSubmitForm.innerHTML = "⏳ Guardando en Servidor...";

        const formData = new FormData(reportForm);

        try {
            const response = await fetch('save_report.php', {
                method: 'POST',
                // Fetch enviará los headers correctos en content-type para multipart automáticamente
                body: formData
            });

            // Parse response
            let result;
            try {
                result = await response.json();
            } catch (err) {
                // If Laragon PHP has an error, it might spit out HTML block instead of JSON
                throw new Error("El servidor no respondió con un formato JSON válido.");
            }

            if (response.ok) {
                alert("¡Éxito! " + result.message);
                
                // Limpieza visual
                reportModal.classList.remove('active');
                reportForm.reset();
                if(tempMarker) map.removeLayer(tempMarker);
                
                // Actualizar inmediatamente mapa
                const activeCat = document.querySelector('.chip.active').getAttribute('data-category');
                loadReports(activeCat);
            } else {
                alert("Error Interno de API: " + (result.message || 'Desconocido'));
            }
        } catch (error) {
            console.error("Submission trigger error:", error);
            alert("Error de red: Imposible conectar con el entorno local de Laragon. Revisa que Apache y MySQL estén corriendo y config/database.php sea correcto.");
        } finally {
            btnSubmitForm.disabled = false;
            btnSubmitForm.innerHTML = "🚀 Guardar y Publicar Reporte";
        }
    });

});
