let map;
let autocomplete;
let marker;

function initAutocomplete() {
    // Inicializar el mapa
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: -34.6037, lng: -58.3816 }, // Coordenadas iniciales: Buenos Aires
        zoom: 13,
    });

    // Inicializar Autocomplete
    const input = document.getElementById('park-search');
    autocomplete = new google.maps.places.Autocomplete(input, {
        types: ['park'], // Solo parques
        componentRestrictions: { country: 'AR' }, // Solo Argentina
        fields: ['geometry', 'name', 'formatted_address', 'opening_hours', 'place_id', 'types', 'photos'], // 🔹 Agregar 'photos'
    });

    // Listener para el evento "place_changed"
    autocomplete.addListener('place_changed', handlePlaceChanged);
}

function handlePlaceChanged() {
    const place = autocomplete.getPlace();
    

    if (!place.geometry || !place.geometry.location) {
        alert('No se pudo obtener información de ubicación para este lugar. Intenta seleccionar otro parque.');
        return;
    }

    if (!place.types || !place.types.includes('park')) {
        alert('Solo puedes seleccionar parques o plazas. Por favor, elige un parque válido.');
        document.getElementById('park-search').value = ''; 
        return;
    }

    const location = place.geometry.location;
    map.setCenter(location);
    map.setZoom(15);

    if (marker) marker.setMap(null);
    marker = new google.maps.Marker({
        position: location,
        map: map,
    });

    // Obtener hasta 4 fotos
    let photoReferences = [];
    if (place.photos && place.photos.length > 0) {
        for (let i = 0; i < Math.min(4, place.photos.length); i++) {
            let photoUrl = place.photos[i].getUrl({ maxWidth: 400 });
            if (photoUrl) {
                photoReferences.push(photoUrl);
            }
        }
    } else {
        console.log("⚠️ El parque seleccionado no tiene fotos en Google Places.");
    }

    document.getElementById('park_name').value = place.name || 'Nombre desconocido';
    document.getElementById('lat').value = location.lat();
    document.getElementById('lng').value = location.lng();
    document.getElementById('location').value = place.formatted_address || 'No disponible';
    document.getElementById('opening_hours').value = place.opening_hours
        ? JSON.stringify(place.opening_hours.weekday_text)
        : 'No disponible';

    // Convertir array de fotos a string para enviarlo al backend
    document.getElementById('photo_references').value = JSON.stringify(photoReferences);

    
}


// Asegurarse de que el script de Google Maps llama a esta función
window.initAutocomplete = initAutocomplete;
