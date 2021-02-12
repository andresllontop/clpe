<?php

require_once './modelos/videousuarioModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

require_once './classes/principal/cuenta.php';
require_once './classes/principal/subtitulo.php';

class videousuarioControlador extends videousuarioModelo
{
    public function agregar_videousuario_controlador()
    {
        $Comentario = mainModel::limpiar_cadena($_POST['Comentario-reg']);
        $SubtituloCodigo = mainModel::limpiar_cadena($_POST['SubtituloCodigo-reg']);
        $CuentaCodigo = mainModel::limpiar_cadena($_POST['CuentaCodigo-reg']);
        $original = $_FILES['Video-reg'];

        if ($original['name'] == "blob") {
            $nombre = $original['name'] . ".webm";
        } else {
            $nombre = $original['name'];
        }

        if ($original['error'] > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Se encontro un error al subir el archivo, seleccione nuevamente el video",
                "Tipo" => "error",

            ];
        } else {
            $resultado_guardado = self::archivo(array("video/webm", "video/mp4"), 500 * 1024, $original, $nombre, "../adjuntos/video-usuarios/");
            if ($resultado_guardado != "") {
                $data = [
                    "Comentario" => $Comentario,
                    "SubtituloCodigo" => $SubtituloCodigo,
                    "CuentaCodigo" => $CuentaCodigo,
                    "Video" => $resultado_guardado,
                ];
                $guardarvideousuario = videousuarioModelo::agregar_videousuario_modelo($data);
                if ($guardarvideousuario >= 1) {
                    $data2 = [
                        "SubtituloCodigo" => $SubtituloCodigo,
                        "CuentaCodigo" => $CuentaCodigo,
                    ];
                    $guardarleccion = videousuarioModelo::agregar_videousuarioLeccion_modelo($data2);
                    if ($guardarleccion >= 1) {
                        $alerta = [
                            "Alerta" => "limpiar",
                            "Titulo" => "Comentario Registrado",
                            "Texto" => "Tu comentario y video fueron registrados con exito en el sistema",
                            "Tipo" => "success",
                        ];
                    } else {
                        $alerta = [
                            "Alerta" => "Simple",
                            "Titulo" => "Ocurrio un error inesperado",
                            "Texto" => "leccion no registrada en el sistema",
                            "Tipo" => "error",
                        ];
                    }
                } else {

                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrio un error inesperado",
                        "Texto" => "No hemos podido registrar Tu comentario y video,vuelva a intentarlo",
                        "Tipo" => "error",

                    ];
                }
            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado",
                    "Texto" => "Hubo un error al guardar el video,vuelva a intentarlo",
                    "Tipo" => "error",

                ];

            }

        }

        return json_encode($alerta);
    }
    public function datos_videousuario_controlador($tipo, $codigo)
    {
        $tipo = mainModel::limpiar_cadena($tipo);

        return videousuarioModelo::datos_videousuario_modelo($tipo, $codigo);

    }
    public function paginador_videousuario_controlador($pagina, $registros, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        $insBeanPagination = new BeanPagination();
        $pagina = mainModel::limpiar_cadena($pagina);
        $registros = mainModel::limpiar_cadena($registros);
        $codigo = mainModel::limpiar_cadena($codigo);
        $tabla = "";
        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
        $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
        $conexion = mainModel::__construct();

        $datos = $conexion->query("SELECT COUNT(b.idvideoUsuario) AS CONTADOR FROM `videousuario` as b
        WHERE b.cuenta_codigoCuenta='$codigo'");
        $datos = $datos->fetchAll();
        foreach ($datos as $row) {
            $insBeanPagination->setCountFilter($row['CONTADOR']);
        }

        $datos = $conexion->query("SELECT * FROM `videousuario` as b
        inner join cuenta as c ON b.cuenta_codigoCuenta=c.CuentaCodigo
        WHERE b.cuenta_codigoCuenta='$codigo'
        ORDER BY  b.subtitulo_codigosubtitulo ASC LIMIT $inicio,$registros");
        //var_dump($datos);
        $datos = $datos->fetchAll();

        foreach ($datos as $row) {

            $insSubTitulo = new SubTitulo();
            $insSubTitulo->setCodigo($row['subtitulo_codigosubtitulo']);

            $insCuenta = new Cuenta();
            $insCuenta->setCuentaCodigo($row['cuenta_codigoCuenta']);

            $insVideoUsuario = new VideoUsuario();
            $insVideoUsuario->setIdvideoUsuario($row['idvideoUsuario']);
            $insVideoUsuario->setComentario($row['comentario']);
            $insVideoUsuario->setVideo($row['video']);

            $insVideoUsuario->setCuenta($insCuenta->__toString());
            $insVideoUsuario->setSubTitulo($insSubTitulo->__toString());

            $insBeanPagination->setList($insVideoUsuario->__toString());
        }

        // $insBeanCrud->setMessageServer("ok");
        $insBeanCrud->setBeanPagination($insBeanPagination->__toString());
        return $insBeanCrud->__toString();
    }
    public function eliminar_videousuario_controlador()
    {
        $lista = videousuarioModelo::datos_videousuario_modelo("unico", mainModel::limpiar_cadena($_POST['ID-reg']));
        $guardarAdmin = videousuarioModelo::eliminar_videousuario_modelo(mainModel::limpiar_cadena($_POST['ID-reg']));
        $guardar = videousuarioModelo::eliminar_leccion_modelo(mainModel::limpiar_cadena($_POST['cuentaCodigo-reg']), mainModel::limpiar_cadena($_POST['subtitulo-reg']));
        if ($guardarAdmin >= 1 && $guardar >= 1) {
            unlink('../adjuntos/video-usuarios/' . $lista[0]['video']);
            $alerta = [
                "Alerta" => "limpiar",
                "Titulo" => "Eliminado",
                "Texto" => "Se Elimino con éxito en el sistema",
                "Tipo" => "success",
            ];

        } else {

            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "No hemos podido Eliminar",
                "Tipo" => "error",

            ];
        }
        return json_encode($alerta);

    }
    protected function archivo($permitidos, $limite_MB, $original, $nombre, $destino)
    {

        if ($original['size'] <= $limite_MB * 1024) {
            $array_nombre = explode('.', $nombre);
            $extension = array_pop($array_nombre);
            $array = glob($destino . $array_nombre[0] . "*." . $extension);
            $cantidad = count($array);
            $nombreImagen = $array_nombre[0] . $cantidad . "." . $extension;
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
