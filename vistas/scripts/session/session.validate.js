let user_session;
let contextPah = getHostFrontEnd();
document.addEventListener("DOMContentLoaded", function () {
  if (Cookies.get("clpe_token") === undefined) {
    location.href = contextPah + "index";
  } else if (parseJwt(Cookies.get("clpe_token"))) {
    //CARGAMOS LOS DATOS DEL USUARIO
    user_session = Cookies.getJSON('clpe_user');
    let user = user_session;
    //SET DATOS USER
    document.querySelectorAll('.name-user-session').forEach(element => {
      element.innerHTML = getStringCapitalize(user.usuario.split(" ")[0].toLowerCase());

    });
    document.querySelectorAll('.name-type-user-session').forEach(element => {
      element.innerHTML = getStringTipoUsuario(user.tipo_usuario);
    });
    let url_foto;
    if (user.foto != "" && user.foto != null) {
      url_foto = getHostFrontEnd() + "adjuntos/clientes/" + user.foto;
    } else {
      url_foto = getHostFrontEnd() + "vistas/assets/img/userclpe.png";
    }
    setUrlFotoUserSession(url_foto);
    //ADD ITEMS MENU AL SIDEBAR
    addMenus(user);
  } else {
    closeSession();
  }

});

function getStringTipoUsuario(tipo_usuario) {
  let st = "";
  switch (tipo_usuario) {
    case 1:
      st = "Usuario CLPE";
      break;
    default:
      st = "User";
      break;
  }
  //st = getStringCapitalize(st.toLowerCase());
  return st;
}

function addMenus() {
  //console.log(usuario.tipo_usuario);
  switch (parseInt(user_session.tipo_usuario)) {
    case 1:
      //CLPE
      createHTML_CLPE((user_session.perfil).toString());
      break;
    case 2:
      //AULA
      createHTML_AULA(parseInt(user_session.tipo_usuario));
      break;

    default:
      break;

  }
}

