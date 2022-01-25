var beanPaginationCliente,
	beanPaginationAjusteCita,
	beanPaginationCita,
	beanPaginationMaximoCita;
var clienteSelected, citaSelected;
var beanRequestCliente = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
	beanRequestCliente.entity_api = 'tareas';
	beanRequestCliente.operation = 'alumno';
	beanRequestCliente.type_request = 'GET';

	$('#sizePageCliente').change(function () {
		$('#modalCargandoCliente').modal('show');
	});
	document.querySelector('#tipoOpcionHeaderCurso').innerHTML = 'CITAS';
	document.querySelector('#titleManagerCurso_c').innerHTML = 'CITAS';
	//$('#modalCargandoCurso_c').modal('show');
	processAjaxTarea();
	$('#modalCargandoCliente').on('shown.bs.modal', function () {
		processAjaxCliente();
	});
	$('#modalCargandoCita').on('shown.bs.modal', function () {
		processAjaxCita();
	});
	$('#ventanaModalCitaAdd').on('hide.bs.modal', function () {
		beanRequestCliente.entity_api = 'cita';
		beanRequestCliente.type_request = 'GET';
		beanRequestCliente.operation = 'paginate';
	});
	$('#ventanaModalCitaLista').on('hide.bs.modal', function () {
		beanRequestCliente.entity_api = 'cita';
		beanRequestCliente.type_request = 'GET';
		beanRequestCliente.operation = 'paginate';
		PromiseInitCitaMaximo();
	});
	$('#ventanaModalCitaLista').on('shown.bs.modal', function () {
		beanRequestCliente.entity_api = 'cita';
		beanRequestCliente.type_request = 'GET';
		beanRequestCliente.operation = 'paginate';
		processAjaxCita();
	});

	$('#formularioClienteSearch').submit(function (event) {
		event.preventDefault();
		event.stopPropagation();
		$('#modalCargandoCliente').modal('show');
	});
	$('#formularioCita').submit(function (event) {
		event.preventDefault();
		event.stopPropagation();
		$('#modalCargandoCita').modal('show');
	});
	$('#btnAbrirCita').click(function () {
		beanRequestCliente.entity_api = 'cita';
		beanRequestCliente.type_request = 'POST';
		beanRequestCliente.operation = 'add';
		document.querySelector('#txtSubtituloCita').value =
			clienteSelected.subTitulo.codigo +
			' - ' +
			clienteSelected.subTitulo.nombre;
		$('#ventanaModalCitaAdd').modal('show');
	});

	document.querySelectorAll('.btn-regresar').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			beanRequestCliente.entity_api = 'tareas';
			beanRequestCliente.operation = 'alumno';
			beanRequestCliente.type_request = 'GET';
			document.querySelector('#cursoHTML').classList.remove('d-none');
			document.querySelector('#seccion-cliente').classList.add('d-none');
		};
	});

	document.getElementById('txtTipoCita').addEventListener('change', (event) => {
		if (event.currentTarget.checked) {
			removeClass(document.querySelector('#htmlSubtituloCita'), 'd-none');
			addClass(document.querySelector('#htmlAsuntoCita'), 'd-none');
		} else {
			removeClass(document.querySelector('#htmlAsuntoCita'), 'd-none');
			addClass(document.querySelector('#htmlSubtituloCita'), 'd-none');
		}
	});
});

