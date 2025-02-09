document.addEventListener('DOMContentLoaded', function () {
    const trainingsList = document.getElementById('trainings-list');

    function renderTrainingsForDay(trainings) {
        trainingsList.innerHTML = '';

        if (trainings.length === 0) {
            trainingsList.innerHTML = '<p class="text-center text-secondary">No hay entrenamientos para este día.</p>';
            return;
        }

        trainings.forEach(training => {
            const trainingDiv = document.createElement('div');
            trainingDiv.classList.add('bg-light', 'p-3', 'rounded', 'shadow-sm', 'mb-3');
            trainingDiv.innerHTML = `
                <strong>${training.title || 'Sin título'}</strong><br>
                Actividad: ${training.activity?.name || 'Sin actividad'}<br>
                Nivel: ${training.level || 'Sin nivel definido'}<br>
                <h4>Precios:</h4>
                <ul>${training.prices?.map(price => `<li>${price.weekly_sessions} veces por semana: $${price.price}</li>`).join('') || 'No hay precios definidos.'}</ul>
            `;
            trainingsList.appendChild(trainingDiv);
        });
    }
});
