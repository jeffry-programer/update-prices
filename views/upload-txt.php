<?php

include('../controllers/controller.php');
include('../models/model.php');

error_reporting(0);

set_time_limit(600);

$routeFile = "C:/xampp/htdocs/update-prices/files/";


/*$content = file('https://www.carulla.com/harina-pan-maiz-blanca-x-1000-gr-370181/p');
if ($content != null) {
    foreach ($content as $num_línea => $línea) {
        if ($num_línea == 206) {
            $array = explode("content", htmlspecialchars($línea));
            if (isset(explode("meta", $array[count($array) - 2])[0])) {
                $price = explode("meta", $array[count($array) - 2])[0];
                $int = (int) filter_var($price, FILTER_SANITIZE_NUMBER_INT);
                if ($int == 0) {
                    echo "Not available";
                } else {
                    echo $int;
                }
            } else {
                echo NULL;
            }
        }
    }
}*/

uploadFile();

function uploadFile()
{
    $file = $_FILES['files']['name'];
    $routeFile = $_FILES['files']['tmp_name'];
    $response = moveArchive($routeFile, $file);
    if ($response == "exist") {
        echo "This file already exist!!";
    } else if ($response != "error") {
        $finalyFile = $GLOBALS["routeFile"] . $response;
        $controller = new ControllerProducts();
        $controller->readFile($finalyFile, $_POST['bussiness']);
    } else {
        echo "Ups ha ocurrido un error";
    }
}

function moveArchive($routeFile, $file)
{
    if (!str_contains($file, '.sql')) return "archive-not-valid";
    $folder = $GLOBALS["routeFile"];
    $nameImg = $file;
    $file = $folder . $nameImg;
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
        if (file_exists($folder)) {
            if (move_uploaded_file($routeFile, $file)) {
                return $nameImg;
            } else {
                return "error";
            }
        }
    } else {
        if (file_exists($file)) return "exist";
        if (move_uploaded_file($routeFile, $file)) {
            return $nameImg;
        } else {
            return "error";
        }
    }
}
