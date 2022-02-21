var beanPaginationCliente,
	beanPaginationAjusteCita,
	beanPaginationCita,
	beanPaginationMaximoCita;
var clienteSelected,
	citaSelected,
	ADAUTOMATICO = false;
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
	});
	$('#ventanaModalCitaLista').on('shown.bs.modal', function () {
		beanRequestCliente.entity_api = 'cita';
		beanRequestCliente.type_request = 'GET';
		beanRequestCliente.operation = 'paginate';
		if (!ADAUTOMATICO) {
			processAjaxCita();
		} else {
			ADAUTOMATICO = false;
		}
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
	$('#formularioCitaUpdate').submit(function (event) {
		event.preventDefault();
		event.stopPropagation();
		$('#modalCargandoCita').modal('show');
	});
	$('#btnAbrirCita').click(function () {
		addClass(document.querySelector('#htmlClienteCita'), 'd-none');
		removeClass(document.querySelector('#htmlTipoCita'), 'd-none');
		let hoy = new Date();
		document.querySelector('#txtFechaSolicitudCita').value =
			getDateJava(hoy).split('/')[2] +
			'-' +
			getDateJava(hoy).split('/')[1] +
			'-' +
			getDateJava(hoy).split('/')[0] +
			'T' +
			getFullDateJava(hoy).split(' ')[1].split(':')[0] +
			':' +
			getFullDateJava(hoy).split(' ')[1].split(':')[1];
		clickAddCita();
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
	document
		.getElementById('txtTipoAlumno')
		.addEventListener('change', (event) => {
			if (event.currentTarget.checked) {
				removeClass(document.querySelector('#txtAlumnoSelect'), 'd-none');
				addClass(document.querySelector('#txtAlumnoInput'), 'd-none');
			} else {
				removeClass(document.querySelector('#txtAlumnoInput'), 'd-none');
				addClass(document.querySelector('#txtAlumnoSelect'), 'd-none');
			}
		});
});
function clickAddCita() {
	document.querySelector('#txtAlumnoInput').value = '';
	document.querySelector('#txtAlumnoSelect').value = '';
	document.querySelector('#txtSubtituloDesde').value = '';
	document.querySelector('#txtAsuntoCita').value = '';
	if (
		beanPaginationSubtituloC == undefined ||
		libroExterno != curso_cSelected.codigo
	) {
		libroExterno = curso_cSelected.codigo;
		beanRequestSubtituloC.operation = 'obtener';
		beanRequestSubtituloC.type_request = 'GET';
		processAjaxSubtituloC(undefined);
	}
	beanRequestCliente.entity_api = 'cita';
	beanRequestCliente.type_request = 'POST';
	beanRequestCliente.operation = 'add';
	document.querySelector('#modalTitleCitaAdd').innerHTML = 'REGISTRAR CITA';
	$('#ventanaModalCitaAdd').modal('show');
}
function clickUpdateCita() {
	if (
		beanPaginationSubtituloC == undefined ||
		libroExterno != curso_cSelected.codigo
	) {
		libroExterno = curso_cSelected.codigo;
		beanRequestSubtituloC.operation = 'obtener';
		beanRequestSubtituloC.type_request = 'GET';
		processAjaxSubtituloC(undefined);
	}
	document.querySelector('#txtFechaCita').value = citaSelected.fechaAtendida;
	document.querySelector('#txtFechaSolicitudCita').value =
		citaSelected.fechaSolicitud.split(' ')[0] +
		'T' +
		citaSelected.fechaSolicitud.split(' ')[1];
	beanRequestCliente.entity_api = 'cita';
	beanRequestCliente.type_request = 'POST';
	beanRequestCliente.operation = 'update';
	document.querySelector('#txtAsuntoCita').value = citaSelected.asunto;
	document.querySelector('#txtSubtituloDesde').value =
		citaSelected.tipo == '1'
			? citaSelected.subtitulo.codigo != undefined
				? citaSelected.subtitulo.codigo
				: citaSelected.subtitulo
			: '';
	document.querySelector('#txtTipoCita').checked = citaSelected.tipo == '1';
	document.querySelector('#txtTipoAlumno').checked =
		citaSelected.cliente.cuenta == '' ? false : true;
	if (citaSelected.tipo == '1') {
		addClass(document.querySelector('#htmlAsuntoCita'), 'd-none');
		removeClass(document.querySelector('#htmlSubtituloCita'), 'd-none');
	} else {
		addClass(document.querySelector('#htmlSubtituloCita'), 'd-none');
		removeClass(document.querySelector('#htmlAsuntoCita'), 'd-none');
	}
	document.querySelector('#modalTitleCitaAdd').innerHTML = 'ACTUALIZAR CITA';

	$('#ventanaModalCitaAdd').modal('show');
}
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
			if (
				beanPaginationSubtituloC == undefined ||
				libroExterno != curso_cSelected.codigo
			) {
				libroExterno = curso_cSelected.codigo;
				beanRequestSubtituloC.operation = 'obtener';
				beanRequestSubtituloC.type_request = 'GET';
				processAjaxSubtituloC(undefined);
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
		beanRequestCliente.operation == 'add' ||
		beanRequestCliente.operation == 'add-automatico'
	) {
		json = {
			tipo: document.querySelector('#txtTipoCita').checked ? '1' : '2',
			cliente: clienteSelected
				? clienteSelected.cuenta
				: document.querySelector('#txtTipoAlumno').checked
				? document.querySelector('#txtAlumnoSelect').value
				: '',
			clienteExterno: clienteSelected
				? ''
				: document.querySelector('#txtTipoAlumno').checked
				? ''
				: document.querySelector('#txtAlumnoInput').value,
			subtitulo:
				beanRequestCliente.operation == 'add-automatico'
					? clienteSelected.subTitulo.codigo
					: document.querySelector('#txtTipoCita').checked
					? document.querySelector('#txtSubtituloDesde').value
					: '',
			fechaSolicitud: document.querySelector('#txtFechaSolicitudCita').value,
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
			json.cliente = citaSelected.cliente.cuenta;
			json.clienteExterno = citaSelected.clienteExterno;
			json.estadoSolicitud = citaSelected.estadoSolicitud;
			json.fechaAtendida = document.querySelector('#txtFechaCita').value;
			form_data.append('class', JSON.stringify(json));
			break;
		case 'add':
		case 'add-automatico':
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
			(beanRequestCliente.operation == 'add-automatico'
				? 'add'
				: beanRequestCliente.operation) +
			parameters_pagination,
		type: beanRequestCliente.type_request,
		headers: {
			Authorization: 'Bearer ' + Cookies.get('clpe_token'),
		},
		data: form_data,
		cache: false,
		contentType:
			beanRequestCliente.operation == 'update' ||
			beanRequestCliente.operation == 'add' ||
			beanRequestCliente.operation == 'add-automatico'
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
				if (proccessCrono) {
					$('#modalCargandoCronograma').modal('show');
				} else {
					beanPaginationCita = beanCrudResponse.beanPagination;
					listaCita(beanPaginationCita);
				}
				proccessCrono = false;
			}

			if (beanRequestCliente.operation == 'update') {
				$('#ventanaModalCitaUpdate').modal('hide');
				updateCitaAlumno(json);
			}
			if (beanRequestCliente.operation == 'add-automatico') {
				updateCitaAlumno(json, 'PROGRAMADA');
				ADAUTOMATICO = true;
				$('#ventanaModalCitaLista').modal('show');
			}
			if (beanRequestCliente.operation == 'delete') {
				json = {
					subtitulo: citaSelected.subtitulo.codigo,
					cliente: citaSelected.cliente.cuenta,
				};
				updateCitaAlumno(json, 'PENDIENTE');
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
<td class="text-center ver-lecciones pt-5">${cliente.subTitulo.codigo} <br>${
			cliente.subTitulo.nombre
		}</td>
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
						? '<button class="btn btn-danger mr-2 add-cita" >PENDIDENTE</button>'
						: filtrarAjusteCitaMaximo(cliente.subTitulo.codigo).fechaAtendida !=
						  null
						? '<button class="btn btn-success mr-2">REALIZADA</button>'
						: '<button class="btn btn-warning mr-2" >PROGRAMADA</button>'
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
		row += `<tr idcita="${cita.idcita}"  class="aula-cursor-mano">

<td class="text-center">${
			cita.subtitulo.codigo != ''
				? cita.subtitulo.codigo + '<br>' + cita.subtitulo.nombre
				: cita.asunto
		}</td>

<td class="text-center">${
			cita.fechaSolicitud.split(' ')[0].split('-')[2] +
			'-' +
			cita.fechaSolicitud.split(' ')[0].split('-')[1] +
			'-' +
			cita.fechaSolicitud.split(' ')[0].split('-')[0] +
			'<br>' +
			cita.fechaSolicitud.split(' ')[1]
		}</td>
		<td class="text-center">${
			cita.fechaAtendida == null
				? '<button class="btn btn-warning atendido-cita">ATENDIDO</button>'
				: cita.fechaAtendida.split(' ')[0].split('-')[2] +
				  '-' +
				  cita.fechaAtendida.split(' ')[0].split('-')[1] +
				  '-' +
				  cita.fechaAtendida.split(' ')[0].split('-')[0] +
				  '<br>' +
				  cita.fechaSolicitud.split(' ')[1]
		}</td>
		<td class="text-center">
		<button class="btn btn-info update-cita mr-2"><i class="zmdi zmdi-edit"></i></button>
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
				addClass(document.querySelector('#htmlClienteCita'), 'd-none');
				removeClass(document.querySelector('#htmlTipoCita'), 'd-none');
				clickUpdateCita();
			} else {
				swal('No se encontró el alumno', '', 'info');
			}
		};
	});
	document.querySelectorAll('.atendido-cita').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			citaSelected = findByCita(
				btn.parentElement.parentElement.getAttribute('idcita')
			);
			if (citaSelected != undefined) {
				beanRequestCliente.entity_api = 'cita';
				beanRequestCliente.type_request = 'POST';
				beanRequestCliente.operation = 'update';
				citaSelected.estadoSolicitud = '3';
				let hoy = new Date();
				document.querySelector('#txtFechaCita').value =
					getDateJava(hoy).split('/')[2] +
					'-' +
					getDateJava(hoy).split('/')[1] +
					'-' +
					getDateJava(hoy).split('/')[0] +
					'T' +
					getFullDateJava(hoy).split(' ')[1].split(':')[0] +
					':' +
					getFullDateJava(hoy).split(' ')[1].split(':')[1];

				document.querySelector('#txtAsuntoCita').value = citaSelected.asunto;
				document.querySelector('#txtSubtituloDesde').value =
					citaSelected.tipo == '1' ? citaSelected.subtitulo.codigo : '';
				document.querySelector('#txtTipoCita').checked =
					citaSelected.tipo == '1';
				document.querySelector('#txtFechaSolicitudCita').value =
					citaSelected.fechaSolicitud.split(' ')[0] +
					'T' +
					citaSelected.fechaSolicitud.split(' ')[1];
				$('#ventanaModalCitaUpdate').modal('show');
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
function updateCitaAlumno(json, status = 'REALIZADA') {
	document.querySelectorAll('#tbodyCliente tr').forEach((btn) => {
		//AGREGANDO EVENTO CLICK

		if (
			json.cliente == btn.getAttribute('cuenta') &&
			json.subtitulo == clienteSelected.subTitulo.codigo
		) {
			console.log(status);
			if (status == 'REALIZADA') {
				removeClass(btn.children[5].firstElementChild, 'btn-danger');
				addClass(btn.children[5].firstElementChild, 'btn-success');
				removeClass(btn.children[5].firstElementChild, 'add-cita');
				btn.children[5].firstElementChild.setAttribute('disabled', true);
			} else if (status == 'PROGRAMADA') {
				removeClass(btn.children[5].firstElementChild, 'btn-danger');
				addClass(btn.children[5].firstElementChild, 'btn-warning');
				removeClass(btn.children[5].firstElementChild, 'add-cita');
				btn.children[5].firstElementChild.setAttribute('disabled', true);
			} else {
				removeClass(btn.children[5].firstElementChild, 'btn-success');
				addClass(btn.children[5].firstElementChild, 'btn-danger');
				addClass(btn.children[5].firstElementChild, 'add-cita');
				btn.children[5].firstElementChild.setAttribute('disabled', false);
			}
			btn.children[5].firstElementChild.innerHTML = status;
		}
	});
}
function addEventsButtonsCliente() {
	document.querySelectorAll('.ver-lecciones').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			clienteSelected = findByCliente(
				btn.parentElement.getAttribute('idtarea')
			);
		};
	});
	document.querySelectorAll('.ver-cita').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			clienteSelected = findByCliente(
				btn.parentElement.parentElement.getAttribute('idtarea')
			);
			if (clienteSelected) {
				if (
					beanPaginationSubtituloC == undefined ||
					libroExterno != curso_cSelected.codigo
				) {
					libroExterno = curso_cSelected.codigo;
					beanRequestSubtituloC.operation = 'obtener';
					beanRequestSubtituloC.type_request = 'GET';

					processAjaxSubtituloC(undefined);
				} else {
					document.querySelector('#txtSubtituloDesde').value =
						clienteSelected.subTitulo ? clienteSelected.subTitulo.codigo : '';
				}
				$('#ventanaModalCitaLista').modal('show');
			} else {
				swal('No se encontró el alumno', '', 'info');
			}
		};
	});
	document.querySelectorAll('.add-cita').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			clienteSelected = findByCliente(
				btn.parentElement.parentElement.getAttribute('idtarea')
			);
			if (clienteSelected != undefined) {
				beanRequestCliente.entity_api = 'cita';
				beanRequestCliente.type_request = 'POST';
				beanRequestCliente.operation = 'add-automatico';
				document.querySelector('#txtTipoCita').checked = true;
				document.querySelector('#txtSubtituloDesde').value =
					clienteSelected.subTitulo.codigo;
				$('#modalCargandoCita').modal('show');
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
function findByCita(idcita, beanPagination = beanPaginationCita) {
	return beanPagination.list.find((Cita) => {
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
