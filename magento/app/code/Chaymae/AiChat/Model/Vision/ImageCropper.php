<?php
namespace Chaymae\AiChat\Model\Vision;

/**
 * Recadre une image binaire sur une zone rectangulaire (bounding box),
 * typiquement celle detectee par YOLOv8.
 */
class ImageCropper
{
    /**
     * @param string $imageBinaryData Image source (contenu binaire brut)
     * @param array{x1:int,y1:int,x2:int,y2:int} $box Coordonnees de la zone a garder
     * @return string Image recadree, encodee en JPEG (binaire brut)
     */
    public function crop(string $imageBinaryData, array $box): string
    {
        $source = imagecreatefromstring($imageBinaryData);
        if ($source === false) {
            // Si l'image ne peut pas etre lue, on retourne l'original tel quel
            return $imageBinaryData;
        }

        $x1 = max(0, (int) $box['x1']);
        $y1 = max(0, (int) $box['y1']);
        $width = max(1, (int) $box['x2'] - $x1);
        $height = max(1, (int) $box['y2'] - $y1);

        $cropped = imagecreatetruecolor($width, $height);
        imagecopy($cropped, $source, 0, 0, $x1, $y1, $width, $height);

        ob_start();
        imagejpeg($cropped, null, 90);
        $result = ob_get_clean();

        imagedestroy($source);
        imagedestroy($cropped);

        return $result;
    }
}
