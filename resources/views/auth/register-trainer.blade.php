@extends('layouts.main')

@section('title', 'Registro de Entrenador')

@section('content')
<div class="container mt-5 d-flex justify-content-center align-items-center">
    <div class="card shadow-sm p-4" style="width: 100%; max-width: 600px;">
        <h1 class="text-center mb-4 text-naranja">Registro de Entrenador</h1>

        <form action="{{ route('store.trainer') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="role" value="entrenador">
            <!-- Nombre -->
            <div class="mb-3">
                <label for="name" class="form-label">Nombre completo *</label>
                <input type="text" 
                       class="form-control @error('name') is-invalid @enderror"  
                       id="name" 
                       name="name" 
                       placeholder="Tu nombre completo" 
                       value="{{ old('name') }}"  
                       >
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Correo electrónico -->
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico *</label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror"  
                       id="email" 
                       name="email" 
                       placeholder="ejemplo@correo.com" 
                       value="{{ old('email') }}"  
                        >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="mercado_pago_email" class="form-label">mercado_pago_email *</label>
                <input type="mercado_pago_email" 
                       class="form-control @error('mercado_pago_email') is-invalid @enderror"  
                       id="mercado_pago_email" 
                       name="mercado_pago_email" 
                       placeholder="ejemplo@correo.com" 
                       value="{{ old('mercado_pago_email') }}"  
                        >
                @error('mercado_pago_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Contraseña -->
            <div class="mb-3 position-relative">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       id="password" 
                       name="password" 
                       placeholder="Crea una contraseña" 
                        >
                <button type="button" 
                        class="btn btn-link position-absolute end-0 top-0 mt-3 me-2 toggle-password" 
                        data-target="password" 
                        aria-label="Mostrar/Ocultar contraseña">
                    <i class="bi bi-eye-slash" id="toggle-password-icon"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirmar Contraseña -->
            <div class="mb-3 position-relative">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                <input type="password" 
                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       placeholder="Repite tu contraseña" 
                        >
                <button type="button" 
                        class="btn btn-link position-absolute end-0 top-0 mt-3 me-2 toggle-password" 
                        data-target="password_confirmation" 
                        aria-label="Mostrar/Ocultar contraseña">
                    <i class="bi bi-eye-slash" id="toggle-password-confirmation-icon"></i>
                </button>
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Certificación -->
            <div class="mb-3">
                <label for="certification" class="form-label">Certificación Profesional *</label>
                <input type="text" 
                       class="form-control @error('certification') is-invalid @enderror" 
                       id="certification" 
                       name="certification" 
                       placeholder="Ej: Entrenador Personal Certificado" 
                       value="{{ old('certification') }}" 
                        >
                @error('certification')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Foto de Certificación -->
            <div class="mb-3">
                <label for="certification_pic" class="form-label">Foto de la certificación (Opcional)</label>
                <input type="file" 
                       class="form-control @error('certification_pic') is-invalid @enderror"  
                       id="certification_pic" 
                       name="certification_pic" 
                       accept="image/*">
                @error('certification_pic')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Biografía -->
            <div class="mb-3">
                <label for="biography" class="form-label">Breve biografía (Opcional)</label>
                <textarea class="form-control @error('biography') is-invalid @enderror" 
                          id="biography" 
                          name="biography" 
                          placeholder="Escribe una breve biografía (máximo 500 caracteres)" 
                          maxlength="500">{{ old('biography') }}</textarea>
                @error('biography')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="collector_id" class="form-label">Collector_id (Opcional)</label>
                <input type="text" name="collector_id" id="collector_id" class="form-control @error('collector_id') is-invalid @enderror"
                value="{{ old('collector_id') }}"  
                       >
                <small class="form-text text-muted">Ingresa tu Collector ID de Mercado Pago.</small>
                @error('collector_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Especialidad -->
            <div class="mb-3">
                <label for="especialty" class="form-label">Áreas de especialidad (Opcional)</label>
                <input type="text" 
                       class="form-control @error('especialty') is-invalid @enderror" 
                       id="especialty" 
                       name="especialty" 
                       placeholder="Ej: Funcional, CrossFit, Yoga, HIIT, etc." 
                       value="{{ old('especialty') }}">
                @error('especialty')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Fecha de Nacimiento -->
            <div class="mb-3">
                <label for="birth" class="form-label">Fecha de Nacimiento *</label>
                <input type="date" 
                       id="birth" 
                       name="birth" 
                       class="form-control @error('birth') is-invalid @enderror" 
                       value="{{ old('birth') }}" 
                       max="{{ now()->subYears(18)->format('Y-m-d') }}" 
                        >
                @error('birth')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Debes tener al menos 18 años para registrarte.</small>
            </div>

            <!-- Foto de Perfil -->
            <div class="mb-3">
                <label for="profile_pic" class="form-label">Foto de perfil</label>
                <input type="file" 
                       class="form-control @error('profile_pic') is-invalid @enderror"  
                       id="profile_pic" 
                       name="profile_pic" 
                       accept="image/*">
                @error('profile_pic')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Apto medico -->
            <div class="mb-3">
                <label for="medical_fit" class="form-label">>Apto médico</label>
                <input type="file" 
                       class="form-control @error('medical_fit') is-invalid @enderror"  
                       id="medical_fit" 
                       name="medical_fit" 
                       accept="image/*">
                @error('medical_fit')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="park-search" class="form-label">Parque de preferencia *</label>
                <input 
                    id="park-search" 
                    class="form-control @error('park_name') is-invalid @enderror" 
                    type="text" 
                    name="park_name" 
                    placeholder="Escribe el nombre del parque" 
                    value="{{ old('park_name') }}" 
                     >
                @error('park_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Mapa -->
            <div id="map" class="mb-3" style="height: 300px; border-radius: 8px; border: 1px solid #ccc;"></div>

            <!-- Campos ocultos -->
            <input type="hidden" id="park_name" name="park_name">
            <input type="hidden" id="lat" name="latitude">
            <input type="hidden" id="lng" name="longitude">
            <input type="hidden" id="location" name="location">
            <input type="hidden" id="opening_hours" name="opening_hours">
            <input type="hidden" name="role" value="entrenador">
            <input type="hidden" id="photo_references" name="photo_references">
            <hr>
<!-- Experiencia Laboral -->
<h5>Años de experiencia (Opcional)</h5>
<div id="experience-container">
    <div class="experience-item border rounded p-3 mb-3">
    <h6>Experiencia #1</h6>
        <label for="role-0" class="form-label">Rol:</label>
        <input 
            type="text" 
            name="experiences[0][role]" 
            class="form-control @error('experiences.0.role') is-invalid @enderror" 
            placeholder="Ej: Entrenador personal" 
            value="{{ old('experiences.0.role') }}">
        @error('experiences.0.role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <label for="company-0" class="form-label">Empresa o Gimnasio:</label>
        <input 
            type="text" 
            name="experiences[0][company]" 
            class="form-control @error('experiences.0.company') is-invalid @enderror" 
            placeholder="Ej: Gimnasio XYZ" 
            value="{{ old('experiences.0.company') }}">
        @error('experiences.0.company')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <label for="year-start-0" class="form-label">Año de Inicio:</label>
        <input 
            type="number" 
            name="experiences[0][year_start]" 
            id="year-start-0" 
            class="form-control @error('experiences.0.year_start') is-invalid @enderror" 
            placeholder="Ej: 2020" 
            value="{{ old('experiences.0.year_start') }}" 
            min="1900" 
            max="{{ now()->year }}" 
            list="year-start-options-0">
        <datalist id="year-start-options-0">
            @for ($year = 1900; $year <= now()->year; $year++)
                <option value="{{ $year }}">{{ $year }}</option>
            @endfor
        </datalist>
        @error('experiences.0.year_start')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <label for="year-end-0" class="form-label">Año de Fin:</label>
        <input 
            type="number" 
            name="experiences[0][year_end]" 
            id="year-end-0" 
            class="form-control @error('experiences.0.year_end') is-invalid @enderror year-end-input" 
            placeholder="Ej: {{ now()->year }}" 
            value="{{ old('experiences.0.year_end') }}" 
            min="1900" 
            max="{{ now()->year }}" 
            list="year-end-options-0">
        <datalist id="year-end-options-0">
            @for ($year = 1900; $year <= now()->year; $year++)
                <option value="{{ $year }}">{{ $year }}</option>
            @endfor
        </datalist>
        @error('experiences.0.year_end')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="form-check mt-2">
            <!-- Campo oculto para enviar 0 cuando el checkbox no esté marcado -->
            <input 
                type="hidden" 
                name="experiences[0][currently_working]" 
                value="0">
            <input 
                type="checkbox" 
                name="experiences[0][currently_working]" 
                id="currently-working-0" 
                class="form-check-input currently-working-checkbox @error('experiences.0.currently_working') is-invalid @enderror" 
                data-year-end-field="year-end-0" 
                value="1" 
                {{ old('experiences.0.currently_working') ? 'checked' : '' }}>
            <label for="currently-working-0" class="form-check-label">Actualmente trabajando aquí</label>
            @error('experiences.0.currently_working')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<button type="button" id="add-experience" class="btn btn-primary btn-sm">Agregar Otra Experiencia</button>
            

            <div class="text-end mt-3">
                <a href="{{ route('login') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn text-white bg-naranja">Registrar como Entrenador</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <p>¿Eres alumno? <a href="{{ route('register.student') }}" class="text-naranja">Regístrate aquí</a></p>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="/js/mapas/showMap.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.places_api_key') }}&libraries=places&callback=initAutocomplete" async defer></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");

    form.addEventListener("submit", function (event) {
        let photoReference = document.getElementById("photo_reference").value;
        if (!photoReference) {
            event.preventDefault(); // Detener el envío del formulario si `photo_reference` está vacío
            alert("Error: No se ha obtenido la foto del parque. Selecciona un parque válido.");
            return;
        }
    });
});
</script>
<script>
document.getElementById('add-experience').addEventListener('click', () => {
    const container = document.getElementById('experience-container');
    const index = container.children.length;

    const template = document.createElement('div');
    template.classList.add('experience-item', 'border', 'rounded', 'p-3', 'mb-3');
    template.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6>Experiencia #${index + 1}</h6>
            <button type="button" class="btn btn-danger btn-sm remove-experience" data-index="${index}">Eliminar</button>
        </div>
        <label for="role-${index}" class="form-label">Rol:</label>
        <input type="text" name="experiences[${index}][role]" class="form-control" placeholder="Ej: Entrenador personal">

        <label for="company-${index}" class="form-label">Empresa o Gimnasio:</label>
        <input type="text" name="experiences[${index}][company]" class="form-control" placeholder="Ej: Gimnasio XYZ">

        <label for="year-start-${index}" class="form-label">Año de Inicio:</label>
        <input type="number" name="experiences[${index}][year_start]" class="form-control" placeholder="Ej: 2020" min="1900" max="${new Date().getFullYear()}">

        <label for="year-end-${index}" class="form-label">Año de Fin:</label>
        <input type="number" name="experiences[${index}][year_end]" class="form-control" placeholder="Ej: ${new Date().getFullYear()}" min="1900" max="${new Date().getFullYear()}">

        <div class="form-check mt-2">
            <input type="hidden" name="experiences[${index}][currently_working]" value="0">
            <input type="checkbox" name="experiences[${index}][currently_working]" class="form-check-input" id="currently-working-${index}" data-year-end-field="year-end-${index}" value="1">
            <label for="currently-working-${index}" class="form-check-label">Actualmente trabajando aquí</label>
        </div>
    `;

    container.appendChild(template);

    // Reaplicar evento al checkbox
    template.querySelector(`#currently-working-${index}`).addEventListener('change', function () {
        const yearEndField = template.querySelector(`#year-end-${index}`);
        yearEndField.disabled = this.checked;
        if (this.checked) yearEndField.value = '';
    });

    // Agregar evento para eliminar la experiencia
    template.querySelector('.remove-experience').addEventListener('click', function () {
        template.remove(); // Elimina el contenedor de la experiencia
        updateExperienceHeaders(); // Actualiza los índices y encabezados
    });
});

// Función para actualizar los índices y encabezados después de eliminar
function updateExperienceHeaders() {
    const container = document.getElementById('experience-container');
    const items = container.querySelectorAll('.experience-item');
    items.forEach((item, newIndex) => {
        const header = item.querySelector('h6');
        header.textContent = `Experiencia #${newIndex + 1}`;
    });
}
document.querySelectorAll('.toggle-password').forEach(button => {
    const input = document.getElementById(button.getAttribute('data-target'));

    // Evento para alternar la visibilidad de la contraseña al hacer clic en el botón
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Evitar comportamiento por defecto del botón
        const icon = this.querySelector('i');

        // Cambiar entre texto y contraseña
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }

        // Mantener el foco en el campo
        input.focus();
    });

    // Evento para volver a encriptar al perder el foco
    input.addEventListener('blur', function () {
        if (input.type === 'text') {
            input.type = 'password';
            const icon = button.querySelector('i');
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    });
});

</script>
@endsection