var beanPaginationCronograma,
	proccessCrono = false;
var lunesDate,
	martesDate = new Date(),
	miercolesDate = new Date(),
	juevesDate = new Date(),
	viernesDate = new Date(),
	sabadoDate = new Date(),
	domingoDate = new Date();
document.addEventListener('DOMContentLoaded', function () {
	let hoy = new Date();
	let firstday = getMonday(hoy);
	let lastday = addDays(firstday, 6);

	setParameterData(firstday, lastday);
	$('#modalCargandoCronograma').on('shown.bs.modal', function () {
		processAjaxCronograma();
	});
	$('#btnAbrirCronograma').click(function () {
		removeClass(document.querySelector('#htmlCronograma'), 'd-none');
		addClass(document.querySelector('#seccion-cliente'), 'd-none');
		$('#modalCargandoCronograma').modal('show');
	});

	$('#btn-regresar-cronograma').click(function () {
		addClass(document.querySelector('#htmlCronograma'), 'd-none');
		if (window.location.pathname.includes('cronograma')) {
			if (!curso_cSelected.idlibro) {
				document.querySelector('#cursoHTML').classList.remove('d-none');
				processAjaxTarea();
			}
		} else {
			removeClass(document.querySelector('#seccion-cliente'), 'd-none');
		}
	});
	$('#btn-filter-anterior').click(function () {
		let firstday = new Date(),
			lastday = new Date();
		firstday = removeDays(lunesDate, 7);
		lastday = removeDays(lunesDate, 1);
		setParameterData(firstday, lastday);
		$('#modalCargandoCronograma').modal('show');
	});
	$('#btn-filter-posterior').click(function () {
		let firstday = new Date(),
			lastday = new Date();
		firstday = addDays(domingoDate, 1);
		lastday = addDays(domingoDate, 7);
		setParameterData(firstday, lastday);
		$('#modalCargandoCronograma').modal('show');
	});
	$('input:radio[name="citaColor"]').change(function (e) {
		console.log($(this).val());
		if ($(this).val() == '1') {
			document.querySelector('#txtColorCita').value = '#f9f348';
		} else if ($(this).val() == '2') {
			document.querySelector('#txtColorCita').value = '#ee7474';
		} else {
			document.querySelector('#txtColorCita').value = '';
		}
	});
});
function getMonday(d) {
	d = new Date(d);
	var day = d.getDay(),
		diff = d.getDate() - day + (day == 0 ? -6 : 1); // adjust when day is sunday
	return new Date(d.setDate(diff));
}

