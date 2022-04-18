var beanPaginationProspecto;
var prospectoSelected;
var capituloSelected;
var beanRequestProspecto = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
	beanRequestProspecto.entity_api = 'prospectos';
	beanRequestProspecto.operation = 'paginate';
	beanRequestProspecto.type_request = 'GET';

	$('#modalCargandoProspecto').modal('show');

	$('#modalCargandoProspecto').on('shown.bs.modal', function () {
		processAjaxProspecto();
	});
	$('#ventanaModalManProspecto').on('hide.bs.modal', function () {
		beanRequestProspecto.type_request = 'GET';
		beanRequestProspecto.operation = 'paginate';
	});
	$('#btnAbrirProspecto').click(function () {
		$('#tituloModalManProspecto').html('REGISTRAR PROSPECTO');
		addProspecto();
		$('#ventanaModalManProspecto').modal('show');
	});
	$('#btnEliminarProspecto').click(function () {
		if (prospectoSelected) {
			if (prospectoSelected.idprospecto == '1') {
				showAlertTopEnd(
					'info',
					'No es posible Eliminar el Prospecto Principal',
					prospectoSelected.nombre.toUpperCase()
				);
			} else {
				beanRequestProspecto.type_request = 'GET';
				beanRequestProspecto.operation = 'delete';
				$('#modalCargandoProspecto').modal('show');
			}
		} else {
			showAlertTopEnd('info', '', 'Seleccione Prospecto');
		}
	});

	$('#formularioProspecto').submit(function (event) {
		beanRequestProspecto.operation = 'add';
		beanRequestProspecto.type_request = 'POST';
		if (validarDormularioVideo()) {
			$('#modalCargandoProspecto').modal('show');
		}
		event.preventDefault();
		event.stopPropagation();
	});
	$('#formularioProspectoUpdate').submit(function (event) {
		beanRequestProspecto.operation = 'update';
		beanRequestProspecto.type_request = 'POST';
		if (prospectoSelected) {
			$('#modalCargandoProspecto').modal('show');
		} else {
			showAlertTopEnd('info', '', 'Seleccione Prospecto');
		}

		event.preventDefault();
		event.stopPropagation();
	});
});

