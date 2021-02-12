<script src="<?php echo SERVERURL; ?>vistas/app-js/nav-lateral.js"></script>
<div class="navbar-lateral full-reset">
  <div class="visible-xs font-movile-menu mobile-menu-button"></div>
  <div
    class="full-reset container-menu-movile custom-scroll-containers
    mCustomScrollbar _mCS_1 mCS-autoHide"
  >
    <div
      id="mCSB_1"
      class="mCustomScrollBox mCS-dark-thin mCSB_vertical mCSB_inside"
      tabindex="0"
      style="max-height: none;"
    >
      <div
        id="mCSB_1_container"
        class="mCSB_container"
        style="position: relative; top: 0px; left: 0px; height:100%;"
        dir="ltr"
      >
        <div class="logo full-reset all-tittles text-center">
          <i
            class="visible-xs zmdi zmdi-close pull-left mobile-menu-button"
            style="line-height: 55px; cursor: pointer; padding: 0 10px; margin-left: 7px;"
          ></i>
          Sistema de Libro
        </div>
        <div
          class="full-reset"
          style="background-color:#2B3D51; padding: 10px 0; color:#fff;"
        >
          <figure>
            <img
              src="<?php echo SERVERURL; ?>adjuntos/logoHeader.jpg"
              alt="Biblioteca"
              class="img-responsive center-box mCS_img_loaded"
              style="width:55%; border-radius:70px;"
            />
          </figure>
          <p class="text-center" style="padding-top: 15px;">
            <?php echo (COMPANY); ?>
          </p>
        </div>
        <div class="full-reset nav-lateral-list-menu">
          <ul class="list-unstyled">

            <li>
              <div class="dropdown-menu-button">
                <i class="zmdi zmdi-eye zmdi-hc-fw"></i>&nbsp;&nbsp; Vista
                Publica
                <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
              </div>
              <ul class="list-unstyled" style="display: none;">
                <li>
                  <div class="dropdown-menu-button">
                    &nbsp;&nbsp;
                    <i class="zmdi zmdi-assignment-o zmdi-hc-fw"></i
                    >&nbsp;&nbsp; Vista Inicio
                    <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
                  </div>
                  <ul class="list-unstyled">
                    <li>
                      <a href="<?php echo SERVERURL; ?>noticia"
                        >&nbsp;&nbsp;&nbsp;&nbsp;<i
                          class="zmdi zmdi-dns zmdi-hc-fw"
                        >
                        </i
                        >&nbsp;&nbsp; Nueva Imagen</a
                      >
                    </li>
                    <li>
                      <a href="<?php echo SERVERURL; ?>videoInicio"
                        >&nbsp;&nbsp;&nbsp;&nbsp;<i
                          class="zmdi zmdi-videocam-switch zmdi-hc-fw"
                        ></i
                        >&nbsp;&nbsp; Video</a
                      >
                    </li>
                    <li>
                      <a href="<?php echo SERVERURL; ?>frase"
                        >&nbsp;&nbsp;&nbsp;&nbsp;<i
                          class="zmdi zmdi-videocam-switch zmdi-hc-fw"
                        ></i
                        >&nbsp;&nbsp; Frase</a
                      >
                    </li>
                    <li>
                      <a href="<?php echo SERVERURL; ?>institution"
                        >&nbsp;&nbsp;&nbsp;&nbsp;<i
                          class="zmdi zmdi-balance zmdi-hc-fw"
                        ></i
                        >&nbsp;&nbsp; Pie de Página</a
                      >
                    </li>
                  </ul>
                </li>
                <li>
                  <div class="dropdown-menu-button">
                    &nbsp;&nbsp;
                    <i class="zmdi zmdi-assignment-o zmdi-hc-fw"></i
                    >&nbsp;&nbsp; Vista Curso
                    <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
                  </div>
                  <ul class="list-unstyled">
                  <li>
                      <a href="<?php echo SERVERURL; ?>objetivo"
                        >&nbsp;&nbsp;&nbsp;&nbsp;<i
                          class="zmdi zmdi-dns zmdi-hc-fw"
                        >
                        </i
                        >&nbsp;&nbsp; Objetivo e Imagen</a
                      >
                    </li>
                    <li>
                      <a href="<?php echo SERVERURL; ?>BeneficioCurso"
                        >&nbsp;&nbsp;&nbsp;&nbsp;<i
                          class="zmdi zmdi-dns zmdi-hc-fw"
                        >
                        </i
                        >&nbsp;&nbsp; Beneficios del Curso</a
                      >
                    </li>
                    <li>
                      <a href="<?php echo SERVERURL; ?>BeneficioLibro"
                        >&nbsp;&nbsp;&nbsp;&nbsp;<i
                          class="zmdi zmdi-dns zmdi-hc-fw"
                        >
                        </i
                        >&nbsp;&nbsp; Beneficios del Libro</a
                      >
                    </li>
                    <li>
                      <a href="<?php echo SERVERURL; ?>frecuentePregunta"
                        >&nbsp;&nbsp;&nbsp;&nbsp;<i
                          class="zmdi zmdi-collection-plus zmdi-hc-fw"
                        ></i
                        >&nbsp;&nbsp; Preguntas Frecuentes</a
                      >
                    </li>
                
                  </ul>
                </li>
                <li>
                  <div class="dropdown-menu-button">
                    &nbsp;&nbsp;
                    <i class="zmdi zmdi-assignment-o zmdi-hc-fw"></i
                    >&nbsp;&nbsp; Vista Nosotros
                    <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
                  </div>
                  <ul class="list-unstyled">
                    
                    <li>
                      <a href="<?php echo SERVERURL; ?>promotor"
                        > &nbsp;&nbsp; &nbsp;&nbsp;<i class="zmdi zmdi-account zmdi-hc-fw"></i>&nbsp;&nbsp;
                        Nuestro Equipo</a
                      >
                    </li>
                    <li>
                      <a href="<?php echo SERVERURL; ?>videos"
                        >&nbsp;&nbsp; &nbsp;&nbsp;<i class="zmdi zmdi-collection-video zmdi-hc-fw"></i
                        >&nbsp;&nbsp; Videos</a
                      >
                    </li>
                    <li>
                      <a href="<?php echo SERVERURL; ?>historia"
                        >&nbsp;&nbsp;&nbsp;&nbsp;<i
                          class="zmdi zmdi-dns zmdi-hc-fw"
                        >
                        </i
                        >&nbsp;&nbsp; Historia del CLPE</a
                      >
                    </li>
                    <!-- <li>
                      <a href="testimonioCliente"
                        >&nbsp;&nbsp;&nbsp;&nbsp;<i
                          class="zmdi zmdi-collection-plus zmdi-hc-fw"
                        ></i
                        >&nbsp;&nbsp; Nuevo Testimonio</a
                      >
                    </li> -->
                    <!-- <li>
                      <a href="testimonioAlbum"
                        >&nbsp;&nbsp;&nbsp;&nbsp;<i
                          class="zmdi zmdi-collection-image zmdi-hc-fw"
                        ></i
                        >&nbsp;&nbsp; Nueva Galeria</a
                      >
                    </li> -->

                    <!-- <li>
                      <a href="videoNosotros"
                        >&nbsp;&nbsp;&nbsp;&nbsp;<i
                          class="zmdi zmdi-videocam-switch zmdi-hc-fw"
                        ></i
                        >&nbsp;&nbsp; Video</a
                      >
                    </li> -->
                  </ul>
                </li>
               
                <!-- <li>
                  <div class="dropdown-menu-button">
                    &nbsp;&nbsp;
                    <i class="zmdi zmdi-assignment-o zmdi-hc-fw"></i
                    >&nbsp;&nbsp; Vista Matricula
                    <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
                  </div>
                  <ul class="list-unstyled">
                    <li>
                      <a href="noticia"
                        >&nbsp;&nbsp;&nbsp;&nbsp;<i
                          class="zmdi zmdi-dns zmdi-hc-fw"
                        >
                        </i
                        >&nbsp;&nbsp; Nuevo Slider</a
                      >
                    </li>
                    <li>
                      <a href="testimonioAlbum"
                        >&nbsp;&nbsp;&nbsp;&nbsp;<i
                          class="zmdi zmdi-videocam-switch zmdi-hc-fw"
                        ></i
                        >&nbsp;&nbsp; Video</a
                      >
                    </li>
                  </ul>
                </li> -->

                <!-- <li>
                  <div class="dropdown-menu-button">
                    &nbsp;&nbsp;
                    <i class="zmdi zmdi-assignment-o zmdi-hc-fw"></i
                    >&nbsp;&nbsp; Vista Promotores
                    <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
                  </div>
                  <ul class="list-unstyled">
                    <li>
                      <a href="promotor"
                        > &nbsp;&nbsp; &nbsp;&nbsp;<i class="zmdi zmdi-account zmdi-hc-fw"></i>&nbsp;&nbsp;
                        Nuevo Promotor</a
                      >
                    </li>
                    <li>
                      <a href="videos"
                        >&nbsp;&nbsp; &nbsp;&nbsp;<i class="zmdi zmdi-collection-video zmdi-hc-fw"></i
                        >&nbsp;&nbsp; Videos</a
                      >
                    </li>
                  </ul>
                </li> -->

                <li>
                  <div class="dropdown-menu-button">
                    &nbsp;&nbsp;
                    <i class="zmdi zmdi-assignment-o zmdi-hc-fw"></i
                    >&nbsp;&nbsp; Testimonios
                    <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
                  </div>
                  <ul class="list-unstyled">
                  
                    <li>
                      <a href="<?php echo SERVERURL; ?>testimonioCliente"
                        >&nbsp;&nbsp;&nbsp;&nbsp;<i
                          class="zmdi zmdi-videocam-switch zmdi-hc-fw"
                        ></i
                        >&nbsp;&nbsp; Videos y Comentarios</a
                      >
                    </li>
                  </ul>
                </li>

                <li>
                  <div class="dropdown-menu-button">
                    &nbsp;&nbsp;
                    <i class="zmdi zmdi-assignment-o zmdi-hc-fw"></i
                    >&nbsp;&nbsp; Blog
                    <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
                  </div>
                  <ul class="list-unstyled">
                  
                    <li>
                      <a href="<?php echo SERVERURL; ?>blogCliente"
                        >&nbsp;&nbsp;&nbsp;&nbsp;<i
                          class="zmdi zmdi-facebook zmdi-hc-fw"
                        ></i
                        >&nbsp;&nbsp; Blog</a
                      >
                    </li>
                    <li>
                      <a href="<?php echo SERVERURL; ?>publicidad"
                        >&nbsp;&nbsp;&nbsp;&nbsp;<i
                          class="zmdi zmdi-android zmdi-hc-fw"
                        ></i
                        >&nbsp;&nbsp; Publicidad</a
                      >
                    </li>
                  </ul>
                </li>

                



              </ul>
            </li>

            <li>
              <div class="dropdown-menu-button">
                <i class="zmdi zmdi-eye zmdi-hc-fw"></i>&nbsp;&nbsp; Vista
                Usuarios
                <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
              </div>
              <ul class="list-unstyled" style="display: none;">
                <li>
                  <a href="<?php echo SERVERURL; ?>userclienteActivo"
                    >&nbsp;<i class="zmdi zmdi-accounts zmdi-hc-fw"></i
                    >&nbsp;Alumnos Matriculados
                    <!-- <span class="label label-danger pull-right label-mhover"
                          >7</span> -->
                  </a>
                </li>
                <li>
                  <a href="<?php echo SERVERURL; ?>usercliente"
                    >&nbsp;<i class="zmdi zmdi-accounts zmdi-hc-fw"></i
                    >&nbsp;Alumnos No Matriculados
                    <!-- <span class="label label-danger pull-right label-mhover"
                          >7</span> -->
                  </a>
                </li>
                <li>
                <li>
                      <a href="<?php echo SERVERURL; ?>book"
                        >&nbsp;<i
                          class="zmdi zmdi-book zmdi-hc-fw"
                        ></i
                        >&nbsp;&nbsp;Libro</a
                      >
                    </li>

                </li>
                    <li>
                      <a href="<?php echo SERVERURL; ?>listlecciones"
                        ><i
                          class="zmdi zmdi-account-box zmdi-hc-fw"
                        ></i
                        >&nbsp; Tarea de Alumnos</a
                      >
                    </li>

                <li>
                  <a href="<?php echo SERVERURL; ?>restriccion"
                    ><i class="zmdi zmdi-account-box zmdi-hc-fw"></i
                    >&nbsp; Recursos Para Alumnos</a
                  >
                </li>

                <li>
                 <a href="<?php echo SERVERURL; ?>cuestionario"
                        ><i
                          class="zmdi zmdi-edit zmdi-hc-fw"
                        ></i
                        >&nbsp; Nuevo Cuestionario</a
                      >
                </li>
                <li>
                  <a href="<?php echo SERVERURL; ?>mensaje" class="Noti-Mensaje"></a>
                </li>
              </ul>
            </li>

            <!-- <li>
              <div class="dropdown-menu-button">
                <i class="zmdi zmdi-eye zmdi-hc-fw"></i>&nbsp;&nbsp; Vista
                Patrocinador
                <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
              </div>
              <ul class="list-unstyled" style="display: none;">
                <li>
                      <a href="userpatrocinador"
                        >&nbsp;&nbsp;&nbsp;&nbsp;<i
                          class="zmdi zmdi-dns zmdi-hc-fw"
                        >
                        </i
                        >&nbsp;&nbsp; Lista de Patrocinador</a
                      >
                </li>
              </ul>
            </li> -->

            <li>
              <div class="dropdown-menu-button">
                <i class="zmdi zmdi-case zmdi-hc-fw"></i>&nbsp;&nbsp; Control
                <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
              </div>
              <ul class="list-unstyled" style="display: none;">
                <!-- <li>
                  <a href="<?php echo SERVERURL; ?>institution"
                    ><i class="zmdi zmdi-balance zmdi-hc-fw"></i>&nbsp;&nbsp;
                    Datos de la Empresa</a
                  >
                </li> -->
                <li>
                  <a href="<?php echo SERVERURL; ?>visita"
                    ><i class="zmdi zmdi-male-female zmdi-hc-fw"></i
                    >&nbsp;&nbsp; Visitantes</a
                  >
                </li>
                <li>
                  <a href="<?php echo SERVERURL; ?>bitacoraActivo"
                    ><i class="zmdi zmdi-male-female zmdi-hc-fw"></i
                    >&nbsp;&nbsp; Usuarios Activos</a
                  >
                </li>
                <li>
                  <a href="<?php echo SERVERURL; ?>bitacora"
                    ><i class="zmdi zmdi-male-female zmdi-hc-fw"></i
                    >&nbsp;&nbsp; Usuarios Inactivos</a
                  >
                </li>
                <div class="dropdown-menu-button">
                  &nbsp;
                  <i class="zmdi zmdi-account-add zmdi-hc-fw"></i>&nbsp;&nbsp;
                  Registro de usuarios
                  <i class="zmdi zmdi-chevron-down pull-right zmdi-hc-fw"></i>
                </div>
                <ul class="list-unstyled" style="display: none;">
                  <li>
                    <a href="<?php echo SERVERURL; ?>admin"
                      >&nbsp;&nbsp;&nbsp;&nbsp;<i
                        class="zmdi zmdi-male-female zmdi-hc-fw"
                      ></i
                      >&nbsp;&nbsp; Nuevo personal administrativo</a
                    >
                  </li>
                  <li>
                    <a href="<?php echo SERVERURL; ?>usuarioPublico"
                      >&nbsp;&nbsp;&nbsp;&nbsp;<i
                        class="zmdi zmdi-male-female zmdi-hc-fw"
                      ></i
                      >&nbsp;&nbsp; Lista de usuarios Publicos</a
                    >
                  </li>
                </ul>
                <li>
                  <a href="<?php echo SERVERURL; ?>report"
                    ><i class="zmdi zmdi-trending-up zmdi-hc-fw"></i
                    >&nbsp;&nbsp;Reportes</a
                  >
                </li>
              </ul>
            </li>


            <!-- <li><a href="report"><i class="zmdi zmdi-trending-up zmdi-hc-fw"></i>&nbsp;&nbsp; Reportes y estadísticas</a></li>
                        <li><a href="advancesettings"><i class="zmdi zmdi-wrench zmdi-hc-fw"></i>&nbsp;&nbsp; Configuraciones avanzadas</a></li> -->
          </ul>
        </div>
      </div>
    </div>
  </div>
  <!-- /srcroll -->
  <div
    id="mCSB_1_scrollbar_vertical"
    class="mCSB_scrollTools mCSB_1_scrollbar mCS-dark-thin mCSB_scrollTools_vertical"
    style="display: block;"
  >
    <a
      href="#"
      class="mCSB_buttonUp"
      oncontextmenu="return false;"
      style="display: block;"
    >
    </a>
    <div class="mCSB_draggerContainer"></div>
    <a
      href="#"
      class="mCSB_buttonDown"
      oncontextmenu="return false;"
      style="display: block;"
    ></a>
  </div>

  <!-- /scrooll -->
</div>
