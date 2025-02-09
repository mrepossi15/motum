const scheduleContainer = document.getElementById('schedule-container');
const addScheduleButton = document.getElementById('add-schedule');

    addScheduleButton.addEventListener('click', function () {
        const index = scheduleContainer.children.length;
        const scheduleBlock = document.createElement('div');
        scheduleBlock.classList.add('border', 'rounded', 'p-3', 'mb-3');
        scheduleBlock.innerHTML = `
            <label>Días:</label>
            <div>
                <label><input type="checkbox" name="schedule[days][${index}][]" value="Lunes"> Lunes</label>
                <label><input type="checkbox" name="schedule[days][${index}][]" value="Martes"> Martes</label>
                <label><input type="checkbox" name="schedule[days][${index}][]" value="Miércoles"> Miércoles</label>
                <label><input type="checkbox" name="schedule[days][${index}][]" value="Jueves"> Jueves</label>
                <label><input type="checkbox" name="schedule[days][${index}][]" value="Viernes"> Viernes</label>
                <label><input type="checkbox" name="schedule[days][${index}][]" value="Sábado"> Sábado</label>
                <label><input type="checkbox" name="schedule[days][${index}][]" value="Domingo"> Domingo</label>
            </div>
            <label>Hora de Inicio:</label>
            <input type="time" class="form-control" name="schedule[start_time][${index}]" required>
            <label>Hora de Fin:</label>
            <input type="time" class="form-control" name="schedule[end_time][${index}]" required>
            <button type="button" class="btn btn-danger mt-2" onclick="removeSchedule(this)">Eliminar</button>
        `;
        scheduleContainer.appendChild(scheduleBlock);
    });

    const pricesContainer = document.getElementById('prices-container');
    const addPriceButton = document.getElementById('add-price-button');

    addPriceButton.addEventListener('click', function () {
        const priceBlock = document.createElement('div');
        priceBlock.classList.add('border', 'rounded', 'p-3', 'mb-3');
        priceBlock.innerHTML = `
            <label>Veces por Semana:</label>
            <input type="number" class="form-control" name="prices[weekly_sessions][]" min="1" required>
            <label>Precio:</label>
            <input type="number" class="form-control" name="prices[price][]" step="0.01" required>
            <button type="button" class="btn btn-danger mt-2" onclick="removePrice(this)">Eliminar</button>
        `;
        pricesContainer.appendChild(priceBlock);
    });

    function removeSchedule(button) {
        button.parentElement.remove();
    }

    function removePrice(button) {
        button.parentElement.remove();
    }
