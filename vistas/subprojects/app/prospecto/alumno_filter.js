/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var beanPaginationAlumnoProspectoC;
var alumnoCSelected;
var beanRequestAlumnoProspectoC = new BeanRequest();

document.addEventListener('DOMContentLoaded', function () {
	//INICIALIZANDO VARIABLES DE SOLICITUD
	beanRequestAlumnoProspectoC.entity_api = 'prospectos';
	beanRequestAlumnoProspectoC.operation = 'paginate';
	beanRequestAlumnoProspectoC.type_request = 'GET';

	$('#modalCargandoAlumnoProspectoC').on('shown.bs.modal', function () {
		processAjaxAlumnoProspectoC();
	});
	$('#modalCargandoAlumnoProspectoC').on('hidden.bs.modal', function () {
		beanRequestAlumnoProspectoC.operation = 'paginate';
		beanRequestAlumnoProspectoC.type_request = 'GET';
	});
});

function processAjaxAlumnoProspectoC() {
	let parameters_pagination = '';
	switch (beanRequestAlumnoProspectoC.operation) {
		case 'paginate':
			parameters_pagination += '?filtro=';
			parameters_pagination += '&pagina=1';
			parameters_pagination += '&registros=500';
			break;
		default:
			parameters_pagination += '?filtro=';
			parameters_pagination += '&pagina=1';
			parameters_pagination += '&registros=500';

			break;
	}
	$.ajax({
		url:
			getHostAPI() +
			beanRequestAlumnoProspectoC.entity_api +
			'/' +
			beanRequestAlumnoProspectoC.operation +
			parameters_pagination,
		type: beanRequestAlumnoProspectoC.type_request,
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
			$('#modalCargandoAlumnoProspectoC').modal('hide');
			if (beanCrudResponse.messageServer !== null) {
				if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
					swal({
						title: 'Realizado',
						text: 'Acción realizada existosamente!',
						type: 'success',
						timer: 1200,
						showConfirmButton: false,
					});
					document.querySelector('#pageAlumnoProspectoC').value = 1;
					document.querySelector('#sizePageAlumnoProspectoC').value = 5;
					$('#ventanaModalManAlumno').modal('hide');
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
				beanPaginationAlumnoProspectoC = beanCrudResponse.beanPagination;

				toListAlumnoProspectoC(beanPaginationAlumnoProspectoC);
			}
		})
		.fail(function (jqXHR, textStatus, errorThrown) {
			$('#modalCargandoAlumnoProspectoC').modal('hide');
			showAlertErrorRequest();
		});
}

function toListAlumnoProspectoC(beanPagination) {
	document.querySelector('#txtAlumnoProspectoSelect').innerHTML = '';
	let row = '<option value="">SIN DEFINIR</option>';
	if (beanPagination.list.length == 0) {
		document.querySelector('#txtAlumnoProspectoSelect').innerHTML += row;
		return;
	}
	beanPagination.list.forEach((alumno) => {
		row += `<option value="${alumno.idprospecto}">${
			(alumno.cuenta != null ? alumno.cuenta + ' - ' : '') + alumno.nombre
		}</option>
`;
		// $('[data-toggle="tooltip"]').tooltip();
	});

	document.querySelector('#txtAlumnoProspectoSelect').innerHTML += row;
	if (document.querySelector('#txtAlumnoSelectUpdate')) {
		document.querySelector('#txtAlumnoSelectUpdate').innerHTML += row;
	}

	document.querySelector('#txtAlumnoProspectoSelect').value = '1';
	addEventsAlumnosProspectoC();
}

function addEventsAlumnosProspectoC() {
	document
		.querySelectorAll('.click-selection-alumno')
		.forEach(function (element) {
			element.onclick = function () {
				alumnoCSelected = findByAlumnoProspectoC(
					this.getAttribute('idsubTitulo')
				);
				if (alumnoCSelected != undefined) {
					if (document.querySelector('#txtSubTituloAlbum')) {
						switch (tipoSelected) {
							case 1:
								//ALBUM HASTA
								alumnoHastaSelected = alumnoCSelected;
								document.querySelector('#txtSubTituloAlbum').value =
									alumnoCSelected.codigo +
									' - ' +
									alumnoCSelected.nombre.toUpperCase();
								$('#ventanaModalSelectedAlumnoProspectoC').modal('hide');

								break;

							default:
								//ALBUM DESDE
								alumnoSelected = alumnoCSelected;
								document.querySelector('#txtSubTituloRecurso').value =
									alumnoCSelected.codigo +
									' - ' +
									alumnoCSelected.nombre.toUpperCase();
								$('#ventanaModalSelectedAlumnoProspectoC').modal('hide');
								break;
						}
						tipoSelected = 0;
					} else {
						alumnoSelected = alumnoCSelected;
						document.querySelector('#txtSubTituloRecurso').value =
							alumnoCSelected.codigo +
							' - ' +
							alumnoCSelected.nombre.toUpperCase();
						$('#ventanaModalSelectedAlumnoProspectoC').modal('hide');
					}
				}
			};
		});
}

function findByAlumnoProspectoC(cuenta) {
	let alumno_;
	beanPaginationAlumnoProspectoC.list.forEach((alumno) => {
		if (cuenta == alumno.cuenta) {
			alumno_ = alumno;
			return;
		}
	});
	return alumno_;
}
function validateFormAlumno() {
	if (limpiar_campo(document.querySelector('#txtNombreAlumno').value) == '') {
		showAlertTopEnd('warning', 'Por favor ingrese nombre');
		document.querySelector('#txtNombrenAlumno').focus();
		return false;
	}
	return true;
}
