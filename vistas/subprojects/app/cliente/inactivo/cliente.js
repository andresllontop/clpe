var beanPaginationCliente;
var clienteSelected;
var beanRequestCliente = new BeanRequest();
var beanRequestCurso_c = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
	beanRequestCliente.entity_api = 'cliente';
	beanRequestCliente.operation = 'paginate';
	beanRequestCliente.type_request = 'GET';
	beanRequestCurso_c.entity_api = 'libros';
	beanRequestCurso_c.operation = 'obtener';
	beanRequestCurso_c.type_request = 'GET';
	$('#sizePageCliente').change(function () {
		beanRequestCliente.type_request = 'GET';
		beanRequestCliente.operation = 'paginate';
		$('#modalCargandoCliente').modal('show');
	});
	processAjaxCurso_c();
	$('#modalCargandoCliente').modal('show');

	$('#modalCargandoCliente').on('shown.bs.modal', function () {
		processAjaxCliente();
	});
	$('#btnAbrirCliente').click(function () {
		beanRequestCliente.operation = 'add';
		beanRequestCliente.type_request = 'POST';
		document.querySelector('#btnSubmit').innerHTML = 'REGISTRAR';
		addClass(document.querySelector('#viewDatoMonetario'), 'd-none');
		removeClass(document.querySelector('#htmlHeaderOption'), 'd-none');

		addClass(
			document.querySelector('#txtTipoInscripcionCliente').parentElement
				.parentElement,
			'd-none'
		);

		removeClass(document.querySelector('#viewCliente'), 'd-none');

		$('#tituloModalManCliente').html('REGISTRAR ALUMNO');
		$('#ventanaModalManCliente').modal('show');
		addCliente();
	});
	$('#formularioClienteSearch').submit(function (event) {
		event.preventDefault();
		event.stopPropagation();

		beanRequestCliente.type_request = 'GET';
		beanRequestCliente.operation = 'paginate';
		$('#modalCargandoCliente').modal('show');
	});
	$('#formularioCliente').submit(function (event) {
		event.preventDefault();
		event.stopPropagation();
		if (beanRequestCliente.operation == 'add') {
			if (document.getElementsByName('radioTipoCliente')[1].checked) {
				beanRequestCliente.operation = 'add-libro';
				if (validarFormularioClienteRegistrado()) {
					$('#modalCargandoCliente').modal('show');
				}
			} else {
				beanRequestCliente.operation = 'add';
				if (validarFormularioCliente()) {
					$('#modalCargandoCliente').modal('show');
				}
			}
		} else {
			if (validarFormularioCliente()) {
				$('#modalCargandoCliente').modal('show');
			}
		}
	});
	document.querySelector('#ButtonPassword1').onclick = () => {
		if (
			document
				.querySelector('#ButtonPassword1')
				.firstElementChild.className.includes('zmdi-eye-off')
		) {
			document
				.querySelector('#txtPasswordUsuario')
				.setAttribute('type', 'password');
			document
				.querySelector('#ButtonPassword1')
				.firstElementChild.classList.remove('zmdi-eye-off');
			document
				.querySelector('#ButtonPassword1')
				.firstElementChild.classList.add('zmdi-eye');
		} else {
			document
				.querySelector('#txtPasswordUsuario')
				.setAttribute('type', 'text');
			document
				.querySelector('#ButtonPassword1')
				.firstElementChild.classList.remove('zmdi-eye');
			document
				.querySelector('#ButtonPassword1')
				.firstElementChild.classList.add('zmdi-eye-off');
		}
	};
	document.querySelector('#txtNombreCliente').onkeyup = (e) => {
		document.querySelector('#txtUsuarioName').value = e.target.value;
	};
	document.querySelector('#txtEmailUsuario').onkeyup = (e) => {
		document.querySelector('#txtPasswordUsuario').value =
			'(' + e.target.value + ')';
		document.querySelector('#txtPasswordUsuario2').value =
			'(' + e.target.value + ')';
	};
	$('input[type="radio"]').on('change', function (e) {
		if (e.target.name == 'radioTipoCliente') {
			if (e.target.value == '1') {
				removeClass(document.querySelector('#htmlAddNew'), 'd-none');
				addClass(document.querySelector('#htmlAdd'), 'd-none');
			} else {
				removeClass(document.querySelector('#htmlAdd'), 'd-none');
				addClass(document.querySelector('#htmlAddNew'), 'd-none');
			}
		}
	});
});
function processAjaxCurso_c() {
	let parameters_pagination = '';
	switch (beanRequestCurso_c.operation) {
		case 'delete':
			parameters_pagination = '?id=' + curso_cSelected.idlibro;
			break;
		default:
			break;
	}
	$.ajax({
		url:
			getHostAPI() +
			beanRequestCurso_c.entity_api +
			'/' +
			beanRequestCurso_c.operation +
			parameters_pagination,
		type: beanRequestCurso_c.type_request,
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
				beanPaginationCurso_c = beanCrudResponse.beanPagination;
				listaCurso_c(beanPaginationCurso_c);
			}
		})
		.fail(function (jqXHR, textStatus, errorThrown) {
			$('#modalCargandoCurso_c').modal('hide');
			showAlertErrorRequest();
		});
}
function processAjaxCliente() {
	let form_data = new FormData();

	let parameters_pagination = '';
	let json = '';
	if (
		beanRequestCliente.operation == 'update' ||
		beanRequestCliente.operation == 'add' ||
		beanRequestCliente.operation == 'add-libro'
	) {
		let radioButTrat = document.getElementsByName('radioTipoComunicacion');
		let valorRadio = 0;
		for (var i = 0; i < radioButTrat.length; i++) {
			if (radioButTrat[i].checked == true) {
				valorRadio = radioButTrat[i].value;
			}
		}
		let today = new Date();
		let date =
			today.getFullYear() +
			'-' +
			(today.getMonth() + 1) +
			'-' +
			today.getDate();
		let time =
			today.getHours() + ':' + today.getMinutes() + ':' + today.getSeconds();
		if (document.getElementsByName('radioTipoCliente')[1].checked) {
			json = {
				email: document.querySelector('#txtEmailOtherUsuario').value,
				libro: document.querySelector('#txtLibroOtherCliente').value,
			};
		} else {
			json = {
				nombre: document.querySelector('#txtNombreCliente').value.trim(),
				apellido: document.querySelector('#txtApellidoCliente').value.trim(),
				ocupacion: document.querySelector('#txtEspecialidadCliente').value,
				telefono: document.querySelector('#txtTelefonoCliente').value,
				vendedor:
					document.querySelector('#txtCodigoVendedorCliente').value == ''
						? null
						: document.querySelector('#txtCodigoVendedorCliente').value,
				pais: document.querySelector('#txtPaisCliente').value,
				tipomedio: valorRadio,
				tipo_inscripcion: document.querySelector('#txtTipoInscripcionCliente')
					.value,
				cuenta: {
					idcuenta: 0,
					codigo: '',
					email: document.querySelector('#txtEmailUsuario').value,
					usuario: document.querySelector('#txtUsuarioName').value,
					clave: document.querySelector('#txtPasswordUsuario').value,
					precio: document.querySelector('#txtMontoCliente').value,
					libro: document.querySelector('#txtLibroCliente').value,
				},

				economico: {
					nombre_banco: document.querySelector('#txtNombreBancoCliente').value,
					comision: document.querySelector('#txtComisionCliente').value,
					moneda: document.querySelector('#txtTipoMonedaCliente').value,
					precio: document.querySelector('#txtMontoCliente').value,
					tipo: 2,
					fecha: date + ' ' + time,
					libro: document.querySelector('#txtLibroCliente').value,
				},
			};
		}
	} else {
		form_data = null;
	}

	switch (beanRequestCliente.operation) {
		case 'delete':
			parameters_pagination = '?id=' + clienteSelected.idcliente;
			parameters_pagination += '&libro=' + clienteSelected.cuenta.libro.codigo;
			parameters_pagination += '&estado=0';
			break;

		case 'update':
			json.idcliente = clienteSelected.idcliente;
			json.cuenta.idlibroCuenta = clienteSelected.cuenta.idlibroCuenta;
			json.cuenta.idcuenta = clienteSelected.cuenta.cuenta.idcuenta;
			json.cuenta.codigo = clienteSelected.cuenta.cuenta.cuentaCodigo;
			json.cuenta.estado = 1;
			json.cuenta.cuentaverificacion = Math.floor(
				Math.random() * (9999 - 1000) + 1000
			);
			if (document.querySelector('#txtImagenVoucher').files.length != 0) {
				let dataImagen = $('#txtImagenVoucher').prop('files')[0];
				form_data.append('txtImagenVoucher', dataImagen);
			}
			form_data.append('class', JSON.stringify(json));
			break;
		case 'updateestado':
			form_data = new FormData();
			form_data.append('class', JSON.stringify(json));
			break;

		case 'add-libro':

		case 'add':
			form_data.append('class', JSON.stringify(json));
			break;

		default:
			parameters_pagination +=
				'?filtro=' + document.querySelector('#txtSearchCliente').value.trim();
			parameters_pagination += '&estado=0&libro=';
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
			beanRequestCliente.operation == 'updateestado' ||
			beanRequestCliente.operation == 'update' ||
			beanRequestCliente.operation == 'add' ||
			beanRequestCliente.operation == 'add-libro'
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
						$('.progress-bar-cliente').css({
							width: Math.round(percentComplete * 100) + '%',
						});
						$('.progress-bar-cliente').text(
							Math.round(percentComplete * 100) + '%'
						);
						$('.progress-bar-cliente').attr(
							'aria-valuenow',
							+Math.round(percentComplete * 100)
						);
						if (percentComplete === 1) {
							// $('.progress-bar-cliente').addClass('hide');
							$('.progress-bar-cliente').css({
								width: +'100%',
							});
							$('.progress-bar-cliente').text('Cargando ... 100%');
							$('.progress-bar-cliente').attr('aria-valuenow', '100');
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
						$('.progress-bar-cliente').css({
							width: Math.round(percentComplete * 100) + '%',
						});
						$('.progress-bar-cliente').text(
							Math.round(percentComplete * 100) + '%'
						);
						$('.progress-bar-cliente').attr(
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
			$('#modalCargandoCliente').modal('toggle');
			if (beanCrudResponse.messageServer !== null) {
				if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
					swal({
						title: 'Realizado',
						text: 'Acción realizada existosamente!',
						type: 'success',
						timer: 2000,
						showConfirmButton: false,
					});
				} else {
					swal({
						title: 'VERIFICACIÓN!',
						text: beanCrudResponse.messageServer,
						type: 'info',
						timer: 2000,
						showConfirmButton: false,
					});
				}
				$('#ventanaModalManCliente').modal('hide');
			}
			if (beanCrudResponse.beanPagination !== null) {
				beanPaginationCliente = beanCrudResponse.beanPagination;
				listaCliente(beanPaginationCliente);
			}
		})
		.fail(function () {
			$('#modalCargandoCliente').modal('hide');
			showAlertErrorRequest();
		});
}

