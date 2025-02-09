const scheduleContainer = document.getElementById('schedule-container');
const addScheduleButton = document.getElementById('add-schedule');

document.getElementById('schedule-container').addEventListener('change', function () {
    const maxDays = getTotalSelectedDays();
    document.querySelectorAll('input[name="prices[weekly_sessions][]"]').forEach(input => {
        input.max = maxDays;
    });
});

// Añadir bloque de horario dinámico
addScheduleButton.addEventListener('click', function () {
    const scheduleBlock = document.createElement('div');
    scheduleBlock.classList.add('border', 'rounded', 'p-3', 'mb-3');
    scheduleBlock.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <label>Días:</label>
                <div>
                    <label class="form-check-label me-2">
                        <input type="checkbox" class="form-check-input" name="schedule[days][${scheduleContainer.children.length}][]" value="Lunes"> Lunes
                    </label>
                    <label class="form-check-label me-2">
                        <input type="checkbox" class="form-check-input" name="schedule[days][${scheduleContainer.children.length}][]" value="Martes"> Martes
                    </label>
                    <label class="form-check-label me-2">
                        <input type="checkbox" class="form-check-input" name="schedule[days][${scheduleContainer.children.length}][]" value="Miércoles"> Miércoles
                    </label>
                    <label class="form-check-label me-2">
                        <input type="checkbox" class="form-check-input" name="schedule[days][${scheduleContainer.children.length}][]" value="Jueves"> Jueves
                    </label>
                    <label class="form-check-label me-2">
                        <input type="checkbox" class="form-check-input" name="schedule[days][${scheduleContainer.children.length}][]" value="Viernes"> Viernes
                    </label>
                    <label class="form-check-label me-2">
                        <input type="checkbox" class="form-check-input" name="schedule[days][${scheduleContainer.children.length}][]" value="Sábado"> Sábado
                    </label>
                    <label class="form-check-label me-2">
                        <input type="checkbox" class="form-check-input" name="schedule[days][${scheduleContainer.children.length}][]" value="Domingo"> Domingo
                    </label>
                </div>
            </div>
            <div class="col-md-3">
                <label>Hora de Inicio:</label>
                <input type="time" class="form-control" name="schedule[start_time][${scheduleContainer.children.length}]" required>
            </div>
            <div class="col-md-3">
                <label>Hora de Fin:</label>
                <input type="time" class="form-control" name="schedule[end_time][${scheduleContainer.children.length}]" required>
            </div>
        </div>
        <div class="text-end">
            <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeSchedule(this)">Eliminar</button>
        </div>
    `;
    scheduleContainer.appendChild(scheduleBlock);
});

function removeSchedule(button) {
    button.closest('.border').remove();
}

// Contenedor de precios dinámicos
const pricesContainer = document.getElementById('prices');
const addPriceButton = document.getElementById('add-price-button');

// Añadir bloque de precios dinámico
document.getElementById('add-price-button').addEventListener('click', function () {
    const maxDays = getTotalSelectedDays();
    
    // Obtener todos los valores actuales de "veces por semana"
    const weeklySessionsInputs = document.querySelectorAll('input[name="prices[weekly_sessions][]"]');
    const weeklySessionsValues = Array.from(weeklySessionsInputs).map(input => input.value);

    // Verificar si hay valores repetidos
    const uniqueValues = new Set(weeklySessionsValues);
    if (weeklySessionsValues.length !== uniqueValues.size) {
        alert("No puedes agregar más de un precio con la misma cantidad de sesiones por semana.");
        return;
    }

    const priceBlock = document.createElement('div');
    priceBlock.classList.add('border', 'rounded', 'p-3', 'mb-3');
    priceBlock.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <label>Veces por Semana:</label>
                <input type="number" class="form-control" name="prices[weekly_sessions][]" min="1" max="${maxDays}" placeholder="Ej: 2" required>
            </div>
            <div class="col-md-6">
                <label>Precio:</label>
                <input type="number" class="form-control" name="prices[price][]" step="0.01" placeholder="Ej: 500.00" required>
            </div>
        </div>
        <div class="text-end">
            <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removePrice(this)">Eliminar</button>
        </div>
    `;
    document.getElementById('prices').appendChild(priceBlock);
});

// Validar antes de enviar el formulario
document.querySelector('form').addEventListener('submit', function (event) {
    const weeklySessionsInputs = document.querySelectorAll('input[name="prices[weekly_sessions][]"]');
    const weeklySessionsValues = Array.from(weeklySessionsInputs).map(input => input.value);
    
    // Verificar si hay valores repetidos
    const uniqueValues = new Set(weeklySessionsValues);
    if (weeklySessionsValues.length !== uniqueValues.size) {
        event.preventDefault(); // Evita que el formulario se envíe
        alert("No puedes agregar más de un precio con la misma cantidad de sesiones por semana.");
    }
});

function removePrice(button) {
    button.closest('.border').remove();
}

// Función para obtener la cantidad de días seleccionados
function getTotalSelectedDays() {
    const checkboxes = document.querySelectorAll('input[name^="schedule[days]"]:checked');
    return checkboxes.length;
}
