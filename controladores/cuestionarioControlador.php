<?php

require_once './modelos/cuestionarioModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';
class cuestionarioControlador extends cuestionarioModelo
{
    public function agregar_cuestionario_controlador()
    {
        $codigo = mainModel::limpiar_cadena($_POST['Cuenta-reg']);
        $idtest = mainModel::limpiar_cadena($_POST['ID-reg']);
        $resp1 = mainModel::limpiar_cadena($_POST['Respuesta1-reg']);
        $resp2 = mainModel::limpiar_cadena($_POST['Respuesta2-reg']);
        $resp3 = mainModel::limpiar_cadena($_POST['Respuesta3-reg']);
        $resp4 = mainModel::limpiar_cadena($_POST['Respuesta4-reg']);
        $resp5 = mainModel::limpiar_cadena($_POST['Respuesta5-reg']);
        $resp6 = mainModel::limpiar_cadena($_POST['Respuesta6-reg']);
        $resp7 = mainModel::limpiar_cadena($_POST['Respuesta7-reg']);
        $resp8 = mainModel::limpiar_cadena($_POST['Respuesta8-reg']);
        $resp9 = mainModel::limpiar_cadena($_POST['Respuesta9-reg']);
        $resp10 = mainModel::limpiar_cadena($_POST['Respuesta10-reg']);

        $data = [
            "CodigoCuenta" => $codigo,
            "Respuesta_p1" => $resp1,
            "Respuesta_p2" => $resp2,
            "Respuesta_p3" => $resp3,
            "Respuesta_p4" => $resp4,
            "Respuesta_p5" => $resp5,
            "Respuesta_p6" => $resp6,
            "Respuesta_p7" => $resp7,
            "Respuesta_p8" => $resp8,
            "Respuesta_p9" => $resp9,
            "Respuesta_p10" => $resp10,
            "IDtest" => $idtest,
        ];
        $guardarcuestionario = cuestionarioModelo::agregar_cuestionario_modelo($data);
        if ($guardarcuestionario >= 1) {
            $alerta = [
                "Alerta" => "limpiar",
                "Titulo" => "Test Realizado",
                "Texto" => "El Test de Preguntas se realizo exito",
                "Tipo" => "success",
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "No hemos podido guardar el test",
                "Tipo" => "error",
            ];
        }
        return json_encode($alerta);
    }
    public function datos_cuestionario_controlador($tipo, $codigo)
    {
        $tipo = mainModel::limpiar_cadena($tipo);

        return cuestionarioModelo::datos_cuestionario_modelo($tipo, $codigo);

    }
    public function paginador_cuestionario_controlador($buscar)
    {

        $codigo = mainModel::limpiar_cadena($buscar);
        $tabla = "";
        $conexion = mainModel::__construct();

        $datos = $conexion->query("SELECT SQL_CALC_FOUND_ROWS  c.usuario,t.nombre,b.*,t.idtitulo
         FROM `cuestionario_cliente` as b
        inner join `cuenta` as c ON b.codigo_cuenta=c.CuentaCodigo
        inner join `test` as t ON b.idtest=t.idtest
        WHERE  b.codigo_cuenta='$codigo'
        ORDER BY c.usuario ASC");
        $datos = $datos->fetchAll();
        $total = $conexion->query("SELECT  FOUND_ROWS()");

        $total = (int) $total->fetchColumn();
        if ($total >= 1) {
            return json_encode($datos);
        } else {
            $tabla = 'ninguno';
            return $tabla;
        }

    }
    public function eliminar_cuestionario_controlador()
    {
        $codigo = mainModel::limpiar_cadena($_POST['ID-reg']);
        $ultimo = cuestionarioModelo::datos_cuestionario_modelo("ultimo-titulo", "L01.N01");
        $tu = $ultimo[0]['codigo'];
        $consulta1 = mainModel::ejecutar_consulta_simple("SELECT * FROM `titulo`
        WHERE codigoTitulo='$tu'");
        if ($consulta1[0]['idtitulo'] == mainModel::limpiar_cadena($_POST['IDtitulo-reg'])) {
            $guardarAdmin = cuestionarioModelo::eliminar_cuestionario_modelo($codigo);
            if ($guardarAdmin >= 1) {
                $alerta = [
                    "Alerta" => "limpiar",
                    "Titulo" => "Cuestionario Eliminado",
                    "Texto" => "El cuestionario del Alumno se Elimino con éxito en el sistema",
                    "Tipo" => "success",
                ];

            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado",
                    "Texto" => "No hemos podido Eliminar El cuestionario del Alumno",
                    "Tipo" => "error",
                ];

            }
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "El alumno todavia no termina el curso",
                "Tipo" => "error",

            ];
        }

        return json_encode($alerta);

    }
}
