<?php

date_default_timezone_set('Europe/Madrid');

function comprobar_formatos($path){

    $formatos = array(".jpg", "jpeg", ".png");

    $dir = opendir($path);

    $files = array();

    while ($current = readdir($dir)) {

        if ($current != "." && $current != "..") {

            if (!is_dir($path . $current)) {
          
                if (!in_array(substr($current, -4), $formatos)) {
                    $files[0] = 'error';
                }

           }
        }
    }

    if(count($files)==0){
            return true;
    }

    else{
        return false;
    }

}

function redimensionarJPG($max_ancho, $max_alto, $ruta){

    $imagenes = check_images_ext($ruta, "jpg");

    $calidad = 100;

    for ($x = 0; $x < count($imagenes); $x++) {

        $salida = $ruta . "/" . $imagenes[$x];

        if (substr($imagenes[$x], -4, 1) == ".") {

            $img_original = imagecreatefromjpeg("./" . $ruta . "/" . $imagenes[$x]);

            list($ancho, $alto) = getimagesize("./" . $ruta . "/" . $imagenes[$x]);

            //Se calcula ancho y alto de la imagen final
            $x_ratio = $max_ancho / $ancho;
            $y_ratio = $max_alto / $alto;
            $tmp = imagecreatetruecolor($max_ancho, $max_alto);
            imagecopyresampled($tmp, $img_original, 0, 0, 0, 0, $max_ancho, $max_alto, $ancho, $alto);
            imagedestroy($img_original);
            imagejpeg($tmp, $salida, $calidad);
        }
    }
}

function check_images_ext($path, $extension){
    $dir = opendir($path);
    $files = array();
    while ($current = readdir($dir)) {
        if ($current != "." && $current != "..") {
            if (is_dir($path . $current)) {
                showFiles($path . $current . '/');
            } else {
                if (substr($current, -3) == $extension) {
                    $files[] = $current;
                }
            }
        }
    }
	sort($files);
    return $files;
}

function showFiles($path){
    $dir = opendir($path);
    $files = array();
    while ($current = readdir($dir)) {
        if ($current != "." && $current != "..") {
            if (is_dir($path . $current)) {
                showFiles($path . $current . '/');
            } else {
                if (substr($current, -4, 1) == "." || substr($current, -3, 1) == "." || substr($current, -5, 1) == ".") {
                    $files[] = $current;
                }
            }
        }
    }
	sort($files);
    return $files;
}

function check_images($path){
    $dir = opendir($path);
    $files = array();
    while ($current = readdir($dir)) {
        if ($current != "." && $current != "..") {
            if (is_dir($path . $current)) {
                showFiles($path . $current . '/');
            } else {
                $files[] = $current;
            }
        }
    }
	sort($files);
    return $files;
}

function jpeg_jpg(array $imagenes, $ruta){
    for ($x = 0; $x < count($imagenes); $x++) {
        if (substr($imagenes[$x], -4) == "jpeg") {
            rename($ruta . "/" . $imagenes, $ruta . "/" . substr($imagenes, 0, -4) . ".jpg");
        }
    }
}

function png_a_jpg($ruta){
    $imagenes = check_images_ext($ruta, "png");
    for ($x = 0; $x < count($imagenes); $x++) {
        $jpg = $ruta . "/" . substr($imagenes[$x], 0, -3) . "jpg";
        $image = imagecreatefrompng($ruta . "/" . $imagenes[$x]);
        imagejpeg($image, $jpg, 100);
        unlink($ruta . "/" . $imagenes[$x]);
    }
}

$borrar = showFiles("img/");
$num_imagenes=count($borrar);

if($num_imagenes<=170){
	
	$comprobacion = comprobar_formatos("img");

	if ($comprobacion) {

        $imagenes = check_images("img");
        
        jpeg_jpg($imagenes, "img");
        
        png_a_jpg("img");
        
        $imagenes = check_images_ext("img", "jpg");
        
		$ancho = array();
		$alto = array();
		
		if ($imagenes != null && $imagenes != "" || check_images_ext("img", "png") != null && check_images_ext("img", "png") != "") {
		
			for ($x = 0; $x < count($imagenes); $x++) {
				$imagen = getimagesize("img/" . $imagenes[$x]);
				$ancho[] = $imagen[0];
				$alto[] = $imagen[1];
			}
			
			$anchofinal = min($ancho);
			$altofinal = min($alto);
			
			if ($anchofinal > 640) {
				$anchofinal = 640;
			}
			
			if ($altofinal > 480) {
				$altofinal = 480;
			}
			
			//redimensionarJPG($anchofinal, $altofinal, "img");
			
			if (count($imagenes) > 1) {
				require "src/GifCreator/AnimGif.php";
				$anim = new GifCreator\AnimGif();
				if (count($imagenes) <= 10) {
					$rapidez = 20;
				} else {
					$rapidez = 5;
				}
				$archivo = date("Y") . "_" . date("d") . "_" . date("m") . "_" . date("H") . "-" . date("i") . "-" . date("s");
				$anim->create("img/", array($rapidez))
					->save('Output/' . $archivo . '.gif');
			}
		}
		else {
			print '<h1 name="salida">Folder empty</h1>';
		}
	}
}

$borrar = showFiles("img/");

if ($num_imagenes > 0) {
	
    for ($x = 0; $x < count($borrar); $x++) {
        unlink("img/" . $borrar[$x]);
    }
	
}

header('Location:index.php');

?>
