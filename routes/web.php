<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ParkController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ReservationController;

use App\Http\Controllers\FavoriteController;

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/

// Login y logout
Route::get('/', [UserController::class, 'loginForm'])
    ->name('login');
Route::post('/login', [UserController::class, 'login'])
    ->name('login.post');
Route::post('/cerrar-sesion', [UserController::class, "logout"])
    ->name('auth.logout.process');

/*
|--------------------------------------------------------------------------
| Rutas para Entrenadores
|--------------------------------------------------------------------------
*/
// Registro de entrenadores
Route::get('/register/trainer', [TrainerController::class, 'registerTrainer'])
    ->name('register.trainer');
Route::post('/register/trainer', [TrainerController::class, 'storeTrainer'])
    ->name('store.trainer');

// Perfil del entrenador
Route::get('/trainer/profile', [TrainerController::class, 'showTrainerProfile'])
    ->name('trainer.profile')
    ->middleware(['auth', 'role:entrenador']);

//Mostrar form de actualziar perfil del entrenador
Route::get('/trainer/profile/edit', [TrainerController::class, 'editTrainerProfile'])
    ->name('trainer.editProfile')
    ->middleware(['auth', 'role:entrenador']);

//Procesar los cambios del perfil
Route::put('/trainer/profile/update', [TrainerController::class, 'updateTrainer'])
    ->name('trainer.update')
    ->middleware(['auth', 'role:entrenador']);

// Calendario de entrenamientos
Route::get('/trainer/calendar', [TrainerController::class, 'calendar'])
    ->name('trainer.calendar')
    ->middleware(['auth', 'role:entrenador']);

// Todos los entrenamientos del entrenador
Route::get('/mis-entrenamientos', [TrainerController::class, 'index'])
->name('trainer.index')
->middleware(['auth', 'role:entrenador']);

   
/*
|--------------------------------------------------------------------------
| Rutas para Entrenamientos | POV Entrenador
|--------------------------------------------------------------------------
*/

// Crear, editar y gestionar entrenamientos
Route::get('/trainings/create', [TrainingController::class, 'create'])
    ->name('trainer.training.create')
    ->middleware(['auth', 'role:entrenador']);
Route::post('/trainings/store', [TrainingController::class, 'store'])
    ->name('trainings.store')
    ->middleware(['auth', 'role:entrenador']);
Route::get('/trainings/{id}', [TrainingController::class, 'show'])
    ->name('trainer.training.show')
    ->middleware(['auth', 'role:entrenador']);
Route::get('/trainings/{id}/edit', [TrainingController::class, 'edit'])
    ->name('trainer.training.edit')
    ->middleware(['auth', 'role:entrenador']);
Route::put('/trainings/{id}', [TrainingController::class, 'update'])
    ->name('trainings.update')
    ->middleware(['auth', 'role:entrenador']);
Route::delete('/trainings/{id}', [TrainingController::class, 'destroy'])
    ->name('trainings.destroy')
    ->middleware(['auth', 'role:entrenador']);
    
Route::delete('/trainings/{id}/all', [TrainingController::class, 'destroyAll'])
    ->name('destroy.all')
    ->middleware(['auth', 'role:entrenador']);

// Detalle del entrenamiento 
Route::get('/entrenamientos/{id}', [TrainerController::class, 'showAll'])
->name('trainer.showAll')
->middleware(['auth', 'role:entrenador']);





// API: Obtener entrenamientos por parque
Route::get('/api/trainings', [TrainingController::class, 'getTrainingsByPark']);

/*
|--------------------------------------------------------------------------
| Rutas para Parques | POV Entrenador
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:entrenador'])->group(function () {
    // Agregar un parque
    Route::get('/trainer/add-park', [ParkController::class, 'create'])
    ->name('trainer.add.park')
    ->middleware(['auth', 'role:entrenador']);
    Route::post('/trainer/add-park', [ParkController::class, 'store'])
    ->name('trainer.store.park')
    ->middleware(['auth', 'role:entrenador']);

    // Mostrar un parque
});
Route::get('/parks/{id}', [ParkController::class, 'show'])->name('parks.show');



// API: Parques cercanos
Route::get('/api/parks-nearby', [ParkController::class, 'getNearbyParks']);

/*
|--------------------------------------------------------------------------
| Rutas para Alumnos 
|--------------------------------------------------------------------------
*/
Route::get('/register', [StudentController::class, 'registerStudent'])
    ->name('register.student');
