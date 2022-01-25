var beanPaginationCliente,
	totalLecciones = 0;
var clienteSelected;
var beanRequestCliente = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
	parametro = 'tarea';
	beanRequestCliente.entity_api = 'tareas';
	beanRequestCliente.operation = 'alumno';
	beanRequestCliente.type_request = 'GET';

	$('#sizePageCliente').change(function () {
		$('#modalCargandoCliente').modal('show');
	});
	document.querySelector('#tipoOpcionHeaderCurso').innerHTML =
		'RESUMEN GENERAL';
	document.querySelector('#titleManagerCurso_c').innerHTML = 'RESUMEN GENERAL';
	//$('#modalCargandoCurso_c').modal('show');
	processAjaxTarea();
	$('#modalCargandoCliente').on('shown.bs.modal', function () {
		processAjaxCliente();
	});

	$('#formularioClienteSearch').submit(function (event) {
		event.preventDefault();
		event.stopPropagation();
		$('#modalCargandoCliente').modal('show');
	});
	document.querySelectorAll('.btn-regresar').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			document.querySelector('#cursoHTML').classList.remove('d-none');
			document.querySelector('#seccion-cliente').classList.add('d-none');
		};
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
				beanRequestCliente.entity_api +
				'/' +
				beanRequestCliente.operation +
				'?filter=&pagina=1&registros=20' +
				'&libro=' +
				curso_cSelected.codigo,
			fetOptions
		),

		fetch(getHostAPI() + 'subtitulos/total', fetOptions),
	])
		.then((responses) => Promise.all(responses.map((res) => res.json())))
		.then((json) => {
			if (json[1].beanPagination !== null) {
				totalLecciones = json[1].beanPagination.countFilter;
			}
			if (json[0].beanPagination !== null) {
				beanPaginationCliente = json[0].beanPagination;
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
function listaCliente(beanPagination) {
	let row = '';
	document.querySelector('#tbodyCliente').innerHTML = '';
	document.querySelector('#titleManagerCliente').innerHTML =
		'RESUMEN GENERAL (TAREAS)';
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
<td class="text-center ver-grafica">
<p style="transform: translateY(69px);margin-top:-52px; font-size: 20px;" class="f-weight-700">0%</p>
<!-- Chart -->
<canvas class="mx-auto mb-sm-0 mb-md-5 mb-xl-0" class="proposal-doughnut"
    data-fill="50" height="80" width="80"></canvas>
<!-- /chart -->
</td>
<td class="text-center pt-5 ver-lecciones  f-weight-700">${
			cliente.subTitulo.titulo.nombre
		}</td>
<td class="text-center ver-lecciones pt-5">${cliente.subTitulo.nombre}</td>
<td class="text-center ver-lecciones pt-5">${
			cliente.fecha.split(' ')[0].split('-')[2] +
			'-' +
			cliente.fecha.split(' ')[0].split('-')[1] +
			'-' +
			cliente.fecha.split(' ')[0].split('-')[0] +
			'<br> ' +
			cliente.fecha.split(' ')[1]
		}</td>

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

function addEventsButtonsCliente() {
	let color = Chart.helpers.color;
	let chartColors = {
		red: '#f37070',
		pink: '#ff445d',
		orange: '#ff8f3a',
		yellow: '#ffde16',
		lightGreen: '#24cf91',
		green: '#4ecc48',
		blue: '#5797fc',
		skyBlue: '#33d4ff',
		gray: '#cfcfcf',
	};

	document.querySelectorAll('.ver-grafica').forEach((btn) => {
		clienteSelected = findByClienteByCuenta(
			btn.parentElement.getAttribute('cuenta')
		);
		if (clienteSelected != undefined) {
			var proposal_data = {
				labels: ['Realizó(%) ', 'Faltan(%) '],
				datasets: [
					{
						data: [
							parseInt(clienteSelected.registro.totalnoestado) +
								parseInt(clienteSelected.registro.totalestado),
							totalLecciones -
								(parseInt(clienteSelected.registro.totalnoestado) +
									parseInt(clienteSelected.registro.totalestado)),
						],
						backgroundColor: [
							color(chartColors.green).alpha(0.8).rgbString(),
							color(chartColors.red).alpha(0.8).rgbString(),
						],
						hoverBackgroundColor: [
							color(chartColors.green).alpha(0.8).rgbString(),
							color(chartColors.red).alpha(0.8).rgbString(),
						],
					},
				],
			};

			new Chart(btn.lastElementChild, {
				type: 'doughnut',
				data: proposal_data,
				options: {
					cutoutPercentage: 80,
					responsive: false,
					legend: {
						display: false,
					},
					tooltips: {
						callbacks: {
							label: function (tooltipItem) {
								return tooltipItem.yLabel;
							},
						},
					},
				},
			});
			btn.firstElementChild.innerHTML =
				Math.round(
					(100 *
						(parseInt(clienteSelected.registro.totalnoestado) +
							parseInt(clienteSelected.registro.totalestado))) /
						totalLecciones
				) + '%';
		} else {
			swal('No se encontró el alumno', '', 'info');
		}
	});
	document.querySelectorAll('.ver-lecciones').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			clienteSelected = findByCliente(
				btn.parentElement.getAttribute('idtarea')
			);

			if (clienteSelected != undefined) {
				clienteSelected = {
					nombre: clienteSelected.registro,
					apellido: clienteSelected.apellido,
					cuenta: { cuentaCodigo: clienteSelected.cuenta },
				};
				document.querySelector('#seccion-cliente').classList.add('d-none');
				document.querySelector('#seccion-leccion').classList.remove('d-none');
				document.querySelector('#seccion-cuestionario').classList.add('d-none');
				beanRequestLeccion.type_request = 'GET';
				beanRequestLeccion.operation = 'paginate';
				$('#modalCargandoLeccion').modal('show');
			} else {
				swal('No se encontró el alumno', '', 'info');
			}
		};
	});
	document.querySelectorAll('.ver-cuestionarios-sub').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			clienteSelected = findByCliente(
				btn.parentElement.parentElement.getAttribute('idcliente')
			);

			if (clienteSelected != undefined) {
				document.querySelector('#seccion-cliente').classList.add('d-none');
				document.querySelector('#seccion-leccion').classList.add('d-none');
				document.querySelector('#seccion-respuesta').classList.remove('d-none');
				beanRequestRespuesta.operation = 'obtener';
				beanRequestRespuesta.type_request = 'GET';
				respuestaSelected = { tipo: 2 };
				document.querySelector('#tablaNombreRespuesta').innerHTML = 'SUBTITULO';
				$('#modalCargandoRespuesta').modal('show');
			} else {
				swal('No se encontró el alumno', '', 'info');
			}
		};
	});
	document.querySelectorAll('.ver-cuestionarios-cap').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			clienteSelected = findByCliente(
				btn.parentElement.parentElement.getAttribute('idcliente')
			);

			if (clienteSelected != undefined) {
				document.querySelector('#seccion-cliente').classList.add('d-none');
				document.querySelector('#seccion-leccion').classList.add('d-none');
				document.querySelector('#seccion-respuesta').classList.remove('d-none');
				beanRequestRespuesta.operation = 'obtener';
				beanRequestRespuesta.type_request = 'GET';
				respuestaSelected = { tipo: 1 };
				document.querySelector('#tablaNombreRespuesta').innerHTML = 'CAPÍTULO';
				$('#modalCargandoRespuesta').modal('show');
			} else {
				swal('No se encontró el alumno', '', 'info');
			}
		};
	});
}