function setParameterData(firstday, lastday) {
	lunesDate = firstday;
	martesDate = addDays(firstday, 1);
	miercolesDate = addDays(firstday, 2);
	juevesDate = addDays(firstday, 3);
	viernesDate = addDays(firstday, 4);
	sabadoDate = addDays(firstday, 5);
	domingoDate = lastday;
	document.querySelector('#dateInitial').value =
		getDateJava(firstday).split('/')[2] +
		'-' +
		getDateJava(firstday).split('/')[1] +
		'-' +
		getDateJava(firstday).split('/')[0];
	document.querySelector('#dateFinally').value =
		getDateJava(lastday).split('/')[2] +
		'-' +
		getDateJava(lastday).split('/')[1] +
		'-' +
		getDateJava(lastday).split('/')[0];
}
function processAjaxCronograma() {
	let parameters_pagination = '';
	parameters_pagination +=
		'?dateInitial=' + document.querySelector('#dateInitial').value;
	parameters_pagination +=
		'&dateFinally=' + document.querySelector('#dateFinally').value;

	$.ajax({
		url: getHostAPI() + 'cita/cronograma' + parameters_pagination,
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
			$('#modalCargandoCronograma').modal('hide');
			if (beanCrudResponse.messageServer !== null) {
				if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
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
				beanPaginationCronograma = beanCrudResponse.beanPagination;
				listarCronograma();
			}
		})
		.fail(function (jqXHR, textStatus, errorThrown) {
			$('#modalCargandoCronograma').modal('hide');
			showAlertErrorRequest();
		});
}
function filterByTime(day, time) {
	let parameterFirstDay =
		getDateJava(day).split('/')[2] +
		'-' +
		getDateJava(day).split('/')[1] +
		'-' +
		getDateJava(day).split('/')[0] +
		' ' +
		time.split(':')[0] +
		':' +
		time.split(':')[1] +
		':' +
		time.split(':')[2];
	let parameterSecondDay =
		getDateJava(day).split('/')[2] +
		'-' +
		getDateJava(day).split('/')[1] +
		'-' +
		getDateJava(day).split('/')[0] +
		' ' +
		(parseInt(time.split(':')[1]) + 30 == 60
			? parseInt(time.split(':')[0]) + 1 > 9
				? parseInt(time.split(':')[0]) + 1
				: '0' + (parseInt(time.split(':')[0]) + 1)
			: time.split(':')[0]) +
		':' +
		(parseInt(time.split(':')[1]) + 30 == 60 ? '00' : '30') +
		':' +
		time.split(':')[2];
	let value = [],
		row = '';
	beanPaginationCronograma.list.filter((detail) => {
		if (
			detail.fechaSolicitud >= parameterFirstDay &&
			detail.fechaSolicitud < parameterSecondDay
		) {
			value.push(detail);
		}
	});
	value.forEach((element) => {
		row += `<div style="width: 110px;"><p class="text-truncate">- ${
			element.clienteExterno == null || element.clienteExterno == ''
				? element.cliente.nombre != null
					? element.cliente.nombre + ' ' + element.cliente.apellido
					: element.asunto
				: element.clienteExterno
		}</p><span idcita="${element.idcita}">${
			element.fechaAtendida == null || element.fechaAtendida == ''
				? '<button class="btn btn-warning atendido-cita-cronograma p-0 px-1"><small>PEND</small></button>'
				: ''
		}<button class="btn btn-info update-cita-cronograma p-0 mx-1 px-1"><i class="zmdi zmdi-edit"></i></button>
			<button class="btn btn-danger eliminar-cita-cronograma p-0 px-1"><i class="zmdi zmdi-delete"></i></button>	</span>
			</div>`;
	});
	return value.length == 0 ? '' : row;
}
function findByTimeStatus(day, time) {
	let parameterFirstDay =
		getDateJava(day).split('/')[2] +
		'-' +
		getDateJava(day).split('/')[1] +
		'-' +
		getDateJava(day).split('/')[0] +
		' ' +
		time.split(':')[0] +
		':' +
		time.split(':')[1] +
		':' +
		time.split(':')[2];
	let parameterSecondDay =
		getDateJava(day).split('/')[2] +
		'-' +
		getDateJava(day).split('/')[1] +
		'-' +
		getDateJava(day).split('/')[0] +
		' ' +
		(parseInt(time.split(':')[1]) + 30 == 60
			? parseInt(time.split(':')[0]) + 1 > 9
				? parseInt(time.split(':')[0]) + 1
				: '0' + (parseInt(time.split(':')[0]) + 1)
			: time.split(':')[0]) +
		':' +
		(parseInt(time.split(':')[1]) + 30 == 60 ? '00' : '30') +
		':' +
		time.split(':')[2];

	return beanPaginationCronograma.list.find(
		(detail) =>
			detail.fechaSolicitud >= parameterFirstDay &&
			detail.fechaSolicitud < parameterSecondDay
	);
}
function horaEnSegundos(q) {
	return q * 60 * 60;
}

