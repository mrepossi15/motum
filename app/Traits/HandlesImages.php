<?php

namespace App\Traits;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

trait HandlesImages
{
    /**
     * Redimensionar y guardar una imagen.
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @param string $path
     * @param int $width
     * @param int|null $height
     * @return string Ruta de la imagen guardada.
     */
    public function resizeAndSaveImage($image, $path, $width, $height = null)
    {
        $imagePath = $path . '/' . uniqid() . '.' . $image->getClientOriginalExtension();

        $resizedImage = Image::make($image)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio(); // Mantener la relación de aspecto
            $constraint->upsize(); // Evitar agrandar imágenes más pequeñas
        });

        // Guardar la imagen en el almacenamiento público
        $resizedImage->save(storage_path('app/public/' . $imagePath));

        return $imagePath;
    }

    /**
     * Eliminar una imagen existente del almacenamiento.
     *
     * @param string $path
     * @return void
     */
    public function deleteImageIfExists($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}