function addViewArchivosPreviusCliente() {
	$('#txtImagenCliente').change(function () {
		filePreview(this, '#imagePreview');
	});

	$('#txtVideoCliente').change(function () {
		videoPreview(this, '#videoPreview');
	});
}

function findByClienteByCuenta(cuenta) {
	return beanPaginationCliente.list[1].find((Cliente) => {
		if (cuenta == Cliente.cuenta) {
			return Cliente;
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

var validarDormularioCliente = () => {
	if (document.querySelector('#txtCodigoCliente').value == '') {
		swal({
			title: 'Vacío',
			text: 'Ingrese Código',
			type: 'warning',
			timer: 800,
			showConfirmButton: false,
		});
		return false;
	}
	if (document.querySelector('#txtNombreCliente').value == '') {
		swal({
			title: 'Vacío',
			text: 'Ingrese Nombre',
			type: 'warning',
			timer: 800,
			showConfirmButton: false,
		});
		return false;
	}

	if (beanRequestCliente.operation == 'add') {
		if (document.querySelector('#txtImagenCliente').files.length == 0) {
			swal({
				title: 'Vacío',
				text: 'Ingrese Imagen',
				type: 'warning',
				timer: 800,
				showConfirmButton: false,
			});
			return false;
		}
		if (document.querySelector('#txtVideoCliente').files.length == 0) {
			swal({
				title: 'Vacío',
				text: 'Ingrese Video',
				type: 'warning',
				timer: 800,
				showConfirmButton: false,
			});
			return false;
		}
	}

	return true;
};

function filePreview(input, imagen) {
	if (input.files && input.files[0]) {
		let reader = new FileReader();
		imagen;
		reader.onload = function (e) {
			$(imagen).html(
				"<img width='100%' alt='user-picture' class='img-responsive center-box' src='" +
					e.target.result +
					"' />"
			);
		};
		reader.readAsDataURL(input.files[0]);
	}
}
function videoPreview(input, imagen) {
	if (input.files && input.files[0]) {
		let reader = new FileReader();
		imagen;
		reader.onload = function (e) {
			$(imagen).html(
				"<video width='100%' alt='user-picture' class='img-responsive center-box' controls ><source src='" +
					e.target.result +
					"' type='video/mp4'></video>"
			);
		};
		reader.readAsDataURL(input.files[0]);
	}
}