function createHTML_CLPE(typeProfile) {
  let arrayTypeProfile = Array.from(typeProfile), row = "";

  //TODOS
  if (arrayTypeProfile[0] == 1) {
    row +=
      ` <li class="dt-side-nav__item">
            <a href="javascript:void(0);" class="dt-side-nav__link dt-side-nav__arrow" title="VISTA PUBLICO">
            <i class="zmdi zmdi-eye zmdi-hc-fw"></i>&nbsp;&nbsp; VISTA PUBLICO
            <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
             </a>
            <ul class="list-unstyled dt-side-nav__sub-menu" >
              <li class="dt-side-nav__item">
                <a href="javascript:void(0)" class="dt-side-nav__link dt-side-nav__arrow" title="ventas"> &nbsp;&nbsp;
                <i class="zmdi zmdi-assignment-o zmdi-hc-fw"></i>&nbsp;&nbsp; Vista Inicio
                <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
               </a>
                <ul class="list-unstyled dt-side-nav__sub-menu">
                  <li class="dt-side-nav__item">
                    <a href="${contextPah}app/inicio/noticia" class="dt-side-nav__link">&nbsp;&nbsp;&nbsp;&nbsp;<i
                        class="zmdi zmdi-collection-image-o zmdi-hc-fw">
                      </i>&nbsp;&nbsp; Nueva Imagen</a>
                  </li>
                  <li class="dt-side-nav__item">
                    <a href="${contextPah}app/inicio/video" class="dt-side-nav__link">&nbsp;&nbsp;&nbsp;&nbsp;<i
                        class="zmdi zmdi-videocam-switch zmdi-hc-fw"></i>&nbsp;&nbsp; Video</a>
                  </li>
                  <li class="dt-side-nav__item">
                    <a href="${contextPah}app/inicio/frase" class="dt-side-nav__link">&nbsp;&nbsp;&nbsp;&nbsp;<i
                        class="zmdi zmdi-comment-edit zmdi-hc-fw"></i>&nbsp;&nbsp; Frase</a>
                  </li>
                  <li class="dt-side-nav__item">
                    <a href="${contextPah}app/inicio/empresa" class="dt-side-nav__link">&nbsp;&nbsp;&nbsp;&nbsp;<i
                        class="zmdi zmdi-balance zmdi-hc-fw"></i>&nbsp;&nbsp; Pie de Página</a>
                  </li>
                </ul>
              </li>
              <li class="dt-side-nav__item">
                
                <a href="javascript:void(0)" class="dt-side-nav__link dt-side-nav__arrow" title="ventas">
                &nbsp;&nbsp;
                  <i class="zmdi zmdi-assignment-o zmdi-hc-fw"></i>&nbsp;&nbsp; Vista Curso
                  <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i></a>
                <ul class="list-unstyled dt-side-nav__sub-menu">
                  <li class="dt-side-nav__item">
                    <a href="${contextPah}app/curso" class="dt-side-nav__link">&nbsp;&nbsp;&nbsp;&nbsp;<i
                        class="zmdi zmdi-collection-image zmdi-hc-fw">
                      </i>&nbsp;&nbsp; Crear Curso</a>
                  </li>
                  <li class="dt-side-nav__item">
                  <a href="${contextPah}app/curso/objetivo" class="dt-side-nav__link">&nbsp;&nbsp;&nbsp;&nbsp;<i
                      class="zmdi zmdi-collection-image zmdi-hc-fw">
                    </i>&nbsp;&nbsp; Detalle del Curso</a>
                </li>
                  <li class="dt-side-nav__item">
                    <a href="${contextPah}app/curso/beneficio" class="dt-side-nav__link">&nbsp;&nbsp;&nbsp;&nbsp;<i
                        class="zmdi zmdi-case-check zmdi-hc-fw">
                      </i>&nbsp;&nbsp; Cuadro de Compra</a>
                  </li>
                  <li class="dt-side-nav__item">
                    <a href="${contextPah}app/curso/preguntas" class="dt-side-nav__link">&nbsp;&nbsp;&nbsp;&nbsp;<i
                        class="zmdi zmdi-collection-plus zmdi-hc-fw"></i>&nbsp;&nbsp; Preguntas Frecuentes</a>
                  </li>
                </ul>
              </li>
              <li class="dt-side-nav__item">
                <a href="javascript:void(0)" class="dt-side-nav__link dt-side-nav__arrow" title="ventas">
                &nbsp;&nbsp;
                <i class="zmdi zmdi-assignment-o zmdi-hc-fw"></i>&nbsp;&nbsp; Vista Nosotros
                <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
              </a>
                <ul class="list-unstyled dt-side-nav__sub-menu">
                  <li class="dt-side-nav__item">
                    <a href="${contextPah}app/nosotros/promotores" class="dt-side-nav__link"> &nbsp;&nbsp; &nbsp;&nbsp;<i
                        class="zmdi zmdi-accounts-list-alt zmdi-hc-fw"></i>&nbsp;&nbsp;
                      Nuestro Equipo</a>
                  </li>
                  <li class="dt-side-nav__item">
                    <a href="${contextPah}app/nosotros/videos" class="dt-side-nav__link">&nbsp;&nbsp; &nbsp;&nbsp;<i
                        class="zmdi zmdi-collection-video zmdi-hc-fw"></i>&nbsp;&nbsp; Videos</a>
                  </li>
                  <li class="dt-side-nav__item">
                    <a href="${contextPah}app/nosotros/historia" class="dt-side-nav__link">&nbsp;&nbsp;&nbsp;&nbsp;<i
                        class="zmdi zmdi-dns zmdi-hc-fw">
                      </i>&nbsp;&nbsp; Historia del CLPE</a>
                  </li>
                </ul>
              </li>

              <li class="dt-side-nav__item">
                <a href="javascript:void(0)" class="dt-side-nav__link dt-side-nav__arrow" title="Testimonios">
                &nbsp;&nbsp;
                  <i class="zmdi zmdi-assignment-o zmdi-hc-fw"></i>&nbsp;&nbsp; Testimonios
                  <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
                </a>
                <ul class="list-unstyled dt-side-nav__sub-menu">

                  <li class="dt-side-nav__item">
                    <a href="${contextPah}app/testimonios" class="dt-side-nav__link">&nbsp;&nbsp;&nbsp;&nbsp;<i
                        class="zmdi zmdi-videocam-switch zmdi-hc-fw"></i>&nbsp;&nbsp; Videos y Comentarios</a>
                  </li>
                </ul>
              </li>

              <li class="dt-side-nav__item">
                <a href="javascript:void(0)" class="dt-side-nav__link dt-side-nav__arrow" title="Blog">
                &nbsp;&nbsp;
                <i class="zmdi zmdi-assignment-o zmdi-hc-fw"></i>&nbsp;&nbsp; Blog
                <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
              </a>
                <ul class="list-unstyled dt-side-nav__sub-menu">

                  <li class="dt-side-nav__item">
                    <a href="${contextPah}app/blog" class="dt-side-nav__link">&nbsp;&nbsp;&nbsp;&nbsp;<i
                        class="zmdi zmdi-facebook zmdi-hc-fw"></i>&nbsp;&nbsp; Blog</a>
                  </li>
                  <li class="dt-side-nav__item">
                    <a  href="${contextPah}app/publicidad" class="dt-side-nav__link">&nbsp;&nbsp;&nbsp;&nbsp;<i
                        class="zmdi zmdi-android zmdi-hc-fw"></i>&nbsp;&nbsp; Publicidad</a>
                  </li>
                </ul>
              </li>


              <li class="dt-side-nav__item">
                
              <a href="javascript:void(0)" class="dt-side-nav__link dt-side-nav__arrow" title="Captura de Datos">
              &nbsp;&nbsp;
                <i class="zmdi zmdi-assignment-o zmdi-hc-fw"></i>&nbsp;&nbsp; Captura de Datos
                <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i></a>
              <ul class="list-unstyled dt-side-nav__sub-menu">
                <li class="dt-side-nav__item">
                  <a href="${contextPah}app/convocatoria/registro" class="dt-side-nav__link">&nbsp;&nbsp;&nbsp;&nbsp;<i
                      class="zmdi zmdi-collection-image zmdi-hc-fw">
                    </i>&nbsp;&nbsp; Cuestionarios</a>
                </li>
                <li class="dt-side-nav__item">
                  <a href="${contextPah}app/convocatoria/respuesta" class="dt-side-nav__link">&nbsp;&nbsp;&nbsp;&nbsp;<i
                      class="zmdi zmdi-case-check zmdi-hc-fw">
                    </i>&nbsp;&nbsp; Respuetsas</a>
                </li>
               
              </ul>
            </li>

            </ul>
          </li>
        `;
  }
  //TODOS
  if (arrayTypeProfile[0] == 1) {
    row +=
      ` <li class="dt-side-nav__item">
            <a href="javascript:void(0);" class="dt-side-nav__link dt-side-nav__arrow" title="VISTA ALUMNOS">
            <i class="zmdi zmdi-eye zmdi-hc-fw"></i>&nbsp;&nbsp; VISTA ALUMNOS
            <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
             </a>
            <ul class="list-unstyled dt-side-nav__sub-menu" >
              <li class="dt-side-nav__item">
                <a href="javascript:void(0)" class="dt-side-nav__link dt-side-nav__arrow" title="ventas">
                <i class="zmdi zmdi-assignment-o zmdi-hc-fw"></i>&nbsp;&nbsp;PIENSE Y HAGASE RICO
                <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
               </a>
                <ul class="list-unstyled dt-side-nav__sub-menu">
                <li class="dt-side-nav__item">
                <a href="javascript:void(0)" class="dt-side-nav__link dt-side-nav__arrow" title="PRIMER NIVEL">
                &nbsp;&nbsp;
                  <i class="zmdi zmdi-assignment-o zmdi-hc-fw"></i>&nbsp;&nbsp;PRIMER NIVEL
                  <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i></a>
                      <ul class="list-unstyled dt-side-nav__sub-menu">
                        <li class="dt-side-nav__item">
                        <a href="${contextPah}app/clientes/activo" class="dt-side-nav__link">&nbsp;<i
                          class="zmdi zmdi-accounts-alt zmdi-hc-fw "></i>&nbsp;ALUMNOS INSCRITOS N01
                        
                        </a>
                        </li>
                        <li class="dt-side-nav__item">
                        <a href="${contextPah}app/libros" class="dt-side-nav__link">&nbsp;<i
                            class="zmdi zmdi-book zmdi-hc-fw"></i>&nbsp;&nbsp;LIBRO</a>
                        </li>
                        <li class="dt-side-nav__item">
                        <a href="${contextPah}app/test/interno" class="dt-side-nav__link"><i class="zmdi zmdi-account-box zmdi-hc-fw"></i>&nbsp;
                        PREGUNTAS INTERNAS</a>
                        </li>
                        `;
  }
  //TAREAS
  if (arrayTypeProfile[2] == 1) {
    row +=
      `<li class="dt-side-nav__item">
                      <a href="${contextPah}app/lecciones" class="dt-side-nav__link"><i class="zmdi zmdi-account-box zmdi-hc-fw"></i>&nbsp;
                      TAREAS ALUMNOS N01</a>
                      </li>
                      `;
    row +=
      `<li class="dt-side-nav__item">
                                      <a href="${contextPah}app/lecciones/grafica" class="dt-side-nav__link"><i class="zmdi zmdi-account-box zmdi-hc-fw"></i>&nbsp;
                                     GRÁFICA DE TAREA ALUMNOS N01</a>
                                      </li>
                                      `;
    row +=
      `<li class="dt-side-nav__item">
                                      <a href="${contextPah}app/lecciones/finalizado" class="dt-side-nav__link"><i class="zmdi zmdi-account-box zmdi-hc-fw"></i>&nbsp;
                                      ALUMNOS TERMINADOS N01</a>
                                      </li>
                                      `;
  }
  //TODOS
  if (arrayTypeProfile[0] == 1) {
    row +=
      `
                      <li class="dt-side-nav__item">
                      <a href="${contextPah}app/test/general" class="dt-side-nav__link"><i class="zmdi zmdi-label zmdi-hc-fw"></i>&nbsp;
                        PREGUNTAS DE REFORSAMIENTO</a>
                      </li>
                      <li class="dt-side-nav__item">
                      <a href="${contextPah}app/recursos" class="dt-side-nav__link"><i class="zmdi zmdi-label zmdi-hc-fw"></i>&nbsp;
                        RECURSOS N01</a>
                      </li>
                      `;
  }
  //MENSAJERIA
  if (arrayTypeProfile[3] == 1) {
    row +=
      `
                      <li class="dt-side-nav__item">
                      <a href="${contextPah}app/mensajes" class="Noti-Mensaje dt-side-nav__link"><i class="zmdi zmdi-email zmdi-hc-fw"></i>&nbsp;MENSAJERÍA N01</a>
                      <!-- <span class="label label-danger pull-right label-mhover"
                      >7</span> -->
                      </li>
                      `;
  }
  //TODOS
  if (arrayTypeProfile[0] == 1) {
    row +=
      `
  <li class="dt-side-nav__item">
  <a href="${contextPah}app/album" class="Noti-Mensaje dt-side-nav__link"><i class="zmdi zmdi-label zmdi-hc-fw"></i>&nbsp;INICIO ALUMNOS N01</a>
  </li>
  <li class="dt-side-nav__item">
  <a href="${contextPah}app/certificados" class="Noti-Mensaje dt-side-nav__link"><i class="zmdi zmdi-label zmdi-hc-fw"></i>&nbsp;CERTIFICADOS N01</a>
  </li>
</ul>
</li>
</ul>
</li>
<li class="dt-side-nav__item">
<a href="${contextPah}app/notificaciones" class="dt-side-nav__link"><i class="zmdi zmdi-comment-alt-text zmdi-hc-fw"></i>&nbsp;&nbsp;NOTIFICACIONES</a>
</li>
<li class="dt-side-nav__item">
<a href="${contextPah}app/conferencias" class="dt-side-nav__link"><i class="zmdi zmdi-tv-alt-play zmdi-hc-fw"></i>&nbsp;&nbsp;CONFERENCIAS</a>
</li>
<li class="dt-side-nav__item">
<a href="${contextPah}app/clientes/inactivo" class="dt-side-nav__link"><i class="zmdi zmdi-label zmdi-hc-fw"></i>&nbsp;&nbsp;PERSONAS REGISTRADAS</a>
</li>
</ul>
</li>
`;
  }

  if (arrayTypeProfile[0] == 1) {
    row +=
      `<li class="dt-side-nav__item">
        <a href="javascript:void(0)" class="dt-side-nav__link dt-side-nav__arrow" title="CONTROL">
        <i class="zmdi zmdi-case zmdi-hc-fw"></i>&nbsp;&nbsp; INDICADORES
          <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
        </a>
        <ul class="list-unstyled dt-side-nav__sub-menu" >
          <!-- <li class="dt-side-nav__item">
          <a href=${contextPah}app/empresa"
            ><i class="zmdi zmdi-balance zmdi-hc-fw"></i>&nbsp;&nbsp;
            Datos de la Empresa</a
          >
        </li> -->
          <li class="dt-side-nav__item">
            <a href="${contextPah}app/visitas"><i
                class="zmdi zmdi-male-female zmdi-hc-fw"></i>&nbsp;&nbsp;
              VISITANTES</a>
          </li>
          <li class="dt-side-nav__item">
            <a href="${contextPah}app/bitacoras/activos"><i
                class="zmdi zmdi-accounts-alt zmdi-hc-fw"></i>&nbsp;&nbsp;ALUMNOS ACTIVOS</a>
          </li>
          <li class="dt-side-nav__item">
            <a href="${contextPah}app/bitacoras/inactivos"><i
                class="zmdi zmdi-accounts-outline zmdi-hc-fw"></i>&nbsp;&nbsp; ALUMNOS INACTIVOS</a>
          </li>
          <li class="dt-side-nav__item">
          <a href="${contextPah}app/publicos"><i
              class="zmdi zmdi-accounts-outline zmdi-hc-fw"></i>&nbsp;&nbsp; USUARIOS PÚBLICOS</a>
          </li>
          <li class="dt-side-nav__item">
            <a href="${contextPah}app/reportes"><i
                class="zmdi zmdi-trending-up zmdi-hc-fw"></i>&nbsp;&nbsp;Reportes</a>
          </li>
        </ul>
      </li>`;
  }
  if (arrayTypeProfile[4] == 1) {
    row +=
      `<li class="dt-side-nav__item">
                  <a href="javascript:void(0)" class="dt-side-nav__link dt-side-nav__arrow" title="CONTROL ADMINISTRATIVO">
                  <i class="zmdi zmdi-case zmdi-hc-fw"></i>&nbsp;&nbsp;CONTROL ADMINISTRATIVO
                    <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
                  </a>
                  <ul class="list-unstyled dt-side-nav__sub-menu" >
                    <li class="dt-side-nav__item">
                      <a href="${contextPah}app/personal"><i
                          class="zmdi zmdi-trending-up zmdi-hc-fw"></i>Nuevo Personal Administrativo</a>
                    </li>
                  </ul>
                </li>`;
  }
  if (arrayTypeProfile[5] == 1) {
    row +=
      `<li class="dt-side-nav__item">
                  <a href="javascript:void(0)" class="dt-side-nav__link dt-side-nav__arrow" title="CONTROL ECONÓMICO">
                  <i class="zmdi zmdi-case zmdi-hc-fw"></i>&nbsp;&nbsp;CONTROL ECONÓMICO
                    <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
                  </a>
                  <ul class="list-unstyled dt-side-nav__sub-menu" >
                    <li class="dt-side-nav__item">
                      <a href="${contextPah}app/economico"><i
                          class="zmdi zmdi-card zmdi-hc-fw"></i>Historial Económico</a>
                    </li>
                    <li class="dt-side-nav__item">
                    <a href="${contextPah}app/economico/general"><i
                        class="zmdi zmdi-collection-item zmdi-hc-fw"></i>General</a>
                  </li>
                  <li class="dt-side-nav__item">
                    <a href="${contextPah}app/economico/reporte"><i
                        class="zmdi zmdi-trending-up zmdi-hc-fw"></i>Reporte Excel</a>
                  </li>
                  </ul>
                </li>`;
  }
  document.querySelector("#menus_clpe").innerHTML = row;
  include_script(getHostFrontEnd() + "vistas/js/main-app.js?v=0.22");

}