function processAjaxProspecto() {
	let form_data = new FormData();

	let parameters_pagination = '';
	let json = '';
	if (
		beanRequestProspecto.operation == 'update' ||
		beanRequestProspecto.operation == 'add'
	) {
	} else {
		form_data = null;
	}

	switch (beanRequestProspecto.operation) {
		case 'delete':
			parameters_pagination = '?id=' + prospectoSelected.idprospecto;
			break;
		case 'update':
			json = {
				idprospecto: prospectoSelected.idprospecto,
				nombre: document
					.querySelector('#txtNombreProspectoUpdate')
					.value.trim(),
				cuenta: null,
				documento: null,
				pais: document.querySelector('#txtPaisProspectoUpdate').value.trim(),
				telefono: document
					.querySelector('#txtTelefonoProspectoUpdate')
					.value.trim(),
				especialidad: document
					.querySelector('#txtEspecialidadProspectoUpdate')
					.value.trim(),
				email: document.querySelector('#txtEmailProspectoUpdate').value.trim(),
				idFatherProspecto:
					document.querySelector('#txtAlumnoSelectUpdate').value != ''
						? document.querySelector('#txtAlumnoSelectUpdate').value
						: prospectoSelected.idprospecto == '1'
						? null
						: '1',
			};
			form_data.append('class', JSON.stringify(json));
			break;
		case 'add':
			json = {
				nombre: document.querySelector('#txtNombreProspecto').value.trim(),
				cuenta: null,
				documento: null,
				pais: document.querySelector('#txtPaisProspecto').value.trim(),
				telefono: document.querySelector('#txtTelefonoProspecto').value.trim(),
				especialidad: document
					.querySelector('#txtEspecialidadProspecto')
					.value.trim(),
				email: document.querySelector('#txtEmailProspecto').value.trim(),
				idFatherProspecto:
					document.querySelector('#txtAlumnoSelect').value != ''
						? document.querySelector('#txtAlumnoSelect').value
						: '1',
			};
			form_data.append('class', JSON.stringify(json));
			break;

		default:
			parameters_pagination += '?filtro=';
			parameters_pagination += '&pagina=1';
			parameters_pagination += '&registros=1000';
			break;
	}
	$.ajax({
		url:
			getHostAPI() +
			beanRequestProspecto.entity_api +
			'/' +
			beanRequestProspecto.operation +
			parameters_pagination,
		type: beanRequestProspecto.type_request,
		headers: {
			Authorization: 'Bearer ' + Cookies.get('clpe_token'),
		},
		data: form_data,
		cache: false,
		contentType:
			beanRequestProspecto.operation == 'update' ||
			beanRequestProspecto.operation == 'add'
				? false
				: 'application/json; charset=UTF-8',
		processData: false,
		dataType: 'json',
	})
		.done(function (beanCrudResponse) {
			if (beanCrudResponse.messageServer !== null) {
				if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
					if (beanRequestProspecto.operation == 'delete') {
						document.querySelector('#txtNombreProspectoUpdate').value = '';
						document.querySelector('#txtTelefonoProspectoUpdate').value = '';
						document.querySelector('#txtPaisProspectoUpdate').value = '';
						document.querySelector('#txtEspecialidadProspectoUpdate').value =
							'';
						document.querySelector('#txtEmailProspectoUpdate').value = '';
						prospectoSelected = undefined;
					}
					showAlertTopEnd(
						'success',
						'Realizado',
						'Acción realizada existosamente!'
					);
					if (beanRequestProspecto.operation == 'add') {
						$('#ventanaModalManProspecto').modal('hide');
					}
				} else {
					showAlertTopEnd('warning', 'Error', beanCrudResponse.messageServer);
				}
			}
			$('#modalCargandoProspecto').modal('hide');
			if (beanCrudResponse.beanPagination !== null) {
				beanPaginationProspecto = beanCrudResponse.beanPagination;
				listaProspecto(beanPaginationProspecto);
				toListAlumnoC(beanPaginationProspecto);
			}
		})
		.fail(function () {
			$('#modalCargandoProspecto').modal('hide');
			showAlertErrorRequest();
		});
}
function toListAlumnoC(beanPagination) {
	document.querySelector('#txtAlumnoSelect').innerHTML = '';
	let row = '<option value="">SIN DEFINIR</option>';
	if (beanPagination.list.length == 0) {
		document.querySelector('#txtAlumnoSelect').innerHTML += row;
		return;
	}
	beanPagination.list.forEach((alumno) => {
		row += `<option value="${alumno.idprospecto}">${
			(alumno.cuenta != null ? alumno.cuenta + ' - ' : '') + alumno.nombre
		}</option>
`;
		// $('[data-toggle="tooltip"]').tooltip();
	});

	document.querySelector('#txtAlumnoSelect').innerHTML += row;
	if (document.querySelector('#txtAlumnoSelectUpdate')) {
		document.querySelector('#txtAlumnoSelectUpdate').innerHTML = row;
	}

	document.querySelector('#txtAlumnoSelect').value = '1';
}
function addProspecto(prospecto = undefined) {
	//LIMPIAR LOS CAMPOS

	document.querySelector('#txtNombreProspecto').value =
		prospecto == undefined ? '' : prospecto.nombre;

	document.querySelector('#txtTelefonoProspecto').value =
		prospecto == undefined ? '' : prospecto.telefono;
	document.querySelector('#txtPaisProspecto').value =
		prospecto == undefined ? '' : prospecto.pais;
	document.querySelector('#txtEspecialidadProspecto').value =
		prospecto == undefined ? '' : prospecto.especialidad;
	document.querySelector('#txtEmailProspecto').value =
		prospecto == undefined ? '' : prospecto.email;
}

function listaProspecto(beanPagination) {
	document.querySelector('#tbodyProspecto').innerHTML = '';
	document.querySelector('#titleManagerProspecto').innerHTML = 'PROSPECTOS';
	let row = '';
	if (beanPagination.list.length == 0) {
		row = `NO HAY PROSPECTOS`;

		document.querySelector('#tbodyProspecto').innerHTML += row;
		return;
	}
	row += '<ol class="list-group">';
	let list = new Array();
	list = filterByFatherId(beanPagination.list, null);
	list.forEach((p) => {
		row += `<li class="list-group-item px-1" idprospecto="${
			p.idprospecto
		}"><div class="ver-prospecto aula-cursor-mano"><i class="zmdi zmdi-plus text-info mx-2"></i><span>${p.nombre.toUpperCase()}</span></div><div></div></li>`;
	});
	row += '</ol>';
	document.querySelector('#tbodyProspecto').innerHTML += row;

	addEventsButtonsAdmin();
}