function minutosEnSegundos(q) {
	return q * 60;
}
function listarCronograma() {
	document.querySelector('#tbodyCronograma').innerHTML = '';
	let hora = 3600;
	let horaInicio = horaEnSegundos(5);
	let horaFin = horaEnSegundos(24);
	let progresion = minutosEnSegundos(30);
	let row = '',
		rowHead = '';
	rowHead = `
	
	<span class="bg-dark py-2 text-white text-center" style="width:12.5%">HORA</span>
	<span class="bg-dark py-2 text-white text-center" style="width:12.5%">LUNES  ${getDateJava(
		lunesDate
	).slice(0, -5)}</span>
	<span class="bg-dark py-2 text-white text-center" style="width:12.5%">MARTES  ${getDateJava(
		martesDate
	).slice(0, -5)}</span>
	<span class="bg-dark py-2 text-white text-center" style="width:12.5%">MIERCOLES  ${getDateJava(
		miercolesDate
	).slice(0, -5)}</span>
	<span class="bg-dark py-2 text-white text-center" style="width:12.5%">JUEVES  ${getDateJava(
		juevesDate
	).slice(0, -5)}</span>
	<span class="bg-dark py-2 text-white text-center" style="width:12.5%">VIERNES  ${getDateJava(
		viernesDate
	).slice(0, -5)}</span>
	<span class="bg-dark py-2 text-white text-center" style="width:12.5%">SABADO  ${getDateJava(
		sabadoDate
	).slice(0, -5)}</span>
	<span class="bg-dark py-2 text-white text-center" style="width:12.5%">DOMINGO  ${getDateJava(
		domingoDate
	).slice(0, -5)}</span>
	
	`;

	while (horaInicio < horaFin) {
		horaInicio = horaInicio + progresion;

		hora = parseInt(horaInicio / 3600) % 24;
		minutos = parseInt(horaInicio / 60) % 60;
		segundos = horaInicio % 60;

		let resultado =
			(hora < 10 ? '0' + hora : hora) +
			':' +
			(minutos < 10 ? '0' + minutos : minutos) +
			':' +
			(segundos < 10 ? '0' + segundos : segundos);
		let filterLunes = filterByTime(lunesDate, resultado),
			filterMartes = filterByTime(martesDate, resultado),
			filterMiercoles = filterByTime(miercolesDate, resultado),
			filterJueves = filterByTime(juevesDate, resultado),
			filterViernes = filterByTime(viernesDate, resultado),
			filterSabado = filterByTime(sabadoDate, resultado),
			filterDomingo = filterByTime(domingoDate, resultado),
			findLunes = findByTimeStatus(lunesDate, resultado),
			findMartes = findByTimeStatus(martesDate, resultado),
			findMiercoles = findByTimeStatus(miercolesDate, resultado),
			findJueves = findByTimeStatus(juevesDate, resultado),
			findViernes = findByTimeStatus(viernesDate, resultado),
			findSabado = findByTimeStatus(sabadoDate, resultado),
			findDomingo = findByTimeStatus(domingoDate, resultado);
		row += `<tr >
		<td scope="row">${resultado}</td>
		<td style="width:12.5%; ${
			filterLunes == ''
				? ''
				: findLunes.color != null && findLunes.fechaAtendida == null
				? 'background-color: ' + findLunes.color + ' !important;'
				: ''
		}" class="${
			filterLunes == ''
				? 'add-cita-cronograma aula-cursor-mano'
				: !findLunes
				? ''
				: findLunes.fechaAtendida != null
				? 'bg-blue-200'
				: findLunes.clienteExterno == '' && findLunes.fechaAtendida == null
				? 'bg-green-200'
				: ''
		}" fecha-actual="${resultado + '|' + getDateJava(lunesDate)}">
		
	
		${filterLunes} </td>
		<td style="width:12.5%; ${
			filterMartes == ''
				? ''
				: findMartes.color != null && findMartes.fechaAtendida == null
				? 'background-color: ' + findMartes.color + ' !important;'
				: ''
		}" class="${
			filterMartes == ''
				? 'add-cita-cronograma aula-cursor-mano'
				: !findMartes
				? ''
				: findMartes.fechaAtendida != null
				? 'bg-blue-200'
				: findMartes.clienteExterno == '' && findMartes.fechaAtendida == null
				? 'bg-green-200'
				: ''
		}"  fecha-actual="${resultado + '|' + getDateJava(martesDate)}">
	
		${filterMartes}</td>
		<td style="width:12.5%; ${
			filterMiercoles == ''
				? ''
				: findMiercoles.color != null && findMiercoles.fechaAtendida == null
				? 'background-color: ' + findMiercoles.color + ' !important;'
				: ''
		}" class="${
			filterMiercoles == ''
				? 'add-cita-cronograma aula-cursor-mano'
				: !findMiercoles
				? ''
				: findMiercoles.fechaAtendida != null
				? 'bg-blue-200'
				: findMiercoles.clienteExterno == '' &&
				  findMiercoles.fechaAtendida == null
				? 'bg-green-200'
				: ''
		}"  fecha-actual="${resultado + '|' + getDateJava(miercolesDate)}">
		
		${filterMiercoles}</td>
		<td style="width:12.5%; ${
			filterJueves == ''
				? ''
				: findJueves.color != null && findJueves.fechaAtendida == null
				? 'background-color: ' + findJueves.color + ' !important;'
				: ''
		}" class="${
			filterJueves == ''
				? 'add-cita-cronograma aula-cursor-mano'
				: !findJueves
				? ''
				: findJueves.fechaAtendida != null
				? 'bg-blue-200'
				: findJueves.clienteExterno == '' && findJueves.fechaAtendida == null
				? 'bg-green-200'
				: ''
		}"  fecha-actual="${resultado + '|' + getDateJava(juevesDate)}">
	
		${filterJueves}</td>
		<td style="width:12.5%; ${
			filterViernes == ''
				? ''
				: findViernes.color != null && findViernes.fechaAtendida == null
				? 'background-color: ' + findViernes.color + ' !important;'
				: ''
		}" class="${
			filterViernes == ''
				? 'add-cita-cronograma aula-cursor-mano'
				: !findViernes
				? ''
				: findViernes.fechaAtendida != null
				? 'bg-blue-200'
				: findViernes.clienteExterno == '' && findViernes.fechaAtendida == null
				? 'bg-green-200'
				: ''
		}"  fecha-actual="${resultado + '|' + getDateJava(viernesDate)}">
		
		${filterViernes}</td>
		<td style="width:12.5%; ${
			filterSabado == ''
				? ''
				: filterSabado.color != null && filterSabado.fechaAtendida == null
				? 'background-color: ' + filterSabado.color + ' !important;'
				: ''
		}" class="${
			filterSabado == ''
				? 'add-cita-cronograma aula-cursor-mano'
				: !findSabado
				? ''
				: findSabado.fechaAtendida != null
				? 'bg-blue-200'
				: findSabado.clienteExterno == '' && findSabado.fechaAtendida == null
				? 'bg-green-200'
				: ''
		}"  fecha-actual="${resultado + '|' + getDateJava(sabadoDate)}">
		
		${filterSabado}</td>
		<td  style="width:12.5%; ${
			filterDomingo == ''
				? ''
				: filterDomingo.color != null && filterDomingo.fechaAtendida == null
				? 'background-color: ' + filterDomingo.color + ' !important;'
				: ''
		}" class="${
			filterDomingo == ''
				? 'add-cita-cronograma aula-cursor-mano'
				: !findDomingo
				? ''
				: findDomingo.fechaAtendida != null
				? 'bg-blue-200'
				: findDomingo.clienteExterno == '' && findDomingo.fechaAtendida == null
				? 'bg-green-200'
				: ''
		}"  fecha-actual="${resultado + '|' + getDateJava(domingoDate)}">
		
		${filterDomingo}</td>
	</tr>`;
	}
	document.querySelector('#theadCronograma').innerHTML = rowHead;
	document.querySelector('#tbodyCronograma').innerHTML += row;
	addEventsButtonsCronograma();
}
function addEventsButtonsCronograma() {
	document.querySelectorAll('.add-cita-cronograma').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			document.querySelector('#txtTipoAlumno').checked = true;
			removeClass(document.querySelector('#txtAlumnoSelect'), 'd-none');
			addClass(document.querySelector('#txtAlumnoInput'), 'd-none');
			let fecha = btn.getAttribute('fecha-actual')
				? btn.getAttribute('fecha-actual').split('|')[1]
				: btn.parentElement.parentElement
						.getAttribute('fecha-actual')
						.split('|')[1];

			document.querySelector('#txtFechaSolicitudCita').value =
				fecha.split('/')[2] +
				'-' +
				fecha.split('/')[1] +
				'-' +
				fecha.split('/')[0] +
				'T' +
				(btn.getAttribute('fecha-actual')
					? btn.getAttribute('fecha-actual').split('|')[0]
					: btn.parentElement.parentElement
							.getAttribute('fecha-actual')
							.split('|')[0]);
			clienteSelected = undefined;
			removeClass(document.querySelector('#htmlClienteCita'), 'd-none');
			removeClass(document.querySelector('#htmlTipoCita'), 'd-none');
			if (!beanPaginationAlumnoC) {
				$('#modalCargandoAlumnoC').modal('show');
			}
			proccessCrono = true;
			clickAddCita();
		};
	});
	document.querySelectorAll('.update-cita-cronograma').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			citaSelected = findByCita(
				btn.parentElement.getAttribute('idcita'),
				beanPaginationCronograma
			);
			if (citaSelected != undefined) {
				proccessCrono = true;
				addClass(document.querySelector('#htmlClienteCita'), 'd-none');
				removeClass(document.querySelector('#htmlTipoCita'), 'd-none');

				clickUpdateCita();
				document.querySelector('#modalTitleCitaAdd').innerHTML =
					document.querySelector('#modalTitleCitaAdd').innerHTML +
					'<br> <small class="f-weight-700">' +
					(citaSelected.clienteExterno == null ||
					citaSelected.clienteExterno == ''
						? citaSelected.cliente.nombre != null
							? citaSelected.cliente.nombre +
							  ' ' +
							  citaSelected.cliente.apellido
							: 'ASUNTO'
						: citaSelected.clienteExterno) +
					'</small>';
			} else {
				swal('No se encontró el alumno', '', 'info');
			}
		};
	});
	document.querySelectorAll('.atendido-cita-cronograma').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			citaSelected = findByCita(
				btn.parentElement.getAttribute('idcita'),
				beanPaginationCronograma
			);
			if (citaSelected != undefined) {
				proccessCrono = true;
				beanRequestCliente.entity_api = 'cita';
				beanRequestCliente.type_request = 'POST';
				beanRequestCliente.operation = 'update';
				citaSelected.estadoSolicitud = '3';

				let fecha = btn.parentElement.parentElement.parentElement
					.getAttribute('fecha-actual')
					.split('|')[1];

				document.querySelector('#txtFechaCita').value =
					fecha.split('/')[2] +
					'-' +
					fecha.split('/')[1] +
					'-' +
					fecha.split('/')[0] +
					'T' +
					btn.parentElement.parentElement.parentElement
						.getAttribute('fecha-actual')
						.split('|')[0];

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
	document.querySelectorAll('.eliminar-cita-cronograma').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			citaSelected = findByCita(
				btn.parentElement.getAttribute('idcita'),
				beanPaginationCronograma
			);
			if (citaSelected != undefined) {
				beanRequestCliente.entity_api = 'cita';
				beanRequestCliente.type_request = 'GET';
				beanRequestCliente.operation = 'delete';
				proccessCrono = true;
				$('#modalCargandoCita').modal('show');
			} else {
				swal('No se encontró el alumno', '', 'info');
			}
		};
	});
}