function addEventsButtonsCurso_c() {
	document.querySelectorAll('.detalle-curso').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			curso_cSelected = findByCurso_c(
				btn.parentElement.parentElement.getAttribute('idlibro')
			);

			if (curso_cSelected != undefined) {
				addClass(document.querySelector('#cursoHTML'), 'd-none');
				removeClass(document.querySelector('#seccion-cliente'), 'd-none');
				beanRequestCliente.operation = 'alumno';
				beanRequestCliente.type_request = 'GET';
				document.querySelector('#titleLibro').innerHTML =
					curso_cSelected.nombre;
				PromiseInit();
			} else {
				console.log('warning', 'No se encontró el Almacen para poder editar');
			}
		};
	});
	document.querySelectorAll('.detalle-other-curso').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			curso_cSelected = findByCurso_c(btn.getAttribute('idlibro'));

			if (curso_cSelected != undefined) {
				addClass(document.querySelector('#cursoHTML'), 'd-none');
				removeClass(document.querySelector('#seccion-cliente'), 'd-none');
				beanRequestCliente.operation = 'alumno';
				beanRequestCliente.type_request = 'GET';
				document.querySelector('#titleLibro').innerHTML =
					curso_cSelected.nombre;
				PromiseInit();
			} else {
				console.log('warning', 'No se encontró el Almacen para poder editar');
			}
		};
	});
}
function PromiseInit() {
	document.querySelector('#tbodyCliente').innerHTML = `<tr>
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
				beanRequestCliente.entity_api +
				'/' +
				beanRequestCliente.operation +
				'?filter=&pagina=1&registros=20' +
				'&libro=' +
				curso_cSelected.codigo,
			fetOptions
		),

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
				beanPaginationCliente = json[0].beanPagination;
			}
			if (json[1].beanPagination !== null) {
				beanPaginationAjusteCita = json[1].beanPagination;
			}
			if (json[2].beanPagination !== null) {
				beanPaginationMaximoCita = json[2].beanPagination;
				listaCliente(beanPaginationCliente);
			}
		})
		.catch((err) => {
			console.log(err);
			showAlertErrorRequest();
		});
}
function processAjaxCliente() {
	let form_data = new FormData();

	let parameters_pagination = '';
	let json = '';

	switch (beanRequestCliente.operation) {
		default:
			parameters_pagination +=
				'?filter=' + document.querySelector('#txtSearchCliente').value.trim();
			parameters_pagination += '&libro=' + curso_cSelected.codigo;
			parameters_pagination +=
				'&pagina=' + document.querySelector('#pageCliente').value.trim();
			parameters_pagination +=
				'&registros=' + document.querySelector('#sizePageCliente').value.trim();
			break;
	}
	$.ajax({
		url:
			getHostAPI() +
			beanRequestCliente.entity_api +
			'/' +
			beanRequestCliente.operation +
			parameters_pagination,
		type: beanRequestCliente.type_request,
		headers: {
			Authorization: 'Bearer ' + Cookies.get('clpe_token'),
		},
		data: form_data,
		cache: false,
		contentType:
			beanRequestCliente.operation == 'update' ||
			beanRequestCliente.operation == 'add'
				? false
				: 'application/json; charset=UTF-8',
		processData: false,
		dataType: 'json',
	})
		.done(function (beanCrudResponse) {
			$('#modalCargandoCliente').modal('hide');
			if (beanCrudResponse.messageServer !== null) {
				if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
					showAlertTopEnd(
						'success',
						'Realizado',
						'Acción realizada existosamente!'
					);
					document.querySelector('#pageCliente').value = 1;
					document.querySelector('#sizePageCliente').value = 20;
					$('#ventanaModalManCliente').modal('hide');
				} else {
					showAlertTopEnd('warning', 'Error', beanCrudResponse.messageServer);
				}
			}
			if (beanCrudResponse.beanPagination !== null) {
				beanPaginationCliente = beanCrudResponse.beanPagination;
				listaCliente(beanPaginationCliente);
			}
		})
		.fail(function (jqXHR, textStatus, errorThrown) {
			$('#modalCargandoCliente').modal('hide');
			showAlertErrorRequest();
		});
}
function processAjaxTarea() {
	$.ajax({
		url: getHostAPI() + 'tareas/libros',
		type: 'GET',
		headers: {
			Authorization: 'Bearer ' + Cookies.get('clpe_token'),
		},
		data: null,
		cache: false,
		contentType: 'application/json; charset=UTF-8',
		processData: false,
		dataType: 'json',
	})
		.done(function (beanCrudResponse) {
			$('#modalCargandoCliente').modal('hide');
			if (beanCrudResponse.beanPagination !== null) {
				beanPaginationCurso_c = beanCrudResponse.beanPagination;
				listaCurso_c(beanPaginationCurso_c);
			}
		})
		.fail(function (jqXHR, textStatus, errorThrown) {
			$('#modalCargandoCliente').modal('hide');
			showAlertErrorRequest();
		});
}
function processAjaxCita() {
	let form_data = new FormData();

	let parameters_pagination = '';
	let json = '';
	if (
		beanRequestCliente.operation == 'update' ||
		beanRequestCliente.operation == 'add'
	) {
		json = {
			tipo: document.querySelector('#txtTipoCita').checked ? '1' : '2',
			cliente: clienteSelected.cuenta,
			subtitulo: document.querySelector('#txtTipoCita').checked
				? document.querySelector('#txtSubtituloCita').value.split('-')[0].trim()
				: '',
			estadoSolicitud: 1,
			asunto: document.querySelector('#txtTipoCita').checked
				? ''
				: document.querySelector('#txtAsuntoCita').value,
		};
	} else {
		form_data = null;
	}
	switch (beanRequestCliente.operation) {
		case 'delete':
			parameters_pagination = '?id=' + citaSelected.idcita;
			break;

		case 'update':
			json.idcita = citaSelected.idcita;
			json.estadoSolicitud = 3;
			form_data.append('class', JSON.stringify(json));
			break;
		case 'add':
			form_data.append('class', JSON.stringify(json));
			break;
		default:
			parameters_pagination += '?filter=' + clienteSelected.cuenta;
			parameters_pagination +=
				'&pagina=' + document.querySelector('#pageCita').value.trim();
			parameters_pagination +=
				'&registros=' + document.querySelector('#sizePageCita').value.trim();
			break;
	}

	$.ajax({
		url:
			getHostAPI() +
			beanRequestCliente.entity_api +
			'/' +
			beanRequestCliente.operation +
			parameters_pagination,
		type: beanRequestCliente.type_request,
		headers: {
			Authorization: 'Bearer ' + Cookies.get('clpe_token'),
		},
		data: form_data,
		cache: false,
		contentType:
			beanRequestCliente.operation == 'update' ||
			beanRequestCliente.operation == 'add'
				? false
				: 'application/json; charset=UTF-8',
		processData: false,
		dataType: 'json',
	})
		.done(function (beanCrudResponse) {
			$('#modalCargandoCita').modal('hide');
			if (beanCrudResponse.messageServer !== null) {
				if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
					$('#ventanaModalCitaAdd').modal('hide');
					showAlertTopEnd(
						'success',
						'Realizado',
						'Acción realizada existosamente!'
					);
				} else {
					showAlertTopEnd('warning', 'Error', beanCrudResponse.messageServer);
				}
			}
			if (beanCrudResponse.beanPagination !== null) {
				beanPaginationCita = beanCrudResponse.beanPagination;
				listaCita(beanPaginationCita);
			}
		})
		.fail(function (jqXHR, textStatus, errorThrown) {
			$('#modalCargandoCita').modal('hide');
			showAlertErrorRequest();
		});
}
function listaCliente(beanPagination) {
	let row = '';
	document.querySelector('#tbodyCliente').innerHTML = '';
	document.querySelector('#titleManagerCliente').innerHTML = 'ALUMNOS';
	document.querySelector('#txtCountCliente').value = beanPagination.countFilter;

	if (beanPagination.list.length == 0) {
		destroyPagination($('#paginationCliente'));
		row += `<tr>
        <td class="text-center" colspan="9">NO HAY TAREAS</td>
        </tr>`;

		document.querySelector('#tbodyCliente').innerHTML += row;
		return;
	}
	beanPagination.list[0].forEach((cliente) => {
		row += `<tr  cuenta="${cliente.cuenta}"  idtarea="${
			cliente.idtarea
		}" class="aula-cursor-mano">
