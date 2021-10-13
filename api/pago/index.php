<?php
/**
 * Ejemplo 2
 * Como crear un charge a una tarjeta usando Culqi PHP.
 */

try {

    $values_path = $_SERVER['REDIRECT_URL'];
    //HACEMOS UN SPLIT PARA DEJAR EL PATH SIN PARAMETROS
    $values_path = explode("/", $values_path);
    $accion = $values_path[sizeof($values_path) - 1];

    if (isset($_SERVER['CONTENT_TYPE'])) {
        if (preg_match('/multipart\/form-data/i', $_SERVER['CONTENT_TYPE'])) {
            require_once './classes/principal/empresa.php';
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    // if ($accion == "charge") {
                    // require_once './classes/principal/curso.php';
                    // require_once './controladores/cursoControlador.php';
                    // require_once './core/functions.php';
                    // $inscurso = new cursoControlador();
                    // $compraData = json_decode($_POST['class']);
                    // $insCursoClass = new Curso();
                    // $insCursoClass->setIdCurso($compraData->curso);
                    // $curso = $inscurso->datos_curso_controlador("unico", $insCursoClass)['beanPagination'];

                    // header("HTTP/1.1 200");
                    // header('Content-Type: application/json; charset=utf-8');
                    // if ($curso['countFilter'] > 0) {
                    //     $curso = $curso['list']['0'];
                    //     $compraData->curso = $curso['libro'];

                    //     //FALTA VALIDAR QUE EL USUARIO NO TENGA DOS LIBROS REGISTRADOS REPETIDOS.
                    //     $respuestaValidar = json_decode(ValidaUsuario($compraData));

                    //     if ($respuestaValidar->messageServer == "ok") {
                    //         $authorization = generateAuthorization($curso['precio'], $compraData->purchase, $compraData->transactionToken, generateToken());
                    //         //print_r($authorization);
                    //         if (isset($authorization->errorCode)) {
                    //             $respuestaValidar->messageServer = 'Pago denegado: ' . $authorization->data->ACTION_DESCRIPTION . ' -- Fecha :' . date('Y-m-d H:i:s') . ' -- N° Pedido :' . $authorization->data->TRACE_NUMBER;
                    //             echo (json_encode($respuestaValidar));
                    //         } else {
                    //             if (isset($authorization->dataMap)) {
                    //                 if ($authorization->dataMap->ACTION_CODE == "000") {
                    //                     CreateUsuario($compraData, json_decode(json_encode(array(
                    //                         "nombre_banco" => $authorization->dataMap->BRAND,
                    //                         //"comision" => (($authorization->dataMap->AMOUNT) * 26.05) / 100,
                    //                         "comision" => 0,
                    //                         "moneda" => $authorization->order->currency,
                    //                         "precio" => $authorization->dataMap->AMOUNT,
                    //                         "tipo" => 1,
                    //                         "requestNiubiz" => $authorization,
                    //                         "fecha" => date('Y-m-d H:i:s'),
                    //                     )
                    //                     )));
                    //                 }
                    //             } else if (isset($authorization->data)) {

                    //                 if ($authorization->data->ACTION_CODE != "000") {
                    //                     $respuestaValidar->messageServer = 'Pago denegado: ' . $authorization->data->ACTION_DESCRIPTION . ' -- Fecha :' . date('Y-m-d H:i:s') . ' -- N° Pedido :' . $authorization->data->TRACE_NUMBER;
                    //                     echo (json_encode($respuestaValidar));
                    //                 }
                    //             }
                    //         }

                    //     } else {
                    //         echo (json_encode($respuestaValidar));

                    //     }

                    // } else {
                    //     $respuestaValidar->messageServer = 'No se encuentra el Curso seleccionado ';
                    //     echo (json_encode($respuestaValidar));
                    // }

                    if ($accion == "charge") {
                        require_once './classes/principal/curso.php';
                        require_once './controladores/cursoControlador.php';
                        require_once './core/functions.php';
                        $inscurso = new cursoControlador();
                        $compraData = json_decode($_POST['class']);
                        $insCursoClass = new Curso();
                        $insCursoClass->setIdCurso($compraData->curso);
                        $curso = $inscurso->datos_curso_controlador("unico", $insCursoClass)['beanPagination'];

                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        if ($curso['countFilter'] > 0) {
                            $curso = $curso['list']['0'];
                            $compraData->curso = $curso['libro'];

                            //FALTA VALIDAR QUE EL USUARIO NO TENGA DOS LIBROS REGISTRADOS REPETIDOS.
                            $respuestaValidar = json_decode(ValidaUsuarioLibro($compraData));

                            if ($respuestaValidar->messageServer == "Ya se encuentra Registrado el Usuario") {

                                $authorization = generateAuthorization($curso['precio'], $compraData->purchase, $compraData->transactionToken, generateToken());
                                // print_r($authorization);
                                if (isset($authorization->errorCode)) {
                                    $respuestaValidar->messageServer = 'Pago denegado: ' . $authorization->data->ACTION_DESCRIPTION . ' -- Fecha :' . date('Y-m-d H:i:s') . ' -- N° Pedido :' . $authorization->data->TRACE_NUMBER;
                                    echo (json_encode($respuestaValidar));
                                } else {
                                    if (isset($authorization->dataMap)) {
                                        if ($authorization->dataMap->ACTION_CODE == "000") {
                                            AgregarLibroToUsuario($compraData, json_decode(json_encode(array(
                                                "nombre_banco" => $authorization->dataMap->BRAND,
                                                //"comision" => (($authorization->dataMap->AMOUNT) * 26.05) / 100,
                                                "comision" => 0,
                                                "moneda" => $authorization->order->currency,
                                                "precio" => $authorization->dataMap->AMOUNT,
                                                "tipo" => 1,
                                                "requestNiubiz" => $authorization,
                                                "fecha" => date('Y-m-d H:i:s'),
                                            )
                                            )));
                                        }
                                    } else if (isset($authorization->data)) {

                                        if ($authorization->data->ACTION_CODE != "000") {
                                            $respuestaValidar->messageServer = 'Pago denegado: ' . $authorization->data->ACTION_DESCRIPTION . ' -- Fecha :' . date('Y-m-d H:i:s') . ' -- N° Pedido :' . $authorization->data->TRACE_NUMBER;
                                            echo (json_encode($respuestaValidar));
                                        }
                                    }
                                }

                            } else {
                                echo (json_encode($respuestaValidar));

                            }

                        } else {
                            $respuestaValidar->messageServer = 'No se encuentra el Curso seleccionado ';
                            echo (json_encode($respuestaValidar));
                        }

                    } elseif ($accion == "clasico") {

                        require_once './classes/principal/curso.php';
                        require_once './controladores/cursoControlador.php';
                        require_once './plugins/PHPMailer/src/PHPMailer.php';
                        require_once './plugins/PHPMailer/src/SMTP.php';
                        require_once './plugins/PHPMailer/src/Exception.php';
                        $compraData = json_decode($_POST['class']);
                        $inscurso = new cursoControlador();
                        $insCursoClass = new Curso();
                        $insCursoClass->setIdCurso($compraData->curso);
                        $curso = $inscurso->datos_curso_controlador("unico", $insCursoClass)['beanPagination'];
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        if ($curso['countFilter'] > 0) {
                            $curso = $curso['list']['0'];
                            $compraData->curso = $curso['libro'];

                            $respuestaValidar = json_decode(ValidaUsuarioLibro($compraData));

                            if ($respuestaValidar->messageServer == "Ya se encuentra Registrado el Usuario") {
                                $inscliente = new clienteControlador();
                                //agregar libro con el alumno existente;
                                $insCuentaClass = new Cuenta();
                                $insCuentaClass->setEmail($compraData->address);
                                $insCuentaClass->setUsuario($compraData->nombre);
                                //curso
                                $insCuentaClass->setPerfil($compraData->curso);
                                $insCuentaClass->setVerificacion(0);

                                echo $inscliente->agregar_libro_publico_cliente_otro_medio_controlador($insCuentaClass);
                            } else {
                                echo (json_encode($respuestaValidar));
                            }
                        } else {
                            $respuestaValidar->messageServer = 'No se encuentra el Curso seleccionado ';
                            echo (json_encode($respuestaValidar));
                        }

                    } else {
                        //if ($accion == "clasico") {

                        // require_once './classes/principal/curso.php';
                        // require_once './controladores/cursoControlador.php';
                        // require_once './plugins/PHPMailer/src/PHPMailer.php';
                        // require_once './plugins/PHPMailer/src/SMTP.php';
                        // require_once './plugins/PHPMailer/src/Exception.php';
                        // $compraData = json_decode($_POST['class']);
                        // $inscurso = new cursoControlador();
                        // $insCursoClass = new Curso();
                        // $insCursoClass->setIdCurso($compraData->curso);
                        // $curso = $inscurso->datos_curso_controlador("unico", $insCursoClass)['beanPagination'];

                        // header("HTTP/1.1 200");
                        // header('Content-Type: application/json; charset=utf-8');
                        // if ($curso['countFilter'] > 0) {
                        //     $curso = $curso['list']['0'];
                        //     $compraData->curso = $curso['libro'];
                        //     $respuestaValidar = json_decode(ValidaUsuario($compraData));
                        //     if ($respuestaValidar->messageServer == "ok") {
                        //         $inscliente = new clienteControlador();
                        //         $insClienteClass = new Cliente();
                        //         $insClienteClass->setNombre($compraData->nombre);
                        //         $insClienteClass->setApellido($compraData->apellido);
                        //         $insClienteClass->setTelefono($compraData->telefono);
                        //         $insClienteClass->setOcupacion($compraData->profesion);
                        //         $insClienteClass->setPais($compraData->pais);
                        //         $insClienteClass->setVendedor($compraData->vendedor);
                        //         $insClienteClass->setTipoMedio($compraData->tipomedio);
                        //         $insCuentaClass = new Cuenta();
                        //         $insCuentaClass->setEmail($compraData->address);
                        //         $insCuentaClass->setUsuario($compraData->nombre);
                        //         $insCuentaClass->setClave($compraData->pass);
                        //         //curso
                        //         $insCuentaClass->setPerfil($compraData->curso);
                        //         //precio
                        //         $insCuentaClass->setVerificacion(0);
                        //         $insClienteClass->setCuenta((object) $insCuentaClass->__toString());
                        //         echo $inscliente->agregar_publico_cliente_otro_medio_controlador($insClienteClass);
                        //     } else {
                        //         echo (json_encode($respuestaValidar));
                        //     }
                        // } else {
                        //     $respuestaValidar->messageServer = 'No se encuentra el Curso seleccionado ';
                        //     echo (json_encode($respuestaValidar));
                        // }

                        header("HTTP/1.1 500");
                    }

                    break;
            }
        } else {
            header("HTTP/1.1 500");
        }
    } else {

        header("HTTP/1.1 500");

    }

} catch (Exception $e) {
    echo json_encode($e->getMessage());
}

