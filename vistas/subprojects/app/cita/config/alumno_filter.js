/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var beanPaginationAlumnoC;
var alumnoCSelected;
var beanRequestAlumnoC = new BeanRequest();

document.addEventListener('DOMContentLoaded', function () {
	//INICIALIZANDO VARIABLES DE SOLICITUD
	beanRequestAlumnoC.entity_api = 'cliente';
	beanRequestAlumnoC.operation = 'conteo';
	beanRequestAlumnoC.type_request = 'GET';

	$('#modalCargandoAlumnoC').on('shown.bs.modal', function () {
		processAjaxAlumnoC();
	});
	$('#modalCargandoAlumnoC').on('hidden.bs.modal', function () {
		beanRequestAlumnoC.operation = 'conteo';
		beanRequestAlumnoC.type_request = 'GET';
	});
});

function processAjaxAlumnoC() {
	let parameters_pagination = '';
	switch (beanRequestAlumnoC.operation) {
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
			beanRequestAlumnoC.entity_api +
			'/' +
			beanRequestAlumnoC.operation +
			parameters_pagination,
		type: beanRequestAlumnoC.type_request,
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
			$('#modalCargandoAlumnoC').modal('hide');
			if (beanCrudResponse.messageServer !== null) {
				if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
					swal({
						title: 'Realizado',
						text: 'Acción realizada existosamente!',
						type: 'success',
						timer: 1200,
						showConfirmButton: false,
					});
					document.querySelector('#pageAlumnoC').value = 1;
					document.querySelector('#sizePageAlumnoC').value = 5;
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
				beanPaginationAlumnoC = beanCrudResponse.beanPagination;
				// toListAlumnoC(beanPaginationAlumnoC);
				if (document.querySelector('#txtNombreTest')) {
					if (beanRequestTest.operation == 'add') {
						addDetalle();
					} else {
						toListTestDetalle(listDetalleTest);
					}
				} else {
					toListAlumnoC(beanPaginationAlumnoC);
				}
			}
		})
		.fail(function (jqXHR, textStatus, errorThrown) {
			$('#modalCargandoAlumnoC').modal('hide');
			showAlertErrorRequest();
		});
}

function toListAlumnoC(beanPagination) {
	document.querySelector('#txtAlumno').innerHTML = '';

	let row = '<option value="">SIN DEFINIR</option>';
	if (beanPagination.list.length == 0) {
		document.querySelector('#txtAlumnoSelect').innerHTML += row;
		return;
	}
	beanPagination.list.forEach((alumno) => {
		row += `<option value="${alumno.cuenta}">${alumno.cuenta} - ${
			alumno.nombre + ' ' + alumno.apellido
		}</option>
`;
		// $('[data-toggle="tooltip"]').tooltip();
	});

	document.querySelector('#txtAlumnoSelect').innerHTML += row;
	addEventsAlumnosC();
}

function addEventsAlumnosC() {
	document
		.querySelectorAll('.click-selection-alumno')
		.forEach(function (element) {
			element.onclick = function () {
				alumnoCSelected = findByAlumnoC(this.getAttribute('idsubTitulo'));
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
								$('#ventanaModalSelectedAlumnoC').modal('hide');

								break;

							default:
								//ALBUM DESDE
								alumnoSelected = alumnoCSelected;
								document.querySelector('#txtSubTituloRecurso').value =
									alumnoCSelected.codigo +
									' - ' +
									alumnoCSelected.nombre.toUpperCase();
								$('#ventanaModalSelectedAlumnoC').modal('hide');
								break;
						}
						tipoSelected = 0;
					} else {
						alumnoSelected = alumnoCSelected;
						document.querySelector('#txtSubTituloRecurso').value =
							alumnoCSelected.codigo +
							' - ' +
							alumnoCSelected.nombre.toUpperCase();
						$('#ventanaModalSelectedAlumnoC').modal('hide');
					}
				}
			};
		});
}

function findByAlumnoC(cuenta) {
	let alumno_;
	beanPaginationAlumnoC.list.forEach((alumno) => {
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
