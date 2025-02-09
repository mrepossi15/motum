<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Algolia\AlgoliaSearch\Api\SearchClient;

use App\Models\Park;
/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sync:parks', function () {
    // Inicializar el cliente de Algolia
    $client = SearchClient::create(
        env('ALGOLIA_APP_ID'),
        env('ALGOLIA_API_KEY')
    );

    // Datos de los parques con actividades
    $parks = Park::with('trainings.activity')->get()->map(function ($park) {
        return [
            'objectID' => $park->id,              // ID único del parque
            'name' => $park->name,               // Nombre del parque
            'location' => $park->location,       // Dirección
            '_geoloc' => [                       // Coordenadas geoespaciales
                'lat' => $park->latitude,
                'lng' => $park->longitude,
            ],
            'activities' => $park->trainings     // Actividades asociadas
                ->pluck('activity.name')         // Obtener nombres de actividades
                ->unique()                       // Eliminar duplicados
                ->values()                       // Reindexar el array
                ->all(),                         // Convertir a un array
        ];
    })->toArray();

    try {
        // Reemplazar el contenido del índice
        $client->replaceAllObjects('parks', $parks);
        $this->info(count($parks) . ' parques sincronizados en Algolia.');
    } catch (\Exception $e) {
        $this->error('Error al sincronizar con Algolia: ' . $e->getMessage());
    }
})->describe('Sincroniza los datos del índice "parks" en Algolia.');


Artisan::command('delete:parks', function () {
    // Inicializar el cliente de Algolia
    $client = SearchClient::create(
        env('ALGOLIA_APP_ID'),    // App ID desde .env
        env('ALGOLIA_API_KEY')   // Admin API Key desde .env
    );

    try {
        // Eliminar el índice 'parks'
        $client->deleteIndex('parks');
        $this->info('Índice "parks" eliminado correctamente.');
    } catch (\Exception $e) {
        $this->error('Error al eliminar el índice: ' . $e->getMessage());
    }
})->describe('Elimina el índice "parks" en Algolia.');
