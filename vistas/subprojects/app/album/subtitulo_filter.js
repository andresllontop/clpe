/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var beanPaginationSubtituloC;
var subtituloCSelected;
var beanRequestSubtituloC = new BeanRequest();

document.addEventListener('DOMContentLoaded', function () {
	//INICIALIZANDO VARIABLES DE SOLICITUD
	beanRequestSubtituloC.entity_api = 'subtitulos';
	beanRequestSubtituloC.operation = 'paginate';
	beanRequestSubtituloC.type_request = 'GET';
});

function processAjaxSubtituloC(recurso = undefined) {
	$('#modalCargandoSubtituloC').modal('show');
	let parameters_pagination = '';
	switch (beanRequestSubtituloC.operation) {
		case 'paginate':
			parameters_pagination += '?filtro=&capitulo=' + capituloSelected.idtitulo;
			parameters_pagination += '&pagina=1';
			parameters_pagination += '&registros=500';
			break;
		default:
			parameters_pagination += '?filtro=';
			parameters_pagination += '?libro=' + libroExterno ? libroExterno : '';
			parameters_pagination += '&pagina=1';
			parameters_pagination += '&registros=500';

			break;
	}
	$.ajax({
		url:
			getHostAPI() +
			beanRequestSubtituloC.entity_api +
			'/' +
			beanRequestSubtituloC.operation +
			parameters_pagination,
		type: beanRequestSubtituloC.type_request,
		headers: {
			Authorization: 'Bearer ' + Cookies.get('clpe_token'),
		},

		cache: false,
		contentType: 'application/json; charset=UTF-8',
		data: null,
		cache: false,
		processData: false,
	})
		.done(function (beanCrudResponse) {
			$('#modalCargandoSubtituloC').modal('hide');
			if (beanCrudResponse.messageServer !== null) {
				if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
					swal({
						title: 'Realizado',
						text: 'Acción realizada existosamente!',
						type: 'success',
						timer: 1200,
						showConfirmButton: false,
					});
					document.querySelector('#pageSubtituloC').value = 1;
					document.querySelector('#sizePageSubtituloC').value = 5;
					$('#ventanaModalManSubtitulo').modal('hide');
				} else {
					swal({
						title: 'Error',
						text: beanCrudResponse.messageServer,
						type: 'error',
						timer: 1200,
						showConfirmButton: false,
					});
				}
			}
			if (beanCrudResponse.beanPagination !== null) {
				beanPaginationSubtituloC = beanCrudResponse.beanPagination;
				toListSubtituloC(beanPaginationSubtituloC);
				addAlbum(recurso);
			}
		})
		.fail(function (jqXHR, textStatus, errorThrown) {
			$('#modalCargandoSubtituloC').modal('hide');
			showAlertErrorRequest();
		});
}

function toListSubtituloC(beanPagination) {
	if (document.querySelector('#txtSubtituloDesde')) {
		document.querySelector('#txtSubtituloDesde').innerHTML = '';
	}
	document.querySelector('#txtSubtituloHasta').innerHTML = '';

	let row = '<option value="0">SIN DEFINIR</option>';
	if (beanPagination.list.length == 0) {
		document.querySelector('#txtSubtitulo').innerHTML += row;
		return;
	}
	beanPagination.list.forEach((subtitulo) => {
		row += `<option value="${subtitulo.codigo}">${subtitulo.codigo} - ${subtitulo.nombre}</option>
`;
		// $('[data-toggle="tooltip"]').tooltip();
	});
	if (document.querySelector('#txtSubtituloDesde')) {
		document.querySelector('#txtSubtituloDesde').innerHTML += row;
		if (clienteSelected) {
			document.querySelector('#txtSubtituloDesde').value =
				clienteSelected.subTitulo ? clienteSelected.subTitulo.codigo : '';
		}
	}

	document.querySelector('#txtSubtituloHasta').innerHTML += row;

	addEventsSubtitulosC();
}

function addEventsSubtitulosC() {
	document
		.querySelectorAll('.click-selection-subtitulo')
		.forEach(function (element) {
			element.onclick = function () {
				subtituloCSelected = findBySubtituloC(this.getAttribute('idsubTitulo'));
				if (subtituloCSelected != undefined) {
					if (document.querySelector('#txtSubTituloAlbum')) {
						switch (tipoSelected) {
							case 1:
								//ALBUM HASTA
								subtituloHastaSelected = subtituloCSelected;
								document.querySelector('#txtSubTituloAlbum').value =
									subtituloCSelected.codigo +
									' - ' +
									subtituloCSelected.nombre.toUpperCase();
								$('#ventanaModalSelectedSubtituloC').modal('hide');

								break;

							default:
								//ALBUM DESDE
								subtituloSelected = subtituloCSelected;
								document.querySelector('#txtSubTituloRecurso').value =
									subtituloCSelected.codigo +
									' - ' +
									subtituloCSelected.nombre.toUpperCase();
								$('#ventanaModalSelectedSubtituloC').modal('hide');
								break;
						}
						tipoSelected = 0;
					} else {
						subtituloSelected = subtituloCSelected;
						document.querySelector('#txtSubTituloRecurso').value =
							subtituloCSelected.codigo +
							' - ' +
							subtituloCSelected.nombre.toUpperCase();
						$('#ventanaModalSelectedSubtituloC').modal('hide');
					}
				}
			};
		});
}

function findBySubtituloC(codigo) {
	let subtitulo_;
	beanPaginationSubtituloC.list.forEach((subtitulo) => {
		if (codigo == subtitulo.codigo) {
			subtitulo_ = subtitulo;
			return;
		}
	});
	return subtitulo_;
}
function validateFormSubtitulo() {
	if (
		limpiar_campo(document.querySelector('#txtNombreSubtitulo').value) == ''
	) {
		showAlertTopEnd('warning', 'Por favor ingrese nombre');
		document.querySelector('#txtNombrenSubtitulo').focus();
		return false;
	}
	return true;
}