function CreateUsuario($Data, $repuestaCulqi)
{
    if (isset($Data->telefono) && isset($Data->address) && isset($Data->nombre) && isset($Data->apellido) && isset($Data->profesion) && isset($Data->pass) && isset($Data->pais)) {
        //
        require_once './plugins/PHPMailer/src/PHPMailer.php';
        require_once './plugins/PHPMailer/src/SMTP.php';
        require_once './plugins/PHPMailer/src/Exception.php';
        require_once './classes/principal/usuario.php';
        require_once './api/security/auth.php';
        //
        $inscliente = new clienteControlador();
        $insClienteClass = new Cliente();
        $insClienteClass->setNombre($Data->nombre);
        $insClienteClass->setApellido($Data->apellido);
        $insClienteClass->setTelefono($Data->telefono);
        $insClienteClass->setOcupacion($Data->profesion);
        $insClienteClass->setPais($Data->pais);
        $insClienteClass->setFecha($Data->curso);
        $insCuentaClass = new Cuenta();
        $insCuentaClass->setEmail($Data->address);
        $insCuentaClass->setUsuario($Data->nombre);
        $insCuentaClass->setClave($Data->pass);
        $insCuentaClass->setVerificacion($Data->precio);
        $insClienteClass->setCuenta((object) $insCuentaClass->__toString());
        //

        echo $inscliente->agregar_publico_culqui_cliente_controlador($insClienteClass, $repuestaCulqi);
    } else {
        header("HTTP/1.1 401");
    }
}

