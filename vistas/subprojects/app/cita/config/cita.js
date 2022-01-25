var beanPaginationAjusteCita;
var ajusteCitaSelected;
var subtituloSelected;
var tipoSelected = 0;
var beanRequestAjusteCita = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
	beanRequestAjusteCita.entity_api = 'ajuste/cita';
	beanRequestAjusteCita.operation = 'paginate';
	beanRequestAjusteCita.type_request = 'GET';

	$('#sizePageAjusteCita').change(function () {
		beanRequestAjusteCita.type_request = 'GET';
		beanRequestAjusteCita.operation = 'paginate';
		$('#modalCargandoAjusteCita').modal('show');
	});

	$('#modalCargandoAjusteCita').on('shown.bs.modal', function () {
		processAjaxAjusteCita();
	});
	$('#ventanaModalManAjusteCita').on('hide.bs.modal', function () {
		beanRequestAjusteCita.type_request = 'GET';
		beanRequestAjusteCita.operation = 'paginate';
	});

	$('#ventanaModalAjusteCitaLista').on('hide.bs.modal', function () {
		beanRequestAjusteCita.type_request = 'GET';
		beanRequestAjusteCita.operation = 'paginate';
		beanRequestCliente.entity_api = 'tareas';
		beanRequestCliente.operation = 'alumno';
		beanRequestCliente.type_request = 'GET';
		PromiseInitCitaMaximo();
	});
	$('#btnAbrirListaAjusteCita').click(function () {
		beanRequestAjusteCita.type_request = 'GET';
		beanRequestAjusteCita.operation = 'paginate';
		$('#ventanaModalAjusteCitaLista').modal('show');
		$('#modalCargandoAjusteCita').modal('show');
	});
	$('#btnAbrirAjusteCita').click(function () {
		beanRequestAjusteCita.operation = 'add';
		beanRequestAjusteCita.type_request = 'POST';
		$('#tituloModalManAjusteCita').html('REGISTRAR SUBTITULOS');
		if (beanPaginationSubtituloC == undefined) {
			beanRequestSubtituloC.operation = 'obtener';
			beanRequestSubtituloC.type_request = 'GET';
			processAjaxSubtituloC(undefined);
		}
		$('#ventanaModalManAjusteCita').modal('show');
	});
	$('#formularioAjusteCita').submit(function (event) {
		if (validarFormularioAjusteCita()) {
			$('#modalCargandoAjusteCita').modal('show');
		}
		event.preventDefault();
		event.stopPropagation();
	});

	document.querySelector('#txtSubtituloHasta').onchange = function () {
		subtituloSelected = findBySubtituloC(
			document.querySelector('#txtSubtituloHasta').value
		);
	};
});
function PromiseInitCitaMaximo() {
	document.querySelector('#tbodyCliente').innerHTML += `<tr>
    <td class="text-center" colspan="10">Espere cargando ...</td>
    </tr>`;
	let fetOptions = {
		headers: {
			'Content-Type': 'application/json; charset=UTF-8',
			// "Access-Control-Allow-Origin": "*",
			Authorization: 'Bearer ' + Cookies.get('clpe_token'),
		},
		method: 'GET',
	};
	Promise.all([
		fetch(
			getHostAPI() +
				'ajuste/cita/paginate' +
				'?filter=' +
				curso_cSelected.codigo +
				'&pagina=1&registros=200',
			fetOptions
		),
		fetch(getHostAPI() + 'cita/obtener', fetOptions),
	])
		.then((responses) => Promise.all(responses.map((res) => res.json())))
		.then((json) => {
			if (json[0].beanPagination !== null) {
				beanPaginationAjusteCita = json[0].beanPagination;
			}
			if (json[1].beanPagination !== null) {
				beanPaginationMaximoCita = json[1].beanPagination;
				listaCliente(beanPaginationCliente);
			}
		})
		.catch((err) => {
			console.log(err);
			showAlertErrorRequest();
		});
}
function processAjaxAjusteCita() {
	let form_data = new FormData();

	let parameters_pagination = '';
	let json = '';
	if (
		beanRequestAjusteCita.operation == 'update' ||
		beanRequestAjusteCita.operation == 'add'
	) {
		json = {
			tipo: 2,
			subtitulo: subtituloSelected.codigo,
		};
	} else {
		form_data = null;
	}

	switch (beanRequestAjusteCita.operation) {
		case 'delete':
			parameters_pagination = '?id=' + ajusteCitaSelected.idajusteCita;
			break;

		case 'update':
			json.idajusteCita = ajusteCitaSelected.idajusteCita;

			form_data.append('class', JSON.stringify(json));
			break;
		case 'add':
			form_data.append('class', JSON.stringify(json));
			break;

		default:
			parameters_pagination += '?filtro=';
			parameters_pagination +=
				'&pagina=' + document.querySelector('#pageAjusteCita').value.trim();
			parameters_pagination +=
				'&registros=' +
				document.querySelector('#sizePageAjusteCita').value.trim();
			break;
	}
	$.ajax({
		url:
			getHostAPI() +
			beanRequestAjusteCita.entity_api +
			'/' +
			beanRequestAjusteCita.operation +
			parameters_pagination,
		type: beanRequestAjusteCita.type_request,
		headers: {
			Authorization: 'Bearer ' + Cookies.get('clpe_token'),
		},
		data: form_data,
		cache: false,
		contentType:
			beanRequestAjusteCita.operation == 'update' ||
			beanRequestAjusteCita.operation == 'add'
				? false
				: 'application/json; charset=UTF-8',
		processData: false,
		dataType: 'json',
		xhr: function () {
			var xhr = new window.XMLHttpRequest();
			xhr.upload.addEventListener(
				'progress',
				function (evt) {
					if (evt.lengthComputable) {
						var percentComplete = evt.loaded / evt.total;
						$('.progress-bar-ajusteCita').css({
							width: Math.round(percentComplete * 100) + '%',
						});
						$('.progress-bar-ajusteCita').text(
							Math.round(percentComplete * 100) + '%'
						);
						$('.progress-bar-ajusteCita').attr(
							'aria-valuenow',
							+Math.round(percentComplete * 100)
						);
						if (percentComplete === 1) {
							// $('.progress-bar-parrafo').addClass('hide');
							$('.progress-bar-ajusteCita').css({
								width: +'100%',
							});
							$('.progress-bar-ajusteCita').text('Cargando ... 100%');
							$('.progress-bar-ajusteCita').attr('aria-valuenow', '100');
						}
					}
				},
				false
			);
			xhr.addEventListener(
				'progress',
				function (evt) {
					if (evt.lengthComputable) {
						var percentComplete = evt.loaded / evt.total;
						$('.progress-bar-parrafo').css({
							width: Math.round(percentComplete * 100) + '%',
						});
						$('.progress-bar-parrafo').text(
							Math.round(percentComplete * 100) + '%'
						);
						$('.progress-bar-parrafo').attr(
							'aria-valuenow',
							+Math.round(percentComplete * 100)
						);
					}
				},
				false
			);
			return xhr;
		},
	})
		.done(function (beanCrudResponse) {
			$('#modalCargandoAjusteCita').modal('hide');
			if (beanCrudResponse.messageServer !== null) {
				if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
					showAlertTopEnd(
						'success',
						'Realizado',
						'Acción realizada existosamente!'
					);
					document.querySelector('#pageAjusteCita').value = 1;
					document.querySelector('#sizePageAjusteCita').value = 20;
					$('#ventanaModalManAjusteCita').modal('hide');
				} else {
					showAlertTopEnd('info', beanCrudResponse.messageServer, '');
				}
			}
			if (beanCrudResponse.beanPagination !== null) {
				beanPaginationAjusteCita = beanCrudResponse.beanPagination;
				listaAjusteCita(beanPaginationAjusteCita);
			}
		})
		.fail(function (jqXHR, textStatus, errorThrown) {
			$('#modalCargandoAjusteCita').modal('hide');
			showAlertErrorRequest();
		});
}