Route::post('/register', [StudentController::class, 'storeStudent'])
    ->name('store.student');
    Route::get('/profile/{id}', [StudentController::class, 'studentProfile'])
    ->name('student.profile')
    ->middleware('auth'); // Solo autenticados pueden ver perfiles
Route::get('/student/profile/edit', [StudentController::class, 'editStudentProfile'])
    ->name('student.editProfile')->middleware('auth');
Route::put('/student/profile/edit', [StudentController::class, 'updateStudent'])
    ->name('student.updateProfile')->middleware('auth');


/*
|--------------------------------------------------------------------------
| Rutas para Entrenamientos | POV Alumno
|--------------------------------------------------------------------------
*/
Route::get('/my-trainings', [TrainingController::class, 'myTrainings'])
    ->middleware('auth')
    ->name('student.training.myTrainings');
Route::get('alumnos/trainings/{id}', [TrainingController::class, 'select'])
    ->name('students-trainings.show');


    Route::get('/parks/{parkId}/activities/{activityId}/trainings', [TrainingController::class, 'showTrainings'])
    ->name('parks.trainings');

/*
|--------------------------------------------------------------------------
| Rutas para Parques | POV Alumno
|--------------------------------------------------------------------------
*/

// Actividades y entrenamientos en parques
Route::get('/parks/{park}/activities/{activity}', [TrainingController::class, 'showTrainings'])
    ->name('activities.trainings');
Route::get('alumnos/trainings/{id}', [TrainingController::class, 'select'])
    ->name('students-trainings.show');



/*
|--------------------------------------------------------------------------
| Rutas del Mapa
|--------------------------------------------------------------------------
*/
Route::get('/mapa', [parkController::class, 'map'])
->name('map')->middleware('auth');




Route::post('/reviews', [ReviewController::class, 'store'])
    ->name('reviews.store')
    ->middleware(['auth', 'role:alumno']);

/*
|--------------------------------------------------------------------------
| Rutas para restaurar contrasena
|--------------------------------------------------------------------------
*/ 

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
->name('password.email');

Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])
->name('password.update');


/*
|--------------------------------------------------------------------------
| Rutas para el carrito
|--------------------------------------------------------------------------
*/ 
Route::middleware(['auth'])->group(function () {
Route::post('/cart/add', [CartController::class, 'add'])
->name('cart.add');;
Route::get('/cart/view', [CartController::class, 'viewCart'])
->name('cart.view');
Route::post('/cart/remove', [CartController::class, 'remove'])
->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])
->name('cart.clear');
Route::post('/payment/split', [PaymentController::class, 'createSplitPayment']);
});

Route::post('/payment/webhook', [PaymentController::class, 'handleWebhook']);
Route::get('/payment/success', [PaymentController::class, 'success']);
Route::get('/payment/failure', [PaymentController::class, 'failure']);
Route::get('/payment/pending', [PaymentController::class, 'pending']);
Route::get('/mis-compras', [PaymentController::class, 'userPayments'])->name('user.payments')->middleware('auth');



  

Route::middleware('auth')->group(function () {
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggleFavorite'])->name('favorites.toggle');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
});



Route::post('/trainings/suspend', [TrainingController::class, 'suspendClass'])->name('trainings.suspend');

Route::get('/api/trainings', [TrainingController::class, 'getTrainingsForWeek'])->name('api.trainings');




/*
|--------------------------------------------------------------------------
| Rutas de Reservas
|--------------------------------------------------------------------------
*/

Route::get('/trainings/{id}/reserve', [ReservationController::class, 'reserveTrainingView'])
    ->middleware('auth')
    ->name('reserve.training.view');

Route::post('/trainings/{id}/reserve', [ReservationController::class, 'storeReservation'])
    ->middleware('auth')
    ->name('store.reservation');

Route::delete('/trainings/{id}/delete', [ReservationController::class, 'cancelReservation'])
    ->middleware('auth')
    ->name('cancel.reservation');

Route::get('/trainings/{id}/available-times', [ReservationController::class, 'getAvailableTimes'])
    ->middleware('auth')
    ->name('trainings.available-times');

    // Me parece que no lo uso
Route::get('/training-reservations/{id}/{date}', [ReservationController::class, 'reservationDetail'])
->name('training.reservation.detail');