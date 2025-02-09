
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel Trainer App')</title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (request()->secure())
    <link rel="stylesheet" href="{{ secure_asset('css/styles.css') }}">
@else
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
@endif

</head>
<body>
    <header>
    <x-nav></x-nav>
    </header>
    <main>
    @if (session()->has('feedback.message'))
        <div class="alert alert-{{ session()->get('feedback.type', 'success') }}">
            {!! session()->get('feedback.message') !!}
        </div>
    @endif
     <div class="container mt-5">
        @yield('content')
    </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".favorite-btn").forEach(button => {
        button.addEventListener("click", function () {
            const favoritableId = this.getAttribute("data-id");
            const favoritableType = this.getAttribute("data-type");

            fetch("/favorites/toggle", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ favoritable_id: favoritableId, favoritable_type: favoritableType })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
            })
            .catch(error => console.error("Error al guardar en favoritos:", error));
        });
    });
});
    </script>

</body>
</html>
