<?php

class ControllerProducts
{

    public function readFile($routeFile, $bussiness)
    {
        $start = microtime(true);
        $controller = new ControllerProducts();
        $arrayProdsScrape = array();
        $arrayProdNotScrape = array();
        $arrayProdsNotAvailable = array();
        $arrayPrices = array();
        if (($gestor = fopen($routeFile, "r")) !== FALSE) {
            while (($data = fgetcsv($gestor, 1000, ",")) !== FALSE) {
                $numero = count($data);
                $rowSend = "";
                for ($c = 0; $c < $numero; $c++) {
                    $rowSend = $rowSend . $data[$c];
                }
                if (str_contains($rowSend, "('") && str_contains($rowSend, "')")) {
                    $controller = new ControllerProducts();
                    $linkProd = $controller->clearData($rowSend);
                    $res = $controller->scrapProduct($linkProd, $bussiness);
                    if ($res == 'Not available') {
                        $arrayProdsNotAvailable[count($arrayProdsNotAvailable)] = $linkProd;
                    } else if ($res == "error") {
                        $arrayProdNotScrape[count($arrayProdNotScrape)] = $linkProd;
                    } else {
                        $arrayProdsScrape[count($arrayProdsScrape)] = $linkProd;
                        $arrayPrices[count($arrayPrices)] = $res;
                    }
                }
            }

            $controller = new ControllerProducts();
            $controller->createOutputFiles($arrayProdNotScrape, $arrayProdsScrape, $arrayProdsNotAvailable, $arrayPrices);

            fclose($gestor);
            unlink($routeFile);

            echo "<br>Screapeado exitosamente<br>";

            $time_elapsed = microtime(true) - $start;

            echo "<br>El tiempo de ejecucion ha sido: " . $time_elapsed . 's';
        }
    }

    public function createOutputFiles($arrayNot, $arrayScrape, $arrayProdsNotAvailable, $arrayPrices)
    {
        $string = "";
        $string2 = "";
        $string3 = "";

        foreach ($arrayNot as $key) {
            $string = $string . $key . '<br>';
        }

        for ($i = 0; $i < count($arrayScrape); $i++) {
            $string2 = $string2 . 'UPDATE producto_has_empresa SET precioReal = ' . $arrayPrices[$i] . ' WHERE linkProducto = "' . $arrayScrape[$i] . '";';
        }

        foreach ($arrayProdsNotAvailable as $key) {
            $string3 = $string3 . $key . '<br>';
        }

        $fileScrape = fopen("../files/fileScrape.sql", "w+") or die("Error al crear el archivo");
        $fileNotScraper = fopen("../files/fileNotScrape.txt", "w+") or die("Error al crear el archivo");
        $fileNotAvailable = fopen("../files/fileNotAvailable.txt", "w+") or die("Error al crear el archivo");

        fwrite($fileNotScraper, $string);
        fwrite($fileScrape, $string2);
        fwrite($fileNotAvailable, $string3);
    }

    public function clearData($rowSend)
    {
        $rowSend = str_replace("('", "", $rowSend);
        $rowSend = str_replace("')", "", $rowSend);
        $rowSend = str_replace(";", "", $rowSend);

        return $rowSend;
    }

    public function scrapProduct($linkProd, $bussiness)
    {
        $controller = new ControllerProducts();
        $price = $controller->scrapeData($linkProd, $bussiness);
        if ($price == "Not available") {
            return "Not available";
        } else if ($price !== NULL) {
            return $price;
        } else {
            return "error";
        }
    }

    public function findBussiness($nameWebSiteBussiness)
    {
        $model = new Model();
        $bussinessId = 0;
        $response = $model->findBussiness();
        foreach ($response as $bussiness) {
            if (str_contains($nameWebSiteBussiness, $bussiness['nombreEmpresa'])) {
                $bussinessId = $bussiness['idEmpresa'];
            }
        }
        return $bussinessId;
    }

    public function scrapeData($link, $bussiness)
    {
        try {
            if (!file($link)) {
                return NULL;
            }
            $líneas = file($link);
            if ($líneas != null) {
                $controller = new ControllerProducts();
                if ($bussiness == 'exito') {
                    $price = $controller->clearDataExito($líneas);
                } else if($bussiness == 'metro'){
                    $price = $controller->clearDataMetro($líneas);
                }else if($bussiness == 'carulla'){
                    $price = $controller->clearDataCarulla($líneas);
                }else{
                    $price = $controller->clearDataJumbo($líneas);
                }
                return $price;
            } else {
                return NULL;
            }
        } catch (Exception $error) {
            echo "An error has ocurred: " . $error . '<br>';
        }
    }

    public function clearDataJumbo($líneas)
    {
        foreach ($líneas as $num_línea => $línea) {
            if ($num_línea == 163) {
                $array = explode("content", htmlspecialchars($línea));
                if (isset(explode("meta", $array[count($array) - 1])[0])) {
                    $price = explode("meta", $array[count($array) - 1])[0];
                    $int = (int) filter_var($price, FILTER_SANITIZE_NUMBER_INT);
                    if ($int == 0) {
                        return "Not available";
                    } else {
                        return $int;
                    }
                } else {
                    return NULL;
                }
            }
        }
    }

    public function clearDataExito($líneas)
    {
        foreach ($líneas as $num_línea => $línea) {
            if ($num_línea == 214) {
                $array = explode("content", htmlspecialchars($línea));
                if (isset(explode("meta", $array[count($array) - 2])[0])) {
                    $price = explode("meta", $array[count($array) - 2])[0];
                    $int = (int) filter_var($price, FILTER_SANITIZE_NUMBER_INT);
                    if ($int == 0) {
                        return "Not available";
                    } else {
                        return $int;
                    }
                } else {
                    return NULL;
                }
            }
        }
    }

    public function clearDataMetro($líneas)
    {
        foreach ($líneas as $num_línea => $línea) {
            if ($num_línea == 169) {
                $array = explode("content", htmlspecialchars($línea));
                if (isset(explode("meta", $array[count($array) - 1])[0])) {
                    $price = explode("meta", $array[count($array) - 1])[0];
                    $int = (int) filter_var($price, FILTER_SANITIZE_NUMBER_INT);
                    if ($int == 0) {
                        return "Not available";
                    } else {
                        return $int;
                    }
                } else {
                    return NULL;
                }
            }
        }
    }

    public function clearDataCarulla($líneas){
        foreach ($líneas as $num_línea => $línea) {
            if ($num_línea == 206) {
                $array = explode("content", htmlspecialchars($línea));
                if (isset(explode("meta", $array[count($array) - 2])[0])) {
                    $price = explode("meta", $array[count($array) - 2])[0];
                    $int = (int) filter_var($price, FILTER_SANITIZE_NUMBER_INT);
                    if ($int == 0) {
                        return "Not available";
                    } else {
                        return $int;
                    }
                } else {
                    return NULL;
                }
            }
        }
    }
}