function createHTML_AULA(typeProfile) {
  //SERVICIOS
  if (typeProfile == 2) {
    document.querySelector("#menus_aula").innerHTML +=
      `<li class="tooltips-general" style="float: left !important;">
        <a href="${contextPah}aula/index" class="py-2"><i class="zmdi zmdi-home zmdi-hc-fw"></i>INICIO</a>
      </li>
      <li class="tooltips-general" style="float: left !important;">
      <a href="${contextPah}aula/libro" class="py-2"><i class="zmdi zmdi-book zmdi-hc-fw"></i>CURSO</a>
      </li>
      <li class="tooltips-general" style="float: left !important;">
        <a href="${contextPah}aula/recursos" class="py-2"><i class="zmdi zmdi-folder-outline zmdi-hc-fw"></i>RECURSOS</a>
      </li>
     
      <li class="tooltips-general" style="float: left !important;">
        <a href="${contextPah}aula/lecciones" class="py-2"><i class="zmdi zmdi-videocam zmdi-hc-fw"></i>LECCIONES
          REALIZADAS</a>
      </li>
      <li class="tooltips-general" style="float: left !important;">
        <a href="${contextPah}aula/mensajes" class="py-2"><i class="zmdi zmdi-email zmdi-hc-fw"></i>MENSAJERÍA</a>
      </li>
      <li class="tooltips-general" style="float: left !important;">
        <a href="https://web.whatsapp.com/send?phone=${user_session.empresa.telefono}" class="py-2" class="telefono-usuario" target="blank">
          <i class="zmdi zmdi-whatsapp zmdi-hc-fw"></i> +${user_session.empresa.telefono}
        </a>
      </li>
      <li class="tooltips-general" style="float: left !important;">
      <a href="${contextPah}aula/conferencias" class="py-2"><i class="zmdi zmdi-cast zmdi-hc-fw"></i>CONFERENCIAS</a>
    </li> 
        `;
  }

  include_script(getHostFrontEnd() + "vistas/js/main.js?v=0.22");

}

function createHTML_ATE_ACTIVATION_ACCOUNT() {
  document.querySelector("#menus_clpe").innerHTML =
    `
        <!-- Menu Header -->
        <li class="dt-side-nav__item dt-side-nav__header">
            <span class="dt-side-nav__text">Dashboard</span>
        </li>
        <!-- /menu header -->
        <!-- Menu Item -->
        <li class="dt-side-nav__item">
            <a href="${contextPah}app/ate/index" class="dt-side-nav__link a-index-no" title="Inicio">
                <i class="icon icon-home icon-fw icon-lg"></i>
                <span class="dt-side-nav__text">Inicio</span>
            </a>
        </li>
        <!-- /menu item -->
    `;
}
