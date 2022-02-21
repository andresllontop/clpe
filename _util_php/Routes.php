<?php
class Routes
{

    public function getResourceForContainerApp()
    {
        $routes = new Routes();
        //$routes = $routes->isURLValidate();
        $path_resource_server = SERVERURL . "vistas/subprojects/";
        $path_resource = "vistas/subprojects/";
        $path_scripts = "";
        $path_style = "";
        //VALIDAMOS SI ES UNA URL CORRECTA
        if ($routes->isURLValidate()) {
            $version_proyect = "1.32";
            /*
            $version_proyect = 1.0; -> antes del 02/09/2020
             */
            /*CAMBIAR EL CONTEXTO DE ACUERDO AL PROYECTO. DEJAR EN <</>> CUANDO ESTA EN PRODUCCIÓN */
            //$context = '/';
            $context = 'clpe./';
            //EXTRAEMOS EL CONTEXTO + EL PATH
            $context_path = $_SERVER['REQUEST_URI'];

            //EXTRAEMOS SOLO EL PATH DEL (CONTEXTO + PATH)
            $path = substr($context_path, strlen($context));
            //HACEMOS UN SPLIT PARA DEJAR EL PATH SIN PARAMETROS
            $values_path = explode("?", $path);
            //TOMAMOS LA PRIMERA PARTICIÓN
            $path = $values_path[0];
            //VERIFICAMOS SI EL ULTIMO CARACTER ES /
            if (substr($path, strlen($path) - 1, strlen($path)) == "/") {
                //EXTRAEMOS EL PATH SIN EL CARACTER PARA QUE VALIDE BIEN NUESTRA ITERACIÓN DE ABAJO
                $path = substr($path, 0, strlen($path) - 1);
            }
            /*
            AQUÍ ES DONDE VAMOS A CONFIGURAR NUESTRAS PAGINAS
            //EXAMPLE -> new BeanResource(path,path_resource);
            //array_push($list_pages, $resource);
             */
            $list_pages = array();

            /* ----MODULO DE ALUMNOS---- */
            //INDEX
            $resource = new BeanResource('aula/home', array($path_resource . 'aula/index/index.html', $path_resource . 'aula/notificacion/notificacion.html'), array($path_resource_server . 'aula/index/index.js?v=' . $version_proyect, $path_resource_server . 'aula/notificacion/notificacion.js?v=' . $version_proyect, 'plugins/flowplayer/js/flowplayer.min.js', 'plugins/flowplayer/js/flowplayer.hlsjs.light.min.js'), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect, $path_resource_server . 'aula/notificacion/style.scss?v=' . $version_proyect, SERVERURL . 'plugins/video/css/video.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //PERFIL
            $resource = new BeanResource('aula/perfil', array($path_resource . 'aula/perfil/perfil.html'), array($path_resource_server . 'aula/perfil/perfil.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            /*
            $resource = new BeanResource('aula/index', array($path_resource . 'aula/index/index.html'), array($path_resource . 'aula/index/index.js?v=' . $version_proyect, 'plugins/flowplayer/js/flowplayer.async.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect, 'plugins/flowplayer/css/flowplayer.css?v=' . $version_proyect));
            array_push($list_pages, $resource);*/
            //LIBRO
            $resource = new BeanResource('aula/libro', array($path_resource . 'aula/subtitulo/subtitulo.html', $path_resource . 'aula/subtitulo/cuestionario.html', $path_resource . 'aula/subtitulo/certificado.html'), array($path_resource_server . 'aula/subtitulo/class.js?v=' . $version_proyect, $path_resource_server . 'aula/subtitulo/subtitulo.js?v=' . $version_proyect, $path_resource_server . 'aula/subtitulo/respuesta.js?v=' . $version_proyect, $path_resource_server . 'aula/subtitulo/recorder.js?v=' . $version_proyect, $path_resource_server . 'aula/subtitulo/certificado.js?v=' . $version_proyect, SERVERURL . 'plugins/chart/dist/Chart.min.js'), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);

            /* $resource = new BeanResource('aula/libro', array($path_resource . 'aula/subtitulo/subtitulo.html', $path_resource . 'aula/subtitulo/cuestionario.html'), array($path_resource . 'aula/subtitulo/class.js?v=' . $version_proyect, $path_resource . 'aula/subtitulo/subtitulo.js?v=' . $version_proyect, $path_resource . 'aula/subtitulo/respuesta.js?v=' . $version_proyect, $path_resource . 'aula/subtitulo/recorder.js?v=' . $version_proyect, 'plugins/flowplayer/js/flowplayer.min.js?v=' . $version_proyect, 'plugins/flowplayer/js/asel.min.js?v=' . $version_proyect, 'plugins/flowplayer/js/hls.min.js?v=' . $version_proyect, 'plugins/flowplayer/js/playlist.min.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect, 'plugins/flowplayer/css/flowplayer.css?v=' . $version_proyect));
            array_push($list_pages, $resource);*/

            //RECURSOS
            $resource = new BeanResource('aula/recursos', array($path_resource . 'aula/recurso/recurso.html', $path_resource . 'aula/recurso/detalle/detalle.html'), array($path_resource_server . 'aula/recurso/recurso.js?v=' . $version_proyect, $path_resource_server . 'aula/recurso/detalle/detalle.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //LECCIONES REALIZADAS
            $resource = new BeanResource('aula/lecciones', array($path_resource . 'aula/leccion/tarea/tarea.html', $path_resource . 'aula/leccion/leccion.html', $path_resource . 'aula/leccion/respuesta/respuesta.html'), array($path_resource_server . 'aula/leccion/leccion.js?v=' . $version_proyect, $path_resource_server . 'aula/leccion/tarea/tarea.js?v=' . $version_proyect, $path_resource_server . 'aula/leccion/respuesta/respuesta.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect, SERVERURL . 'plugins/video/css/video.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //MENSAJE
            $resource = new BeanResource('aula/mensajes', array($path_resource . 'aula/mensaje/mensaje.html'), array($path_resource_server . 'aula/mensaje/mensaje.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //CONFERENCIA
            $resource = new BeanResource('aula/conferencias', array($path_resource . 'aula/conferencia/conferencia.html'), array($path_resource_server . 'aula/conferencia/conferencia.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect, $path_resource_server . 'aula/conferencia/style.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //CONFERENCIA
            $resource = new BeanResource('aula/index', array($path_resource . 'aula/curso/curso.html'), array($path_resource_server . 'aula/curso/curso.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect, $path_resource_server . 'publico/matricula/css/style.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //MATRICULA
            $resource = new BeanResource('aula/matricula', array($path_resource . 'aula/matricula/curso/curso.html'), array($path_resource_server . 'aula/matricula/curso/curso.js?v=' . $version_proyect, $path_resource_server . 'publico/matricula/js/modernizr.custom.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect, SERVERURL . 'vistas/publico/Dale/flag-icon-css/css/flag-icon.css?v=' . $version_proyect, $path_resource_server . 'aula/matricula/css/style.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //COMPRAR
            $resource = new BeanResource('aula/comprar/:', array($path_resource . 'aula/comprar/comprar.html'), array($path_resource_server . 'aula/comprar/comprar.js?v=' . $version_proyect, $path_resource_server . 'aula/matricula/js/modernizr.custom.js?v=' . $version_proyect, $path_resource_server . 'aula/comprar/style.css?v=' . $version_proyect, 'https://pocpaymentserve.s3.amazonaws.com/payform.min.js'), array('https://pocpaymentserve.s3.amazonaws.com/payform.min.css', SERVERURL . 'css/clpe.css?v=' . $version_proyect, $path_resource_server . 'aula/comprar/style.css?v=' . $version_proyect, SERVERURL . 'vistas/publico/Dale/flag-icon-css/css/flag-icon.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            /* ----MODULO DE MANAGER---- */
            //INDEX
            $resource = new BeanResource('app/index', array($path_resource . 'app/home/home.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/home/home.js?v=' . $version_proyect, $path_resource . 'app/footer/footer.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //PERFIL
            $resource = new BeanResource('app/perfil', array($path_resource . 'app/perfil/perfil.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/perfil/perfil.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //CLIENTES ACTIVOS
            $resource = new BeanResource('app/clientes/activo', array($path_resource . 'app/cliente/activo/cliente.html', $path_resource . 'app/footer/footer.html', $path_resource . 'app/libro/curso_c.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/cliente/activo/cliente.js?v=' . $version_proyect, $path_resource . 'app/libro/curso_c.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //CLIENTES INACTIVOS
            $resource = new BeanResource('app/clientes/inactivo', array($path_resource . 'app/cliente/inactivo/cliente.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/cliente/inactivo/cliente.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //PERSONAL
            $resource = new BeanResource('app/personal', array($path_resource . 'app/personal/personal.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/personal/personal.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //PERSONAL
            $resource = new BeanResource('app/vendedores', array($path_resource . 'app/vendedor/vendedor.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/vendedor/vendedor.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //CONVOCATORIA-CUESTIONARIO
            $resource = new BeanResource('app/convocatoria/registro', array($path_resource . 'app/convocatoria/cuestionario/convocatoria.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/convocatoria/cuestionario/convocatoria.js?v=' . $version_proyect, $path_resource . 'app/convocatoria/cuestionario/class.js?v=' . $version_proyect, 'vistas/js/editor.js', $path_resource . 'app/footer/footer.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //CONVOCATORIA-RESPUESTA
            $resource = new BeanResource('app/convocatoria/respuesta', array($path_resource . 'app/convocatoria/respuesta/respuesta.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/convocatoria/respuesta/respuesta.js?v=' . $version_proyect, $version_proyect, 'vistas/js/editor.js', $path_resource . 'app/footer/footer.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //TESTIMONIOS
            $resource = new BeanResource('app/testimonios', array($path_resource . 'app/testimonio/testimonio.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/testimonio/testimonio.js?v=' . $version_proyect, 'vistas/js/editor.js'), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //TESTIMONIOS - FRASE
            $resource = new BeanResource('app/testimonios/frase', array($path_resource . 'app/testimonio/frase/frase.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/testimonio/frase/frase.js?v=' . $version_proyect, 'vistas/js/editor.js'), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //RECURSOS
            $resource = new BeanResource('app/recursos', array($path_resource . 'app/recurso/recurso.html', $path_resource . 'app/recurso/detalle/detalle.html', $path_resource . 'app/recurso/subtitulo_filter.html', $path_resource . 'app/footer/footer.html', $path_resource . 'app/libro/curso_c.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/recurso/recurso.js?v=' . $version_proyect, $path_resource . 'app/recurso/detalle/detalle.js?v=' . $version_proyect, $path_resource . 'app/recurso/subtitulo_filter.js?v=' . $version_proyect, $path_resource . 'app/libro/curso_c.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //CUESTIONARIO
            $resource = new BeanResource('app/cuestionarios', array($path_resource . 'app/cuestionario/cuestionario.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/cuestionario/cuestionario.js?v=' . $version_proyect, $path_resource . 'app/libro/capitulo/capitulo_filter.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //TEST GENERAL
            $resource = new BeanResource('app/test/general', array($path_resource . 'app/test/general/test.html', $path_resource . 'app/libro/capitulo/capitulo_filter.html', $path_resource . 'app/test/general/subtitulo_filter.html', $path_resource . 'app/footer/footer.html', $path_resource . 'app/libro/curso_c.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/test/general/test.js?v=' . $version_proyect, $path_resource . 'app/test/general/detalle/detalle.js?v=' . $version_proyect, $path_resource . 'app/test/general/class.js?v=' . $version_proyect, $path_resource . 'app/libro/capitulo/capitulo_filter.js?v=' . $version_proyect, $path_resource . 'app/test/general/subtitulo_filter.js?v=' . $version_proyect, $path_resource . 'app/libro/curso_c.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //TEST INTERNO
            $resource = new BeanResource('app/test/interno', array($path_resource . 'app/test/interno/test.html', $path_resource . 'app/test/interno/subtitulo_filter.html', $path_resource . 'app/footer/footer.html', $path_resource . 'app/libro/curso_c.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/test/interno/test.js?v=' . $version_proyect, $path_resource . 'app/test/interno/class.js?v=' . $version_proyect, $path_resource . 'app/test/interno/detalle/detalle.js?v=' . $version_proyect, $path_resource . 'app/test/interno/subtitulo_filter.js?v=' . $version_proyect, $path_resource . 'app/libro/curso_c.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //MENSAJES
            $resource = new BeanResource('app/mensajes', array($path_resource . 'app/mensaje/mensaje.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/mensaje/mensaje.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //VIDEOS POR CADA LECCION
            $resource = new BeanResource('app/album', array($path_resource . 'app/album/album.html', $path_resource . 'app/album/subtitulo_filter.html', $path_resource . 'app/footer/footer.html', $path_resource . 'app/libro/curso_c.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/album/album.js?v=' . $version_proyect, $path_resource . 'app/album/subtitulo_filter.js?v=' . $version_proyect, $path_resource . 'app/libro/curso_c.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);

            //CITAS
            $resource = new BeanResource('app/cita', array($path_resource . 'app/cita/cita.html', $path_resource . 'app/cita/config/cita.html', $path_resource . 'app/cita/cronograma/cronograma.html', $path_resource . 'app/footer/footer.html', $path_resource . 'app/libro/curso_c.html', $path_resource . 'app/cita/cita.html', $path_resource . 'app/album/subtitulo_filter.html', $path_resource . 'app/cita/config/alumno_filter.html'), array($path_resource . 'app/cita/cita.js?v=' . $version_proyect, $path_resource . 'app/cita/config/cita.js?v=' . $version_proyect, $path_resource . 'app/cita/cronograma/cronograma.js?v=' . $version_proyect, $path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/libro/curso_c.js?v=' . $version_proyect, $path_resource . 'app/album/subtitulo_filter.js?v=' . $version_proyect, $path_resource . 'app/cita/config/alumno_filter.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);

            //PUBLICOS
            $resource = new BeanResource('app/publicos', array($path_resource . 'app/publico/publico.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/publico/publico.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //BITACORAS
            $resource = new BeanResource('app/bitacoras/activos', array($path_resource . 'app/bitacora/activo/bitacora.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/bitacora/activo/bitacora.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //BITACORAS
            $resource = new BeanResource('app/bitacoras/inactivos', array($path_resource . 'app/bitacora/inactivo/bitacora.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/bitacora/inactivo/bitacora.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //VISITAS
            $resource = new BeanResource('app/visitas', array($path_resource . 'app/bitacora/visita/visita.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/bitacora/visita/visita.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //LIBRO
            $resource = new BeanResource('app/libros', array($path_resource . 'app/libro/libro.html', $path_resource . 'app/libro/capitulo/capitulo.html', $path_resource . 'app/libro/parrafo/parrafo.html', $path_resource . 'app/libro/subtitulo/subtitulo.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/libro/libro.js?v=' . $version_proyect, $path_resource . 'app/libro/capitulo/capitulo.js?v=' . $version_proyect, $path_resource . 'app/libro/subtitulo/subtitulo.js?v=' . $version_proyect, $path_resource . 'app/libro/parrafo/parrafo.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);

            //LECCIONES
            $resource = new BeanResource('app/lecciones', array($path_resource . 'app/leccion/cliente.html', $path_resource . 'app/leccion/comentario/comentario.html', $path_resource . 'app/leccion/cuestionario/cuestionario.html', $path_resource . 'app/leccion/respuesta/respuesta.html', $path_resource . 'app/libro/curso_c.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/leccion/cliente.js?v=' . $version_proyect, $path_resource . 'app/leccion/comentario/comentario.js?v=' . $version_proyect, $path_resource . 'app/leccion/respuesta/respuesta.js?v=' . $version_proyect, $path_resource . 'app/leccion/cuestionario/cuestionario.js?v=' . $version_proyect, $path_resource . 'app/libro/curso_c.js?v=' . $version_proyect, 'plugins/chart/dist/Chart.min.js'), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);

            //INDICADORES
            /////TAREAS
            $resource = new BeanResource('app/indicadores/tarea', array($path_resource . 'app/indicadores/tarea/cliente.html', $path_resource . 'app/footer/footer.html', $path_resource . 'app/libro/curso_c.html', $path_resource . 'app/leccion/comentario/comentario.html', $path_resource . 'app/leccion/cuestionario/cuestionario.html', $path_resource . 'app/leccion/respuesta/respuesta.html'), array($path_resource . 'app/leccion/comentario/comentario.js?v=' . $version_proyect, $path_resource . 'app/leccion/respuesta/respuesta.js?v=' . $version_proyect, $path_resource . 'app/leccion/cuestionario/cuestionario.js?v=' . $version_proyect, $path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/indicadores/tarea/cliente.js?v=' . $version_proyect, 'plugins/chart/dist/Chart.min.js', $path_resource . 'app/libro/curso_c.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //LECCIONES finalizado
            $resource = new BeanResource('app/lecciones/finalizado', array($path_resource . 'app/leccion/finalizado/cliente.html', $path_resource . 'app/leccion/finalizado/comentario/comentario.html', $path_resource . 'app/leccion/finalizado/cuestionario/cuestionario.html', $path_resource . 'app/leccion/finalizado/respuesta/respuesta.html', $path_resource . 'app/footer/footer.html', $path_resource . 'app/libro/curso_c.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/leccion/finalizado/cliente.js?v=' . $version_proyect, $path_resource . 'app/leccion/finalizado/comentario/comentario.js?v=' . $version_proyect, $path_resource . 'app/leccion/finalizado/respuesta/respuesta.js?v=' . $version_proyect, $path_resource . 'app/leccion/finalizado/cuestionario/cuestionario.js?v=' . $version_proyect, $path_resource . 'app/libro/curso_c.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //CERTIFICADOS
            $resource = new BeanResource('app/certificados', array($path_resource . 'app/certificado/cliente.html', $path_resource . 'app/footer/footer.html', $path_resource . 'app/libro/curso_c.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/certificado/cliente.js?v=' . $version_proyect, $path_resource . 'app/libro/curso_c.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //CERTIFICADOS
            $resource = new BeanResource('app/notificaciones', array($path_resource . 'app/notificacion/notificacion.html', $path_resource . 'app/footer/footer.html', $path_resource . 'app/libro/curso_c.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/notificacion/notificacion.js?v=' . $version_proyect, $path_resource . 'app/libro/curso_c.js?v=' . $version_proyect, 'vistas/js/editor.js'), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //CONFERENCIAS
            $resource = new BeanResource('app/conferencias', array($path_resource . 'app/conferencia/conferencia.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/conferencia/conferencia.js?v=' . $version_proyect, 'vistas/js/editor.js'), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //REPORTES
            $resource = new BeanResource('app/reportes', array($path_resource . 'app/reportes/reporte.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/reportes/reporte.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            /* MODULO DE INICIO */
            //NOTICIA
            $resource = new BeanResource('app/inicio/noticia', array($path_resource . 'app/inicio/noticia/noticia.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/inicio/noticia/noticia.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //VIDEO
            $resource = new BeanResource('app/inicio/video', array($path_resource . 'app/inicio/video/video.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/inicio/video/video.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //FRASE
            $resource = new BeanResource('app/inicio/frase', array($path_resource . 'app/inicio/frase/frase.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/inicio/frase/frase.js?v=' . $version_proyect, 'vistas/js/editor.js'), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //TERMINO CONDICION
            $resource = new BeanResource('app/inicio/terminocondicion', array($path_resource . 'app/terminocondicion/terminocondicion.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/terminocondicion/terminocondicion.js?v=' . $version_proyect, 'vistas/js/editor.js'), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //empresa
            $resource = new BeanResource('app/inicio/empresa', array($path_resource . 'app/inicio/empresa/empresa.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/inicio/empresa/empresa.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            /* MODULO DE CURSO */
            //OBJETIVO
            $resource = new BeanResource('app/curso/objetivo', array($path_resource . 'app/curso/objetivo/objetivo.html', $path_resource . 'app/curso/curso_c.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/curso/objetivo/objetivo.js?v=' . $version_proyect, $path_resource . 'app/curso/curso_c.js?v=' . $version_proyect, 'vistas/js/editor.js'), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //BENEFICIO-LIBRO
            $resource = new BeanResource('app/curso/beneficio/libro', array($path_resource . 'app/curso/beneficio-libro/beneficio-libro.html', $path_resource . 'app/curso/curso_c.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/curso/beneficio-libro/beneficio-libro.js?v=' . $version_proyect, $path_resource . 'app/curso/curso_c.js?v=' . $version_proyect, 'vistas/js/editor.js'), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //BENEFICIO-CURSO
            $resource = new BeanResource('app/curso/beneficio', array($path_resource . 'app/curso/beneficio/beneficio.html', $path_resource . 'app/curso/curso_c.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/curso/beneficio/beneficio.js?v=' . $version_proyect, $path_resource . 'app/curso/curso_c.js?v=' . $version_proyect, 'vistas/js/editor.js'), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //PREGUNTAS FRECUENTES
            $resource = new BeanResource('app/curso/preguntas', array($path_resource . 'app/curso/pregunta/pregunta.html', $path_resource . 'app/curso/curso_c.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/curso/pregunta/pregunta.js?v=' . $version_proyect, $path_resource . 'app/curso/curso_c.js?v=' . $version_proyect, 'vistas/js/editor.js'), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            /* MODULO DE NOSOTROS */
            //NOTICIA
            $resource = new BeanResource('app/nosotros/noticia', array($path_resource . 'app/nosotros/noticia/noticia.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/nosotros/noticia/noticia.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //PROMOTORES
            $resource = new BeanResource('app/nosotros/promotores', array($path_resource . 'app/nosotros/promotor/promotor.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/nosotros/promotor/promotor.js?v=' . $version_proyect, 'vistas/js/editor.js'), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //PROMOTOR VIDEO
            $resource = new BeanResource('app/nosotros/videos', array($path_resource . 'app/nosotros/video/video.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/nosotros/video/video.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //HISTORIA
            $resource = new BeanResource('app/nosotros/historia', array($path_resource . 'app/nosotros/historia/historia.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/nosotros/historia/historia.js?v=' . $version_proyect, 'vistas/js/editor.js'), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            /* MODULO DE BLOG */
            //BLOG
            $resource = new BeanResource('app/blog', array($path_resource . 'app/blog/blog.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/blog/blog.js?v=' . $version_proyect, 'vistas/js/editor.js'), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //publicidad
            $resource = new BeanResource('app/publicidad', array($path_resource . 'app/publicitarias/publicitarias.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/publicitarias/publicitarias.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //ECONOMICO
            //////historial
            $resource = new BeanResource('app/economico', array($path_resource . 'app/economico/historial/economico.html', $path_resource . 'app/footer/footer.html', $path_resource . 'app/libro/curso_c.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/economico/historial/economico.js?v=' . $version_proyect, $path_resource . 'app/libro/curso_c.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);

            //////general
            $resource = new BeanResource('app/economico/general', array($path_resource . 'app/economico/general/general.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/economico/general/general.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //////reporte excel
            $resource = new BeanResource('app/economico/reporte', array($path_resource . 'app/economico/reporte/reporte.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/economico/reporte/reporte.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //CURSOS
            $resource = new BeanResource('app/curso', array($path_resource . 'app/curso/curso.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/curso/curso.js?v=' . $version_proyect), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //SOCIAL
            $resource = new BeanResource('app/convocatoria/publicidad', array($path_resource . 'app/convocatoria/social/social.html', $path_resource . 'app/footer/footer.html'), array($path_resource . 'app/footer/footer.js?v=' . $version_proyect, $path_resource . 'app/convocatoria/social/social.js?v=' . $version_proyect, 'vistas/js/editor.js'), array('css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);

            /* ----MODULO DE PUBLICO---- */
            //AUTH
            ///////RECOVERY
            $resource = new BeanResource('auth/recovery', array($path_resource . 'auth/recovery/recovery.html'), array($path_resource_server . 'auth/recovery/recovery.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect, $path_resource_server . 'auth/login/login.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            ///////restaurar contraseña email
            $resource = new BeanResource('auth/recovery/verificaty', array($path_resource . 'auth/recovery/verificaty/verificaty.html'), array($path_resource_server . 'auth/recovery/verificaty/verificaty.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect, $path_resource_server . 'auth/login/login.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            ///////CODIGO DE VERIFICACION
            $resource = new BeanResource('auth/verification', array($path_resource . 'auth/verificaty/verificaty.html'), array($path_resource_server . 'auth/verificaty/verificaty.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect));
            array_push($list_pages, $resource);

            ///////SIGNUP
            $resource = new BeanResource('auth/signup-xxx', array($path_resource . 'auth/signup/signup.html'), array($path_resource_server . 'auth/signup/signup.js?v=' . $version_proyect, $path_resource_server . 'auth/login/login.html', $path_resource_server . 'auth/login/login.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/footer.js?v=' . $version_proyect), array(SERVERURL . 'vistas/publico/Dale/flag-icon-css/css/flag-icon.min.css'));
            array_push($list_pages, $resource);
            //INDEX
            $resource = new BeanResource('index', array($path_resource . 'publico/home/home.html', $path_resource . 'auth/login/login.html', $path_resource . 'auth/register/register.html'), array($path_resource_server . 'publico/home/home.js?v=' . $version_proyect, $path_resource_server . 'auth/login/login.js?v=' . $version_proyect, $path_resource_server . 'publico/home/video.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/footer.js?v=' . $version_proyect, $path_resource_server . 'auth/register/register.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/js/chat.js?v=' . $version_proyect, 'plugins/video/js/video.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect, 'plugins/video/css/video.css?v=' . $version_proyect, $path_resource_server . 'auth/login/login.css?v=' . $version_proyect, SERVERURL . 'vistas/publico/Dale/flag-icon-css/css/flag-icon.css?v=' . $version_proyect, SERVERURL . 'plugins/video/css/video.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //INDEX
            $resource = new BeanResource('', array($path_resource . 'publico/home/home.html', $path_resource . 'auth/login/login.html', $path_resource . 'auth/register/register.html'), array($path_resource_server . 'publico/home/home.js?v=' . $version_proyect, $path_resource_server . 'auth/login/login.js?v=' . $version_proyect, $path_resource_server . 'publico/home/video.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/footer.js?v=' . $version_proyect, $path_resource_server . 'auth/register/register.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/js/chat.js?v=' . $version_proyect, 'plugins/video/js/video.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect, $path_resource_server . 'auth/login/login.css?v=' . $version_proyect, SERVERURL . 'vistas/publico/Dale/flag-icon-css/css/flag-icon.css?v=' . $version_proyect, SERVERURL . 'plugins/video/css/video.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //BLOG
            $resource = new BeanResource('blog', array($path_resource . 'publico/blog/blog.html', $path_resource . 'auth/login/login.html', $path_resource . 'auth/register/register.html'), array($path_resource_server . 'publico/blog/blog.js?v=' . $version_proyect, $path_resource_server . 'auth/login/login.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/footer.js?v=' . $version_proyect, $path_resource_server . 'auth/register/register.js?v=' . $version_proyect, $path_resource_server . 'publico/blog/js/jquery.mousewheel.js?v=' . $version_proyect, $path_resource_server . 'publico/blog/js/jquery.masonry.min.js?v=' . $version_proyect, $path_resource_server . 'publico/blog/js/jquery.gpCarousel.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/js/chat.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect, $path_resource_server . 'auth/login/login.css?v=' . $version_proyect, SERVERURL . 'vistas/publico/Dale/flag-icon-css/css/flag-icon.css?v=' . $version_proyect, $path_resource_server . 'publico/blog/css/style.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //BLOG DETALLE
            $resource = new BeanResource('blog/detalle/:', array($path_resource . 'publico/blog/comentario/comentario.html', $path_resource . 'auth/login/login.html', $path_resource . 'auth/register/register.html'), array($path_resource_server . 'publico/blog/comentario/comentario.js?v=' . $version_proyect, $path_resource_server . 'publico/blog/comentario/publicidad-detalle.js?v=' . $version_proyect, $path_resource_server . 'auth/login/login.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/footer.js?v=' . $version_proyect, $path_resource_server . 'auth/register/register.js?v=' . $version_proyect, SERVERURL . 'vistas/publico/Dale/js/owl.carousel.min.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/js/chat.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect, $path_resource_server . 'auth/login/login.css?v=' . $version_proyect, SERVERURL . 'vistas/publico/Dale/flag-icon-css/css/flag-icon.css?v=' . $version_proyect));
            array_push($list_pages, $resource);

            //TESTIMONIOS
            $resource = new BeanResource('testimonios', array($path_resource . 'publico/testimonio/testimonio.html', $path_resource . 'auth/login/login.html', $path_resource . 'auth/register/register.html'), array($path_resource_server . 'publico/testimonio/testimonio.js?v=' . $version_proyect, $path_resource_server . 'auth/login/login.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/footer.js?v=' . $version_proyect, $path_resource_server . 'auth/register/register.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/js/chat.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect, $path_resource_server . 'auth/login/login.css?v=' . $version_proyect, SERVERURL . 'vistas/publico/Dale/flag-icon-css/css/flag-icon.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //NOSOTROS
            $resource = new BeanResource('nosotros', array($path_resource . 'publico/promotor/promotor.html', $path_resource . 'auth/login/login.html', $path_resource . 'auth/register/register.html'), array($path_resource_server . 'publico/promotor/promotor.js?v=' . $version_proyect, $path_resource_server . 'publico/promotor/video.js?v=' . $version_proyect, $path_resource_server . 'publico/promotor/js/jquery.gallery.js?v=' . $version_proyect, $path_resource_server . 'publico/promotor/js/modernizr.custom.53451.js?v=' . $version_proyect, $path_resource_server . 'auth/login/login.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/footer.js?v=' . $version_proyect, $path_resource_server . 'auth/register/register.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/js/chat.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect, $path_resource_server . 'publico/promotor/css/style.css?v=' . $version_proyect, $path_resource_server . 'auth/login/login.css?v=' . $version_proyect, SERVERURL . 'vistas/publico/Dale/flag-icon-css/css/flag-icon.css?v=' . $version_proyect));
            array_push($list_pages, $resource);

            //CURSO
            $resource = new BeanResource('matricula', array($path_resource . 'publico/matricula/curso/curso.html', $path_resource . 'auth/login/login.html', $path_resource . 'auth/register/register.html'), array($path_resource_server . 'publico/matricula/curso/curso.js?v=' . $version_proyect, $path_resource_server . 'auth/login/login.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/footer.js?v=' . $version_proyect, $path_resource_server . 'auth/register/register.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/js/chat.js?v=' . $version_proyect, $path_resource_server . 'publico/matricula/js/modernizr.custom.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect, $path_resource_server . 'auth/login/login.css?v=' . $version_proyect, SERVERURL . 'vistas/publico/Dale/flag-icon-css/css/flag-icon.css?v=' . $version_proyect, $path_resource_server . 'publico/matricula/css/style.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //CURSO DETALLE
            $resource = new BeanResource('matricula/detalle/:', array($path_resource . 'publico/matricula/detalle/detalle.html', $path_resource . 'auth/login/login.html', $path_resource . 'auth/register/register.html'), array($path_resource_server . 'publico/matricula/detalle/detalle.js?v=' . $version_proyect, $path_resource_server . 'auth/login/login.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/footer.js?v=' . $version_proyect, $path_resource_server . 'auth/register/register.js?v=' . $version_proyect, $path_resource_server . 'publico/matricula/js/modernizr.custom.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect, $path_resource_server . 'auth/login/login.css?v=' . $version_proyect, SERVERURL . 'vistas/publico/Dale/flag-icon-css/css/flag-icon.css?v=' . $version_proyect, $path_resource_server . 'publico/matricula/css/book.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //COMPRA
            /*
            $resource = new BeanResource('comprar', array($path_resource . 'publico/comprar/comprar.html', $path_resource . 'auth/login/login.html', $path_resource . 'auth/register/register.html'), array($path_resource_server . 'auth/login/login.js?v=' . $version_proyect, $path_resource_server . 'publico/comprar/comprar.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/footer.js?v=' . $version_proyect, $path_resource_server . 'auth/register/register.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/js/chat.js?v=' . $version_proyect, $path_resource_server . 'publico/matricula/js/modernizr.custom.js?v=' . $version_proyect, $path_resource_server . 'publico/comprar/style.css?v=' . $version_proyect, 'https://static-content.vnforapps.com/elements/v1/payform.min.js'), array('https://static-content.vnforapps.com/elements/v1/payform.min.css', SERVERURL . 'css/clpe.css?v=' . $version_proyect, $path_resource_server . 'auth/login/login.css?v=' . $version_proyect, $path_resource_server . 'publico/comprar/style.css?v=' . $version_proyect, SERVERURL . 'vistas/publico/Dale/flag-icon-css/css/flag-icon.css?v=' . $version_proyect));
             */
            $resource = new BeanResource('comprar/:', array($path_resource . 'publico/comprar/comprar.html', $path_resource . 'auth/login/login.html', $path_resource . 'auth/register/register.html'), array($path_resource_server . 'auth/login/login.js?v=' . $version_proyect, $path_resource_server . 'publico/comprar/comprar.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/footer.js?v=' . $version_proyect, $path_resource_server . 'auth/register/register.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/js/chat.js?v=' . $version_proyect, $path_resource_server . 'publico/matricula/js/modernizr.custom.js?v=' . $version_proyect, $path_resource_server . 'publico/comprar/style.css?v=' . $version_proyect, 'https://pocpaymentserve.s3.amazonaws.com/payform.min.js'), array('https://pocpaymentserve.s3.amazonaws.com/payform.min.css', SERVERURL . 'css/clpe.css?v=' . $version_proyect, $path_resource_server . 'auth/login/login.css?v=' . $version_proyect, $path_resource_server . 'publico/comprar/style.css?v=' . $version_proyect, SERVERURL . 'vistas/publico/Dale/flag-icon-css/css/flag-icon.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //terminoscondiciones
            $resource = new BeanResource('terminoscondiciones', array($path_resource . 'publico/terminocondicion/terminocondicion.html', $path_resource . 'auth/login/login.html', $path_resource . 'auth/register/register.html'), array($path_resource_server . 'publico/terminocondicion/terminocondicion.js?v=' . $version_proyect, $path_resource_server . 'auth/login/login.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/footer.js?v=' . $version_proyect, $path_resource_server . 'auth/register/register.js?v=' . $version_proyect, $path_resource_server . 'publico/footer/js/chat.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect, $path_resource_server . 'auth/login/login.css?v=' . $version_proyect, SERVERURL . 'vistas/publico/Dale/flag-icon-css/css/flag-icon.css?v=' . $version_proyect));
            array_push($list_pages, $resource);

            $exists = false;

            foreach ($list_pages as $_resource) {
                if (substr($_resource->path, -1) == ":") {
                    // echo (substr($_resource->path, -1));
                    //echo (substr($_resource->path, 0, -2));
                    //    var_dump($routes->contiene_palabra($path, substr($_resource->path, 0, -2)));
                    if ($routes->contiene_palabra($path, substr($_resource->path, 0, -2)) == 1) {
                        $exists = true;
                        $path_resource = $_resource->path_resource;
                        $path_scripts = $_resource->path_scripts;
                        $path_style = $_resource->path_styles;
                        break;
                    }
                } else {
                    if ($path == $_resource->path) {
                        $exists = true;
                        $path_resource = $_resource->path_resource;
                        $path_scripts = $_resource->path_scripts;
                        $path_style = $_resource->path_styles;
                        break;
                    }
                }

            }

            /* foreach ($list_pages as $_resource) {

            if ($path == $_resource->path) {
            $exists = true;
            $path_resource = $_resource->path_resource;
            $path_scripts = $_resource->path_scripts;
            $path_style = $_resource->path_styles;
            break;
            }

            }
             */
            if (!$exists) {
                $path_resource = [$path_resource . 'modulo/404.html'];
            }

        } else {
            /*URL NO VALIDO */
            $path_resource = [$path_resource . 'modulo/404.html'];
        }
        $resources = new BeanResource($path, $path_resource, $path_scripts, $path_style);
        return $resources;
    }
    public function getResourceForContainerConvocatoriaApp()
    {
        $routes = new Routes();
        //$routes = $routes->isURLValidate();
        $path_resource = "vistas/subprojects/convocatoria/";
        $path_scripts = "";
        $path_style = "";
        //VALIDAMOS SI ES UNA URL CORRECTA
        if ($routes->isURLValidate()) {
            $version_proyect = "1.30";
            /*
            $version_proyect = 1.0; -> antes del 02/09/2020
             */
            /*CAMBIAR EL CONTEXTO DE ACUERDO AL PROYECTO. DEJAR EN <</>> CUANDO ESTA EN PRODUCCIÓN */
            //$context = '/';
            $context = 'clpe./';
            //EXTRAEMOS EL CONTEXTO + EL PATH
            $context_path = $_SERVER['REQUEST_URI'];

            //EXTRAEMOS SOLO EL PATH DEL (CONTEXTO + PATH)
            $path = substr($context_path, strlen($context));
            //HACEMOS UN SPLIT PARA DEJAR EL PATH SIN PARAMETROS
            $values_path = explode("?", $path);
            //TOMAMOS LA PRIMERA PARTICIÓN
            $path = $values_path[0];
            //VERIFICAMOS SI EL ULTIMO CARACTER ES /
            if (substr($path, strlen($path) - 1, strlen($path)) == "/") {
                //EXTRAEMOS EL PATH SIN EL CARACTER PARA QUE VALIDE BIEN NUESTRA ITERACIÓN DE ABAJO
                $path = substr($path, 0, strlen($path) - 1);
            }
            /*
            AQUÍ ES DONDE VAMOS A CONFIGURAR NUESTRAS PAGINAS
            //EXAMPLE -> new BeanResource(path,path_resource);
            //array_push($list_pages, $resource);
             */
            $list_pages = array();
            //social
            $resource = new BeanResource('public/social/:', array($path_resource . 'social/social.html'), array(SERVERURL . $path_resource . 'social/social.js?v=' . $version_proyect), array(SERVERURL . 'vistas/subprojects/publico/matricula/css/style.css?v=' . $version_proyect, SERVERURL . 'css/clpe.css?v=' . $version_proyect, SERVERURL . 'vistas/publico/Dale/flag-icon-css/css/flag-icon.css?v=' . $version_proyect, SERVERURL . $path_resource . 'social/style.css?v=' . $version_proyect));
            array_push($list_pages, $resource);
            //REGISTER
            $resource = new BeanResource('public/formulario/:', array($path_resource . 'registro/registro.html'), array(SERVERURL . $path_resource . 'registro/registro.js?v=' . $version_proyect), array(SERVERURL . 'css/clpe.css?v=' . $version_proyect, SERVERURL . $path_resource . 'registro/style.css?v=' . $version_proyect));
            array_push($list_pages, $resource);

            $exists = false;

            foreach ($list_pages as $_resource) {
                if (substr($_resource->path, -1) == ":") {
                    // echo (substr($_resource->path, -1));
                    //echo (substr($_resource->path, 0, -2));
                    //    var_dump($routes->contiene_palabra($path, substr($_resource->path, 0, -2)));
                    if ($routes->contiene_palabra($path, substr($_resource->path, 0, -2)) == 1) {
                        $exists = true;
                        $path_resource = $_resource->path_resource;
                        $path_scripts = $_resource->path_scripts;
                        $path_style = $_resource->path_styles;
                        break;
                    }
                } else {
                    if ($path == $_resource->path) {
                        $exists = true;
                        $path_resource = $_resource->path_resource;
                        $path_scripts = $_resource->path_scripts;
                        $path_style = $_resource->path_styles;
                        break;
                    }
                }

            }

            if (!$exists) {
                $path_resource = ['vistas/subprojects/modulo/404.html'];
            }

        } else {
            /*URL NO VALIDO */
            $path_resource = ['vistas/subprojects/modulo/404.html'];
        }
        $resources = new BeanResource($path, $path_resource, $path_scripts, $path_style);
        return $resources;
    }

    public function isURLValidate()
    {
        $url_actual = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if (filter_var($url_actual, FILTER_VALIDATE_URL)) {
            return true;
        } else {
            return false;
        }
    }
    public function subProject()
    {
        $routes = new Routes();
        if (strpos($routes->getResourceForContainerApp()->path, 'api') !== false) {
            include 'api.php';
        } else if (strpos($routes->getResourceForContainerApp()->path, 'aula') !== false) {
            include 'aula.php';
        } else if (strpos($routes->getResourceForContainerApp()->path, 'app') !== false) {
            include 'app.php';
        } else if (strpos($routes->getResourceForContainerApp()->path, 'auth') !== false) {
            include 'auth.php';
        } else if (strpos($routes->getResourceForContainerConvocatoriaApp()->path, 'public') !== false) {
            include 'convocatoria.php';
        } else {
            include 'publico.php';
        }

    }

    public function getResourceForContainerAuth()
    {
        $routes = new Routes();
        //$routes = $routes->isURLValidate();
        $path_resource = "view/subprojects/";
        $path_scripts = "";
        $path_style = "";
        //VALIDAMOS SI ES UNA URL CORRECTA
        if ($routes->isURLValidate()) {
            $version_proyect = "1.30";
            /*
            $version_proyect = 1.0; -> antes del 27/07/2019
             */
            /*CAMBIAR EL CONTEXTO DE ACUERDO AL PROYECTO. DEJAR EN <</>> CUANDO ESTA EN PRODUCCIÓN */
            $context = 'molino-frontend./';
            //EXTRAEMOS EL CONTEXTO + EL PATH
            $context_path = $_SERVER['REQUEST_URI'];
            //EXTRAEMOS SOLO EL PATH DEL (CONTEXTO + PATH)
            $path = substr($context_path, strlen($context));
            //HACEMOS UN SPLIT PARA DEJAR EL PATH SIN PARAMETROS
            $values_path = explode("?", $path);
            //TOMAMOS LA PRIMERA PARTICIÓN
            $path = $values_path[0];
            //VERIFICAMOS SI EL ULTIMO CARACTER ES /
            if (substr($path, strlen($path) - 1, strlen($path)) == "/") {
                //EXTRAEMOS EL PATH SIN EL CARACTER PARA QUE VALIDE BIEN NUESTRA ITERACIÓN DE ABAJO
                $path = substr($path, 0, strlen($path) - 1);
            }
            /*
            AQUÍ ES DONDE VAMOS A CONFIGURAR NUESTRAS PAGINAS
            //EXAMPLE -> new BeanResource(path,path_resource);
            //array_push($list_pages, $resource);
             */
            $list_pages = array();

            /* AUTH */
            //login
            $resource = new BeanResource('auth/login', array($path_resource . 'auth/login/login.html'), array($path_resource . 'auth/login/login.js?v=' . $version_proyect), array('css/style_molino.css?v=' . $version_proyect));
            array_push($list_pages, $resource);

            $exists = false;
            foreach ($list_pages as $_resource) {
                if ($path == $_resource->path) {
                    $exists = true;
                    $path_resource = $_resource->path_resource;
                    $path_scripts = $_resource->path_scripts;
                    $path_style = $_resource->path_styles;
                    break;
                }
            }
            if (!$exists) {
                $path_resource = ['zinclude_error/app_404.html'];
            }
        } else {
            /*URL NO VALIDO */
            $path_resource = ['zinclude_error/app_404.html'];
        }
        $resources = new BeanResource($path, $path_resource, $path_scripts, $path_style);
        return $resources;
    }

    public function getResourceForContainerApi()
    {
        $routes = new Routes();
        //$routes = $routes->isURLValidate();
        $path_resource = "api/";
        $path_scripts = "";
        $path_style = "";
        //VALIDAMOS SI ES UNA URL CORRECTA
        if ($routes->isURLValidate()) {
            $version_proyect = "1.32";
            /*
            $version_proyect = 1.0; -> antes del 01/09/2020
             */
            /*CAMBIAR EL CONTEXTO DE ACUERDO AL PROYECTO. DEJAR EN <</>> CUANDO ESTA EN PRODUCCIÓN */
            $context = 'clpe./';
            //$context = '/';
            //EXTRAEMOS EL CONTEXTO + EL PATH
            $context_path = $_SERVER['REQUEST_URI'];
            //EXTRAEMOS SOLO EL PATH DEL (CONTEXTO + PATH)
            $path = substr($context_path, strlen($context));
            //HACEMOS UN SPLIT PARA DEJAR EL PATH SIN PARAMETROS
            $values_path = explode("?", $path);
            //TOMAMOS LA PRIMERA PARTICIÓN
            $path = $values_path[0];
            //VERIFICAMOS SI EL ULTIMO CARACTER ES /
            if (substr($path, strlen($path) - 1, strlen($path)) == "/") {
                //EXTRAEMOS EL PATH SIN EL CARACTER PARA QUE VALIDE BIEN NUESTRA ITERACIÓN DE ABAJO
                $path = substr($path, 0, strlen($path) - 1);
            }
            /*
            AQUÍ ES DONDE VAMOS A CONFIGURAR NUESTRAS PAGINAS
            //EXAMPLE -> new BeanResource(path,path_resource);
            //array_push($list_pages, $resource);
             */
            $list_pages = array();

            /* AUTH */

            //login
            $resource = new BeanResource('authentication/login', array($path_resource . 'authentication/login.php'), array(), array());
            array_push($list_pages, $resource);
            //VERIFACTY
            $resource = new BeanResource('authentication/verificaty', array($path_resource . 'authentication/verificaty.php'), array(), array());
            array_push($list_pages, $resource);
            //register
            $resource = new BeanResource('authentication/register', array($path_resource . 'authentication/register.php'), array(), array());
            array_push($list_pages, $resource);
            //register
            $resource = new BeanResource('authentication/compraregister', array($path_resource . 'authentication/register.php'), array(), array());
            array_push($list_pages, $resource);
            //recovery
            $resource = new BeanResource('authentication/recovery', array($path_resource . 'authentication/recovery.php'), array(), array());
            array_push($list_pages, $resource);
            //recovery
            $resource = new BeanResource('authentication/passverificaty', array($path_resource . 'authentication/passrecovery.php'), array(), array());
            array_push($list_pages, $resource);

            //blog
            $resource = new BeanResource('api/blog', array($path_resource . 'blogAjax.php'), array(), array());
            array_push($list_pages, $resource);

            //economico
            $resource = new BeanResource('api/economico', array($path_resource . 'economicoAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //promotor
            $resource = new BeanResource('api/promotor', array($path_resource . 'promotorAjax.php'), array(), array());
            array_push($list_pages, $resource);

            //administrador
            $resource = new BeanResource('api/administrador', array($path_resource . 'administradorAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //cliente
            $resource = new BeanResource('api/cliente', array($path_resource . 'clienteAjax.php'), array(), array());
            array_push($list_pages, $resource);

            //albunes
            $resource = new BeanResource('api/album', array($path_resource . 'albumAjax.php'), array(), array());
            array_push($list_pages, $resource);

            //bitacora
            $resource = new BeanResource('api/bitacora', array($path_resource . 'bitacoraAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //capitulo
            $resource = new BeanResource('api/capitulo', array($path_resource . 'capituloAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //contactar
            $resource = new BeanResource('api/contactar', array($path_resource . 'contactarAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //cuenta
            $resource = new BeanResource('api/cuenta', array($path_resource . 'cuentaAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //cuestionario/cliente
            $resource = new BeanResource('api/cuestionario/cliente', array($path_resource . 'cuestionarioclienteAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //curso
            $resource = new BeanResource('api/cursos', array($path_resource . 'cursoAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //declaracion
            $resource = new BeanResource('api/declaracion', array($path_resource . 'declaracionAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //empresa
            $resource = new BeanResource('api/empresa', array($path_resource . 'empresaAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //libro
            $resource = new BeanResource('api/libro', array($path_resource . 'libroAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //librocuenta
            $resource = new BeanResource('api/book/cuenta', array($path_resource . 'librocuentaAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //certificado

            $resource = new BeanResource('api/certificado', array($path_resource . 'certificadoAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //noticia
            $resource = new BeanResource('api/noticias', array($path_resource . 'noticiaAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //pagar
            $resource = new BeanResource('api/pagar', array($path_resource . 'pagarAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //patrocinadoreconomico
            $resource = new BeanResource('api/patrocinador/economico', array($path_resource . 'patrocinadoreconomicoAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //publico
            $resource = new BeanResource('api/publico', array($path_resource . 'publicoAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //testimonio
            $resource = new BeanResource('api/testimonio', array($path_resource . 'testimonioAjax.php'), array(), array());
            array_push($list_pages, $resource);

            //VIDEOS PROMOTOR
            $resource = new BeanResource('api/video/promotor', array($path_resource . 'videos/promotor/videoAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //VIDEOS INICIO
            $resource = new BeanResource('api/video/inicio', array($path_resource . 'videos/inicio/videoAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //LIBROS
            $resource = new BeanResource('api/libros', array($path_resource . 'videos/libroAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //CAPITULOS
            $resource = new BeanResource('api/capitulos', array($path_resource . 'capituloAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //SUBTITULOS
            $resource = new BeanResource('api/subtitulos', array($path_resource . 'subcapituloAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //PARRAFOS
            $resource = new BeanResource('api/parrafos', array($path_resource . 'videos/subcapitulo/videoAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //comentarios
            $resource = new BeanResource('api/comentarios', array($path_resource . 'videos/cliente/videoAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //cuestionarios
            $resource = new BeanResource('api/cuestionarios', array($path_resource . 'cuestionarioAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //test
            $resource = new BeanResource('api/test', array($path_resource . 'testAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //RECURSOS
            $resource = new BeanResource('api/recursos', array($path_resource . 'recursoAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //DETALLES RECURSOS
            $resource = new BeanResource('api/detalles/recursos', array($path_resource . 'detallerecursoAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //DETALLES RECURSOS
            $resource = new BeanResource('api/detalles/test', array($path_resource . 'detalletestAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //EMPRESA
            $resource = new BeanResource('api/empresa', array($path_resource . 'empresaAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //SUBITEM
            $resource = new BeanResource('api/subitems', array($path_resource . 'subitemAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //MENSAJES
            $resource = new BeanResource('api/mensajes', array($path_resource . 'mensajeAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //VISITAS
            $resource = new BeanResource('api/visitas', array($path_resource . 'visitaAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //LECCIONES
            $resource = new BeanResource('api/lecciones', array($path_resource . 'leccionAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //LECCIONES
            $resource = new BeanResource('api/respuestas', array($path_resource . 'respuestaAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //TAREAS
            $resource = new BeanResource('api/tareas', array($path_resource . 'tareaAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //REPORTES
            $resource = new BeanResource('api/alumno/reporte', array($path_resource . 'reporteAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //COMPRAR
            $resource = new BeanResource('api/compra', array($path_resource . 'pago/index.php'), array(), array());
            array_push($list_pages, $resource);
            //convocatoria
            $resource = new BeanResource('api/convocatoria', array($path_resource . 'convocatoriaAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //notificacion
            $resource = new BeanResource('api/notificacion', array($path_resource . 'notificacionAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //vendedor
            $resource = new BeanResource('api/vendedor', array($path_resource . 'vendedorAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //conferencia
            $resource = new BeanResource('api/conferencia', array($path_resource . 'conferenciaAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //niubiz
            $resource = new BeanResource('api/niubiz', array($path_resource . 'pago/sesionNiubiz.php'), array(), array());
            array_push($list_pages, $resource);
            //social
            $resource = new BeanResource('api/social', array($path_resource . 'socialAjax.php'), array(), array());
            array_push($list_pages, $resource);
            //cita
            $resource = new BeanResource('api/cita', array($path_resource . 'citaAjax.php'), array(), array());
            array_push($list_pages, $resource);

            $resource = new BeanResource('api/ajuste/cita', array($path_resource . 'ajustecitaAjax.php'), array(), array());
            array_push($list_pages, $resource);

            $exists = false;

            foreach ($list_pages as $_resource) {
                if (strpos($path, $_resource->path) !== false) {

                    $exists = true;
                    $path_resource = $_resource->path_resource;
                    $path_scripts = $_resource->path_scripts;
                    $path_style = $_resource->path_styles;
                    break;
                }
            }
            if (!$exists) {
                $path_resource = [];
            }
        } else {
            /*URL NO VALIDO */
            $path_resource = [];
        }
        $resources = new BeanResource($path, $path_resource, $path_scripts, $path_style);
        return $resources;
    }

    public function contiene_palabra($texto, $palabra)
    {
        return preg_match('*\b' . $palabra . '\b*i', $texto);
    }

}