function addCliente(cliente = undefined) {
	//LIMPIAR LOS CAMPOS

	document.querySelector('#txtNombreCliente').value =
		cliente == undefined ? '' : cliente.nombre;
	document.querySelector('#txtApellidoCliente').value =
		cliente == undefined ? '' : cliente.apellido;
	document.querySelector('#txtCodigoVendedorCliente').value =
		cliente == undefined ? '' : cliente.vendedor;
	document.querySelector('#txtLibroCliente').value =
		cliente == undefined ? 'L01' : cliente.cuenta.libro.codigo;

	if (cliente == undefined) {
		document.getElementsByName('radioTipoComunicacion')[4].checked = true;
	} else {
		document.getElementsByName('radioTipoComunicacion')[
			parseInt(
				cliente.tipomedio == null || cliente.tipomedio == 0
					? 5
					: cliente.tipomedio
			) - 1
		].checked = true;
	}

	document.querySelector('#txtTelefonoCliente').value =
		cliente == undefined ? '' : cliente.telefono;
	document.querySelector('#txtEspecialidadCliente').value =
		cliente == undefined ? '' : cliente.ocupacion;
	document.querySelector('#txtPaisCliente').value =
		cliente == undefined ? '' : cliente.pais;
	document.querySelector('#txtMontoCliente').value =
		cliente == undefined ? '' : cliente.cuenta.monto;
	document.querySelector('#txtComisionCliente').value = 0;
	document.querySelector('#txtUsuarioName').value =
		cliente == undefined ? '' : cliente.cuenta.cuenta.usuario;
	document.querySelector('#txtEmailUsuario').value =
		cliente == undefined ? '' : cliente.cuenta.cuenta.email;
	document.querySelector('#txtPasswordUsuario').value =
		cliente == undefined ? '' : cliente.cuenta.cuenta.clave;
	document.querySelector('#txtPasswordUsuario2').value =
		cliente == undefined ? '' : cliente.cuenta.cuenta.clave;

	if (cliente !== undefined) {
		$('#imagePreview').html(
			`<img  style="height: 168px;width: 177px;"  alt='user-picture' class='img-responsive center-box img-circle' src='${getHostFrontEnd()}${
				cliente.cuenta.cuenta.foto == '' || cliente.cuenta.cuenta.foto == null
					? 'vistas/assets/img/userclpe.png'
					: 'adjuntos/clientes/' + cliente.cuenta.cuenta.foto
			}' />`
		);
		if (cliente.cuenta.imagen == '' || cliente.cuenta.imagen == null) {
			$('#imagenVaucherPreview').html(``);
			// document.querySelector("#txtMontoCliente").parentElement.parentElement.style.marginTop = "5em";
		} else {
			$('#imagenVaucherPreview').html(
				`<img  style="height:180px;width: 100%;"  alt='user-picture' class='img-responsive center-box ' src='${
					getHostFrontEnd() +
					'adjuntos/clientes/comprobante/' +
					cliente.cuenta.imagen
				}' />`
			);
			//document.querySelector("#txtMontoCliente").parentElement.parentElement.style.marginTop = 0;
		}
	} else {
		$('#imagePreview').html(
			`<img  style="height: 168px; width: 177px;"  alt='user-picture' class='img-responsive center-box img-circle' src='${getHostFrontEnd()}vistas/assets/img/userclpe.png' />`
		);
		$('#imagenVaucherPreview').html(
			`<img  style="height:180px;width: 100%;" alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}vistas/assets/img/framed.png' />`
		);
	}
	addViewArchivosPrevius();
}

