var beanPaginationCurso;
var cursoSelected;
var beanRequestCurso = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
	beanRequestCurso.entity_api = 'cursos';
	beanRequestCurso.operation = 'paginate';
	beanRequestCurso.type_request = 'GET';

	$('#sizePageCurso').change(function () {
		beanRequestCurso.type_request = 'GET';
		beanRequestCurso.operation = 'paginate';
		$('#modalCargandoCurso').modal('show');
	});

	$('#modalCargandoCurso').modal('show');

	$('#modalCargandoCurso').on('shown.bs.modal', function () {
		processAjaxCurso();
	});

	$('#ventanaModalManCurso').on('hide.bs.modal', function () {
		beanRequestCurso.type_request = 'GET';
		beanRequestCurso.operation = 'paginate';
	});

	$('#btnAbrirbook').click(function () {
		beanRequestCurso.operation = 'add';
		beanRequestCurso.type_request = 'POST';
		$('#imagePreview').html('');
		$('#tituloModalManCurso').html('REGISTRAR CURSO');
		addCurso();
		$('#ventanaModalManCurso').modal('show');
	});
	$('#formularioCurso').submit(function (event) {
		event.preventDefault();
		event.stopPropagation();

		if (validateFormCurso()) {
			$('#modalCargandoCurso').modal('show');
		}
	});
	$('#txtTipoFileCurso').change(function (e) {
		if (e.target.value == '1') {
			removeClass(document.querySelector('#fileImagenCurso'), 'd-none');
			addClass(document.querySelector('#fileVideoCurso'), 'd-none');
		} else {
			removeClass(document.querySelector('#fileVideoCurso'), 'd-none');
			addClass(document.querySelector('#fileImagenCurso'), 'd-none');
		}
	});
});

function processAjaxCurso() {
	let form_data = new FormData();

	let parameters_pagination = '';
	let json = '';
	if (
		beanRequestCurso.operation == 'update' ||
		beanRequestCurso.operation == 'add'
	) {
		json = {
			titulo: document.querySelector('#txtTituloCurso').value,
			precio: document.querySelector('#txtPrecioCurso').value,
			descuento: document.querySelector('#txtDescuentoCurso').value,
			descripcion: document.querySelector('#txtDescripcionCurso').value,
			tipo: document.querySelector('#txtTipoCurso').value,
			video:
				document.querySelector('#txtTipoFileCurso').value == '1'
					? ''
					: document
							.querySelector('#txtVideoCurso')
							.value.trim()
							.replace('https://youtu.be/', ''),
		};
	} else {
		form_data = null;
	}

	switch (beanRequestCurso.operation) {
		case 'delete':
			parameters_pagination = '?id=' + cursoSelected.idcurso;
			break;

		case 'update':
			json.idcurso = cursoSelected.idcurso;

			if (document.querySelector('#txtImagenCurso').files.length !== 0) {
				let dataFoto = $('#txtImagenCurso').prop('files')[0];
				form_data.append('txtImagenCurso', dataFoto);
			}

			if (document.querySelector('#txtImagenPortadaCurso').files.length !== 0) {
				let dataPortada = $('#txtImagenPortadaCurso').prop('files')[0];
				form_data.append('txtImagenPortadaCurso', dataPortada);
			}
			if (
				document.querySelector('#txtImagenPresentacionCurso').files.length !==
					0 &&
				document.querySelector('#txtTipoFileCurso').value == '1'
			) {
				let dataPresentacion = $('#txtImagenPresentacionCurso').prop(
					'files'
				)[0];
				form_data.append('txtImagenPresentacionCurso', dataPresentacion);
			}

			form_data.append('class', JSON.stringify(json));
			break;
		case 'add':
			let data = $('#txtImagenCurso').prop('files')[0];
			form_data.append('txtImagenCurso', data);

			let dataPortada = $('#txtImagenPortadaCurso').prop('files')[0];
			form_data.append('txtImagenPortadaCurso', dataPortada);

			if (document.querySelector('#txtTipoFileCurso').value == '1') {
				let dataPresentacion = $('#txtImagenPresentacionCurso').prop(
					'files'
				)[0];
				form_data.append('txtImagenPresentacionCurso', dataPresentacion);
			}

			form_data.append('class', JSON.stringify(json));
			break;

		default:
			parameters_pagination += '?filtro=';
			parameters_pagination +=
				'&pagina=' + document.querySelector('#pageCurso').value.trim();
			parameters_pagination +=
				'&registros=' + document.querySelector('#sizePageCurso').value.trim();
			break;
	}
	$.ajax({
		url:
			getHostAPI() +
			beanRequestCurso.entity_api +
			'/' +
			beanRequestCurso.operation +
			parameters_pagination,
		type: beanRequestCurso.type_request,
		headers: {
			Authorization: 'Bearer ' + Cookies.get('clpe_token'),
		},

		data: form_data,
		cache: false,
		contentType:
			beanRequestCurso.operation == 'update' ||
			beanRequestCurso.operation == 'add'
				? false
				: 'application/json; charset=UTF-8',
		processData: false,
		dataType: 'json',
	})
		.done(function (beanCrudResponse) {
			$('#modalCargandoCurso').modal('hide');
			if (beanCrudResponse.messageServer !== null) {
				if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
					showAlertTopEnd(
						'success',
						'Realizado',
						'Acción realizada existosamente!'
					);
					document.querySelector('#pageCurso').value = 1;
					document.querySelector('#sizePageCurso').value = 20;
					$('#ventanaModalManCurso').modal('hide');
				} else {
					showAlertTopEnd('warning', 'Error', beanCrudResponse.messageServer);
				}
			}
			if (beanCrudResponse.beanPagination !== null) {
				beanPaginationCurso = beanCrudResponse.beanPagination;
				listaCurso(beanPaginationCurso);
			}
		})
		.fail(function (jqXHR, textStatus, errorThrown) {
			$('#modalCargandoCurso').modal('hide');
			showAlertErrorRequest();
		});
}

