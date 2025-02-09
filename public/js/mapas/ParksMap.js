let map;
let autocomplete;
let userMarker;
let markers = []; // Para almacenar los marcadores en el mapa

document.addEventListener("DOMContentLoaded", () => {
    loadActivities();
});

function initMap() {
    toggleSpinner(true);

    // Inicializar el mapa con una ubicación genérica
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: -34.4560648, lng: -58.8683322 }, // Coordenadas iniciales
        zoom: 13,
    });

    // Configurar Autocomplete
    const searchInput = document.getElementById("park-search");
    autocomplete = new google.maps.places.Autocomplete(searchInput, {
        fields: ["geometry", "name"], // Obtener coordenadas y nombre del parque
        componentRestrictions: { country: "AR" }, // Restringir a Argentina
    });

    // Listener para "place_changed" en Autocomplete
    autocomplete.addListener("place_changed", handlePlaceChanged);

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const userPosition = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };

                map.setCenter(userPosition);

                userMarker = new google.maps.Marker({
                    position: userPosition,
                    map: map,
                    title: "Tu ubicación",
                    icon: {
                        url: "http://maps.google.com/mapfiles/ms/icons/orange-dot.png",
                    },
                });

                // Llamar a la función para cargar los parques cercanos
                fetchFilteredParks(userPosition.lat, userPosition.lng);
            },
            (error) => {
                console.error("Error al obtener la ubicación:", error.message);
                alert("No se pudo obtener tu ubicación.");
                toggleSpinner(false);
            }
        );
    } else {
        alert("La geolocalización no es soportada por tu navegador.");
        toggleSpinner(false);
    }
}

function handlePlaceChanged() {
    const place = autocomplete.getPlace();

    // Validar que el lugar tenga datos de ubicación
    if (!place.geometry || !place.geometry.location) {
        alert("Selecciona un lugar válido.");
        return;
    }

    const location = place.geometry.location;

    // Centrar el mapa en la ubicación seleccionada
    map.setCenter(location);
    map.setZoom(15);

    // Agregar marcador en la nueva ubicación
    if (userMarker) userMarker.setMap(null); // Eliminar marcador previo si existe
    userMarker = new google.maps.Marker({
        position: location,
        map: map,
    });

    // Consultar los parques filtrados según la nueva ubicación
    fetchFilteredParks(location.lat(), location.lng());
}

function loadActivities() {
    fetch("/api/activities")
        .then((response) => response.json())
        .then((activities) => {
            const filterSelect = document.getElementById("activityFilter");
            filterSelect.innerHTML = '<option value="">Todas las actividades</option>';
            activities.forEach((activity) => {
                const option = document.createElement("option");
                option.value = activity.name;
                option.textContent = activity.name;
                filterSelect.appendChild(option);
            });

            // Llamar a la función para cargar los parques con el filtro de actividad por defecto
            const defaultActivity = filterSelect.value;
            const userPosition = { lat: map.getCenter().lat(), lng: map.getCenter().lng() };
            fetchFilteredParks(userPosition.lat, userPosition.lng, defaultActivity);
        })
        .catch((error) => console.error("Error al cargar actividades:", error));
}

function fetchFilteredParks(lat, lng, activity = "") {
    const url = `/api/parks-nearby?lat=${lat}&lng=${lng}&radius=20000${activity ? `&activity=${activity}` : ""}`;

    fetch(url)
        .then((response) => {
            if (!response.ok) {
                console.error("Respuesta de la API no es correcta:", response);
                throw new Error("Error en la respuesta de la API");
            }
            return response.json();
        })
        .then((parks) => {
            console.log("Parques recibidos:", parks); // Imprime la respuesta para ver qué datos se reciben
            clearMarkers();

            // Verificar si la respuesta contiene parques y no está vacía
            if (!parks || parks.length === 0) {
                alert("No se encontraron parques cercanos.");
                return;
            }

            parks.forEach((park) => {
                const parkMarker = new google.maps.Marker({
                    position: { lat: parseFloat(park._geoloc.lat), lng: parseFloat(park._geoloc.lng) },
                    map: map,
                    title: park.name,
                });

                parkMarker.addListener("click", () => {
                    window.location.href = `/parks/${park.objectID}`;
                });

                markers.push(parkMarker);
            });
        })
        .catch((error) => {
            console.error("Error al cargar parques:", error);
            // Solo mostrar el error si realmente hubo un fallo en la respuesta
            alert("Ocurrió un error al cargar los parques. Intenta nuevamente.");
        })
        .finally(() => toggleSpinner(false));
}


function clearMarkers() {
    markers.forEach((marker) => marker.setMap(null));
    markers = [];
}

function toggleSpinner(show) {
    const spinner = document.getElementById("loading-spinner");
    spinner.classList.toggle("d-none", !show);
}

// Listener para actualizar el mapa cuando cambie el filtro de actividad
document.getElementById("activityFilter").addEventListener("change", (e) => {
    const activity = e.target.value;
    const userPosition = { lat: map.getCenter().lat(), lng: map.getCenter().lng() };
    fetchFilteredParks(userPosition.lat, userPosition.lng, activity);
});
