<?php

require_once './modelos/loginModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/principal/bitacora.php';
require_once './classes/principal/empresa.php';
require_once './classes/principal/librocuenta.php';
require_once './classes/other/beanPagination.php';
class loginControlador extends loginModelo
{

    public function agregar_login_controlador()
    {
        $Nombre = mainModel::limpiar_cadena($_POST['nombre-reg']);
        $Dni = mainModel::limpiar_cadena($_POST['DNI-reg']);
        $Apellido = mainModel::limpiar_cadena($_POST['apellido-reg']);
        $Telefono = mainModel::limpiar_cadena($_POST['telefono-reg']);

        $usuario = mainModel::limpiar_cadena($_POST['usuario-reg']);
        $password1 = mainModel::limpiar_cadena($_POST['password1-reg']);
        $password2 = mainModel::limpiar_cadena($_POST['password2-reg']);
        $email = mainModel::limpiar_cadena($_POST['email-reg']);
        $privilegio = mainModel::limpiar_cadena($_POST['privilegio-reg']);

        if ($password1 != $password2) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "las contraseñas que acabas de ingresar no coinciden",
                "Tipo" => "error",

            ];
        } else {
            $consulta1 = mainModel::ejecutar_consulta_simple("SELECT AdminDNI FROM login
                                 WHERE AdminDNI='$Dni'");
            if (count($consulta1) >= 1) {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado",
                    "Texto" => "el DNI que acaba de ingresar ya se encuentra registrado en el sistema",
                    "Tipo" => "error",

                ];
            } else {
                if ($email != "") {
                    $consulta2 = mainModel::ejecutar_consulta_simple("SELECT email FROM cuenta
                    WHERE email=' $email'");
                    $ec = count($consulta2);
                } else {
                    $ec = 0;
                }
                if ($ec >= 1) {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrio un error inesperado",
                        "Texto" => "el EMAIL que acaba de ingresar ya se encuentra registrado en el sistema",
                        "Tipo" => "error",

                    ];
                } else {
                    $consulta3 = mainModel::ejecutar_consulta_simple("SELECT usuario FROM cuenta
                    WHERE usuario=' $usuario'");

                    if (count($consulta3) >= 1) {
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrio un error inesperado",
                            "Texto" => "el Usuario que acaba de ingresar ya se encuentra registrado
                                en el sistema",
                            "Tipo" => "error",

                        ];
                    } else {
                        $consulta4 = mainModel::ejecutar_consulta_simple("SELECT idcuenta FROM cuenta");
                        $numero = count($consulta4) + 1;

                        $codigo = mainModel::generar_codigo_aleatorio("AC", 7, $numero);

                        $clave = mainModel::encryption($password1);
                        $dataAC = [
                            "Codigo" => $codigo,
                            "Privilegio" => $privilegio,
                            "Usuario" => $usuario,
                            "Clave" => $clave,
                            "Email" => $email,
                            "Estado" => "Activo",
                            "Tipo" => "login",
                            "Foto" => "foto",
                        ];
                        $guardarCuenta = mainModel::agregar_cuenta($dataAC);
                        if ($guardarCuenta >= 1) {
                            $dataAD = [
                                "Dni" => $Dni,
                                "Nombre" => $Nombre,
                                "Apellido" => $Apellido,
                                "Telefono" => $Telefono,
                                "Codigo" => $codigo,
                            ];

                            $guardarAdmin = loginModelo::agregar_login_modelo($dataAD);
                            if ($guardarAdmin >= 1) {
                                $alerta = [
                                    "Alerta" => "limpiar",
                                    "Titulo" => "Adminitrador Registrado",
                                    "Texto" => "El login se registro con exito en el sistema",
                                    "Tipo" => "success",
                                ];

                            } else {
                                mainModel::eliminar_cuenta($codigo);
                                $alerta = [
                                    "Alerta" => "simple",
                                    "Titulo" => "Ocurrio un error inesperado",
                                    "Texto" => "No hemos podido registrar el login",
                                    "Tipo" => "error",

                                ];
                            }

                        } else {
                            $alerta = [
                                "Alerta" => "simple",
                                "Titulo" => "Ocurrio un error inesperado",
                                "Texto" => "No hemos podido registrar el login",
                                "Tipo" => "error",

                            ];
                        }

                    }
                }
            }
        }

        return json_encode($alerta);

    }
    public function datos_login_controlador($Cuenta)
    {
        $insBeanCrud = new BeanCrud();
        try {

            $Cuenta->setClave(mainModel::limpiar_cadena($Cuenta->getClave()));

            $clave = mainModel::encryption($Cuenta->getClave());

            if ($clave != null) {
                $Cuenta->setClave($clave);
                $Cuenta->setEmail(mainModel::limpiar_cadena($Cuenta->getEmail()));

                $resultado = loginModelo::datos_login_modelo($this->conexion_db, $Cuenta);

                if ($resultado['countFilter'] > 0) {
                    if ($resultado['list'][0]['cuenta']['verificacion'] != "") {
                        $insBeanCrud->setMessageServer("Se envió un código de confirmación a su Correo Electrónico para confirmar su matricula al curso.");
                    } else {
                        $insEmpresa = new Empresa();
                        $listLibro = array();
                        if ($resultado['list'][0]['cuenta']['tipo'] == 2) {
                            $stmt = $this->conexion_db->query("SELECT * FROM `empresa` LIMIT 0,1 ");
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insEmpresa->setTelefono($row['EmpresaTelefono']);
                                $insEmpresa->setYoutube($row['youtube']);
                                $insEmpresa->setEmail($row['EmpresaEmail']);
                                $insEmpresa->setDireccion($row['EmpresaDireccion']);
                                $insEmpresa->setTelefonoSegundo($row['EmpresaTelefono2']);
                                $insEmpresa->setFacebook($row['facebook']);

                            }
                            //regresar codigo del libro
                            foreach ($resultado['list'] as $rowlibro) {
                                array_push($listLibro, $rowlibro['libro']);
                            }

                        }
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(array(
                            "empresa" => $insEmpresa->__toString(),
                            "libros" => $listLibro,
                            "cuenta" => $resultado['list'][0]['cuenta']));
                    }

                } else {
                    $insBeanCrud->setMessageServer("No se Encuentra Registrado!");
                }

            }
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }

    public function actualizar_login_controlador($Cuenta)
    {
        $insBeanCrud = new BeanCrud();
        $insBeanPagination = new BeanPagination();
        try {
            $this->conexion_db->beginTransaction();
            $Cuenta->setVerificacion(mainModel::limpiar_cadena($Cuenta->getVerificacion()));

            $stmt = $this->conexion_db->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE CuentaCodigo=:Codigo and codigo_verificacion=:Verifica ");
            $stmt->bindValue(":Verifica", $Cuenta->getVerificacion(), PDO::PARAM_STR);
            $stmt->bindValue(":Codigo", $Cuenta->getCuentaCodigo(), PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {

                if ($row['CONTADOR'] > 0) {
                    $stmt = $this->conexion_db->prepare("UPDATE `cuenta` SET
                    codigo_verificacion=:Verifica WHERE CuentaCodigo=:Codigo ");
                    $stmt->bindValue(":Verifica", "");
                    $stmt->bindValue(":Codigo", $Cuenta->getCuentaCodigo(), PDO::PARAM_STR);
                    if ($stmt->execute()) {

                        $stmt = $this->conexion_db->prepare("SELECT * FROM `cuenta` as cuent INNER JOIN `administrador`  as admmini  ON cuent.CuentaCodigo=admmini.Cuenta_Codigo WHERE cuent.CuentaCodigo=:Codigo and cuent.estado=1");
                        $stmt->bindValue(":Codigo", $Cuenta->getCuentaCodigo(), PDO::PARAM_STR);

                        $stmt->execute();
                        $datos = $stmt->fetchAll();
                        $insCuenta = new Cuenta();
                        $insAdministrador = new Cliente();
                        foreach ($datos as $row) {
                            $insCuenta->setEmail($row['email']);
                            $insCuenta->setIdCuenta($row['idcuenta']);
                            $insCuenta->setTipo($row['tipo']);
                            $insCuenta->setCuentaCodigo($row['CuentaCodigo']);
                            $insCuenta->setUsuario($row['usuario']);
                            $insCuenta->setEmail($row['email']);
                            $insCuenta->setEstado($row['estado']);
                            $insCuenta->setVerificacion($row['codigo_verificacion']);
                            $insCuenta->setFoto($row['foto']);

                            $insAdministrador->setIdCliente($row['id']);
                            $insAdministrador->setNombre($row['AdminNombre']);
                            //$insAdministrador->setTelefono($row['AdminTelefono']);
                            $insAdministrador->setApellido($row['AdminApellido']);
                            //$insAdministrador->setOcupacion($row['AdminOcupacion']);
                            //  $insAdministrador->setPais($row['pais']);
                            $insAdministrador->setCuenta($insCuenta->__toString());
                            $insBeanPagination->setList($insCuenta->__toString());
                        }

                        $insEmpresa = new Empresa();
                        if ($insCuenta->getTipo() == 2) {
                            $stmt = $this->conexion_db->query("SELECT * FROM `empresa` LIMIT 0,1 ");
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insEmpresa->setTelefono($row['EmpresaTelefono']);
                                $insEmpresa->setYoutube($row['youtube']);
                                $insEmpresa->setEmail($row['EmpresaEmail']);
                                $insEmpresa->setDireccion($row['EmpresaDireccion']);
                                $insEmpresa->setTelefonoSegundo($row['EmpresaTelefono2']);
                                $insEmpresa->setFacebook($row['facebook']);

                            }
                        }
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        self::enviar_mensaje_registro_controlador($this->conexion_db, $insAdministrador);
                        $insBeanCrud->setBeanPagination(array(
                            "empresa" => $insEmpresa->__toString(),
                            "principal" => $insBeanPagination->__toString()));

                    } else {

                        $insBeanCrud->setMessageServer("no se actualizó la cuenta");
                    }

                } else {
                    $insBeanCrud->setMessageServer("Código Incorrecto!");
                }
            }
        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }

    public function actualizar_clave_controlador($Cuenta)
    {
        $insBeanCrud = new BeanCrud();
        $insBeanPagination = new BeanPagination();
        try {
            $this->conexion_db->beginTransaction();
            $Cuenta->setClave(mainModel::encryption(mainModel::limpiar_cadena($Cuenta->getClave())));
            $stmt = $this->conexion_db->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE CuentaCodigo=:Codigo");
            $stmt->bindValue(":Codigo", $Cuenta->getCuentaCodigo(), PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {

                if ($row['CONTADOR'] > 0) {
                    $stmt = $this->conexion_db->prepare("UPDATE `cuenta` SET
                    clave=:Verifica WHERE CuentaCodigo=:Codigo ");
                    $stmt->bindValue(":Verifica", $Cuenta->getClave(), PDO::PARAM_STR);
                    $stmt->bindValue(":Codigo", $Cuenta->getCuentaCodigo(), PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        $stmt = $this->conexion_db->prepare("SELECT * FROM `cuenta` WHERE CuentaCodigo=:Codigo and estado=1");
                        $stmt->bindValue(":Codigo", $Cuenta->getCuentaCodigo(), PDO::PARAM_STR);

                        $stmt->execute();
                        $datos = $stmt->fetchAll();
                        $insCuenta = new Cuenta();
                        foreach ($datos as $row) {
                            $insCuenta->setEmail($row['email']);
                            $insCuenta->setIdCuenta($row['idcuenta']);
                            $insCuenta->setTipo($row['tipo']);
                            $insCuenta->setCuentaCodigo($row['CuentaCodigo']);
                            $insCuenta->setUsuario($row['usuario']);
                            $insCuenta->setEstado($row['estado']);
                            $insCuenta->setVerificacion($row['codigo_verificacion']);
                            $insCuenta->setFoto($row['foto']);
                            $insBeanPagination->setList($insCuenta->__toString());
                        }

                        $insEmpresa = new Empresa();
                        if ($insCuenta->getTipo() == 2) {
                            $stmt = $this->conexion_db->query("SELECT * FROM `empresa` LIMIT 0,1 ");
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insEmpresa->setTelefono($row['EmpresaTelefono']);
                                $insEmpresa->setYoutube($row['youtube']);
                                $insEmpresa->setEmail($row['EmpresaEmail']);
                                $insEmpresa->setDireccion($row['EmpresaDireccion']);
                                $insEmpresa->setTelefonoSegundo($row['EmpresaTelefono2']);
                                $insEmpresa->setFacebook($row['facebook']);

                            }
                        }
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(array(
                            "empresa" => $insEmpresa->__toString(),
                            "principal" => $insBeanPagination->__toString()));

                    } else {

                        $insBeanCrud->setMessageServer("no se actualizó la cuenta");
                    }

                } else {
                    $insBeanCrud->setMessageServer("Código Incorrecto!");
                }
            }
        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_login_controlador($pagina, $registros, $privilegio, $codigo)
    {
        $pagina = mainModel::limpiar_cadena($pagina);
        $registros = mainModel::limpiar_cadena($registros);
        $codigo = mainModel::limpiar_cadena($codigo);
        $tabla = "";
        $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
        $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
        $conexion = mainModel::__construct();

        $datos = $conexion->query("SELECT SQL_CALC_FOUND_ROWS *
                        FROM login WHERE id!='1' ORDER BY AdminNombre
                        ASC LIMIT $inicio,$registros
                        ");
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
    public function eliminar_login_controlador()
    {
        $codigo = mainModel::limpiar_cadena($_POST['codigo-del']);
        $guardarAdmin = loginModelo::eliminar_login_modelo($codigo);

        if ($guardarAdmin >= 1) {
            $alerta = [
                "Alerta" => "limpiar",
                "Titulo" => "Adminitrador Eliminado",
                "Texto" => "El login se Elimino con éxito en el sistema",
                "Tipo" => "success",
            ];

        } else {

            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "No hemos podido Eliminar el login",
                "Tipo" => "error",

            ];
        }
        return json_encode($alerta);

    }
    public function datos_login_tipo_controlador($tipo, $codigo, $token = null)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $lista = loginModelo::datos_login_tipo_modelo($this->conexion_db, $tipo, $codigo);
            if ($lista['countFilter'] > 0) {
                if ($token != null) {
                    $insCuenta = new Cuenta();
                    $insCuenta->setIdCuenta($lista['list'][0]['cuenta']['idcuenta']);
                    $insCuenta->setUsuario($lista['list'][0]['cuenta']['usuario']);
                    $insCuenta->setEmail($lista['list'][0]['cuenta']['email']);
                    $insCuenta->setTipo($lista['list'][0]['cuenta']['tipo']);
                    $insCuenta->setCuentaCodigo($lista['list'][0]['cuenta']['cuentaCodigo']);
                    $insCliente = new Cliente();
                    $insCliente->setNombre($lista['list'][0]['nombre']);
                    $insCliente->setApellido($lista['list'][0]['apellido']);
                    $insCliente->setCuenta($insCuenta->__toString());

                    $insUser = new Usuario();
                    $insUser->setId($lista['list'][0]['cuenta']['idcuenta']);
                    $insUser->setUsuario($lista['list'][0]['cuenta']['usuario']);
                    $insUser->setEmail($lista['list'][0]['cuenta']['email']);
                    $insUser->setTipo($lista['list'][0]['cuenta']['tipo']);
                    $insUser->setCodigo($lista['list'][0]['cuenta']['cuentaCodigo']);
                    $respuestaToken = $token->autenticar($insUser);
                    $responsemensaje = self::enviar_mensaje_recovery_controlador($this->conexion_db, $insCliente, $respuestaToken['token']);
                    $insBeanCrud->setMessageServer($responsemensaje['messageServer']);

                } else {
                    $insBeanCrud->setBeanPagination($lista);
                }

            } else {
                $insBeanCrud->setMessageServer("No se Encuentra Registrado!");
            }

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function enviar_mensaje_recovery_controlador($conexion, $Cliente, $token)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $empresa = loginModelo::datos_login_tipo_modelo($conexion, 'empresa', 0);
            if ($empresa['countFilter'] > 0) {
                $ServerUrl = SERVERURL;
                $to = $Cliente->getCuenta()['email']; //EMAIL DESTINO
                $from = $empresa['list'][0]['email']; //EMAIL  REMITENTE
                $name = $empresa['list'][0]['nombre']; //NOMBRE DE LA EMPRESA
                $VeriUrl = $ServerUrl . "auth/recovery/verificaty";

                $subject = "Restaurar Contraseña de " . $empresa['list'][0]['nombre']; //ASUNTO
                $Alumno = $Cliente->getNombre() . " " . $Cliente->getApellido();
                $message = "<table
                style='Margin:0;background:#e5e5e5!important;border-collapse:collapse;border-spacing:0;color:#e5e5e5;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;height:100%;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                <tbody>
                    <tr style='padding:0;text-align:left;vertical-align:top'>
                        <td align='center' valign='top'
                            style='Margin:0;border-collapse:collapse!important;color:#e5e5e5;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                            <center style='min-width:580px;width:100%'>
                                <table
                                    style='Margin:0 auto;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:0;text-align:center;vertical-align:top;width:100%'>
                                    <tbody>
                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                            <td height='15px'
                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:10px;font-weight:400;line-height:10px;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                &nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table align='center'
                                    style='Margin:0 auto;background:#fff;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:10px;text-align:center;vertical-align:top;width:580px;margin-left:10px!important;margin-right:10px!important'>
                                    <tbody>
                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                            <td
                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                <table
                                                    style='background-color:#fff;background-image:none;background-position:top left;background-repeat:repeat;border-bottom:1px solid #efeef1;border-collapse:collapse;border-spacing:0;display:table;margin:10px 0 15px 0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <th
                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0!important;padding-left:20px;padding-right:20px;padding-top:0!important;text-align:left;width:560px'>
                                                                <table
                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                    <tbody>
                                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left'>
                                                                                <a href='$ServerUrl'
                                                                                    style='Margin:0;color:#9147ff;font-family:Helvetica,Arial,sans-serif;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:none'
                                                                                    target='_blank'><img
                                                                                        src='$ServerUrl/adjuntos/logoHeader.jpg'
                                                                                        alt='$name'
                                                                                        style='Margin:0 auto;border:none;border-bottom:1px solid #9147ff;clear:both;display:block;float:none;margin:0 auto;max-width:100%;outline:0;padding:25px 0;text-align:center;text-decoration:none;width:114px!important'
                                                                                        class='CToWUd'></a></th>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;width:0'>
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table
                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <td height='15px'
                                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:15px;font-weight:400;line-height:15px;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                                &nbsp;</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table
                                                    style='border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <th
                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:500;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0!important;padding-left:20px;padding-right:20px;padding-top:0!important;text-align:left;width:560px'>
                                                                <table
                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                    <tbody>
                                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:500;line-height:1.3;margin:0;padding:0;text-align:left'>
                                                                                <small>

                                                                                    <h6
                                                                                        style='Margin:0;Margin-bottom:10px;color:inherit;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:500;line-height:1.3;margin:0;margin-bottom:0;padding:0;padding-bottom:0;text-align:center;word-wrap:normal;color:#9147ff'>
                                                                                        Hola, $Alumno :</h6>
                                                                                </small>

                                                                            </th>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;width:0'>
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table
                                                    style='border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <th
                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:16px;padding-left:20px;padding-right:20px;padding-top:10px;text-align:left;width:560px'>
                                                                <p></p>
                                                                <table
                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                    <tbody>
                                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left'>
                                                                                <p style='Margin:0;Margin-bottom:10px;text-align: center;font-family:Helvetica,Arial,Verdana,'
                                                                                    Trebuchet
                                                                                    MS';font-size:16px;font-weight:300;line-height:24px;margin:0;margin-bottom:0;padding:0;padding-bottom:0;text-align:center'>
                                                                                    Ingresa al siguiente link de $name para cambiar
                                                                                    tu contraseña</p>
                                                                                <table
                                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                                    <tbody>
                                                                                        <tr
                                                                                            style='padding:0;text-align:left;vertical-align:top'>
                                                                                            <td height='20px'
                                                                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:10px;font-weight:400;line-height:10px;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                                                                &nbsp;</td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                                <center style='min-width:520px;width:100%'>
                                                                                    <table
                                                                                        style='Margin:0 0 16px 0;border-collapse:collapse;border-spacing:0;float:none;font-weight:600;margin:0 0 16px 0;margin-bottom:0;padding:0;padding-bottom:0;text-align:center;vertical-align:top;width:auto'>
                                                                                        <tbody>
                                                                                            <tr
                                                                                                style='padding:0;text-align:left;vertical-align:top'>
                                                                                                <td
                                                                                                    style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:600;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                                                                    <table
                                                                                                        style='border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%;border:none'>
                                                                                                        <tbody>
                                                                                                            <tr
                                                                                                                style='padding:0;text-align:left;vertical-align:top'>
                                                                                                                <td
                                                                                                                    style='Margin:0;background:#9147ff;border:2px solid #9147ff;border-collapse:collapse!important;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:600;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word;border-radius:2px;overflow:hidden'>
            <a href='$VeriUrl?id=$token'
                                                                                                                        style='Margin:0;border:0 solid #9147ff;border-radius:3px;color:#fff;display:inline-block;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;line-height:1.3;margin:0;padding:10px 55px 10px 55px;text-align:left;text-decoration:none'
                                                                                                                        target='_blank'>Ir
                                                                                                                        a la
                                                                                                                        página</a>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </center><br>
                                                                            </th>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;width:0'>
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table align='center'
                                    style='Margin:0 auto;background:0 0!important;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:0;text-align:center;vertical-align:top;width:580px'>
                                    <tbody>
                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                            <td
                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:12px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                <table
                                                    style='border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <th
                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:12px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:16px;padding-left:20px;padding-right:20px;padding-top:28px;text-align:left;width:560px'>
                                                                <table
                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                    <tbody>
                                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:12px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left'>
                                                                            </th>
                                                                            <td align='center' valign='top'>
                                                                                <table
                                                                                    style='border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                                    <tbody>
                                                                                        <tr
                                                                                            style='padding:0;text-align:left;vertical-align:top'>
                                                                                            <th
                                                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:16px;padding-left:20px;padding-right:20px;padding-top:20px;text-align:left;width:560px'>
                                                                                                <p></p>
                                                                                                <p></p>

                                                                                            </th>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>

                                                                            </td>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;width:0'>
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>

                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </center>
                        </td>
                    </tr>
                </tbody>
                </table>";
                $headers = array();
                $headers[] = "MIME-Version: 1.0";
                $headers[] = "Content-type: text/html; charset=UTF-8";
                $headers[] = "From: {$name} <{$from}>";
                $headers[] = "Reply-To: <{$from}>";
                $headers[] = "Subject: {$subject}";
                $headers[] = "X-Mailer: PHP/" . phpversion();

                if (mail($to, $subject, $message, implode("\r\n", $headers))) {
                    $insBeanCrud->setMessageServer('ok');
                } else {
                    $insBeanCrud->setMessageServer('El mensaje no se envió');
                }
            } else {

            }

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        }
        return $insBeanCrud->__toString();
    }
    public function enviar_mensaje_registro_controlador($conexion, $Cliente)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $empresa = loginModelo::datos_login_tipo_modelo($conexion, 'empresa', 0);
            if ($empresa['countFilter'] > 0) {
                $ServerUrl = SERVERURL;
                $to = $Cliente->getCuenta()['email']; //EMAIL DESTINO
                $from = $empresa['list'][0]['email']; //EMAIL  REMITENTE
                $name = $empresa['list'][0]['nombre']; //NOMBRE DE LA EMPRESA

                $subject = "Bienvenido a " . $empresa['list'][0]['nombre']; //ASUNTO
                $Alumno = $Cliente->getNombre() . " " . $Cliente->getApellido();
                $message = "<table
                style='Margin:0;background:#e5e5e5!important;border-collapse:collapse;border-spacing:0;color:#e5e5e5;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;height:100%;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                <tbody>
                    <tr style='padding:0;text-align:left;vertical-align:top'>
                        <td align='center' valign='top'
                            style='Margin:0;border-collapse:collapse!important;color:#e5e5e5;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                            <center style='min-width:580px;width:100%'>
                                <table
                                    style='Margin:0 auto;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:0;text-align:center;vertical-align:top;width:100%'>
                                    <tbody>
                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                            <td height='15px'
                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:10px;font-weight:400;line-height:10px;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                &nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table align='center'
                                    style='Margin:0 auto;background:#fff;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:10px;text-align:center;vertical-align:top;width:580px;margin-left:10px!important;margin-right:10px!important'>
                                    <tbody>
                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                            <td
                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                <table
                                                    style='background-color:#fff;background-image:none;background-position:top left;background-repeat:repeat;border-bottom:1px solid #efeef1;border-collapse:collapse;border-spacing:0;display:table;margin:10px 0 15px 0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <th
                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0!important;padding-left:20px;padding-right:20px;padding-top:0!important;text-align:left;width:560px'>
                                                                <table
                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                    <tbody>
                                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left'>
                                                                                <a href='$ServerUrl'
                                                                                    style='Margin:0;color:#9147ff;font-family:Helvetica,Arial,sans-serif;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:none'
                                                                                    target='_blank'><img
                                                                                        src='$ServerUrl/adjuntos/logoHeader.jpg'
                                                                                        alt='$name'
                                                                                        style='Margin:0 auto;border:none;border-bottom:1px solid #9147ff;clear:both;display:block;float:none;margin:0 auto;max-width:100%;outline:0;padding:25px 0;text-align:center;text-decoration:none;width:114px!important'
                                                                                        class='CToWUd'></a>
                                                                            </th>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;width:0'>
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table
                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <td height='15px'
                                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:15px;font-weight:400;line-height:15px;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                                &nbsp;</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table
                                                    style='border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <th
                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:500;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0!important;padding-left:20px;padding-right:20px;padding-top:0!important;text-align:left;width:560px'>
                                                                <table
                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                    <tbody>
                                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:500;line-height:1.3;margin:0;padding:0;text-align:left'>
                                                                                <small>

                                                                                    <h6
                                                                                        style='Margin:0;Margin-bottom:10px;color:inherit;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:500;line-height:1.3;margin:0;margin-bottom:0;padding:0;padding-bottom:0;text-align:center;word-wrap:normal;color:#9147ff'>
                                                                                        Hola, $Alumno :</h6>
                                                                                </small>

                                                                            </th>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;width:0'>
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table
                                                    style='border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <th
                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:16px;padding-left:20px;padding-right:20px;padding-top:10px;text-align:left;width:560px'>
                                                                <p></p>
                                                                <table
                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                    <tbody>
                                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left'>
                                                                                <p style='Margin:0;Margin-bottom:10px;text-align: center;font-family:Helvetica,Arial,Verdana,'
                                                                                    Trebuchet
                                                                                    MS';font-size:16px;font-weight:300;line-height:24px;margin:0;margin-bottom:0;padding:0;padding-bottom:0;text-align:center'>
                                                                                    Bienvenido a $name , ahora puedes acceder al
                                                                                    curso online disponible.</p>
                                                                                <table
                                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                                    <tbody>
                                                                                        <tr
                                                                                            style='padding:0;text-align:left;vertical-align:top'>
                                                                                            <td height='20px'
                                                                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:10px;font-weight:400;line-height:10px;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                                                                &nbsp;</td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                                <center style='min-width:520px;width:100%'>
                                                                                    <table
                                                                                        style='Margin:0 0 16px 0;border-collapse:collapse;border-spacing:0;float:none;font-weight:600;margin:0 0 16px 0;margin-bottom:0;padding:0;padding-bottom:0;text-align:center;vertical-align:top;width:auto'>
                                                                                        <tbody>
                                                                                            <tr
                                                                                                style='padding:0;text-align:left;vertical-align:top'>
                                                                                                <td
                                                                                                    style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:600;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                                                                    <table
                                                                                                        style='border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%;border:none'>
                                                                                                        <tbody>
                                                                                                            <tr
                                                                                                                style='padding:0;text-align:left;vertical-align:top'>
                                                                                                                <td
                                                                                                                    style='Margin:0;background:#9147ff;border:2px solid #9147ff;border-collapse:collapse!important;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:600;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word;border-radius:2px;overflow:hidden'>
                                                                                                                    <a href='$ServerUrl'
                                                                                                                        style='Margin:0;border:0 solid #9147ff;border-radius:3px;color:#fff;display:inline-block;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;line-height:1.3;margin:0;padding:10px 55px 10px 55px;text-align:left;text-decoration:none'
                                                                                                                        target='_blank'>Ir
                                                                                                                        a la
                                                                                                                        página</a>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </center><br>
                                                                            </th>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;width:0'>
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table align='center'
                                    style='Margin:0 auto;background:0 0!important;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:0;text-align:center;vertical-align:top;width:580px'>
                                    <tbody>
                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                            <td
                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:12px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                <table
                                                    style='border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <th
                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:12px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:16px;padding-left:20px;padding-right:20px;padding-top:28px;text-align:left;width:560px'>
                                                                <table
                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                    <tbody>
                                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:12px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left'>
                                                                            </th>
                                                                            <td align='center' valign='top'>
                                                                                <table
                                                                                    style='border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                                    <tbody>
                                                                                        <tr
                                                                                            style='padding:0;text-align:left;vertical-align:top'>
                                                                                            <th
                                                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:16px;padding-left:20px;padding-right:20px;padding-top:20px;text-align:left;width:560px'>
                                                                                                <p></p>
                                                                                                <p></p>

                                                                                            </th>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>

                                                                            </td>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;width:0'>
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>

                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </center>
                        </td>
                    </tr>
                </tbody>
            </table>";
                $headers = array();
                $headers[] = "MIME-Version: 1.0";
                $headers[] = "Content-type: text/html; charset=UTF-8";
                $headers[] = "From: {$name} <{$from}>";
                $headers[] = "Reply-To: <{$from}>";
                $headers[] = "Subject: {$subject}";
                $headers[] = "X-Mailer: PHP/" . phpversion();

                if (mail($to, $subject, $message, implode("\r\n", $headers))) {
                    $insBeanCrud->setMessageServer('ok');
                } else {
                    $insBeanCrud->setMessageServer('El mensaje no se envió');
                }
            } else {

            }

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        }
        return $insBeanCrud->__toString();
    }
    public function datos_obtener_controlador($tipo, $codigo)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(loginModelo::datos_login_tipo_modelo($this->conexion_db, $tipo, $codigo));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
}