function listFilterProspecto(value) {
	let row = '<ol class="list-group">';
	let list = new Array();
	list = filterByFatherId(beanPaginationProspecto.list, value);

	list.forEach((p) => {
		row += `<li class="list-group-item px-1" idprospecto="${
			p.idprospecto
		}"><div class="ver-prospecto aula-cursor-mano"> <i class="zmdi zmdi-plus text-info mx-2"></i><span>${p.nombre.toUpperCase()}</span></div><div></div></li>`;
	});
	row += '</ol>';
	return row;
}

function filterByFatherId(list, value) {
	return list.filter((p) => p.idFatherProspecto == value);
}
function addEventsButtonsAdmin() {
	document.querySelectorAll('.ver-prospecto').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			prospectoSelected = findByProspecto(
				btn.parentElement.getAttribute('idprospecto')
			);
			if (prospectoSelected != undefined) {
				document.querySelector('#txtNombreProspectoUpdate').value =
					prospectoSelected.nombre.toUpperCase();
				document.querySelector('#txtTelefonoProspectoUpdate').value =
					prospectoSelected.telefono;
				document.querySelector('#txtPaisProspectoUpdate').value =
					prospectoSelected.pais.toUpperCase();
				document.querySelector('#txtEspecialidadProspectoUpdate').value =
					prospectoSelected.especialidad == null
						? ''
						: prospectoSelected.especialidad.toUpperCase();
				document.querySelector('#txtEmailProspectoUpdate').value =
					prospectoSelected.email == null ? '' : prospectoSelected.email;
				document.querySelector('#txtAlumnoSelectUpdate').value =
					prospectoSelected.idFatherProspecto == null
						? ''
						: prospectoSelected.idFatherProspecto;
				if (btn.firstElementChild.classList.value.includes('zmdi-plus')) {
					removeClass(btn.firstElementChild, 'zmdi-plus');
					addClass(btn.firstElementChild, 'zmdi-minus');
					btn.parentElement.lastChild.innerHTML = listFilterProspecto(
						prospectoSelected.idprospecto
					);
				} else {
					removeClass(btn.firstElementChild, 'zmdi-minus');
					addClass(btn.firstElementChild, 'zmdi-plus');
					btn.parentElement.lastChild.innerHTML = '';
				}

				addEventsButtonsAdmin();
			} else {
				console.log('warning => ', 'No se encontró el capitulo para poder ver');
			}
		};
	});

	document.querySelectorAll('.editar-prospecto').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			prospectoSelected = findByProspecto(
				btn.parentElement.parentElement.getAttribute('idprospecto')
			);

			if (prospectoSelected != undefined) {
				//  addProspecto(prospectoSelected);
				// $("#tituloModalManProspecto").html("EDITAR RESTRICCIONES");
				// $("#ventanaModalManProspecto").modal("show");
				beanRequestProspecto.type_request = 'POST';
				beanRequestProspecto.operation = 'update';
				$('#modalCargandoProspecto').modal('show');
			} else {
				console.log('warning', 'No se encontró el Almacen para poder editar');
			}
		};
	});
	document.querySelectorAll('.eliminar-prospecto').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			//   $('[data-toggle="tooltip"]').tooltip("hide");
			prospectoSelected = findByProspecto(
				btn.parentElement.parentElement.getAttribute('idprospecto')
			);

			if (prospectoSelected != undefined) {
				beanRequestProspecto.type_request = 'GET';
				beanRequestProspecto.operation = 'delete';
				$('#modalCargandoProspecto').modal('show');
			} else {
				console.log('warning', 'No se encontró el Almacen para poder editar');
			}
		};
	});
}

function downloadURL(url) {
	var hiddenIFrameID = 'hiddenDownloader',
		iframe = document.getElementById(hiddenIFrameID);
	if (iframe === null) {
		iframe = document.createElement('iframe');
		iframe.id = hiddenIFrameID;
		iframe.style.width = '100%';
		iframe.style.height = '100%';
		document.querySelector('#modalFrameContenidoProspecto').appendChild(iframe);
	}
	iframe.src = url;
}

function findIndexProspecto(idbusqueda) {
	return beanPaginationProspecto.list.findIndex((Prospecto) => {
		if (Prospecto.idprospecto == parseInt(idbusqueda)) return Prospecto;
	});
}

function findByProspecto(idprospecto) {
	return beanPaginationProspecto.list.find((Prospecto) => {
		if (parseInt(idprospecto) == Prospecto.idprospecto) {
			return Prospecto;
		}
	});
}

var validarDormularioVideo = () => {
	if (document.querySelector('#txtNombreProspecto').value == '') {
		swal({
			title: 'Vacío',
			text: 'Ingrese Nombre',
			type: 'warning',
			timer: 1200,
			showConfirmButton: false,
		});
		return false;
	}

	return true;
};
