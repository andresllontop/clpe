<?php

require_once './core/configAPP.php';

class mainModel
{
    protected function __construct()
    {
        try {
            $conexion_db = new PDO(SGDB, USER, PASS);
            $conexion_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conexion_db->exec(" SET CHARACTER SET 'utf8' ");
            //para tildes y ñ
            return $conexion_db;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

    }

    protected function encryption($string)
    {

        $output = false;
        $key = hash('sha256', SECRET_KEY);
        $iv = substr(hash('sha256', SECRET_IV), 0, 16);
        $output = openssl_encrypt($string, METHOD, $key, 0, $iv);
        $output = base64_encode($output);

        return $output;
    }
    protected function decryption($string)
    {
        $output = null;
        try {
            $key = hash('sha256', SECRET_KEY);
            $iv = substr(hash('sha256', SECRET_IV), 0, 16);
            $output = openssl_decrypt(base64_decode($string), METHOD, $key, 0, $iv);

        } catch (\Throwable $th) {
            echo ($th);
            $output = null;
        }
        return $output;

    }
    protected function generar_codigo_aleatorio($letra, $longitud, $num)
    {
        for ($i = 1; $i <= $longitud; $i++) {
            $numero = rand(0, 9);
            $letra .= $numero;

        }
        return $letra . $num;
    }
    protected function limpiar_cadena($cadena)
    {
        $cadena = trim($cadena);
        $cadena = stripcslashes($cadena); //quitar las barrar invertidas
        $cadena = str_ireplace("<script>", "", $cadena);
        $cadena = str_ireplace("</script>", "", $cadena);
        $cadena = str_ireplace("<script src", "", $cadena);
        $cadena = str_ireplace("<script type", "", $cadena);
        $cadena = str_ireplace("SELECT * FROM", "", $cadena);
        $cadena = str_ireplace("DELETE FROM", "", $cadena);
        $cadena = str_ireplace("INSERT FROM", "", $cadena);
        $cadena = str_ireplace("--", "", $cadena);
        $cadena = str_ireplace("^", "", $cadena);
        $cadena = str_ireplace("[", "", $cadena);
        $cadena = str_ireplace("]", "", $cadena);
        $cadena = str_ireplace("==", "", $cadena);
        return $cadena;

    }

    protected function archivo($permitidos, $limite_KB, $original, $nombre, $destino)
    {

        if (in_array($original['type'], $permitidos) && ($original['size'] <= $limite_KB * 1024)) {
            $array_nombre = explode('.', $nombre);
            $extension = array_pop($array_nombre);
            $array = glob($destino . $array_nombre[0] . "*." . $extension);
            $cantidad = count($array);
            $nombreImagen = $array_nombre[0] . $cantidad . "." . $extension;
            if ($cantidad == 1) {
                $arr = explode('/', $array[0]);
                $igualultimo = array_pop($arr);
                if ($igualultimo == $nombreImagen) {
                    $nombreImagen = "other-" . $nombreImagen;
                }
            }
            $resultado_guardado = move_uploaded_file($original['tmp_name'], $destino . $nombreImagen);
            if ($resultado_guardado) {
                return $nombreImagen;
            } else {
                return "";
            }

        } else {
            return "";
        }

    }

}