function TipoMedioComunicacion(params) {
	switch (parseInt(params)) {
		case 1:
			return 'Facebook';
		case 2:
			return 'Youtube';
		case 3:
			return 'Página web Club de Lectura';
		case 4:
			return 'Página web YYYYY';
		default:
			return 'OTROS MEDIOS';
	}
}
function listaCurso_c(beanPagination) {
	document.querySelector('#txtLibroCliente').innerHTML = '';

	let row = '';
	if (beanPagination.list.length > 0) {
		beanPagination.list.forEach((curso_c) => {
			row += `
		<option value="${curso_c.codigo}">${curso_c.nombre}</option>
		`;
		});
		document.querySelector('#txtLibroCliente').innerHTML += row;
	}
}
function listaCliente(beanPagination) {
	document.querySelector('#tbodyCliente').innerHTML = '';
	document.querySelector('#titleCliente').innerHTML = 'Alumnos No Matriculados';
	document.querySelector('#txtCountCliente').value = beanPagination.countFilter;
	let row = '';
	if (beanPagination.list.length == 0) {
		destroyPagination($('#paginationCliente'));
		row += `<tr>
        <td class="text-center" colspan="10">NO HAY CLIENTES</td>
        </tr>`;

		document.querySelector('#tbodyCliente').innerHTML += row;
		return;
	}
	beanPagination.list.forEach((cliente) => {
		row += `<tr  idcliente="${cliente.idcliente}">
<td class="text-center" >${
			cliente.cuenta.fecha == null
				? ''
				: cliente.cuenta.fecha.split(' ')[0].split('-')[2] +
				  '-' +
				  cliente.cuenta.fecha.split(' ')[0].split('-')[1] +
				  '-' +
				  cliente.cuenta.fecha.split(' ')[0].split('-')[0]
		}</br>${
			cliente.cuenta.fecha == null ? '' : cliente.cuenta.fecha.split(' ')[1]
		}</td>
<td class="text-center" >${cliente.nombre} <br> ${cliente.apellido}</td>
<td class="text-center" >${cliente.telefono}</td>
<td class="text-center" >${cliente.cuenta.cuenta.email}</td>
<td class="text-center" >${cliente.cuenta.libro.nombre}</td>
<td class="text-center" >${TipoMedioComunicacion(cliente.tipomedio)}</td>
<td class="text-center d-none" >${
			cliente.vendedor == null ? '' : cliente.vendedor
		}</td>
<td class="text-center d-none" >${cliente.cuenta.usuario}</td>
<td class="text-center d-none"><img src="${getHostFrontEnd()}${
			cliente.cuenta.foto == '' || cliente.cuenta.foto == null
				? 'vistas/assets/img/userclpe.png'
				: 'adjuntos/clientes/' + cliente.cuenta.foto
		}" class="img-responsive center-box" style="width:50px;height:60px;"></td>
<td class="text-center d-none" >${cliente.cuenta.precio}</td>
<td class="text-center d-none"><img src="${
			getHostFrontEnd() +
			'adjuntos/clientes/comprobante/' +
			cliente.cuenta.voucher
		}" class="img-responsive center-box" style="width:50px;height:60px;"></td>
<td class="text-center d-none">
<button class="btn ${
			cliente.cuenta.estado == 1 ? 'btn-success' : 'btn-warning'
		} editar-estado-cliente" ><i class="zmdi ${
			cliente.cuenta.estado == 1 ? 'zmdi-check-all' : 'zmdi-close'
		}"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-warning inscribir-cliente" >INSCRIBIR </button>
</td>
<td class="text-center">
<button class="btn btn-info editar-cliente" ><i class="zmdi zmdi-eye"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-cliente"><i class="zmdi zmdi-delete"></i></button>
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
	addEventsButtonsAdmin();
}

function addEventsButtonsAdmin() {
	document.querySelectorAll('.editar-estado-cliente').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			clienteSelected = findByCliente(
				btn.parentElement.parentElement.getAttribute('idcliente')
			);

			if (clienteSelected != undefined && clienteSelected.cuenta.estado == 0) {
				removeClass(document.querySelector('#viewDatoMonetario'), 'd-none');
				document.querySelector('#btnSubmit').innerHTML = 'INSCRIBIR';
				clienteSelected.cuenta.estado = 1;
				beanRequestCliente.type_request = 'POST';
				beanRequestCliente.operation = 'update';
				$('#modalCargandoCliente').modal('show');
			} else {
				showAlertTopEnd(
					'info',
					'Vacío!',
					'No se encontró el alumno para poder editar'
				);
			}
		};
	});
	document.querySelectorAll('.editar-cliente').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			clienteSelected = findByCliente(
				btn.parentElement.parentElement.getAttribute('idcliente')
			);

			if (clienteSelected != undefined) {
				addClass(document.querySelector('#htmlHeaderOption'), 'd-none');
				addClass(document.querySelector('#viewDatoMonetario'), 'd-none');
				document.querySelector('#btnSubmit').innerHTML = 'INSCRIBIR';
				addCliente(clienteSelected);
				$('#tituloModalManCliente').html('VER DATOS DEL ALUMNO');
				removeClass(document.querySelector('#viewCliente'), 'd-none');
				addClass(document.querySelector('#btnSubmit'), 'd-none');
				$('#ventanaModalManCliente').modal('show');
				beanRequestCliente.type_request = 'POST';
				beanRequestCliente.operation = 'update';
			} else {
				showAlertTopEnd('warning', 'No se encuentra el cliente', '');
			}
		};
	});
	document.querySelectorAll('.inscribir-cliente').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			clienteSelected = findByCliente(
				btn.parentElement.parentElement.getAttribute('idcliente')
			);

			if (clienteSelected != undefined) {
				addClass(document.querySelector('#htmlHeaderOption'), 'd-none');
				removeClass(document.querySelector('#viewDatoMonetario'), 'd-none');
				removeClass(
					document.querySelector('#txtTipoInscripcionCliente').parentElement
						.parentElement,
					'd-none'
				);

				document.querySelector('#btnSubmit').innerHTML = 'INSCRIBIR';
				addCliente(clienteSelected);
				clienteSelected.cuenta.estado = 1;
				$('#tituloModalManCliente').html('INSCRIBIR ALUMNO');
				addClass(document.querySelector('#viewCliente'), 'd-none');
				removeClass(document.querySelector('#btnSubmit'), 'd-none');

				$('#ventanaModalManCliente').modal('show');
				beanRequestCliente.type_request = 'POST';
				beanRequestCliente.operation = 'update';
			} else {
				showAlertTopEnd('warning', 'No se encuentra el cliente', '');
			}
		};
	});

	document.querySelectorAll('.eliminar-cliente').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			//   $('[data-toggle="tooltip"]').tooltip("hide");
			clienteSelected = findByCliente(
				btn.parentElement.parentElement.getAttribute('idcliente')
			);

			if (clienteSelected != undefined) {
				beanRequestCliente.type_request = 'GET';
				beanRequestCliente.operation = 'delete';
				$('#modalCargandoCliente').modal('show');
			} else {
				showAlertTopEnd('warning', 'No se encuentra el cliente', '');
			}
		};
	});
}

function addViewArchivosPrevius() {
	document.getElementsByName('radioTipoComunicacion').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onchange = function () {
			if (btn.checked == true && parseInt(btn.value) == parseInt(4)) {
				removeClass(
					document.querySelector('#txtCodigoVendedorCliente').parentElement
						.parentElement,
					'd-none'
				);
			} else if (btn.checked == true) {
				addClass(
					document.querySelector('#txtCodigoVendedorCliente').parentElement
						.parentElement,
					'd-none'
				);
			}
		};
	});
	$('#txtImagenCliente').change(function () {
		filePreview(this, '#imagePreview');
	});
	$('#txtImagenVoucher').change(function () {
		filePreview(this, '#imagenVaucherPreview');
		//  document.querySelector("#txtMontoCliente").parentElement.parentElement.style.marginTop = "5em";
	});
}

function filePreview(input, imagen) {
	if (input.files && input.files[0]) {
		let reader = new FileReader();
		imagen;
		reader.onload = function (e) {
			$(imagen).html(
				"<img width='244' alt='user-picture' class='img-responsive center-box' src='" +
					e.target.result +
					"' />"
			);
		};
		reader.readAsDataURL(input.files[0]);
	}
}

function clientePreview(input, imagen) {
	if (input.files && input.files[0]) {
		let reader = new FileReader();
		imagen;
		reader.onload = function (e) {
			$(imagen).html(
				"<cliente width='244' alt='user-picture' class='img-responsive center-box' controls ><source src='" +
					e.target.result +
					"' type='cliente/mp4'></cliente>"
			);
		};
		reader.readAsDataURL(input.files[0]);
	}
}

function findIndexCliente(idbusqueda) {
	return beanPaginationCliente.list.findIndex((Cliente) => {
		if (Cliente.idcliente == parseInt(idbusqueda)) return Cliente;
	});
}

function findByCliente(idcliente) {
	return beanPaginationCliente.list.find((Cliente) => {
		if (parseInt(idcliente) == Cliente.idcliente) {
			return Cliente;
		}
	});
}

var validarFormularioCliente = () => {
	let radioButTrat = document.getElementsByName('radioTipoComunicacion');
	let valorRadio = 0;
	for (var i = 0; i < radioButTrat.length; i++) {
		if (radioButTrat[i].checked == true) {
			valorRadio = radioButTrat[i].value;
		}
	}
	if (valorRadio < 1 && valorRadio > 5) {
		showAlertTopEnd(
			'info',
			'Vacío',
			'Selecciona un medio de comunicación correcto en la pregunta'
		);
		return false;
	}
	if (
		valorRadio == 4 &&
		document.querySelector('#txtCodigoVendedorCliente').value == ''
	) {
		showAlertTopEnd('info', 'Vacío', 'Ingrese Código Vendedor');
		return false;
	}
	if (document.querySelector('#txtNombreCliente').value == '') {
		showAlertTopEnd('info', 'Vacío', 'Ingrese Nombre');
		return false;
	}
	if (document.querySelector('#txtApellidoCliente').value == '') {
		showAlertTopEnd('info', 'Vacío', 'Ingrese Apellido');
		return false;
	}
	let numero = numero_campo(document.querySelector('#txtTelefonoCliente'));
	if (numero != undefined) {
		if (numero.value == '') {
			showAlertTopEnd(
				'info',
				'Formato Incorrecto',
				'Por favor ingrese ' + numero.labels[0].innerText
			);
		} else {
			showAlertTopEnd(
				'info',
				'Formato Incorrecto',
				'Por favor ingrese sólo números, ' + numero.labels[0].innerText
			);
		}

		return false;
	}
	if (document.querySelector('#txtEspecialidadCliente').value == '') {
		showAlertTopEnd('info', 'Vacío', 'Ingrese Especialidad');
		return false;
	}

	if (document.querySelector('#txtUsuarioName').value == '') {
		showAlertTopEnd('info', 'Vacío', 'Ingrese Usuario nombre');
		return false;
	}
	let email = email_campo(document.querySelector('#txtEmailUsuario'));

	if (email != undefined) {
		if (email.value == '') {
			showAlertTopEnd(
				'info',
				'Formato Incorrecto',
				'Por favor ingrese correo electrónico'
			);
		} else {
			showAlertTopEnd(
				'info',
				'Formato Incorrecto',
				'Por favor ingrese un correo electrónico Válido'
			);
		}

		return false;
	}

	if (document.querySelector('#txtPasswordUsuario').value == '') {
		showAlertTopEnd('info', 'Vacío', 'Ingrese Contraseña');
		return false;
	}
	if (
		document.querySelector('#txtPasswordUsuario').value !==
		document.querySelector('#txtPasswordUsuario2').value
	) {
		showAlertTopEnd(
			'info',
			'Formato Inocrrecto',
			'las constraseñas no son iguales'
		);
		return false;
	}
	/*
    if (beanRequestCliente.operation == 'update') {

        if (document.querySelector("#txtImagenVoucher").files.length == 0) {
            showAlertTopEnd("info", "Vacío", "ingrese Imagen");
            return false;
        }
        if (!(document.querySelector("#txtImagenVoucher").files[0].type == "image/png" || document.querySelector("#txtImagenVoucher").files[0].type == "image/jpg" || document.querySelector("#txtImagenVoucher").files[0].type == "image/jpeg")) {
            showAlertTopEnd("info", "Formato Incorrecto", "Ingrese tipo de arhivo Imagen => png,jpg,jpeg");
            return false;
        }
        //menor a   1700 KB
        if (document.querySelector("#txtImagenVoucher").files[0].size > (1700 * 1024)) {
            showAlertTopEnd("info", "Tamaño excedido", "el tamaño del archivo tiene que ser menor a 1700 KB");
            return false;
        }


    }
    */
	return true;
};

var validarFormularioClienteRegistrado = () => {
	if (document.querySelector('#txtLibroOtherCliente').value == '') {
		showAlertTopEnd('info', 'Vacío', 'Seleccione Libro');
		return false;
	}
	let email = email_campo(document.querySelector('#txtEmailOtherUsuario'));
	if (email != undefined) {
		if (email.value == '') {
			showAlertTopEnd(
				'info',
				'Formato Incorrecto',
				'Por favor ingrese correo electrónico'
			);
		} else {
			showAlertTopEnd(
				'info',
				'Formato Incorrecto',
				'Por favor ingrese un correo electrónico Válido'
			);
		}
		return false;
	}
	return true;
};