function AgregarLibroToUsuario($Data, $repuestaCulqi)
{
    if (isset($Data->telefono) && isset($Data->address) && isset($Data->nombre) && isset($Data->apellido) && isset($Data->profesion) && isset($Data->pass) && isset($Data->pais)) {
        //
        require_once './plugins/PHPMailer/src/PHPMailer.php';
        require_once './plugins/PHPMailer/src/SMTP.php';
        require_once './plugins/PHPMailer/src/Exception.php';
        require_once './classes/principal/usuario.php';
        require_once './api/security/auth.php';
        //
        $inscliente = new clienteControlador();
        $insClienteClass = new Cliente();
        $insClienteClass->setNombre($Data->nombre);
        $insClienteClass->setApellido($Data->apellido);
        $insClienteClass->setTelefono($Data->telefono);
        $insClienteClass->setOcupacion($Data->profesion);
        $insClienteClass->setPais($Data->pais);
        $insClienteClass->setFecha($Data->curso);
        $insCuentaClass = new Cuenta();
        $insCuentaClass->setEmail($Data->address);
        $insCuentaClass->setUsuario($Data->nombre);
        $insCuentaClass->setClave($Data->pass);
        $insCuentaClass->setVerificacion($Data->precio);
        $insClienteClass->setCuenta((object) $insCuentaClass->__toString());
        //
        echo $inscliente->agregar_libro_publico_culqui_cliente_controlador($insClienteClass, $repuestaCulqi);
    } else {
        header("HTTP/1.1 401");
    }
}
function ValidaUsuario($Data)
{
    if (isset($Data->telefono) && isset($Data->address) && isset($Data->nombre) && isset($Data->apellido) && isset($Data->profesion) && isset($Data->pass) && isset($Data->pais)) {
        require_once './core/configAPP.php';
        require_once './classes/principal/cliente.php';
        require_once './classes/principal/cuenta.php';
        require_once './controladores/clienteControlador.php';

        //
        $inscliente = new clienteControlador();
        $insClienteClass = new Cliente();
        $insClienteClass->setNombre($Data->nombre);
        $insClienteClass->setApellido($Data->apellido);
        $insClienteClass->setTelefono($Data->telefono);
        $insClienteClass->setOcupacion($Data->profesion);
        $insClienteClass->setPais($Data->pais);
        $insCuentaClass = new Cuenta();
        $insCuentaClass->setEmail($Data->address);
        $insCuentaClass->setUsuario($Data->nombre);
        $insCuentaClass->setClave($Data->pass);
        $insCuentaClass->setVerificacion($Data->precio);
        $insClienteClass->setCuenta((object) $insCuentaClass->__toString());
        //

        return $inscliente->validar_cliente_controlador($insClienteClass);

    } else {
        return json_encode(array("messageServer" => "no tienes acceso",
            "beanPagination" => null));
    }
}
function ValidaUsuarioLibro($Data)
{
    if (isset($Data->telefono) && isset($Data->address) && isset($Data->nombre) && isset($Data->apellido) && isset($Data->profesion) && isset($Data->pass) && isset($Data->pais)) {

        require_once './core/configAPP.php';
        require_once './classes/principal/cliente.php';
        require_once './classes/principal/cuenta.php';
        require_once './controladores/clienteControlador.php';
        //

        $inscliente = new clienteControlador();
        $insClienteClass = new Cliente();
        $insClienteClass->setNombre($Data->nombre);
        $insClienteClass->setApellido($Data->apellido);
        $insClienteClass->setTelefono($Data->telefono);
        $insClienteClass->setOcupacion($Data->profesion);
        $insClienteClass->setPais($Data->pais);
        $insCuentaClass = new Cuenta();
        $insCuentaClass->setEmail($Data->address);
        $insCuentaClass->setUsuario($Data->nombre);
        $insCuentaClass->setClave($Data->pass);
        $insCuentaClass->setVerificacion($Data->precio);
        $insCuentaClass->setTipo($Data->curso);
        $insClienteClass->setCuenta((object) $insCuentaClass->__toString());
        //

        return $inscliente->validar_cliente_libro_controlador($insClienteClass);

    } else {
        return json_encode(array("messageServer" => "no tienes acceso",
            "beanPagination" => null));
    }
}
