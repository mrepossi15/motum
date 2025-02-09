@extends('layouts.main')

@section('title', 'Dashboard del Entrenador')

@section('content')

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


    <!-- Main Content -->
    <main class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
    <!-- Dropdown for Parks -->
    <div class="dropdown">
        <button class="btn bg-naranja text-white dropdown-toggle" type="button" id="parkDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            Mis Parques
        </button>
        <ul class="dropdown-menu" aria-labelledby="parkDropdown" id="parkDropdownMenu">
            @foreach($parks as $park)
                <li><a class="dropdown-item" href="#" data-value="{{ $park->id }}">{{ $park->name }}</a></li>
            @endforeach
            <li><a class="dropdown-item" href="#" data-value="all">Todos</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-naranja fw-bold" href="{{ route('trainer.add.park') }}">Agregar Parque</a></li>
        </ul>
    </div>

    <!-- Add Training Button -->
    <button id="add-training-button" class="btn bg-naranja text-white">
        <i class="bi bi-plus"></i> Agregar Entrenamiento
    </button>


</div>


        <!-- Calendar Header -->
        <h2 id="month-title" class="text-center text-naranja mb-3"></h2>

        <!-- Week Navigation -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button id="prev-week" class="btn border-naranja-lg">
                <i class="bi bi-arrow-left"></i>
            </button>
            <span id="week-range" class="fw-bold text-secondary"></span>
            <button id="next-week" class="btn border-naranja-lg">
                <i class="bi bi-arrow-right"></i>
            </button>
        </div>

        <!-- Days Header -->
        <div class="row text-center text-black fw-bold">
            <div class="col">Lunes</div>
            <div class="col">Martes</div>
            <div class="col">Miércoles</div>
            <div class="col">Jueves</div>
            <div class="col">Viernes</div>
            <div class="col">Sábado</div>
            <div class="col">Domingo</div>
        </div>

        <!-- Calendar Container -->
        <div class="row mt-3" id="calendar-container"></div>

        <!-- Training Details -->
        <div class="mt-5">
            <div id="trainings-list" class="mt-3">
                <p class="text-center text-secondary">Selecciona un día para ver los entrenamientos.</p>
            </div>
        </div>
    </main>
    <script src="/js/entrenador/calendar.js"></script>
@endsection