document.getElementById('add-training-button').addEventListener('click', function () {
    const selectedParkId = document.getElementById('park-select').value;
    const url = `/trainings/create?park_id=${selectedParkId}`;
    window.location.href = url; // Redirige al formulario con el parque seleccionado
});
document.addEventListener('DOMContentLoaded', function () {
        const parkSelect = document.getElementById('park-select');
        const selectedParkId = parkSelect.value;

        loadTrainings(selectedParkId); // Cargar entrenamientos del parque seleccionado

        // Cambiar parque seleccionado y recargar entrenamientos
        parkSelect.addEventListener('change', function () {
            loadTrainings(this.value);
        });
    });
    function loadTrainings(parkId) {
        fetch(`/api/trainings?park_id=${parkId}`)
            .then(response => response.json())
            .then(data => {
                const trainingsList = document.getElementById('trainings-list');
                trainingsList.innerHTML = ''; // Limpiar lista existente
    
                if (data.length === 0) {
                    trainingsList.innerHTML = '<li>No hay entrenamientos para este parque.</li>';
                } else {
                    data.forEach(training => {
                        const title = training.title || 'Sin título';
                        const activity = training.activity ? training.activity.name : 'Sin actividad';
                        const level = training.level || 'Sin nivel definido';
    
                        // Procesar precios
                        let pricesDetails = '';
                        if (training.prices && training.prices.length > 0) {
                            training.prices.forEach(price => {
                                pricesDetails += `<li>${price.weekly_sessions} veces por semana: $${parseFloat(price.price).toFixed(2)}</li>`;
                            });
                        } else {
                            pricesDetails = '<li>No hay precios definidos.</li>';
                        }
    
                        // Agregar entrenamiento a la lista
                        trainingsList.innerHTML += `
                            <li>
                                <strong>${title}</strong><br>
                                Actividad: ${activity}<br>
                                Nivel: ${level}<br>
                                <h4>Precios:</h4>
                                <ul>${pricesDetails}</ul>
                            </li>
                        `;
                    });
                }
            })
            .catch(error => {
                console.error('Error al cargar entrenamientos:', error); // Mostrar errores en la consola
            });
    }
    
    
    
    