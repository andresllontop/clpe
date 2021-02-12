<?php

require_once './modelos/mensajeModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';
require_once './classes/principal/administrador.php';
require_once './classes/principal/cuenta.php';
require_once './classes/principal/empresa.php';

class mensajeControlador extends mensajeModelo
{
    public function agregar_mensaje_controlador($Mensaje)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Mensaje->setDescripcion(mainModel::limpiar_cadena($Mensaje->getDescripcion()));
            $Mensaje->setTitulo(mainModel::limpiar_cadena($Mensaje->getTitulo()));
            $Mensaje->setCuenta(mainModel::limpiar_cadena($Mensaje->getCuenta()));
            $Mensaje->setEstado(mainModel::limpiar_cadena($Mensaje->getEstado()));

            $stmt = mensajeModelo::agregar_mensaje_modelo($this->conexion_db, $Mensaje);
            if ($stmt->execute()) {
                $this->conexion_db->commit();
                $insBeanCrud->setMessageServer('ok');
                $insBeanCrud->setBeanPagination(self::paginador_mensaje_controlador($this->conexion_db, 0, 5));

            } else {
                $insBeanCrud->setMessageServer('No se ha ppodido enviar el mensaje');
            }

        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print '¡Error!: ' . $th->getMessage() . '<br/>';

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print '¡Error Processing Request!: ' . $e->getMessage() . '<br/>';

        } finally {
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }
    public function datos_mensaje_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(mensajeModelo::datos_mensaje_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print '¡Error!: ' . $th->getMessage() . '<br/>';

        } catch (PDOException $e) {
            print '¡Error Processing Request!: ' . $e->getMessage() . '<br/>';

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_mensaje_controlador($conexion, $inicio, $registros)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $stmt = $conexion->query("SELECT COUNT(idmensaje) AS CONTADOR FROM `mensaje`");
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row["CONTADOR"]);
                if ($row["CONTADOR"] > 0) {
                    $stmt = $conexion->prepare("SELECT men.*,admi.*,cuen.email FROM `mensaje` as men INNER JOIN `administrador` as admi ON admi.Cuenta_Codigo=men.cuenta_codigoCuenta INNER JOIN `cuenta` as cuen ON cuen.CuentaCodigo=men.cuenta_codigoCuenta ORDER BY men.cuenta_codigoCuenta ASC LIMIT ?,?");
                    $stmt->bindValue(1, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(2, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insCuenta = new Cuenta();
                        $insCuenta->setCuentaCodigo($row["cuenta_codigoCuenta"]);
                        $insCuenta->setEmail($row["email"]);
                        $insAministrador = new Administrador();
                        $insAministrador->setIdAdministrador($row["id"]);
                        $insAministrador->setTelefono($row["AdminTelefono"]);
                        $insAministrador->setOcupacion($row["AdminOcupacion"]);
                        $insAministrador->setPais($row["pais"]);
                        $insAministrador->setNombre($row["AdminNombre"]);
                        $insAministrador->setApellido($row["AdminApellido"]);
                        $insAministrador->setCuenta($insCuenta->__toString());

                        $insMensaje = new Mensaje();
                        $insMensaje->setIdMensaje($row["idmensaje"]);
                        $insMensaje->setTitulo($row["titulo"]);
                        $insMensaje->setEstado($row["mensajeEstado"]);
                        $insMensaje->setDescripcion($row["descripcion"]);
                        $insMensaje->setCuenta($insAministrador->__toString());
                        $insBeanPagination->setList($insMensaje->__toString());
                    }
                }
            }

            $stmt->closeCursor(); // this is not even required
            $stmt = null; // doing this is mandatory for connection to get closed

        } catch (Exception $th) {
            print '¡Error!: ' . $th->getMessage() . '<br/>';
        } catch (PDOException $e) {
            print '¡Error Processing Request!: ' . $e->getMessage() . '<br/>';
        }
        return $insBeanPagination->__toString();

    }
    public function bean_paginador_mensaje_controlador($pagina, $registros)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_mensaje_controlador($this->conexion_db, $inicio, $registros));

        } catch (Exception $th) {
            print '¡Error!: ' . $th->getMessage() . '<br/>';

        } catch (PDOException $e) {
            print '¡Error Processing Request!: ' . $e->getMessage() . '<br/>';

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_mensaje_controlador($Mensaje)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Mensaje->setIdMensaje(mainModel::limpiar_cadena($Mensaje->getIdMensaje()));
            // $Mensaje->setCuenta(mainModel::limpiar_cadena($Mensaje->getCuenta()));
            $mensaje = mensajeModelo::datos_mensaje_modelo($this->conexion_db, 'unico', $Mensaje);
            if ($mensaje['countFilter'] == 0) {
                $insBeanCrud->setMessageServer('No se encuentra el mensaje');
            } else {
                $stmt = mensajeModelo::eliminar_mensaje_modelo($this->conexion_db, $Mensaje->getIdMensaje());

                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer('ok');
                    $insBeanCrud->setBeanPagination(self::paginador_mensaje_controlador($this->conexion_db, 0, 5));

                } else {
                    $insBeanCrud->setMessageServer('No se eliminó el mensaje');
                }

            }

        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print '¡Error!: ' . $th->getMessage() . '<br/>';

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print '¡Error Processing Request!: ' . $e->getMessage() . '<br/>';

        } finally {
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());

    }
    public function actualizar_mensaje_controlador($Mensaje)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Mensaje->setIdMensaje(mainModel::limpiar_cadena($Mensaje->getIdMensaje()));
            $Mensaje->setCuenta(mainModel::limpiar_cadena($Mensaje->getCuenta()));
            $Mensaje->setEstado(mainModel::limpiar_cadena($Mensaje->getEstado()));
            $mensaje = mensajeModelo::datos_mensaje_modelo($this->conexion_db, 'unico', $Mensaje);
            if ($mensaje['countFilter'] == 0) {
                $insBeanCrud->setMessageServer('No se encuentra el mensaje');
            } else {
                $stmt = mensajeModelo::actualizar_mensaje_modelo($this->conexion_db, $Mensaje);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer('ok');
                    $insBeanCrud->setBeanPagination(self::paginador_mensaje_controlador($this->conexion_db, 0, 5));

                } else {
                    $insBeanCrud->setMessageServer('No se actualizó el mensaje');
                }
            }

        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print '¡Error!: ' . $th->getMessage() . '<br/>';

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print '¡Error Processing Request!: ' . $e->getMessage() . '<br/>';

        } finally {
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }

    public function enviar_mensaje_controlador($Mensaje)
    {
        $insBeanCrud = new BeanCrud();
        try {

            $Mensaje->setDescripcion(mainModel::limpiar_cadena($Mensaje->getDescripcion()));
            $Mensaje->setTitulo(mainModel::limpiar_cadena($Mensaje->getTitulo()));
            $Mensaje->setCuenta(mainModel::limpiar_cadena($Mensaje->getCuenta()));
            $Mensaje->setEstado(mainModel::limpiar_cadena($Mensaje->getEstado()));

            $empresa = mensajeModelo::datos_mensaje_modelo($this->conexion_db, 'empresa', 0);
            if ($empresa['countFilter'] > 0) {

                $ServerUrl = SERVERURL;
                $to = $empresa['list'][0]['email']; //EMAIL DESTINO
                $from = $Mensaje->getCuenta(); //EMAIL  REMITENTE
                $name = $empresa['list'][0]['nombre']; //NOMBRE DE LA EMPRESA
                $subject = $Mensaje->getTitulo(); //ASUNTO
                $Descripcion = $Mensaje->getDescripcion();
                $Alumno = $Mensaje->getEstado();
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
                                                                                    target='_blank'><img src='$ServerUrl/adjuntos/logoHeader.jpg' alt='$name'
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
                                                                                    Mensaje recibido desde la plataforma $name, del usuario
                                                                                    <h6
                                                                                        style='Margin:0;Margin-bottom:10px;color:inherit;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:500;line-height:1.3;margin:0;margin-bottom:0;padding:0;padding-bottom:0;text-align:center;word-wrap:normal;color:#9147ff'> $Alumno :</h6>
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
                                                                                <p
                                                                                    style='Margin:0;Margin-bottom:10px;font-family:Helvetica,Arial,Verdana,'Trebuchet MS';font-size:16px;font-weight:300;line-height:24px;margin:0;margin-bottom:0;padding:0;padding-bottom:0;text-align:center'>$Descripcion</p>
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
                                                                    <table                                            style='Margin:0 0 16px 0;border-collapse:collapse;border-spacing:0;float:none;font-weight:600;margin:0 0 16px 0;margin-bottom:0;padding:0;padding-bottom:0;text-align:center;vertical-align:top;width:auto'>
                                                                    <tbody>
                                                                        <tr style='padding:0;text-align:left;vertical-align:top'> <td
                                                         style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:600;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                         <table                                        style='border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%;border:none'>
                                                                    <tbody>
                                                                                                            <tr
                                                                                                            style='padding:0;text-align:left;vertical-align:top'>
                                                                                                                <td
                                                                style='Margin:0;background:#9147ff;border:2px solid #9147ff;border-collapse:collapse!important;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:600;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word;border-radius:2px;overflow:hidden'>
                                                                 <a href='$ServerUrl'
                                                                style='Margin:0;border:0 solid #9147ff;border-radius:3px;color:#fff;display:inline-block;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;line-height:1.3;margin:0;padding:10px 55px 10px 55px;text-align:left;text-decoration:none'
                                                                target='_blank'>Ir  a la pagina</a>
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
                    $this->conexion_db->beginTransaction();
                    $Mensaje->setEstado(0);
                    $stmt = mensajeModelo::agregar_mensaje_modelo($this->conexion_db, $Mensaje);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer('ok');
                    } else {
                        $insBeanCrud->setMessageServer('No se ha podido enviar el mensaje');
                    }
                } else {

                    $insBeanCrud->setMessageServer('el mensaje no se pudo enviar');
                }
            } else {

            }

        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print '¡Error!: ' . $th->getMessage() . '<br/>';

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print '¡Error Processing Request!: ' . $e->getMessage() . '<br/>';

        } finally {
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }

}
