document.addEventListener('DOMContentLoaded', function () {
    const parkDropdownMenu = document.getElementById('parkDropdownMenu');
    const parkDropdown = document.getElementById('parkDropdown');
    const addTrainingButton = document.getElementById('add-training-button');
    const calendarContainer = document.getElementById('calendar-container');
    const trainingsList = document.getElementById('trainings-list');
    const weekRange = document.getElementById('week-range');
    const monthTitle = document.getElementById('month-title');

    let selectedParkId = 'all'; // Inicialmente "Todos"
    let currentWeekStart = new Date(); // Fecha actual

    // Ajustar al lunes incluso si hoy es domingo
    currentWeekStart.setDate(currentWeekStart.getDate() - ((currentWeekStart.getDay() + 6) % 7));

    // Cargar la semana inicial al iniciar el calendario
    loadWeek(currentWeekStart, selectedParkId);

    // Manejo de selección en el dropdown
    parkDropdownMenu.addEventListener('click', function (event) {
        if (event.target.tagName === 'A') {
            const selectedValue = event.target.dataset.value;
            const selectedText = event.target.textContent;

            // Actualizar el texto del dropdown
            parkDropdown.textContent = selectedText;
            selectedParkId = selectedValue;

            if (selectedValue === 'add') {
                window.location.href = "{{ route('trainer.add.park') }}";
            } else {
                loadWeek(currentWeekStart, selectedParkId);
            }
        }
    });

    // Redirigir al formulario de agregar entrenamiento
    addTrainingButton.addEventListener('click', function () {
        const url = `/trainings/create?park_id=${selectedParkId}`;
        window.location.href = url;
    });

    // Navegación de semanas
    document.getElementById('prev-week').addEventListener('click', function () {
        currentWeekStart.setDate(currentWeekStart.getDate() - 7);
        loadWeek(currentWeekStart, selectedParkId);
    });

    document.getElementById('next-week').addEventListener('click', function () {
        currentWeekStart.setDate(currentWeekStart.getDate() + 7);
        loadWeek(currentWeekStart, selectedParkId);
    });

    // 🔹 **Función para cargar la semana y obtener entrenamientos**
    function loadWeek(startDate, parkId) {
        const startOfWeek = new Date(startDate);
        startOfWeek.setDate(startOfWeek.getDate() - startOfWeek.getDay() + 1); // Asegurar que es lunes

        const weekStartDate = startOfWeek.toISOString().split('T')[0]; // 📅 Formato YYYY-MM-DD

        const params = new URLSearchParams();
        params.append('week_start_date', weekStartDate);
        if (parkId !== 'all') {
            params.append('park_id', parkId);
        }

        fetch(`/api/trainings?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                console.log("📥 Entrenamientos recibidos:", data);
                renderCalendar(data, startOfWeek);
            })
            .catch(error => console.error('❌ Error al cargar entrenamientos:', error));
    }

    // 🔹 **Renderizar el calendario con entrenamientos**
    function renderCalendar(trainings, startOfWeek) {
        calendarContainer.innerHTML = ''; // Limpiar el calendario

        for (let i = 0; i < 7; i++) {
            const currentDate = new Date(startOfWeek);
            currentDate.setDate(currentDate.getDate() + i);
            const formattedDate = currentDate.toISOString().split('T')[0]; // 📅 YYYY-MM-DD
            const dayName = getDayName(i); // Obtener nombre del día

            // Filtrar entrenamientos para el día actual
            const dayTrainings = trainings.filter(training => training.date === formattedDate);

            // 🔹 **Crear columna del día**
            const dayColumn = document.createElement('div');
            dayColumn.classList.add('col', 'p-3', 'text-center', 'day-column');
            dayColumn.innerHTML = `<div class="fw-bold">${dayName} ${currentDate.getDate()}</div>`;

            if (dayTrainings.length > 0) {
                dayColumn.classList.add('border-naranja');
            } else {
                dayColumn.classList.add('border');
            }

            // 🔹 **Agregar entrenamientos al día**
            dayTrainings.forEach(training => {
                const trainingDiv = document.createElement('div');
                trainingDiv.classList.add('bg-light', 'p-2', 'rounded', 'mt-2');

                trainingDiv.innerHTML = `
                    <strong>${training.start_time} - ${training.end_time}</strong>
                    <p>Entrenamiento ID: ${training.training_id}</p>
                `;

                trainingDiv.addEventListener('click', function () {
                    window.location.href = `/trainings/${training.training_id}?date=${training.date}&time=${training.start_time}`;
                });

                dayColumn.appendChild(trainingDiv);
            });

            calendarContainer.appendChild(dayColumn);
        }
    }

    // 🔹 **Función para obtener el nombre del día**
    function getDayName(index) {
        const dayNames = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        return dayNames[index];
    }

    // 🔹 **Suspender entrenamiento**
    function suspendTraining(trainingId, trainingDate) {
        console.log("📅 Suspendiendo clase:", { trainingId, trainingDate });

        fetch(`/api/trainings/suspend`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ training_id: trainingId, date: trainingDate })
        })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(`Error: ${data.error}`);
                } else {
                    alert(`✅ Clase suspendida correctamente para la fecha: ${data.date}`);
                    loadWeek(currentWeekStart, selectedParkId); // 🔄 Refrescar el calendario
                }
            })
            .catch(error => console.error('Error:', error));
    }
});