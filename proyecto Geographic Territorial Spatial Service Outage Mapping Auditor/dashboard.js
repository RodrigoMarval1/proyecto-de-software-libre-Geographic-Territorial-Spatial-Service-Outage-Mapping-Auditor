// public/js/dashboard.js

document.addEventListener('DOMContentLoaded', () => {
    // Referencias a las tablas del DOM
    const statusTable = document.querySelector('#statusTable tbody');
    const categoryTable = document.querySelector('#categoryTable tbody');
    const topSectorsTable = document.querySelector('#topSectorsTable tbody');
    const recentTable = document.querySelector('#recentTable tbody');

    // Función principal para cargar la data de la API analítica
    const loadDashboardData = async () => {
        try {
            const response = await fetch('get_dashboard_stats.php');
            if (!response.ok) throw new Error("Error en conexión de API para Estadísticas");
            
            const data = await response.json();

            // ---- 1. Llenar Tabla de Estados ----
            statusTable.innerHTML = '';
            if (data.by_status && data.by_status.length > 0) {
                data.by_status.forEach(item => {
                    const tr = document.createElement('tr');
                    const statusClass = `status-${item.current_state.replace(' ', '-')}`;
                    tr.innerHTML = `
                        <td><span class="status-badge ${statusClass}">${item.current_state}</span></td>
                        <td><strong style="font-size: 1.1rem">${item.total}</strong></td>
                    `;
                    statusTable.appendChild(tr);
                });
            } else {
                statusTable.innerHTML = '<tr><td colspan="2">No hay reportes registrados aún.</td></tr>';
            }

            // ---- 2. Llenar Tabla de Categorías ----
            categoryTable.innerHTML = '';
            if (data.by_category && data.by_category.length > 0) {
                data.by_category.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td style="text-transform: capitalize; font-weight: 500">${item.category}</td>
                        <td><strong style="font-size: 1.1rem">${item.total}</strong></td>
                    `;
                    categoryTable.appendChild(tr);
                });
            } else {
                 categoryTable.innerHTML = '<tr><td colspan="2">Sin datos de categoría.</td></tr>';
            }

            // ---- 3. Llenar Top Sectores ----
            topSectorsTable.innerHTML = '';
            if (data.top_sectors && data.top_sectors.length > 0) {
                data.top_sectors.forEach((item, index) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>
                            <div style="display:flex; align-items:center;">
                                <span style="display:inline-block; width: 28px; height: 28px; background:var(--primary); color:white; border-radius:50%; text-align:center; line-height:28px; margin-right:12px; font-weight: 700; font-size: 0.85rem;">${index + 1}</span>
                                <div>
                                    <span style="font-size:0.95rem; font-weight:500;">Micro-clúster EPSG:4326</span><br>
                                    <span style="font-family:monospace; font-size:0.85rem; color:var(--primary);">${item.sector_lat}, ${item.sector_lng}</span>
                                </div>
                            </div>
                        </td>
                        <td><strong style="font-size: 1.2rem; color: var(--danger)">${item.count} fallas</strong></td>
                    `;
                    topSectorsTable.appendChild(tr);
                });
            } else {
                 topSectorsTable.innerHTML = '<tr><td colspan="2">No hay concentración de reportes.</td></tr>';
            }

            // ---- 4. Llenar Tráfico Reciente ----
            recentTable.innerHTML = '';
            if (data.recent_reports && data.recent_reports.length > 0) {
                data.recent_reports.forEach(item => {
                    const tr = document.createElement('tr');
                    
                    const dateObj = new Date(item.created_at);
                    const formattedDate = dateObj.toLocaleDateString() + ' ' + dateObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    const statusClass = `status-${item.current_state.replace(' ', '-')}`;
                    
                    tr.innerHTML = `
                        <td style="font-weight: 600; color:var(--primary)">#${String(item.id).padStart(5, '0')}</td>
                        <td style="text-transform: capitalize;">${item.category}</td>
                        <td style="font-weight: 500">${item.title}</td>
                        <td style="font-size:0.85rem; opacity:0.8;">${formattedDate}</td>
                        <td><span class="status-badge ${statusClass}">${item.current_state}</span></td>
                    `;
                    recentTable.appendChild(tr);
                });
            } else {
                 recentTable.innerHTML = '<tr><td colspan="5" style="text-align:center">No hay reportes recientes en el sistema.</td></tr>';
            }

        } catch (error) {
            console.error("Error cargando dashboard:", error);
            statusTable.innerHTML = `<tr><td colspan="2" style="color:red; text-align:center;">Error de red: La API (PHP) no está disponible o la base de datos MySQL no responde.</td></tr>`;
        }
    };

    // 1. Carga visual inmediata
    loadDashboardData();

    // 2. Refresh Polling para monitoreo en vivo (Cada 30 Segundos)
    // Esto es muy útil para tableros de mando / Command Centers en alcaldías o gobernaciones.
    setInterval(loadDashboardData, 30000);
});