function addCurso(curso = undefined) {
	//LIMPIAR LOS CAMPOS

	document.querySelector('#txtTituloCurso').value =
		curso == undefined ? '' : curso.titulo;
	document.querySelector('#txtTipoCurso').value =
		curso == undefined ? '0' : curso.tipo;
	document.querySelector('#txtDescripcionCurso').value =
		curso == undefined ? '' : curso.descripcion;
	document.querySelector('#txtDescuentoCurso').value =
		curso == undefined ? '' : curso.descuento;
	document.querySelector('#txtPrecioCurso').value =
		curso == undefined ? '' : curso.precio;
	document.querySelector('#txtVideoCurso').value =
		curso == undefined
			? ''
			: curso.video == null
			? ''
			: 'https://youtu.be/' + curso.video;
	if (curso != undefined) {
		document.querySelector('#imagenPreview').innerHTML =
			"<img width='244' alt='user-picture' class='img-responsive center-box' src='" +
			getHostFrontEnd() +
			'adjuntos/libros/' +
			curso.imagen +
			"' />";
		document.querySelector('#imagenPortadaPreview').innerHTML =
			"<img width='244' alt='user-picture' class='img-responsive center-box' src='" +
			getHostFrontEnd() +
			'adjuntos/libros/' +
			curso.portada +
			"' />";
		if (curso.video == '' || curso.video == null) {
			removeClass(document.querySelector('#fileImagenCurso'), 'd-none');
			addClass(document.querySelector('#fileVideoCurso'), 'd-none');
			document.querySelector('#txtTipoFileCurso').value = '1';
			document.querySelector('#imagenPresentacionPreview').innerHTML =
				"<img width='244' alt='user-picture' class='img-responsive center-box' src='" +
				getHostFrontEnd() +
				'adjuntos/libros/' +
				curso.presentacion +
				"' />";
		} else {
			removeClass(document.querySelector('#fileVideoCurso'), 'd-none');
			addClass(document.querySelector('#fileImagenCurso'), 'd-none');
			document.querySelector('#txtTipoFileCurso').value = '2';
		}
	}
	addViewArchivosPrevius();
}

