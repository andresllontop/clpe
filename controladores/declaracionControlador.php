<?php

if ($peticionAJAX) {
    require_once '../modelos/declaracionModelo.php';
} else {
    require_once './modelos/declaracionModelo.php';
}

class declaracionControlador extends declaracionModelo
{
    public function agregar_declaracion_controlador()
    {
        $codigo = mainModel::limpiar_cadena($_POST['Codigo']);
        $subtitulo = mainModel::limpiar_cadena($_POST['Subtitulo']);
        $original = $_FILES['Audio-reg'];
       
        if ($original['name'] == "blob") {
            $nombre = $original['name'] . ".webm";
        } else {
            $nombre = $original['name'];
        }
        if ($original['error'] > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Se encontro un error al subir el archivo, seleccione otro Audio",
                "Tipo" => "error",

            ];
        } else {
            $resultado = mainModel::archivo(array("audio/webm","audio/mp3"), 17 * 1024, $original, $nombre, "../adjuntos/audio/");
            if ($resultado != "") {
                $data = [
                    "Codigo" => $codigo,
                    "Nombre" => $resultado,
                    "CodigoSubtitulo" => $subtitulo,

                ];
                $guardarlibro = declaracionModelo::agregar_declaracion_modelo($data);
                if ($guardarlibro >= 1) {
                    $alerta = [
                        "Alerta" => "limpiar",
                        "Titulo" => "Audio Registrado",
                        "Texto" => "Recarga la Pagina o selecciona Aceptar para continuar",
                        "Tipo" => "success",
                    ];

                } else {

                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrio un error inesperado",
                        "Texto" => "No hemos podido registrar el audio",
                        "Tipo" => "error",

                    ];
                }
            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado",
                    "Texto" => "Hubo un error al guardar la imagen,formato no permitido o tamaño excedido",
                    "Tipo" => "error",

                ];
            }
        }
        return json_encode($alerta);
    }
    public function datos_declaracion_controlador($tipo, $codigo)
    {
        $tipo = mainModel::limpiar_cadena($tipo);

        return declaracionModelo::datos_declaracion_modelo($tipo, $codigo);

    }
    public function paginador_declaracion_controlador($pagina, $registros,$codigo)
    {
        $pagina = mainModel::limpiar_cadena($pagina);
        $registros = mainModel::limpiar_cadena($registros);
        $codigo=mainModel::limpiar_cadena($codigo);
        $tabla = "";
        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
        $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
        $conexion = mainModel::__construct();

        $datos = $conexion->query("SELECT SQL_CALC_FOUND_ROWS * FROM `audio` as a
        inner join `cuenta` as c ON a.cuenta_codigo=c.CuentaCodigo
        inner join `subtitulo` as t ON a.codigo_subtitulo=t.codigo_subtitulo
        WHERE a.cuenta_codigo='$codigo'
        ORDER BY a.codigo_subtitulo  ASC LIMIT $inicio,$registros");
        $datos = $datos->fetchAll();
        $total = $conexion->query("SELECT  FOUND_ROWS()");

        $total = (int) $total->fetchColumn();
        $Npaginas = ceil($total / $registros);
        if ($total >= 1 && $pagina <= $Npaginas) {
            return json_encode($datos);
        } else {
            $tabla = 'ninguno';
            return $tabla;
        }

    }
    public function eliminar_declaracion_controlador()
    {
        $codigo = mainModel::limpiar_cadena($_POST['codigo-del']);
        $guardarAdmin = declaracionModelo::eliminar_declaracion_modelo($codigo);

        if ($guardarAdmin >= 1) {
            $alerta = [
                "Alerta" => "limpiar",
                "Titulo" => "Adminitrador Eliminado",
                "Texto" => "El declaracion se Elimino con éxito en el sistema",
                "Tipo" => "success",
            ];

        } else {

            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "No hemos podido Eliminar el declaracion",
                "Tipo" => "error",

            ];
        }
        return json_encode($alerta);

    }
}
