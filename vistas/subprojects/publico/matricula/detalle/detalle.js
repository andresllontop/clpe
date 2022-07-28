var beanPaginationCurso;
var cursoSelected;
var beanRequestCurso = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
	document.body.style.background =
		'#f7f7f7 url(' +
		getHostFrontEnd() +
		'vistas/subprojects/publico/blog/img/pattern.png) repeat top left';
	beanRequestCurso.entity_api = 'subitems';
	beanRequestCurso.operation = 'paginate';
	beanRequestCurso.type_request = 'GET';

	let GETsearch = window.location.pathname;
	if (GETsearch.split('/').length == 5) {
		if (/^[0-9.]*$/.test(GETsearch.split('/')[4])) {
			cursoSelected = { idcurso: GETsearch.split('/')[4] };

			let fetOptions = {
				headers: {
					'Content-Type': 'application/json; charset=UTF-8',
					//"Authorization": "Bearer " + token
				},
				method: 'GET',
			};
			/* PROMESAS LLAMAR A LAS API*/
			circleCargando.containerOcultar = $(
				document.querySelector('#htmlCargandoMatricula')
			);
			circleCargando.container = $(
				document.querySelector('#htmlCargandoMatricula').parentElement
			);
			circleCargando.createLoader();
			circleCargando.toggleLoader('show');
			Promise.all([
				fetch(
					getHostAPI() +
						beanRequestCurso.entity_api +
						'/' +
						beanRequestCurso.operation +
						'?tipo=0&idcurso=' +
						cursoSelected.idcurso +
						'&pagina=1&registros=100',
					fetOptions
				),
				fetch(
					getHostAPI() + 'empresa/obtener' + '?filtro=&pagina=1&registros=1',
					fetOptions
				),
			])
				.then((responses) => Promise.all(responses.map((res) => res.json())))
				.then((json) => {
					circleCargando.toggleLoader('hide');
					if (json[0].beanPagination !== null) {
						beanPaginationCurso = json[0].beanPagination;
						listaCurso(beanPaginationCurso);
					}
					if (json[1].beanPagination !== null) {
						beanPaginationFooterPublico = json[1].beanPagination;
						listaFooterPublico(beanPaginationFooterPublico);
					}
				})
				.catch((err) => {
					showAlertErrorRequest();
				});
			/* */
		} else {
			window.location.href = getHostFrontEnd() + 'matricula';
		}
	} else {
		window.location.href = getHostFrontEnd() + 'matricula';
	}

	$('#btnPreguntasFrecuentes').click(function () {
		$('#modalPreguntaCurso').modal('show');
	});

	/* */
});