<td class="text-center ver-lecciones pt-5">${cliente.registro}</td>
<td class="text-center ver-lecciones pt-5">${cliente.apellido}</td>
<td class="text-center pt-5 ver-lecciones  f-weight-700">${
			cliente.subTitulo.titulo.nombre
		}</td>
<td class="text-center ver-lecciones pt-5">${cliente.subTitulo.nombre}</td>
<td class="text-center ver-lecciones pt-5 d-none">${
			cliente.fecha.split(' ')[0].split('-')[2] +
			'-' +
			cliente.fecha.split(' ')[0].split('-')[1] +
			'-' +
			cliente.fecha.split(' ')[0].split('-')[0] +
			'<br> ' +
			cliente.fecha.split(' ')[1]
		}</td>
        <td class="text-center">
        ${
					filtrarAjusteCita(cliente.subTitulo.codigo).length == 0
						? ''
						: !filtrarAjusteCitaMaximo(cliente.subTitulo.codigo)
						? '<button class="btn btn-danger mr-2" >PENDIDENTE</button>'
						: filtrarAjusteCitaMaximo(cliente.subTitulo.codigo).fechaAtendida !=
						  null
						? '<button class="btn btn-success mr-2">REALIZADA</button>'
						: '<button class="btn btn-danger mr-2" >PROGRAMADA</button>'
				}
        <button class="btn btn-info ver-cita" >VER</button>
        </td>
</tr>`;
	});

	document.querySelector('#tbodyCliente').innerHTML += row;
	buildPagination(
		beanPagination.countFilter,
		parseInt(document.querySelector('#sizePageCliente').value),
		document.querySelector('#pageCliente'),
		$('#modalCargandoCliente'),
		$('#paginationCliente')
	);
	addEventsButtonsCliente();
}
function listaCita(beanPagination) {
	let row = '';
	document.querySelector('#txtAlumno').innerHTML =
		clienteSelected.registro + ' ' + clienteSelected.apellido;
	document.querySelector('#tbodyCita').innerHTML = '';
	if (beanPagination.list.length == 0) {
		destroyPagination($('#paginationCita'));
		row += `<tr>
        <td class="text-center" colspan="9">NO HAY CITAS</td>
        </tr>`;

		document.querySelector('#tbodyCita').innerHTML += row;
		return;
	}
	beanPagination.list.forEach((cita) => {
		row += `<tr  idcita="${cita.idcita}"  class="aula-cursor-mano">

<td class="text-center pt-5">${
			cita.tipo == '1'
				? cita.subtitulo.codigo + '<br>' + cita.subtitulo.nombre
				: cita.asunto
		}</td>

