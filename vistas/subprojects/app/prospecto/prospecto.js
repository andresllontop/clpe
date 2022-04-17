var beanPaginationProspecto;
var prospectoSelected;
var capituloSelected;
var beanRequestProspecto = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
	beanRequestProspecto.entity_api = 'prospectos';
	beanRequestProspecto.operation = 'paginate';
	beanRequestProspecto.type_request = 'GET';

	$('#sizePageProspecto').change(function () {
		beanRequestProspecto.type_request = 'GET';
		beanRequestProspecto.operation = 'paginate';
		$('#modalCargandoProspecto').modal('show');
	});

	$('#modalCargandoProspecto').modal('show');

	$('#modalCargandoProspecto').on('shown.bs.modal', function () {
		processAjaxProspecto();
	});
	$('#ventanaModalManProspecto').on('hide.bs.modal', function () {
		beanRequestProspecto.type_request = 'GET';
		beanRequestProspecto.operation = 'paginate';
	});

	$('#formularioProspecto').submit(function (event) {
		if (validarDormularioVideo()) {
			$('#modalCargandoProspecto').modal('show');
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
		json = {
			estado: 1,
			codigo: prospectoSelected.cuenta.cuenta.cuentaCodigo,
		};
	} else {
		form_data = null;
	}

	switch (beanRequestProspecto.operation) {
		case 'delete':
			parameters_pagination = '?id=' + prospectoSelected.idprospecto;
			break;
		case 'update':
			json.idprospecto = prospectoSelected.idprospecto;
			form_data.append('class', JSON.stringify(json));
			break;
		case 'add':
			form_data.append('class', JSON.stringify(json));
			break;

		default:
			parameters_pagination += '?filtro=';
			parameters_pagination += '&pagina=1';
			parameters_pagination += '&registros=100';
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
			$('#modalCargandoProspecto').modal('hide');
			if (beanCrudResponse.messageServer !== null) {
				if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
					showAlertTopEnd(
						'success',
						'Realizado',
						'Acción realizada existosamente!'
					);

					$('#ventanaModalManProspecto').modal('hide');
				} else {
					showAlertTopEnd('warning', 'Error', beanCrudResponse.messageServer);
				}
			}
			if (beanCrudResponse.beanPagination !== null) {
				beanPaginationProspecto = beanCrudResponse.beanPagination;
				listaProspecto(beanPaginationProspecto);
			}
		})
		.fail(function () {
			$('#modalCargandoProspecto').modal('hide');
			showAlertErrorRequest();
		});
}

function addProspecto(prospecto = undefined) {
	//LIMPIAR LOS CAMPOS

	document.querySelector('#txtNombreProspecto').value =
		prospecto == undefined ? '' : prospecto.nombre;

	capituloSelected = prospecto == undefined ? undefined : prospecto.titulo;
	document.querySelector('#txtCapituloProspecto').value =
		prospecto == undefined
			? ''
			: prospecto.titulo.codigo + ' - ' + prospecto.titulo.nombre;
	document.querySelector('#tbodyPreguntas').innerHTML = '';
	let row = '';
	for (let index = 1; index <= 10; index++) {
		row += `<label for="txtPregunta${index}Prospecto">Pregunta N° ${index}</label>
         <div class="group-material">
        <textarea class="material-control w-100" data-toggle="tooltip" required="" data-placement="top" title="" data-original-title="Escribe la url de youtube" id="txtPregunta${index}Prospecto" rows="3"></textarea></div>`;
	}

	document.querySelector('#tbodyPreguntas').innerHTML += row;
	if (prospecto != undefined) {
		for (let index = 1; index <= 10; index++) {
			document.querySelector('#txtPregunta' + index + 'Prospecto').value =
				prospecto['pregunta_P' + index];
		}
	}
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
	console.log(list);
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
				document.querySelector('#txtNombreProspecto').value =
					prospectoSelected.nombre.toUpperCase();
				document.querySelector('#txtTelefonoProspecto').value =
					prospectoSelected.telefono;
				document.querySelector('#txtPaisProspecto').value =
					prospectoSelected.pais.toUpperCase();
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
	if (capituloSelected == undefined) {
		swal({
			title: 'Vacío',
			text: 'Selecciona Capítulo',
			type: 'warning',
			timer: 1200,
			showConfirmButton: false,
		});
		return false;
	}
	for (let index = 1; index <= 10; index++) {
		if (
			document.querySelector('#txtPregunta' + index + 'Prospecto').value == ''
		) {
			swal({
				title: 'Vacío',
				text: 'Ingrese datos a la Pregunta N° ' + index,
				type: 'warning',
				timer: 1200,
				showConfirmButton: false,
			});
			return false;
		}
	}

	return true;
};
