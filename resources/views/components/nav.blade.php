<div>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-black">
        <div class="container">
            <!-- Brand -->
            <a class="navbar-brand text-naranja" href="#" style="font-style: italic; font-weight: 600;">
                motum
            </a>

            <!-- Toggler for Mobile View -->
            <button class="navbar-toggler text-naranja" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto">
                    @guest
                        <!-- Guest Links -->
                        <li class="nav-item">
                            <a class="nav-link text-naranja" href="{{ route('login') }}">Iniciar sesión</a>
                        </li>
                    @else
                        <!-- Authenticated User Links -->
                        @if (auth()->user()->role === 'entrenador')
                            <li class="nav-item">
                                <a class="nav-link text-naranja active" href="{{ route('trainer.calendar') }}">Calendario</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-naranja" href="{{ route('trainer.profile') }}">Mi Perfil</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-naranja" href="{{ route('trainer.index') }}">Mis Entrenamientos</a>
                            </li>

                        @elseif (auth()->user()->role === 'alumno')
                            <li class="nav-item">
                                <a class="nav-link text-naranja" href="{{ route('map') }}">Mapa</a>
                            </li>
                            <li class="nav-item">
                            <a href="{{ route('student.profile', ['id' => auth()->user()->id]) }}">Mi perfil</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-naranja" href="{{ route('cart.view') }}">Mi Carrito</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-naranja" href="{{ route('student.training.myTrainings') }}">Mis Entrenamientos</a>
                            </li>
                            
                        @endif
                        <!-- Logout Button -->
                        <li class="nav-item">
                            <form action="{{ route('auth.logout.process') }}" method="POST" class="d-inline">
                                @csrf
                                <button 
                                    class="nav-link text-naranja bg-black border-0" 
                                    style="cursor: pointer;">
                                    {{ auth()->user()->email }} (Cerrar sesión)
                                </button>
                            </form>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
</div>