<td class="text-center pt-5">${
			cita.fechaSolicitud.split(' ')[0].split('-')[2] +
			'-' +
			cita.fechaSolicitud.split(' ')[0].split('-')[1] +
			'-' +
			cita.fechaSolicitud.split(' ')[0].split('-')[0]
		}</td>
		<td class="text-center pt-5">${
			cita.fechaAtendida == null
				? '<button class="btn btn-warning update-cita">ATENDIDO</button>'
				: cita.fechaAtendida.split(' ')[0].split('-')[2] +
				  '-' +
				  cita.fechaAtendida.split(' ')[0].split('-')[1] +
				  '-' +
				  cita.fechaAtendida.split(' ')[0].split('-')[0]
		}</td>
		<td class="text-center">
		<button class="btn btn-danger eliminar-cita"><i class="zmdi zmdi-delete"></i></button>
		</td>
</tr>`;
	});

	document.querySelector('#tbodyCita').innerHTML += row;
	buildPagination(
		beanPagination.countFilter,
		parseInt(document.querySelector('#sizePageCita').value),
		document.querySelector('#pageCita'),
		$('#modalCargandoCita'),
		$('#paginationCita')
	);
	addEventsButtonsCita();
}
function addEventsButtonsCita() {
	document.querySelectorAll('.update-cita').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			citaSelected = findByCita(
				btn.parentElement.parentElement.getAttribute('idcita')
			);
			if (citaSelected != undefined) {
				beanRequestCliente.entity_api = 'cita';
				beanRequestCliente.type_request = 'POST';
				beanRequestCliente.operation = 'update';
				$('#modalCargandoCita').modal('show');
			} else {
				swal('No se encontró el alumno', '', 'info');
			}
		};
	});
	document.querySelectorAll('.eliminar-cita').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			citaSelected = findByCita(
				btn.parentElement.parentElement.getAttribute('idcita')
			);
			if (citaSelected != undefined) {
				beanRequestCliente.entity_api = 'cita';
				beanRequestCliente.type_request = 'GET';
				beanRequestCliente.operation = 'delete';
				$('#modalCargandoCita').modal('show');
			} else {
				swal('No se encontró el alumno', '', 'info');
			}
		};
	});
}
function addEventsButtonsCliente() {
	document.querySelectorAll('.ver-lecciones').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			clienteSelected = findByCliente(
				btn.parentElement.getAttribute('idtarea')
			);

			// if (clienteSelected != undefined) {
			// 	clienteSelected = {
			// 		nombre: clienteSelected.registro,
			// 		apellido: clienteSelected.apellido,
			// 		cuenta: { cuentaCodigo: clienteSelected.cuenta },
			// 	};
			// 	document.querySelector('#seccion-cliente').classList.add('d-none');
			// 	document.querySelector('#seccion-leccion').classList.remove('d-none');
			// 	document.querySelector('#seccion-cuestionario').classList.add('d-none');
			// 	beanRequestLeccion.type_request = 'GET';
			// 	beanRequestLeccion.operation = 'paginate';
			// 	$('#modalCargandoLeccion').modal('show');
			// } else {
			// 	swal('No se encontró el alumno', '', 'info');
			// }
		};
	});
	document.querySelectorAll('.ver-cita').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			clienteSelected = findByCliente(
				btn.parentElement.parentElement.getAttribute('idtarea')
			);
			if (clienteSelected != undefined) {
				// beanRequestCliente.entity_api = 'cita';
				// beanRequestCliente.type_request = 'POST';
				// beanRequestCliente.operation = 'add';
				// $('#modalCargandoCita').modal('show');
				$('#ventanaModalCitaLista').modal('show');
			} else {
				swal('No se encontró el alumno', '', 'info');
			}
		};
	});
}
function filtrarAjusteCita(subtitulo) {
	return beanPaginationAjusteCita.list.filter((ajuste) => {
		if (ajuste.subtitulo.codigo == subtitulo) {
			return ajuste;
		}
	});
}
function filtrarAjusteCitaMaximo(subtitulo) {
	return beanPaginationMaximoCita.list.find((ajuste) => {
		if (ajuste.subtitulo == subtitulo) {
			return ajuste;
		}
	});
}
function findByClienteByCuenta(cuenta) {
	return beanPaginationCliente.list[1].find((Cliente) => {
		if (cuenta == Cliente.cuenta) {
			return Cliente;
		}
	});
}
function findByCita(idcita) {
	return beanPaginationCita.list.find((Cita) => {
		if (parseInt(idcita) == parseInt(Cita.idcita)) {
			return Cita;
		}
	});
}
function findByCliente(idtarea) {
	return beanPaginationCliente.list[0].find((Cliente) => {
		if (parseInt(idtarea) == parseInt(Cliente.idtarea)) {
			return Cliente;
		}
	});
}
