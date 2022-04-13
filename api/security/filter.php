<?php
class SecurityFilter
{
    private $codigo_libro = "";
/**
 * Get header Authorization
 * */
    private function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    private function decryption($string)
    {
        $key = hash('sha256', TOKEN_SECRET_KEY);
        $iv = substr(hash('sha256', TOKEN_SECRET_IV), 0, 16);
        $output = openssl_decrypt(base64_decode($string), TOKEN_METHOD, $key, 0, $iv);
        return $output;
    }
/**
 * get access token from header
 * */
    private function getBearerToken()
    {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                if (preg_match('/Clpe\s(\S+)/', $headers, $matchesClpe)) {
                    $this->codigo_libro = $matchesClpe[1];
                }
                return $matches[1];
            }

        }
        return null;
    }
    private function validarBearerToken()
    {

        $token = $this->getBearerToken();
        if (!empty($token)) {
            $stringToken = $this->decryption($token);
            if (!empty($stringToken)) {
                return $stringToken;
            }
        }
        return null;
    }

    public function HeaderToken()
    {
        require_once './core/configGeneral.php';
        require_once "core/configAPP.php";
        $values_path = $_SERVER['REQUEST_URI'];
        
        //$values_path = $_SERVER['REDIRECT_URL'];
        $values_path = explode("?", $values_path)[0];
        //HACEMOS UN SPLIT PARA DEJAR EL PATH SIN PARAMETROS

        $values_path = explode("/", $values_path);
        $accion = $values_path[sizeof($values_path) - 1];
        $resultadoTOKEN = $this->validarBearerToken();

        // echo ("hola es " . $_SERVER['CONTENT_TYPE']);
        if (isset($_SERVER['CONTENT_TYPE'])) {
            if ($_SERVER['CONTENT_TYPE'] == "application/json; charset=UTF-8" ||
                preg_match('/multipart\/form-data/i', $_SERVER['CONTENT_TYPE'])) {
                if (!empty($resultadoTOKEN)) {
                    $json_TOKEN = json_decode($resultadoTOKEN);
                    if ($json_TOKEN->tipo == 1 || $json_TOKEN->tipo == 2) {
                        $json_TOKEN->libro = ($json_TOKEN->tipo == 1) ? "" : $this->codigo_libro;
                        $json_TOKEN->accion = $accion;
                        return $json_TOKEN;
                    } else {
                        return null;
                    }

                } else {
                    return json_decode(json_encode(array(
                        "accion" => $accion,
                    )));
                }
            } else {
                return null;
            }
        } else {

            return null;

        }

    }
    public function DecryptionToken($string)
    {
        require_once "core/configAPP.php";
        $stringToken = $this->decryption($string);
        if (!empty($stringToken)) {
            return $stringToken;
        }
    }

}