function listaCurso(beanPagination) {
	if (beanPagination.countFilter == 0) {
		return;
	}
	let row1 = '',
		row3 = '',
		contador = 0,
		contadorTipo = findByBeneficioLibro(2),
		contadorDeg = 130,
		contadorDism = 18;

	beanPagination.list.forEach((curso) => {
		if (curso.precio == undefined) {
			if (curso.tipo == 1) {
				row1 += curso.detalle;
			} else if (curso.tipo == 2) {
				contadorDism += 2;
				document.querySelector(
					'.page'
				).innerHTML += `<li class="" data-next="${contadorDeg}" data-anterior="${contadorDism}" style="-webkit-transform: rotateY(-${contadorDism}deg);
                    -moz-transform: rotateY(-${contadorDism}deg);
                    transform: rotateY(-${contadorDism}deg);">
                      <h3 class="text-center"> <button class="btn btn-purple-o py-1 px-2 border-radius">
                      ${contadorTipo--}
                    </button></h3>
                      <p>${curso.detalle}</p>
                    </li>`;
				contadorDeg += 3;
			} else if (curso.tipo == 3) {
				row3 += `<li class=" mb-1 ${
					contador == 0 ? 'active pulse-2 m-2' : ''
				} border" style="border-color: #b21aff;"><span><a href="#">${
					curso.titulo
				}</a></span><article><p>${curso.detalle}</p></article></li>`;
				contador++;
			} else if (curso.tipo == 4) {
				if (curso.video == '' || curso.video == null) {
					document.querySelector(
						'#txtImagenCurso'
					).innerHTML = `<img class="w-100" src="${
						getHostFrontEnd() + 'adjuntos/libros/' + curso.imagen
					}">`;
				} else {
					document.querySelector(
						'#txtImagenCurso'
					).innerHTML = `<iframe style="width:100%;height:500px;" src="https://www.youtube-nocookie.com/embed/${curso.video}" frameborder="0"  allowfullscreen></iframe>`;
				}
				document.querySelector('#txtObjetivoCurso').innerHTML = curso.detalle;
			}
		} else {
			if (document.querySelector('.precio-curso')) {
				document.querySelector(
					'.precio-curso'
				).innerHTML = `<span style="font-weight: 100;color: #8c8b8b;">$</span>${curso.precio}<span style="font-size: 25px;
  font-weight: 700;">USD</span><em class="mx-2" style="text-decoration: line-through; font-size: 18px; color: #ccc;">$ ${curso.descuento}</em>`;
			}
			document.querySelector(
				'#imagenLibro'
			).innerHTML = `<img  width="100%" height="100%" src="${
				getHostFrontEnd() + 'adjuntos/libros/' + curso.imagenlibro
			}">`;

			document.querySelector('#comprarCurso').innerHTML =
				curso.tipo == 1
					? '<i class="zmdi zmdi-shopping-cart"></i> Comprar Ahora'
					: '<i class="zmdi zmdi-comment-video"></i> Vía Zoom';
			if (curso.tipo == 1) {
				document
					.querySelector('#comprarCurso')
					.parentElement.setAttribute(
						'href',
						getHostFrontEnd() + 'comprar/' + curso.idcurso
					);
				document
					.querySelector('#comprarCurso')
					.parentElement.setAttribute('target', getHostFrontEnd() + '_blank');
			} else {
				$('#comprarCurso').click(function () {
					$('#ventanaModalPrecioZoom').modal('show');
				});
			}
			document.querySelector('#tituloCurso').innerHTML = curso.titulo;
		}
	});
	document.querySelector('#txtBeneficioCurso').innerHTML = row1;
	document.querySelector('#txtPreguntasCurso').innerHTML = row3;
	if (document.querySelector('li .page').lastElementChild) {
		document
			.querySelector('li .page')
			.lastElementChild.classList.add('page-active');
	}
	eventoLibro();
	eventosAcordion();
}
function addEventsButtonsAdmin() {
	$('.editar-Admin').each(function (index, value) {
		$(this).click(function () {
			var indice = $(
				this.parentElement.parentElement.parentElement.parentElement
					.parentElement.parentElement.parentElement
			).attr('numero');
			$('#ventanaModalcursotest').modal('show');
			$('#Modal-curso').html(
				`< div
      class="flowplayer-embed-container"
      style = "position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width:100%;" >
        <iframe
          style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
          webkitAllowFullScreen mozallowfullscreen allowfullscreen
          src="${indice}"
          title="0" byline="0" portrait="0"
          width="100%" height="100%"
          frameborder="0"
          allow="autoplay">
        </iframe>
      </div > `
			);
		});
	});
}
function eventosAcordion() {
	/************************
	 ****** Accordion ******
	 *************************/
	$('ul.accordion').each(function () {
		if ($(this).height() > 0) {
			$(this).css('height', $(this).height() + 'px');
		}

		$(this)
			.children('li')
			.each(function () {
				var a = $(this).children('span').children('a');
				if ($(this).hasClass('active'))
					$(a).append('<i class="fa fa-chevron-down"></i>');
				else $(a).append('<i class="fa fa-chevron-right"></i>');

				var parent = this;
				$(a).click(function (e) {
					e.preventDefault();
					if (!$(parent).hasClass('active')) {
						$('ul.accordion li.active article').slideUp(
							250,
							'easeOutExpo',
							function () {
								$(this).parent('li').removeClass('active pulse-2 m-2');
								$(this)
									.siblings('span')
									.children('a')
									.children('i')
									.removeClass('fa-chevron-down')
									.addClass('fa-chevron-right');
							}
						);
						$(parent)
							.addClass('active pulse-2 m-2')
							.children('article')
							.slideDown(250, 'easeOutExpo');
						$(a)
							.children('i')
							.removeClass('fa-chevron-right')
							.addClass('fa-chevron-down');
					}
				});
			});
	});
}
function eventoLibro() {
	let otroDegra = 0,
		$pageItem;
	document.querySelector('#VerNext').onclick = () => {
		document.querySelector('.book').classList.add('book-active');
		$pageItem = document.querySelector('li .page-active');
		if ($pageItem.previousElementSibling) {
			otroDegra = parseInt($pageItem.dataset.next);
			$pageItem.setAttribute(
				'style',
				'-webkit-transform: rotateY(-' +
					otroDegra +
					'deg);-moz-transform: rotateY(-' +
					otroDegra +
					'deg);transform: rotateY(-' +
					otroDegra +
					'deg);background-color: white; color: transparent;-webkit-transition:-webkit-transform 1.5s,background-color .6s,color .6s;transition:transform 1.5s, background-color .6s,color .6s;-moz-transition:-moz-transform 1.5s, background-color .6s,color .6s'
			);
			$pageItem.classList.remove('page-active');
			$pageItem.previousElementSibling.classList.add('page-active');
		}
	};

	document.querySelector('#VerAnterior').onclick = () => {
		$pageItem = document.querySelector('li .page-active');
		if ($pageItem.nextElementSibling) {
			otroDegra = parseInt($pageItem.dataset.anterior);
			$pageItem.nextElementSibling.setAttribute(
				'style',
				'-webkit-transform: rotateY(-' +
					otroDegra +
					'deg);-moz-transform: rotateY(-' +
					otroDegra +
					'deg);transform: rotateY(-' +
					otroDegra +
					'deg); -webkit-transition:-webkit-transform 1.5s,color 5s;transition:transform 1.5s, color 5s;-moz-transition:-moz-transform 1.5s, color 5s'
			);
			$pageItem.classList.remove('page-active');
			$pageItem.nextElementSibling.classList.add('page-active');
		} else {
			document.querySelector('.book').classList.remove('book-active');
		}
	};
}
function findByBeneficioLibro(tipo = 0) {
	let contador = 0;
	beanPaginationCurso.list.find((PromotorBeneficioLibro) => {
		if (parseInt(tipo) == parseInt(PromotorBeneficioLibro.tipo)) {
			contador++;
		}
	});
	return contador;
}
