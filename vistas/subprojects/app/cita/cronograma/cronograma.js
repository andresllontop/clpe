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
	let firstday = new Date(hoy.setDate(hoy.getDate() - hoy.getDay() + 1));
	let lastday = new Date(hoy.setDate(hoy.getDate() - hoy.getDay() + 7));
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
		removeClass(document.querySelector('#seccion-cliente'), 'd-none');
		addClass(document.querySelector('#htmlCronograma'), 'd-none');
	});
	$('#btn-filter-anterior').click(function () {
		let firstday = new Date(),
			lastday = new Date();
		firstday = new Date(firstday.setDate(lunesDate.getDate() - 7));
		lastday = new Date(lastday.setDate(lunesDate.getDate() - 1));
		setParameterData(firstday, lastday);
		$('#modalCargandoCronograma').modal('show');
	});
	$('#btn-filter-posterior').click(function () {
		let firstday = new Date(),
			lastday = new Date();
		firstday = new Date(
			firstday.setDate(domingoDate.getDate() - domingoDate.getDay() + 1)
		);
		lastday = new Date(
			lastday.setDate(firstday.getDate() - firstday.getDay() + 7)
		);
		setParameterData(firstday, lastday);
		$('#modalCargandoCronograma').modal('show');
	});
});
function setParameterData(firstday, lastday) {
	lunesDate = firstday;
	martesDate = new Date(
		martesDate.setDate(firstday.getDate() - firstday.getDay() + 2)
	);
	miercolesDate = new Date(
		miercolesDate.setDate(firstday.getDate() - firstday.getDay() + 3)
	);
	juevesDate = new Date(
		juevesDate.setDate(firstday.getDate() - firstday.getDay() + 4)
	);
	viernesDate = new Date(
		viernesDate.setDate(firstday.getDate() - firstday.getDay() + 5)
	);
	sabadoDate = new Date(
		sabadoDate.setDate(firstday.getDate() - firstday.getDay() + 6)
	);
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
		row += `<div style="display: flex;justify-content: space-between;"><span>- ${
			element.clienteExterno == null || element.clienteExterno == ''
				? element.cliente.nombre != null
					? element.cliente.nombre + ' ' + element.cliente.apellido
					: element.asunto
				: element.clienteExterno
		}</span><span idcita="${element.idcita}">${
			element.fechaAtendida == null || element.fechaAtendida == ''
				? '<button class="btn btn-warning atendido-cita-cronograma p-0 mx-1 px-1"><small>PEND</small></button>'
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
	let arrayFilter = new Array();
	beanPaginationCronograma.list.map((detail) => {
		if (
			detail.fechaSolicitud >= parameterFirstDay &&
			detail.fechaSolicitud < parameterSecondDay
		) {
			arrayFilter.push(detail.estadoSolicitud);
		}
	});
	return arrayFilter;
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
	let row = '';
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
		<th scope="row">${resultado}</th>
		<td class="${
			filterLunes == ''
				? 'add-cita-cronograma aula-cursor-mano'
				: findLunes.length == 0
				? ''
				: findLunes.includes('1')
				? 'bg-red-200'
				: findLunes.includes('3')
				? 'bg-green-200'
				: ''
		}" fecha-actual="${resultado + '|' + getDateJava(lunesDate)}">
		<p class="text-right mb-0 ${filterLunes == '' ? 'd-none' : ''}">
		<button class="btn btn-dark add-cita-cronograma aula-cursor-mano mb-2" >
          <i class="zmdi zmdi-plus-square"></i>
        </button>
		</p>
	
		${filterLunes} </td>
		<td class="${
			filterMartes == ''
				? 'add-cita-cronograma aula-cursor-mano'
				: findMartes.length == 0
				? ''
				: findMartes.includes('1')
				? 'bg-red-200'
				: findMartes.includes('3')
				? 'bg-green-200'
				: ''
		}"  fecha-actual="${resultado + '|' + getDateJava(martesDate)}">
		<p class="text-right mb-0 ${filterMartes == '' ? 'd-none' : ''}">
		<button class="btn btn-dark add-cita-cronograma aula-cursor-mano mb-2" >
          <i class="zmdi zmdi-plus-square"></i>
        </button>
		</p>
		${filterMartes}</td>
		<td class="${
			filterMiercoles == ''
				? 'add-cita-cronograma aula-cursor-mano'
				: findMiercoles.length == 0
				? ''
				: findMiercoles.includes('1')
				? 'bg-red-200'
				: findMiercoles.includes('3')
				? 'bg-green-200'
				: ''
		}"  fecha-actual="${resultado + '|' + getDateJava(miercolesDate)}">
		<p class="text-right mb-0 ${filterMiercoles == '' ? 'd-none' : ''}">
		<button class="btn btn-dark add-cita-cronograma aula-cursor-mano mb-2" >
          <i class="zmdi zmdi-plus-square"></i>
        </button>
		</p>
		${filterMiercoles}</td>
		<td class="${
			filterJueves == ''
				? 'add-cita-cronograma aula-cursor-mano'
				: findJueves.length == 0
				? ''
				: findJueves.includes('1')
				? 'bg-red-200'
				: findJueves.includes('3')
				? 'bg-green-200'
				: ''
		}"  fecha-actual="${resultado + '|' + getDateJava(juevesDate)}">
		<p class="text-right mb-0 ${filterJueves == '' ? 'd-none' : ''}">
		<button class="btn btn-dark add-cita-cronograma aula-cursor-mano mb-2" >
          <i class="zmdi zmdi-plus-square"></i>
        </button>
		</p>
		${filterJueves}</td>
		<td class="${
			filterViernes == ''
				? 'add-cita-cronograma aula-cursor-mano'
				: findViernes.length == 0
				? ''
				: findViernes.includes('1')
				? 'bg-red-200'
				: findViernes.includes('3')
				? 'bg-green-200'
				: ''
		}"  fecha-actual="${resultado + '|' + getDateJava(viernesDate)}">
		<p class="text-right mb-0 ${filterViernes == '' ? 'd-none' : ''}">
		<button class="btn btn-dark add-cita-cronograma aula-cursor-mano mb-2" >
          <i class="zmdi zmdi-plus-square"></i>
        </button>
		</p>
		${filterViernes}</td>
		<td class="${
			filterSabado == ''
				? 'add-cita-cronograma aula-cursor-mano'
				: findSabado.length == 0
				? ''
				: findSabado.includes('1')
				? 'bg-red-200'
				: findSabado.includes('3')
				? 'bg-green-200'
				: ''
		}"  fecha-actual="${resultado + '|' + getDateJava(sabadoDate)}">
		<p class="text-right mb-0 ${filterSabado == '' ? 'd-none' : ''}">
		<button class="btn btn-dark add-cita-cronograma aula-cursor-mano mb-2" >
          <i class="zmdi zmdi-plus-square"></i>
        </button>
		</p>
		${filterSabado}</td>
		<td class="${
			filterDomingo == ''
				? 'add-cita-cronograma aula-cursor-mano'
				: findDomingo.length == 0
				? ''
				: findDomingo.includes('1')
				? 'bg-red-200'
				: findDomingo.includes('3')
				? 'bg-green-200'
				: ''
		}"  fecha-actual="${resultado + '|' + getDateJava(domingoDate)}">
		<p class="text-right mb-0 ${filterDomingo == '' ? 'd-none' : ''}">
		<button class="btn btn-dark add-cita-cronograma aula-cursor-mano mb-2" >
          <i class="zmdi zmdi-plus-square"></i>
        </button>
		</p>
		${filterDomingo}</td>
	</tr>`;
	}
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
