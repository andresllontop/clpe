<!DOCTYPE html>
<html lang="en">
<?php
$peticionAJAX = false;
include './controladores/vistasControlador.php';
include './controladores/visitaControlador.php';
require_once './controladores/bitacoraControlador.php';
$insbitacora = new bitacoraControlador();
$peticionAJAX = true;
$vt = new vistasControlador();
$visit= new visitaControlador();
$vistasR = $vt->obtener_vistas_controlador();
$vistasBusca = $vt->obtener_vistas_controlador_parametro($vistasR);
session_start();
if (isset($_SESSION["tipo"])) {
    $tipo2 = $_SESSION["tipo"];
    if ($tipo2 == 'Cliente' && $vistasBusca['Vista'] == "Cliente") {
        $vista = "Cliente";
    } elseif ($tipo2 == 'Administrador' && $vistasBusca['Vista'] == "Admin") {
        $vista = "Admin";
    } else {
        if ($_SESSION["tipo"] == 'Cliente') {
            $dia = getdate();
            $da = [
                "Codigo" => $_SESSION["cuentaCodigo"],
                "Estado" => "Inactivo",
                "ID" => $_SESSION["ID"],
                "HoraFinal" => $dia['hours'] . " : " . $dia['minutes'] . " : " . $dia['seconds']
            ];
            $insbitacora->actualizar_bitacora_controlador($da); 
        }
        session_destroy();
        if ($vistasBusca['Vista'] == "Publico") {
            $vista = "Publico";
        } else {
            // echo ("home");
            $vista = "home";
        }
    }
} else {
    session_destroy();
    if ($vistasBusca['Vista'] == "Publico") {
        $vista = "Publico";
    } else {
        // echo ("home");
        $vista = "home";
    }
}
if ($vistasR == "404"):
    require_once './vistas/modulos/headpublico.php';
    ?>
								<body>
								    <?php
    require_once './vistas/404.php';
    echo("</body>");
else:
// header
    $peticionAJAX = false;
    switch ($vista) {
        case 'Publico':
        require_once './vistas/utilities/Plataforma.php';
            //Llamamos a la función, y ella hace todo :)
            $datosPlataforma=write_visita ();

            $vistResultado=$visit->agregar_visita_controlador($datosPlataforma);
            // echo($vistasBusca['URL']);
            if ($vistasBusca['URL']=="single") {
                require_once './vistas/contenidos/' . $vistasBusca['URL'] . '-view.php';
            } else {
                require_once './vistas/modulos/headpublico.php';
                echo("<body >");
                  //cabecera
            require_once './vistas/modulos/headerpublico.php';
            require_once './vistas/contenidos/' . $vistasBusca['URL'] . '-view.php';
            // footer
            require_once './vistas/modulos/footerpublico.php';
            }
            echo("</body>");
            ?>
                <?php 
            break;
        case 'Cliente':

        // if ($_SESSION["patrocinador"] =="no" && $vistasR =="red") {
        //     $vistasR="catalog";
        // }  
            require_once './vistas/modulos/head.php';
            ?>
					             <body >
					             <?php
             include 'vistas/modulos/navbar.php';
            // contenido
            require_once "./vistas/contenidos/" . $vistasR . "-view.php";
            // footer
            // require_once './vistas/modulos/footer.php';
            //  script
            require_once './vistas/modulos/script.php';
            echo("</body>");
            break;
        case 'Admin':
            require_once './vistas/modulos/headAdmin.php';
            ?>
					             <body>
					             <?php
    include './vistas/modulos/navlateral.php';
            // contenido
            require_once "./vistas/contenidos/" . $vistasR . "-view.php";
            // include './vistas/utilities/scrool-vertical.php';
            // footer
            // require_once './vistas/modulos/footer.php';
            //  script
            // require_once './vistas/modulos/scriptAdmin.php';
            break;
        default:
            require_once './vistas/modulos/headpublico.php';
            //cabecera
            require_once './vistas/modulos/headerpublico.php';
            // contenido
            require_once './vistas/contenidos/homeNew-view.php';
            // footer
            require_once './vistas/modulos/footerpublico.php';
            //  script
            require_once './vistas/modulos/scriptpublico.php';
            echo("</body>");
            break;
    }
endif;
?>
            
</html>