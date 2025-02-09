MOTUM:
Para la tesis estoy creando un proyecto llamado Motum que conecte entrenadores con alumnos interesados en actividades físicas al aire libre, utilizando geolocalización para optimizar la experiencia. La aplicación debe incluir funcionalidades específicas tanto para alumnos como para entrenadores, permitiendo una interacción eficiente y un control adecuado de entrenamientos y reservas en parques públicos. A continuación, detallo los requerimientos para cada tipo de usuario:
 
Requerimientos para alumnos: 
Geolocalización y búsqueda de actividades: (estoy usando la API de Google y Algoria Search)
El usuario debe seleccionar una ubicación manualmente o utilizar su ubicación actual. Mostrar en un mapa las plazas o parques públicos cercanos donde se realicen actividades físicas. Al seleccionar un parque, debe aparecer una lista de actividades disponibles en ese lugar (ejemplo: yoga, fútbol, running, etc.). Tras elegir una actividad, se debe mostrar una lista de entrenamientos asociados, con información como: Horarios disponibles. Precios. Entrenadores responsables. Reserva y pago.
El usuario debe poder seleccionar un entrenamiento y optar por: Pagar una clase de prueba o comprar una membresía con distintas modalidades (por ejemplo: clases ilimitadas o dos veces por semana).
Para asistir a una clase, el usuario debe reservarla con al menos 24 horas de anticipación. Si ya reservó una clase, no debe poder cancelarla con menos de 24 horas de anticipación. 
Gestión de clases reservadas: Mostrar una sección de "Mis clases" donde el usuario pueda ver las clases reservadas, consultar las restricciones de su membresía (por ejemplo, clases limitadas por semana). Bloquear reservas adicionales si exceden el límite permitido por la membresía.
Requerimientos para entrenadores: 
Registro y selección de parques: Durante el registro, el entrenador debe elegir un parque de preferencia donde llevará a cabo sus entrenamientos. Desde el dashboard, el entrenador debe poder: Ver un calendario semanal con sus entrenamientos programados. Agregar entrenamientos nuevos seleccionando un parque de preferencia. Tener la opción de agregar más parques a su lista de preferencia y alternar entre ellos. 
Creación de entrenamientos: Cuando el entrenador cree un entrenamiento, este debe asociarse automáticamente al parque seleccionado previamente. No se debe requerir que el entrenador cargue nuevamente los datos del parque cada vez que agregue una clase. 
Gestión de entrenamientos: Permitir que el entrenador consulte información detallada de cada clase, incluyendo: El número de alumnos inscritos. Los nombres de los alumnos. Facilitar el seguimiento de asistencia y gestión de cupos en tiempo real.

Comandos:
php artisan migrate 
php artisan db:seed --class=ActivitySeeder
php artisan serve

(ME falta todo loq eu son pagos y reservas como asi tmabien tengo un TODO en el archivo Pasos.txt)