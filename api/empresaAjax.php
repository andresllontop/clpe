<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/empresa.php';
    require_once './controladores/empresaControlador.php';
    $insempresa = new empresaControlador();
    $accion = $RESULTADO_token->accion;
    if (!isset($RESULTADO_token->tipo)) {
        if ($accion == "obtener" || $accion == "paginate") {
            switch ($_SERVER['REQUEST_METHOD']) {

                case 'GET':
                    if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insempresa->datos_empresa_controlador("conteo-publico", 0));
                    } elseif ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $empresaBean = $insempresa->datos_empresa_controlador("conteo-publico", 0);
                        // $paiscurrency = convertCurrency($empresaBean["beanPagination"]["list"][0]["precio"], 'USD', ip_info(get_client_ip(), "countrycode"));
                        $paiscurrency = convertCurrency($empresaBean["beanPagination"]["list"][0]["precio"], 'USD', "UY");
                        echo json_encode(array("nombre" => $empresaBean["beanPagination"]["list"][0]["nombre"],
                            "email" => $empresaBean["beanPagination"]["list"][0]["email"],
                            "telefono" => $empresaBean["beanPagination"]["list"][0]["telefono"],
                            "precio" => $paiscurrency["precio"],
                            "precio_USD" => $paiscurrency["precio_USD"],
                            "pais" => $paiscurrency["pais"],
                            "countFilter" => $empresaBean["beanPagination"]["countFilter"],

                        ));

                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;
                default:
                    header("HTTP/1.1 404");
                    break;
            }
        } else {
            return header("HTTP/1.1 403");
        }

    } else {
        switch ($_SERVER['REQUEST_METHOD']) {

            case 'POST':

                if ($accion == "add") {
                    $personData = json_decode($_POST['class']);
                    $insEmpresaClass = new Empresa();
                    $insEmpresaClass->setTelefono($personData->telefono);
                    $insEmpresaClass->setVision($personData->vision);
                    $insEmpresaClass->setYoutube($personData->youtube);
                    $insEmpresaClass->setNombre($personData->nombre);
                    $insEmpresaClass->setMision($personData->mision);
                    $insEmpresaClass->setEmail($personData->email);
                    $insEmpresaClass->setDescripcion($personData->descripcion);
                    $insEmpresaClass->setDireccion($personData->direccion);
                    $insEmpresaClass->setLogo($personData->logo);
                    $insEmpresaClass->setEnlace($personData->enlace);
                    $insEmpresaClass->setTelefonoSegundo($personData->telefonoSegundo);
                    $insEmpresaClass->setFacebook($personData->facebook);
                    $insEmpresaClass->setPrecio($personData->precio);
                    $insEmpresaClass->setInstagram($personData->instagram);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insempresa->agregar_empresa_controlador($insEmpresaClass);
                } else if ($accion == "update") {

                    $personData = json_decode($_POST['class']);
                    $insEmpresaClass = new Empresa();
                    $insEmpresaClass->setIdEmpresa($personData->idempresa);
                    $insEmpresaClass->setTelefono($personData->telefono);
                    $insEmpresaClass->setVision($personData->vision);
                    $insEmpresaClass->setYoutube($personData->youtube);
                    $insEmpresaClass->setNombre($personData->nombre);
                    $insEmpresaClass->setMision($personData->mision);
                    $insEmpresaClass->setEmail($personData->email);
                    $insEmpresaClass->setInstagram($personData->instagram);
                    $insEmpresaClass->setDescripcion($personData->descripcion);
                    $insEmpresaClass->setDireccion($personData->direccion);
                    $insEmpresaClass->setLogo($personData->logo);
                    $insEmpresaClass->setEnlace($personData->enlace);
                    $insEmpresaClass->setTelefonoSegundo($personData->telefonoSegundo);
                    $insEmpresaClass->setFacebook($personData->facebook);
                    $insEmpresaClass->setPrecio($personData->precio);
                    $insEmpresaClass->setFrase($personData->frase);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insempresa->actualizar_empresa_controlador($insEmpresaClass);
                } else if ($accion == "updatefrase") {

                    $personData = json_decode($_POST['class']);
                    $insEmpresaClass = new Empresa();
                    $insEmpresaClass->setIdEmpresa($personData->idempresa);
                    $insEmpresaClass->setMision($personData->mision);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insempresa->actualizar_mision_empresa_controlador($insEmpresaClass);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "delete") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insEmpresaClass = new Empresa();
                    $insEmpresaClass->setIdEmpresa($_GET['id']);
                    echo $insempresa->eliminar_empresa_controlador($insEmpresaClass);
                } else if ($accion == "obtener") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insempresa->datos_empresa_controlador("conteo", 0));
                } else {
                    header("HTTP/1.1 500");
                }

                break;
            default:
                header("HTTP/1.1 404");
                break;
        }
    }
} else {
    return header("HTTP/1.1 403");
}
//Obtiene la IP del cliente
function get_client_ip()
{
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP')) {
        $ipaddress = getenv('HTTP_CLIENT_IP');
    } else if (getenv('HTTP_X_FORWARDED_FOR')) {
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    } else if (getenv('HTTP_X_FORWARDED')) {
        $ipaddress = getenv('HTTP_X_FORWARDED');
    } else if (getenv('HTTP_FORWARDED_FOR')) {
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    } else if (getenv('HTTP_FORWARDED')) {
        $ipaddress = getenv('HTTP_FORWARDED');
    } else if (getenv('REMOTE_ADDR')) {
        $ipaddress = getenv('REMOTE_ADDR');
    } else {
        $ipaddress = 'UNKNOWN';
    }

    return $ipaddress;
}

//Obtiene la info de la IP del cliente desde geoplugin

function ip_info($ip = null, $purpose = "location", $deep_detect = true)
{
    $output = null;
    if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }

            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }

        }
    }
    $purpose = str_replace(array("name", "\n", "\t", " ", "-", "_"), null, strtolower(trim($purpose)));
    $support = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America",
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city" => @$ipdat->geoplugin_city,
                        "state" => @$ipdat->geoplugin_regionName,
                        "country" => @$ipdat->geoplugin_countryName,
                        "country_code" => @$ipdat->geoplugin_countryCode,
                        "continent" => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode,
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1) {
                        $address[] = $ipdat->geoplugin_regionName;
                    }

                    if (@strlen($ipdat->geoplugin_city) >= 1) {
                        $address[] = $ipdat->geoplugin_city;
                    }

                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}
function convertCurrency($amount, $from_currency, $to_currency)
{
    $apikey = '28a171a41d8c554c9882';

    //OBTENER LISTA DE PAISES CON LA MONEDA
    $paises = file_get_contents("https://free.currconv.com/api/v7/countries?apiKey={$apikey}");
    $obj_pais = json_decode($paises, true);
    //print_r($obj_pais["results"]);

    foreach ($obj_pais["results"] as $key => $val) {
        if ($key == $to_currency) {
            $to_currency = $val;
            break;
        }
    }
    // print_r($to_currency["currencyId"]);
    // change to the free URL if you're using the free version
    $from_Currency = urlencode($from_currency);
    $to_Currency = urlencode($to_currency["currencyId"]);
    $query = "{$from_Currency}_{$to_Currency}";
    $json = file_get_contents("https://free.currconv.com/api/v7/convert?q={$query}&compact=ultra&apiKey={$apikey}");
    $obj = json_decode($json, true);
    // print_r($obj);
    $val = floatval($obj["$query"]);

    $total = $val * $amount;
    return array("pais" => $to_currency, "precio" => number_format($total, 2, '.', ''), "precio_USD" => $amount);
}