function addAlbum(ajusteCita = undefined) {
	//LIMPIAR LOS CAMPOS
	subtituloSelected =
		ajusteCita == undefined ? undefined : ajusteCita.subtitulo;

	document.querySelector('#txtSubtituloHasta').value =
		ajusteCita == undefined ? '0' : subtituloSelected.codigo;
}

function listaAjusteCita(beanPagination) {
	document.querySelector('#tbodyAjusteCita').innerHTML = '';
	document.querySelector('#titleManagerAjusteCita').innerHTML = 'SUBTITULOS';
	let row = '';
	if (beanPagination.list.length == 0) {
		destroyPagination($('#paginationAjusteCita'));
		row += `<tr>
        <td class="text-center" colspan="6">NO HAY SUBTITULOS</td>
        </tr>`;
		document.querySelector('#tbodyAjusteCita').innerHTML = row;
		return;
	}
	beanPagination.list.forEach((ajusteCita) => {
		row += `<tr  idajusteCita="${ajusteCita.idajusteCita}">
<td class="text-center">${ajusteCita.subtitulo.nombre} </td>
<td class="text-center">
<button class="btn btn-info editar-ajusteCita" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-ajusteCita"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;
	});

	document.querySelector('#tbodyAjusteCita').innerHTML += row;
	buildPagination(
		beanPagination.countFilter,
		parseInt(document.querySelector('#sizePageAjusteCita').value),
		document.querySelector('#pageAjusteCita'),
		$('#modalCargandoAjusteCita'),
		$('#paginationAjusteCita')
	);
	addEventsButtonsAjusteCita();
}

function addEventsButtonsAjusteCita() {
	document.querySelectorAll('.editar-ajusteCita').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			ajusteCitaSelected = findByAjusteCita(
				btn.parentElement.parentElement.getAttribute('idajusteCita')
			);

			if (ajusteCitaSelected != undefined) {
				if (beanPaginationSubtituloC == undefined) {
					beanRequestSubtituloC.operation = 'obtener';
					beanRequestSubtituloC.type_request = 'GET';

					processAjaxSubtituloC(ajusteCitaSelected);
				} else {
					addAlbum(ajusteCitaSelected);
				}

				$('#tituloModalManAjusteCita').html('EDITAR SUBTITULOS');
				$('#ventanaModalManAjusteCita').modal('show');
				beanRequestAjusteCita.type_request = 'POST';
				beanRequestAjusteCita.operation = 'update';
			} else {
				console.log('warning', 'No se encontró la cita para poder editar');
			}
		};
	});

	document.querySelectorAll('.archivos-ajusteCita').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			ajusteCitaSelected = findByAjusteCita(
				btn.parentElement.parentElement.getAttribute('idajusteCita')
			);

			if (ajusteCitaSelected != undefined) {
				$('#titleManagerDetalle').html(
					'"' + ajusteCitaSelected.subtitulo.nombre + '"'
				);
				$('#ModalDetalle').modal('show');
				$('#modalCargandoDetalle').modal('show');
			} else {
				showAlertTopEnd('info', 'Vacío!', 'No se encontró el ajusteCita');
			}
		};
	});

	document.querySelectorAll('.eliminar-ajusteCita').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			//   $('[data-toggle="tooltip"]').tooltip("hide");
			ajusteCitaSelected = findByAjusteCita(
				btn.parentElement.parentElement.getAttribute('idajusteCita')
			);

			if (ajusteCitaSelected != undefined) {
				beanRequestAjusteCita.type_request = 'GET';
				beanRequestAjusteCita.operation = 'delete';
				$('#modalCargandoAjusteCita').modal('show');
			} else {
				console.log('warning', 'No se encontró el Almacen para poder editar');
			}
		};
	});
}

function findIndexAjusteCita(idbusqueda) {
	return beanPaginationAjusteCita.list.findIndex((AjusteCita) => {
		if (AjusteCita.idajusteCita == parseInt(idbusqueda)) return AjusteCita;
	});
}

function findByAjusteCita(idajusteCita) {
	return beanPaginationAjusteCita.list.find((AjusteCita) => {
		if (parseInt(idajusteCita) == AjusteCita.idajusteCita) {
			return AjusteCita;
		}
	});
}

var validarFormularioAjusteCita = () => {
	if (subtituloSelected == undefined) {
		swal({
			title: 'Vacío',
			text: 'Ingrese Tema',
			type: 'warning',
			timer: 4000,
			showConfirmButton: false,
		});
		return false;
	}

	return true;
};