function listaCurso(beanPagination) {
	document.querySelector('#tbodyCurso').innerHTML = '';
	document.querySelector('#titleManagerCurso').innerHTML =
		'[ ' + beanPagination.countFilter + ' ] CURSOS';
	let row = '';

	if (beanPagination.list.length == 0) {
		destroyPagination($('#paginationCurso'));
		row += `<tr>
        <td class="text-center" colspan="6">NO HAY CURSOS</td>
        </tr>`;

		document.querySelector('#tbodyCurso').innerHTML += row;
		return;
	}

	document.querySelector('#tbodyCurso').innerHTML += row;
	let html2;
	beanPagination.list.forEach((curso) => {
		row += `<tr  idcurso="${curso.idcurso}">
<td class="text-center">${curso.titulo}</td>
<td class="text-center">${curso.descripcion}</td>
<td class="text-center">${
			curso.tipo == 1 ? 'CURSO ONLINE' : 'CURSO POR ZOOM'
		}</td>
<td class="text-center">${curso.precio}</td>
<td class="text-center">${curso.descuento}</td>
<td  class="text-center">
<img  
  src="${getHostFrontEnd()}adjuntos/libros/${curso.imagen}"
  alt="${curso.imagen}"
  class="img-responsive center-box"style="width:65px;height:75px;"
  />
  </td>
  <td  class="text-center"><img  
  src="${getHostFrontEnd()}adjuntos/libros/${curso.portada}"
  alt="${curso.portada}"
  class="img-responsive center-box"style="width:100px;height:60px;"
  /></td>
  <td  class="text-center">`;

		if (curso.video == '' || curso.video == null) {
			row += `
	  <img  
	  src="${getHostFrontEnd()}adjuntos/libros/${curso.presentacion}"
	  alt="${curso.presentacion}"
	  class="img-responsive center-box"style="width:100px;height:60px;"
	  />
  `;
		} else {
			row += `
  <iframe style="width:200px;" src="https://www.youtube-nocookie.com/embed/${curso.video}" frameborder="0"  allowfullscreen></iframe>
  `;
		}

		row += ` 
</td>
<td class="text-center">
<button class="btn btn-info editar-curso" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-curso"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

		// $('[data-toggle="tooltip"]').tooltip();
	});
	document.querySelector('#tbodyCurso').innerHTML += row;
	buildPagination(
		beanPagination.countFilter,
		parseInt(document.querySelector('#sizePageCurso').value),
		document.querySelector('#pageCurso'),
		$('#modalCargandoCurso'),
		$('#paginationCurso')
	);
	addEventsButtonsCurso();
}
function addEventsButtonsCurso() {
	document.querySelectorAll('.editar-curso').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			cursoSelected = findByCurso(
				btn.parentElement.parentElement.getAttribute('idcurso')
			);

			if (cursoSelected != undefined) {
				addCurso(cursoSelected);
				$('#tituloModalManCurso').html('EDITAR CURSO');
				$('#ventanaModalManCurso').modal('show');
				beanRequestCurso.type_request = 'POST';
				beanRequestCurso.operation = 'update';
			} else {
				console.log('warning', 'No se encontró el Almacen para poder editar');
			}
		};
	});
	document.querySelectorAll('.eliminar-curso').forEach((btn) => {
		//AGREGANDO EVENTO CLICK
		btn.onclick = function () {
			//   $('[data-toggle="tooltip"]').tooltip("hide");
			cursoSelected = findByCurso(
				btn.parentElement.parentElement.getAttribute('idcurso')
			);

			if (cursoSelected != undefined) {
				beanRequestCurso.type_request = 'GET';
				beanRequestCurso.operation = 'delete';
				$('#modalCargandoCurso').modal('show');
			} else {
				console.log('warning', 'No se encontró el Almacen para poder editar');
			}
		};
	});
}

function addViewArchivosPrevius() {
	$('#txtImagenCurso').change(function () {
		filePreview(this, '#imagenPreview');
	});
	$('#txtImagenPortadaCurso').change(function () {
		filePreview2(this, '#imagenPortadaPreview');
	});
	$('#txtImagenPresentacionCurso').change(function () {
		filePreview2(this, '#imagenPresentacionPreview');
	});
}
function filePreview(input, imagen) {
	if (input.files && input.files[0]) {
		let reader = new FileReader();
		imagen;
		reader.onload = function (e) {
			$(imagen).html(
				"<img width='150' height='200' alt='user-picture' class='img-responsive center-box' src='" +
					e.target.result +
					"' />"
			);
		};
		reader.readAsDataURL(input.files[0]);
	}
}
function filePreview2(input, imagen) {
	if (input.files && input.files[0]) {
		let reader = new FileReader();
		imagen;
		reader.onload = function (e) {
			$(imagen).html(
				"<img width='300' height='200' alt='user-picture' class='img-responsive center-box' src='" +
					e.target.result +
					"' />"
			);
		};
		reader.readAsDataURL(input.files[0]);
	}
}

function findIndexCurso(idbusqueda) {
	return beanPaginationCurso.list.findIndex((Curso) => {
		if (Curso.idcurso == parseInt(idbusqueda)) return Curso;
	});
}

function findByCurso(idcurso) {
	return beanPaginationCurso.list.find((Curso) => {
		if (parseInt(idcurso) == Curso.idcurso) {
			return Curso;
		}
	});
}
var validateFormCurso = () => {
	if (document.querySelector('#txtTituloCurso').value == '') {
		swal({
			title: 'Vacío',
			text: 'Ingrese titulo',
			type: 'warning',
			timer: 1200,
			showConfirmButton: false,
		});
		return false;
	}
	if (document.querySelector('#txtDescripcionCurso').value == '') {
		swal({
			title: 'Vacío',
			text: 'Ingrese Descripcion',
			type: 'warning',
			timer: 1200,
			showConfirmButton: false,
		});
		return false;
	}
	if (document.querySelector('#txtDescuentoCurso').value == '') {
		swal({
			title: 'Vacío',
			text: 'Ingrese Precio Anterior',
			type: 'warning',
			timer: 1200,
			showConfirmButton: false,
		});
		return false;
	}
	if (document.querySelector('#txtPrecioCurso').value == '') {
		swal({
			title: 'Vacío',
			text: 'Ingrese Precio Actual',
			type: 'warning',
			timer: 1200,
			showConfirmButton: false,
		});
		return false;
	}

	if (document.querySelector('#txtTipoCurso').value == 0) {
		swal({
			title: 'Vacío',
			text: 'Selecciona Tipo',
			type: 'warning',
			timer: 1200,
			showConfirmButton: false,
		});
		return false;
	}
	if (beanRequestCurso.operation == 'add') {
		if (document.querySelector('#txtImagenCurso').files.length == 0) {
			swal({
				title: 'Vacío',
				text: 'Ingrese Imagen',
				type: 'warning',
				timer: 1200,
				showConfirmButton: false,
			});
			return false;
		}
		if (
			!(
				document.querySelector('#txtImagenCurso').files[0].type ==
					'image/png' ||
				document.querySelector('#txtImagenCurso').files[0].type ==
					'image/jpg' ||
				document.querySelector('#txtImagenCurso').files[0].type == 'image/jpeg'
			)
		) {
			swal({
				title: 'Formato Incorrecto',
				text: 'Ingrese formato png, jpeg y jpg',
				type: 'warning',
				timer: 1200,
				showConfirmButton: false,
			});
			return false;
		}
		//menor a   4 MB
		if (
			document.querySelector('#txtImagenCurso').files[0].size >
			4 * 1024 * 1024
		) {
			swal({
				title: 'Tamaño excedido',
				text: 'el tamaño del archivo tiene que ser menor a 900 KB',
				type: 'warning',
				timer: 1200,
				showConfirmButton: false,
			});
			return false;
		}

		//PORTADA
		if (document.querySelector('#txtImagenPortadaCurso').files.length == 0) {
			swal({
				title: 'Vacío',
				text: 'Ingrese portada',
				type: 'warning',
				timer: 1200,
				showConfirmButton: false,
			});
			return false;
		}
		if (
			!(
				document.querySelector('#txtImagenPortadaCurso').files[0].type ==
					'image/png' ||
				document.querySelector('#txtImagenPortadaCurso').files[0].type ==
					'image/jpg' ||
				document.querySelector('#txtImagenPortadaCurso').files[0].type ==
					'image/jpeg'
			)
		) {
			swal({
				title: 'Formato Incorrecto',
				text: 'Ingrese formato png, jpeg y jpg en la portada',
				type: 'warning',
				timer: 1200,
				showConfirmButton: false,
			});
			return false;
		}
		//menor a   4 MB
		if (
			document.querySelector('#txtImagenPortadaCurso').files[0].size >
			4 * 1024 * 1024
		) {
			swal({
				title: 'Tamaño excedido',
				text: 'el tamaño del archivo tiene que ser menor a 900 KB en la portada',
				type: 'warning',
				timer: 1200,
				showConfirmButton: false,
			});
			return false;
		}

		//PRESENTACION
		if (
			document.querySelector('#txtImagenPresentacionCurso').files.length == 0 &&
			document.querySelector('#txtTipoFileCurso').value == '1'
		) {
			swal({
				title: 'Vacío',
				text: 'Ingrese presentación',
				type: 'warning',
				timer: 1200,
				showConfirmButton: false,
			});
			return false;
		}
		if (
			!(
				document.querySelector('#txtImagenPresentacionCurso').files[0].type ==
					'image/png' ||
				document.querySelector('#txtImagenPresentacionCurso').files[0].type ==
					'image/jpg' ||
				document.querySelector('#txtImagenPresentacionCurso').files[0].type ==
					'image/jpeg'
			) &&
			document.querySelector('#txtTipoFileCurso').value == '1'
		) {
			swal({
				title: 'Formato Incorrecto',
				text: 'Ingrese formato png, jpeg y jpg en la presentación',
				type: 'warning',
				timer: 1200,
				showConfirmButton: false,
			});
			return false;
		}
		//menor a   4 MB
		if (
			document.querySelector('#txtImagenPresentacionCurso').files[0].size >
				4 * 1024 * 1024 &&
			document.querySelector('#txtTipoFileCurso').value == '1'
		) {
			swal({
				title: 'Tamaño excedido',
				text: 'el tamaño del archivo tiene que ser menor a 900 KB en la presentación',
				type: 'warning',
				timer: 1200,
				showConfirmButton: false,
			});
			return false;
		}
	} else {
		if (document.querySelector('#txtImagenCurso').files.length != 0) {
			if (document.querySelector('#txtImagenCurso').files.length == 0) {
				swal({
					title: 'Vacío',
					text: 'Ingrese Imagen',
					type: 'warning',
					timer: 1200,
					showConfirmButton: false,
				});
				return false;
			}
			if (
				!(
					document.querySelector('#txtImagenCurso').files[0].type ==
						'image/png' ||
					document.querySelector('#txtImagenCurso').files[0].type ==
						'image/jpg' ||
					document.querySelector('#txtImagenCurso').files[0].type ==
						'image/jpeg'
				)
			) {
				swal({
					title: 'Formato Incorrecto',
					text: 'Ingrese formato png, jpeg y jpg',
					type: 'warning',
					timer: 1200,
					showConfirmButton: false,
				});
				return false;
			}
			//menor a   4 mb
			if (
				document.querySelector('#txtImagenCurso').files[0].size >
				1700 * 1024
			) {
				swal({
					title: 'Tamaño excedido',
					text: 'el tamaño del archivo tiene que ser menor a 1700 KB',
					type: 'warning',
					timer: 1200,
					showConfirmButton: false,
				});
				return false;
			}
		}
		if (document.querySelector('#txtImagenPortadaCurso').files.length != 0) {
			if (document.querySelector('#txtImagenPortadaCurso').files.length == 0) {
				swal({
					title: 'Vacío',
					text: 'Ingrese portada',
					type: 'warning',
					timer: 1200,
					showConfirmButton: false,
				});
				return false;
			}
			if (
				!(
					document.querySelector('#txtImagenPortadaCurso').files[0].type ==
						'image/png' ||
					document.querySelector('#txtImagenPortadaCurso').files[0].type ==
						'image/jpg' ||
					document.querySelector('#txtImagenPortadaCurso').files[0].type ==
						'image/jpeg'
				)
			) {
				swal({
					title: 'Formato Incorrecto',
					text: 'Ingrese formato png, jpeg y jpg en la portada',
					type: 'warning',
					timer: 1200,
					showConfirmButton: false,
				});
				return false;
			}
			//menor a   4 mb
			if (
				document.querySelector('#txtImagenPortadaCurso').files[0].size >
				1700 * 1024
			) {
				swal({
					title: 'Tamaño excedido',
					text: 'el tamaño de la portada tiene que ser menor a 1700 KB',
					type: 'warning',
					timer: 1200,
					showConfirmButton: false,
				});
				return false;
			}
		}
		if (
			document.querySelector('#txtImagenPresentacionCurso').files.length != 0
		) {
			if (
				document.querySelector('#txtImagenPresentacionCurso').files.length == 0
			) {
				swal({
					title: 'Vacío',
					text: 'Ingrese presentación',
					type: 'warning',
					timer: 1200,
					showConfirmButton: false,
				});
				return false;
			}
			if (
				!(
					document.querySelector('#txtImagenPresentacionCurso').files[0].type ==
						'image/png' ||
					document.querySelector('#txtImagenPresentacionCurso').files[0].type ==
						'image/jpg' ||
					document.querySelector('#txtImagenPresentacionCurso').files[0].type ==
						'image/jpeg'
				)
			) {
				swal({
					title: 'Formato Incorrecto',
					text: 'Ingrese formato png, jpeg y jpg en la presentación',
					type: 'warning',
					timer: 1200,
					showConfirmButton: false,
				});
				return false;
			}
			//menor a   4 mb
			if (
				document.querySelector('#txtImagenPresentacionCurso').files[0].size >
				1700 * 1024
			) {
				swal({
					title: 'Tamaño excedido',
					text: 'el tamaño de la presentación tiene que ser menor a 1700 KB',
					type: 'warning',
					timer: 1200,
					showConfirmButton: false,
				});
				return false;
			}
		}
	}

	if (
		document.querySelector('#txtVideoCurso').value.trim() == '' &&
		document.querySelector('#txtTipoFileCurso').value == '2'
	) {
		swal({
			title: 'Vacío',
			text: 'Ingrese Link de Youtube',
			type: 'warning',
			timer: 1200,
			showConfirmButton: false,
		});
		return false;
	}
	return true;
};
