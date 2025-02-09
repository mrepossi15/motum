let map, marker;
let userLat = null;
let userLng = null;
let userLocation = null;
let markers = [];
let searchRadius = 5000; // 🔹 Radio inicial de 5km
let selectedActivity = ''; // 🔹 Variable para la actividad seleccionada

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: -34.6037, lng: -58.3816 },
        zoom: 13,
    });

    getUserLocation(); // 📍 Obtiene la ubicación del usuario al cargar la página

    const input = document.getElementById('address-input');
    const autocomplete = new google.maps.places.Autocomplete(input, {
        types: ['geocode'],
        componentRestrictions: { country: 'AR' },
    });

    autocomplete.addListener('place_changed', () => handleAddressSelection(autocomplete));

    document.getElementById('recenter-btn').addEventListener('click', () => {
        console.log("📍 Botón 'Recentrar' presionado");
        if (userLocation) {
            resetAutocomplete(); // 🔄 Borrar el input de autocompletado
            userLat = userLocation.lat; // 🔄 Reiniciar coordenadas a la ubicación real
            userLng = userLocation.lng;
            console.log("🌍 Coordenadas restauradas a la ubicación actual:", userLat, userLng);
            setMapLocation(userLat, userLng);
            fetchNearbyParks(userLat, userLng, searchRadius, selectedActivity);
        } else {
            getUserLocation(true);
        }
    });

    // ✅ Evento para cambiar el radio sin necesidad de presionar "Filtrar"
    document.getElementById('radius-select').addEventListener('change', function () {
        searchRadius = parseInt(this.value);
        console.log(`📏 Nuevo radio seleccionado: ${searchRadius / 1000} km`);
        if (userLat !== null && userLng !== null) {
            fetchNearbyParks(userLat, userLng, searchRadius, selectedActivity);
        }
    });

    // ✅ Evento para cambiar la actividad sin necesidad de presionar "Filtrar"
    document.getElementById('activity-select').addEventListener('change', function () {
        selectedActivity = this.value;
        console.log(`🏋️‍♂️ Nueva actividad seleccionada: ${selectedActivity}`);
        if (userLat !== null && userLng !== null) {
            fetchNearbyParks(userLat, userLng, searchRadius, selectedActivity);
        }
    });
}

function getUserLocation(forceUpdate = false) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                userLat = position.coords.latitude;
                userLng = position.coords.longitude;

                console.log("📍 Ubicación obtenida:", userLat, userLng);

                if (!forceUpdate && userLocation) return;

                userLocation = { lat: userLat, lng: userLng };
                setMapLocation(userLat, userLng);
                fetchNearbyParks(userLat, userLng, searchRadius, selectedActivity);
            },
            (error) => {
                console.warn("❌ No se pudo obtener la ubicación:", error);
                alert("No pudimos obtener tu ubicación. Ingresa una dirección manualmente.");
            },
            { enableHighAccuracy: true }
        );
    } else {
        alert("Tu navegador no soporta geolocalización.");
    }
}

function handleAddressSelection(autocomplete) {
    const place = autocomplete.getPlace();

    if (!place.geometry || !place.geometry.location) {
        alert("Dirección inválida. Por favor, selecciona una dirección de la lista.");
        return;
    }

    userLat = place.geometry.location.lat();
    userLng = place.geometry.location.lng();

    console.log("🏠 Dirección seleccionada:", userLat, userLng);

    setMapLocation(userLat, userLng);
    fetchNearbyParks(userLat, userLng, searchRadius, selectedActivity);
}

function setMapLocation(lat, lng) {
    const location = new google.maps.LatLng(lat, lng);
    map.setCenter(location);

    if (marker) marker.setMap(null);
    marker = new google.maps.Marker({
        position: location,
        map: map,
        title: "Ubicación seleccionada",
        icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png",
    });
}

// 🔄 Función para borrar el input de autocompletado cuando volvemos a la ubicación actual
function resetAutocomplete() {
    document.getElementById('address-input').value = '';
}

function fetchNearbyParks(lat, lng, radius, activityId = '') {
    let url = `/api/nearby-parks?lat=${lat}&lng=${lng}&radius=${radius}`;
    if (activityId) url += `&activity_id=${activityId}`;

    console.log(`🔍 URL enviada al backend: ${url}`);

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error("No se encontraron parques con esta actividad.");
            }
            return response.json();
        })
        .then(parks => {
            console.log("📞 Respuesta de la API:", parks);
            if (!Array.isArray(parks)) {
                console.error("❌ La respuesta no es un array:", parks);
                alert("No hay parques cerca para esa actividad.");
                return;
            }
            showParksOnMap(parks, lat, lng, radius);
        })
        .catch(error => {
            console.error("❌ Error al obtener parques:", error);
            alert(error.message);
        });
}

function showParksOnMap(parks, lat, lng, radius) {
    markers.forEach(m => m.setMap(null));
    markers = [];

    let bounds = new google.maps.LatLngBounds();
    bounds.extend(new google.maps.LatLng(lat, lng));

    parks.forEach(park => {
        let marker = new google.maps.Marker({
            position: { lat: parseFloat(park.latitude), lng: parseFloat(park.longitude) },
            map: map,
            title: park.name,
            icon: "http://maps.google.com/mapfiles/ms/icons/green-dot.png",
        });

        marker.addListener("click", () => {
            window.location.href = `/parks/${park.id}`;
        });

        markers.push(marker);
        bounds.extend(marker.position);
    });

    google.maps.event.addListenerOnce(map, 'bounds_changed', function() {
        let currentZoom = map.getZoom();
        let targetZoom = getZoomLevel(radius);

        if (currentZoom > targetZoom) {
            map.setZoom(targetZoom);
        }
    });

    map.fitBounds(bounds);
}

function getZoomLevel(radius) {
    if (radius <= 1000) return 16;
    if (radius <= 2000) return 15;
    if (radius <= 3000) return 14;
    if (radius <= 5000) return 13;
    if (radius <= 7000) return 12;
    return 11;
}

window.initMap = initMap;