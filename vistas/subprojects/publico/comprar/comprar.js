
var beanPaginationComprar;
var beanPaginationFooter;
var comprarSelected, paisSelectedCompra;
var beanRequestComprar = new BeanRequest();
//evento deslizador

var current_fs, next_fs, previous_fs; //fieldsets
var left, opacity, scale; //fieldset properties which we will animate
var animating; //flag to prevent quick multi-click glitches
let stateCompra;
document.addEventListener('DOMContentLoaded', function () {
    let paises = "<option value=''>[Pais]</option>", codigos = '';
    circleCargando.containerOcultar = $(document.querySelector("#precioCompra"));
    circleCargando.container = $(document.querySelector("#precioCompra").parentElement);
    circleCargando.createLoader();
    circleCargando.toggleLoader("show");
    fetch(getHostAPI() + "empresa/paginate" +
        "?filtro=&pagina=1&registros=1", {
        headers: {
            "Content-Type": 'application/json; charset=UTF-8',
        },
        method: "GET"
    })
        .then(response => response.json())
        .then(json => {
            circleCargando.toggleLoader("hide");
            beanPaginationFooter = json;
            document.querySelector('#visitaContador').innerHTML = json.countFilter;
            document.querySelector('#precioCompra').innerHTML = `<small class="mr-1">${json.pais.currencySymbol}</small>` + json.precio;
            if (!(json.pais.currencyId == "USD")) {
                document.querySelector('#precioCompraUSD').innerHTML = `= <small class="mr-1">$</small>` + json.precio_USD + `<small class="mx-1">USD</small>` + ` <i class="flag-icon flag-icon-us mr-2"></i>`;
            }


            document.querySelector('#emailComprar').innerHTML = json.email;
            document.querySelector('#emailComprar').setAttribute("href", "mailto:" + json.email);

            paisSelectedCompra = findByPaisesCompra(json.pais.name);
            document.querySelector(".paises-cuentaOtro").innerHTML = `<i class="flag-icon ${paisSelectedCompra.icon} mr-2"></i>${paisSelectedCompra.nombre} (+${paisSelectedCompra.codigo})`;
            document.querySelector(".paises-cuenta").innerHTML = document.querySelector(".paises-cuentaOtro").innerHTML;
            stateCompra.forEach(country => {
                if (removeAccents(country.nombre).toLocaleLowerCase() == removeAccents(json.pais.name).toLocaleLowerCase()) {
                    paises += `<option value="${country.nombre}" selected>${country.nombre}</option>`;
                    codigos += `<li class="aula-cursor-mano"><i class="flag-icon ${country.icon} mr-2"></i>${country.nombre} (+${country.codigo})</li>`;
                    document.querySelector('#precioCompra').innerHTML = `<small class="mr-1">${json.pais.currencySymbol}</small>${json.precio}<i class="flag-icon ${country.icon} mx-2"></i>`;
                } else {
                    paises += `<option value="${country.nombre}">${country.nombre}</option>`;
                    codigos += `<li class="aula-cursor-mano"><i class="flag-icon ${country.icon} mr-2"></i>${country.nombre} (+${country.codigo})</li>`;
                }

            });
            document.querySelector("#txtcountryCuenta").innerHTML = paises;
            document.querySelector("#txtTelefonoCodigoCuenta").innerHTML = codigos;
            document.querySelector("#txtTelefonoCodigoCuentaOtro").innerHTML = codigos;
            document.querySelector("#txtcountryCuentaOtro").innerHTML = paises;

        })
        .catch(err => {
            circleCargando.toggleLoader("hide");
            showAlertErrorRequest();
            console.log(err);
        });
    //INICIALIZANDO VARIABLES DE SOLICITUD
    beanRequestComprar.entity_api = 'compra';
    beanRequestComprar.operation = 'charge';
    beanRequestComprar.type_request = 'POST';
    eventoCompra();



    document.querySelector("#txtcountryCuenta").onchange = function () {
        paisSelectedCompra = findByPaisesCompra(document.querySelector("#txtcountryCuenta").value);
        document.querySelector(".paises-cuenta").innerHTML = `<i class="flag-icon ${paisSelectedCompra.icon} mr-2"></i>${paisSelectedCompra.nombre} (+${paisSelectedCompra.codigo})`;
    };



    $("#txtTelefonoCodigoCuenta > li").click(function (btn) {
        document.querySelector(".paises-cuenta").innerHTML = btn.currentTarget.innerHTML;
    });

    $('#msform').submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
    });

    $('#buyButtonPagarCompra').on('click', function (e) {
        if (validarFormularioRegisterCompra()) {
            if (Culqi.publicKey == "") {
                // Configura tu llave pública
                //Culqi.publicKey = 'pk_live_14dc8b11d5b6330a';
                Culqi.publicKey = 'pk_test_GzO42vPMPg4fZtz3';
                // Configura tu Culqi Checkout
                if (!(beanPaginationFooter.pais.currencyId == "PEN" || beanPaginationFooter.pais.currencyId == "USD")) {
                    beanPaginationFooter.pais.currencyId = "USD";
                    beanPaginationFooter.precio = beanPaginationFooter.precio_USD;
                }
                Culqi.settings({
                    title: 'CLPE',
                    currency: beanPaginationFooter.pais.currencyId,
                    description: 'Curso Online de Club de Lectura',
                    amount: parseFloat(beanPaginationFooter.precio) * 100
                });
            }
            //currency: beanPaginationFooter.pais.currencyId,
            // Abre el formulario con las opciones de Culqi.settings
            beanRequestComprar.operation = 'charge';
            Culqi.open(); e.preventDefault();
        }
    });

    /*OTRO MEDIO */
    $("#txtTelefonoCodigoCuentaOtro > li").click(function (btn) {
        document.querySelector(".paises-cuentaOtro").innerHTML = btn.currentTarget.innerHTML;
    });


    document.querySelector("#txtcountryCuentaOtro").onchange = function () {
        paisSelectedCompra = findByPaisesCompra(document.querySelector("#txtcountryCuentaOtro").value);
        document.querySelector(".paises-cuentaOtro").innerHTML = `<i class="flag-icon ${paisSelectedCompra.icon} mr-2"></i>${paisSelectedCompra.nombre} (+${paisSelectedCompra.codigo})`;
    };


    $('#registerOtroMedio').submit(function (event) {
        if (validarFormularioRegisterOtroCompra()) {
            $('#modalCargandoCompra').modal('show');
            let form_data = new FormData();
            let jso = {
                telefono: paisSelectedCompra.codigo + document.querySelector('#txtTelefonoCuentaOtro').value,
                nombre: document.querySelector('#txtnombreCuentaOtro').value,
                apellido: document.querySelector('#txtapellidoCuentaOtro').value,
                profesion: document.querySelector('#txtprofesionCuentaOtro').value,
                address: document.querySelector('#txtemailCuentaOtro').value,
                pass: document.querySelector('#txtpassCuentaOtro').value,
                pais: paisSelectedCompra.nombre,
                precio: parseFloat(beanPaginationFooter.precio),
                email: document.querySelector('#txtemailCuentaOtro').value
            };
            let dataImagen = $("#txtvoucherCuentaOtro").prop("files")[0];
            form_data.append("txtImagenVoucher", dataImagen);
            form_data.append("class", JSON.stringify(jso));
            beanRequestComprar.operation = 'clasico';
            processAjaxRegisterCompra(form_data);
        }


        event.preventDefault();
        event.stopPropagation();
    });

});

var findByPaisesCompra = (nombre) => {

    return stateCompra.find(
        (detalle) => {
            if (removeAccents(nombre).toLocaleLowerCase() == removeAccents(detalle.nombre).toLocaleLowerCase()) {
                return detalle;
            }


        }
    );
};

function eventoCompra() {
    document.querySelector("#span-ocultar-passCuenta2").onclick = () => {

        if (document.querySelector("#span-ocultar-passCuenta2").firstElementChild.className.includes("zmdi-eye-off")) {
            document.querySelector("#txtpassconfirmCuenta").setAttribute("type", "password");
            document.querySelector("#span-ocultar-passCuenta2").firstElementChild.classList.remove("zmdi-eye-off");
            document.querySelector("#span-ocultar-passCuenta2").firstElementChild.classList.add("zmdi-eye");
        } else {
            document.querySelector("#txtpassconfirmCuenta").setAttribute("type", "text");
            document.querySelector("#span-ocultar-passCuenta2").firstElementChild.classList.remove("zmdi-eye");
            document.querySelector("#span-ocultar-passCuenta2").firstElementChild.classList.add("zmdi-eye-off");
        }

    }
    document.querySelector("#span-ocultar-passCuenta1").onclick = () => {

        if (document.querySelector("#span-ocultar-passCuenta1").firstElementChild.className.includes("zmdi-eye-off")) {
            document.querySelector("#txtpassCuenta").setAttribute("type", "password");
            document.querySelector("#span-ocultar-passCuenta1").firstElementChild.classList.remove("zmdi-eye-off");
            document.querySelector("#span-ocultar-passCuenta1").firstElementChild.classList.add("zmdi-eye");
        } else {
            document.querySelector("#txtpassCuenta").setAttribute("type", "text");
            document.querySelector("#span-ocultar-passCuenta1").firstElementChild.classList.remove("zmdi-eye");
            document.querySelector("#span-ocultar-passCuenta1").firstElementChild.classList.add("zmdi-eye-off");
        }

    }

    document.querySelector("#span-ocultar-passCuentaOtro2").onclick = () => {

        if (document.querySelector("#span-ocultar-passCuentaOtro2").firstElementChild.className.includes("zmdi-eye-off")) {
            document.querySelector("#txtpassconfirmCuentaOtro").setAttribute("type", "password");
            document.querySelector("#span-ocultar-passCuentaOtro2").firstElementChild.classList.remove("zmdi-eye-off");
            document.querySelector("#span-ocultar-passCuentaOtro2").firstElementChild.classList.add("zmdi-eye");
        } else {
            document.querySelector("#txtpassconfirmCuentaOtro").setAttribute("type", "text");
            document.querySelector("#span-ocultar-passCuentaOtro2").firstElementChild.classList.remove("zmdi-eye");
            document.querySelector("#span-ocultar-passCuentaOtro2").firstElementChild.classList.add("zmdi-eye-off");
        }

    }
    document.querySelector("#span-ocultar-passCuentaOtro1").onclick = () => {

        if (document.querySelector("#span-ocultar-passCuentaOtro1").firstElementChild.className.includes("zmdi-eye-off")) {
            document.querySelector("#txtpassCuentaOtro").setAttribute("type", "password");
            document.querySelector("#span-ocultar-passCuentaOtro1").firstElementChild.classList.remove("zmdi-eye-off");
            document.querySelector("#span-ocultar-passCuentaOtro1").firstElementChild.classList.add("zmdi-eye");
        } else {
            document.querySelector("#txtpassCuentaOtro").setAttribute("type", "text");
            document.querySelector("#span-ocultar-passCuentaOtro1").firstElementChild.classList.remove("zmdi-eye");
            document.querySelector("#span-ocultar-passCuentaOtro1").firstElementChild.classList.add("zmdi-eye-off");
        }

    }

    document.body.style.background = "#f7f7f7 url(" + getHostFrontEnd() + "vistas/subprojects/publico/blog/img/pattern.png) repeat top left";
    $("body").append('<script src="https://checkout.culqi.com/v2"></script>');


    stateCompra = new Array({ codigo: "355", icon: "flag-icon-al", nombre: "Albania" }, { codigo: "376", icon: "flag-icon-ad", nombre: "Andorra" }, { codigo: "244", icon: "flag-icon-ao", nombre: "Angola" }, { codigo: "54", icon: "flag-icon-ar", nombre: "Argentina" }, { codigo: "374", icon: "flag-icon-am", nombre: "Armenia" }, { codigo: "61", icon: "flag-icon-au", nombre: "Australia" }, { codigo: "43", icon: "flag-icon-at", nombre: "Austria" }, { codigo: "973", icon: "flag-icon-bh", nombre: "Bahrain" }, { codigo: "880", icon: "flag-icon-bd", nombre: "Bangladesh" }, { codigo: "591", icon: "flag-icon-bo", nombre: "Bolivia" }, { codigo: "55", icon: "flag-icon-br", nombre: "Brazil" }, { codigo: "359", icon: "flag-icon-bg", nombre: "Bulgaria" }, { codigo: "1", icon: "flag-icon-ca", nombre: "Canada" }, { codigo: "236", icon: "flag-icon-cf", nombre: "Central African Republic" }, { codigo: "56", icon: "flag-icon-cl", nombre: "Chile" }, { codigo: "57", icon: "flag-icon-co", nombre: "Colombia" }, { codigo: "506", icon: "flag-icon-cr", nombre: "Costa Rica" }, { codigo: "53", icon: "flag-icon-cu", nombre: "Cuba" }, { codigo: "45", icon: "flag-icon-dk", nombre: "Dinamarca" }, { codigo: "1809", icon: "flag-icon-do", nombre: "Dominican Republic" }, { codigo: "593", icon: "flag-icon-ec", nombre: "Ecuador" }, { codigo: "503", icon: "flag-icon-sv", nombre: "El Salvador" }, { codigo: "001", icon: "flag-icon-us", nombre: "Estados Unidos" }, { codigo: "33", icon: "flag-icon-fr", nombre: "France" }, { codigo: "502", icon: "flag-icon-gt", nombre: "Guatemala" }, { codigo: "504", icon: "flag-icon-hn", nombre: "Honduras" }, { codigo: "39", icon: "flag-icon-it", nombre: "Italy" }, { codigo: "1876", icon: "flag-icon-jm", nombre: "Jamaica" }, { codigo: "81", icon: "flag-icon-jp", nombre: "Japan" }, { codigo: "52", icon: "flag-icon-mx", nombre: "México" }, { codigo: "505", icon: "flag-icon-ni", nombre: "Nicaragua" }, { codigo: "507", icon: "flag-icon-pa", nombre: "Panama" }, { codigo: "595", icon: "flag-icon-py", nombre: "Paraguay" }, { codigo: "51", icon: "flag-icon-pe", nombre: "Perú" }, { codigo: "351", icon: "flag-icon-pt", nombre: "Portugal" }, { codigo: "44", icon: "flag-icon-gb", nombre: "Reino Unido" }, { codigo: "7", icon: "flag-icon-ru", nombre: "Rusia" }, { codigo: "90", icon: "flag-icon-tr", nombre: "Turquía" }, { codigo: "598", icon: "flag-icon-uy", nombre: "Uruguay" }, { codigo: "58", icon: "flag-icon-ve", nombre: "Venezuela" }, { codigo: "260", icon: "flag-icon-zm", nombre: "Zambia" });

    let mastercard = $(".mastercard");
    let visa = $(".visa");
    let amex = $(".amex");
    amex.attr("src", getHostFrontEnd() + "vistas/subprojects/publico/comprar/images/amex.jpg");
    visa.attr("src", getHostFrontEnd() + "vistas/subprojects/publico/comprar/images/visa.jpg");
    mastercard.attr("src", getHostFrontEnd() + "vistas/subprojects/publico/comprar/images/mastercard.jpg");
    $(".bcp").attr("src", getHostFrontEnd() + "vistas/subprojects/publico/comprar/images/bcp.png");
    $(".banco-nacion-peru").attr("src", getHostFrontEnd() + "vistas/subprojects/publico/comprar/images/banco-nacion-peru.png");
    $(".caja-piura").attr("src", getHostFrontEnd() + "vistas/subprojects/publico/comprar/images/caja-piura.jpg");

    $(".next").click(function () {
        if ($(this).parent().children()[0].innerText == "DATOS PERSONALES") {
            if (!validarFormularioRegisterCompraPersonales()) {
                return;
            }
        } else if ($(this).parent().children()[0].innerText == "CREA TU CUENTA") {
            if (!validarFormularioRegisterCompraCuenta()) {
                return;
            }
        }
        if (animating) return false;
        animating = true;

        current_fs = $(this).parent();
        next_fs = $(this).parent().next();

        //activate next step on progressbar using the index of next_fs
        $("#progressbar li").eq($("fieldset").index(current_fs)).addClass("active");

        //show the next fieldset
        next_fs.show();
        //hide the current fieldset with style
        current_fs.animate({ opacity: 0 }, {
            step: function (now, mx) {
                //as the opacity of current_fs reduces to 0 - stored in "now"
                //1. scale current_fs down to 80%
                scale = 1 - (1 - now) * 0.2;
                //2. bring next_fs from the right(50%)
                left = (now * 50) + "%";
                //3. increase opacity of next_fs to 1 as it moves in
                opacity = 1 - now;
                current_fs.css({
                    'transform': 'scale(' + scale + ')',
                    'position': 'absolute'
                });
                next_fs.css({ 'left': left, 'opacity': opacity });
            },
            duration: 800,
            complete: function () {
                current_fs.hide();
                animating = false;
            },
            //this comes from the custom easing plugin
            easing: 'easeInOutBack'
        });


    });

    $(".previous").click(function () {
        if (animating) return false;
        animating = true;

        current_fs = $(this).parent();
        previous_fs = $(this).parent().prev();

        //de-activate current step on progressbar
        $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

        //show the previous fieldset
        previous_fs.show();
        //hide the current fieldset with style
        current_fs.animate({ opacity: 0 }, {
            step: function (now, mx) {
                //as the opacity of current_fs reduces to 0 - stored in "now"
                //1. scale previous_fs from 80% to 100%
                scale = 0.8 + (1 - now) * 0.2;
                //2. take current_fs to the right(50%) - from 0%
                left = ((1 - now) * 50) + "%";
                //3. increase opacity of previous_fs to 1 as it moves in
                opacity = 1 - now;
                current_fs.css({ 'left': left });
                previous_fs.css({ 'transform': 'scale(' + scale + ')', 'opacity': opacity });
            },
            duration: 800,
            complete: function () {
                current_fs.hide();
                animating = false;
            },
            //this comes from the custom easing plugin
            easing: 'easeInOutBack'
        });
    });


    document.querySelector("#compraRadios1").onclick = function () {
        if (this.checked) {
            document.querySelector(".description-2").classList.add("d-none");
            document.querySelector(".description-1").classList.remove("d-none");
        }

    };
    document.querySelector("#compraRadios2").onclick = function () {
        if (this.checked) {
            document.querySelector(".description-1").classList.add("d-none");
            document.querySelector(".description-2").classList.remove("d-none");
        }
    };
}

function culqi() {
    if (Culqi.token) { // ¡Objeto Token creado exitosamente!
        $('#modalCargandoCompra').modal('show');
        let token = Culqi.token.id;
        let email = Culqi.token.email;
        let form_data = new FormData();
        if (!(beanPaginationFooter.pais.currencyId == "PEN" || beanPaginationFooter.pais.currencyId == "USD")) {
            beanPaginationFooter.pais.currencyId = "USD";
            beanPaginationFooter.precio = beanPaginationFooter.precio_USD;
        }
        let jso = {
            id: "",
            telefono: paisSelectedCompra.codigo + document.querySelector('#txtTelefonoCuenta').value,
            nombre: document.querySelector('#txtnombreCuenta').value,
            apellido: document.querySelector('#txtapellidoCuenta').value,
            profesion: document.querySelector('#txtprofesionCuenta').value,
            address: document.querySelector('#txtemailCuenta').value,
            pass: document.querySelector('#txtpassCuenta').value,
            pais: paisSelectedCompra.nombre,
            country_code: beanPaginationFooter.pais.id,
            precio: parseFloat(beanPaginationFooter.precio),
            currency: beanPaginationFooter.pais.currencyId,
            token: token,
            email: email
        };
        form_data.append("class", JSON.stringify(jso));

        processAjaxRegisterCompra(form_data);

    } else { // ¡Hubo algún problema!
        // Mostramos JSON de objeto error en consola
        console.log(Culqi.error);
        if (Culqi.error) {
            showAlertTopEnd("info", Culqi.error, Culqi.error.merchant_message, 10000);
        }



    }
};

var validarFormularioRegisterCompra = () => {
    let letra = letra_campo(
        document.querySelector('#txtnombreCuenta'),
        document.querySelector('#txtapellidoCuenta'),
        document.querySelector('#txtprofesionCuenta')
    );

    if (letra != undefined) {
        if (letra.value == '') {
            showAlertTopEnd('info', "Formato Incorrecto", 'Por favor ingrese datos al campo' + letra.labels[0].innerText);
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese sólo letras, al campo ' + letra.labels[0].innerText
            );
        }

        return false;
    }
    let numero = numero_campo(
        document.querySelector('#txtTelefonoCuenta')
    );

    if (numero != undefined) {
        if (numero.value == '') {
            showAlertTopEnd('info', "Formato Incorrecto", 'Por favor ingrese datos al campo teléfono' + numero.labels[0].innerText);
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese sólo números al campo teléfono');
        }

        return false;
    }
    if (parseInt(document.querySelector('#txtTelefonoCuenta').value).length > 20) {
        showAlertTopEnd(
            'info', "Formato Incorrecto",
            'Por favor ingrese sólo números menor a 20 dígitos'); return false;
    }
    let email = email_campo(
        document.querySelector('#txtemailCuenta')
    );

    if (email != undefined) {
        if (email.value == '') {
            swal({
                title: "vacío!",
                text: "Por favor ingrese Correo electrónico",
                type: "warning",
                timer: 10000,
                showConfirmButton: false
            });
        } else {
            swal({
                title: "Formato Incorrecto!",
                text: "Por favor ingrese Correo electrónico válida",
                type: "warning",
                timer: 10000,
                showConfirmButton: false
            });
        }

        return false;
    }
    if (limpiar_campo(document.querySelector("#txtpassCuenta").value) == "") {
        swal({
            title: "Formato Incorrecto",
            text: "Ingrese Contraseña válida",
            type: "warning",
            timer: 10000,
            showConfirmButton: false
        });
        return false;
    }
    document.querySelector("#txtpassCuenta").value = limpiar_campo(document.querySelector("#txtpassCuenta").value);
    if (limpiar_campo(document.querySelector("#txtpassconfirmCuenta").value) != document.querySelector("#txtpassCuenta").value) {
        swal({
            title: "Formato Incorrecto",
            text: "Las contraseñas no son iguales",
            type: "warning",
            timer: 10000,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtcountryCuenta").value == "") {
        swal({
            title: "Formato Incorrecto",
            text: "Selecciona País",
            type: "warning",
            timer: 10000,
            showConfirmButton: false
        });
        return false;
    }
    return true;
}

var validarFormularioRegisterCompraPersonales = () => {
    let letra = letra_campo(
        document.querySelector('#txtnombreCuenta'),
        document.querySelector('#txtapellidoCuenta'),
        document.querySelector('#txtprofesionCuenta')
    );

    if (letra != undefined) {
        if (letra.value == '') {
            showAlertTopEnd('info', "Formato Incorrecto", 'Por favor ingrese datos al campo ' + letra.labels[0].innerText);
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese sólo letras, al campo ' + letra.labels[0].innerText
            );
        }

        return false;
    }
    let numero = numero_campo(
        document.querySelector('#txtTelefonoCuenta')
    );

    if (numero != undefined) {
        if (numero.value == '') {
            showAlertTopEnd('info', "Formato Incorrecto", 'Por favor ingrese datos al campo ' + numero.labels[0].innerText);
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese sólo números al campo teléfono');
        }

        return false;
    }
    if (document.querySelector("#txtcountryCuenta").value == "") {
        swal({
            title: "Formato Incorrecto",
            text: "Selecciona País",
            type: "warning",
            timer: 10000,
            showConfirmButton: false
        });
        return false;
    }
    return true;
}

var validarFormularioRegisterCompraCuenta = () => {

    let email = email_campo(
        document.querySelector('#txtemailCuenta')
    );

    if (email != undefined) {
        if (email.value == '') {
            swal({
                title: "vacío!",
                text: "Por favor ingrese Correo electrónico",
                type: "warning",
                timer: 10000,
                showConfirmButton: false
            });
        } else {
            swal({
                title: "Formato Incorrecto!",
                text: "Por favor ingrese Correo electrónico válida",
                type: "warning",
                timer: 10000,
                showConfirmButton: false
            });
        }

        return false;
    }
    if (limpiar_campo(document.querySelector("#txtpassCuenta").value) == "") {
        swal({
            title: "Formato Incorrecto",
            text: "Ingrese Contraseña válida",
            type: "warning",
            timer: 10000,
            showConfirmButton: false
        });
        return false;
    }
    document.querySelector("#txtpassCuenta").value = limpiar_campo(document.querySelector("#txtpassCuenta").value);
    if (limpiar_campo(document.querySelector("#txtpassconfirmCuenta").value) != document.querySelector("#txtpassCuenta").value) {
        swal({
            title: "Formato Incorrecto",
            text: "Las contraseñas no son iguales",
            type: "warning",
            timer: 10000,
            showConfirmButton: false
        });
        return false;
    }

    return true;
}
/*OTRO MEDIO */
var validarFormularioRegisterOtroCompra = () => {
    let letra = letra_campo(
        document.querySelector('#txtnombreCuentaOtro'),
        document.querySelector('#txtapellidoCuentaOtro'),
        document.querySelector('#txtprofesionCuentaOtro')
    );

    if (letra != undefined) {
        if (letra.value == '') {
            showAlertTopEnd('info', "Formato Incorrecto", 'Por favor ingrese datos al campo' + letra.labels[0].innerText);
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese sólo letras, al campo ' + letra.labels[0].innerText
            );
        }

        return false;
    }
    let numero = numero_campo(
        document.querySelector('#txtTelefonoCuentaOtro')
    );

    if (numero != undefined) {
        if (numero.value == '') {
            showAlertTopEnd('info', "Formato Incorrecto", 'Por favor ingrese datos al campo teléfono' + numero.labels[0].innerText);
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese sólo números al campo teléfono');
        }

        return false;
    }
    if (parseInt(document.querySelector('#txtTelefonoCuentaOtro').value).length > 20) {
        showAlertTopEnd(
            'info', "Formato Incorrecto",
            'Por favor ingrese sólo números menor a 20 dígitos'); return false;
    }
    let email = email_campo(
        document.querySelector('#txtemailCuentaOtro')
    );

    if (email != undefined) {
        if (email.value == '') {
            swal({
                title: "vacío!",
                text: "Por favor ingrese Correo electrónico",
                type: "warning",
                timer: 10000,
                showConfirmButton: false
            });
        } else {
            swal({
                title: "Formato Incorrecto!",
                text: "Por favor ingrese Correo electrónico válida",
                type: "warning",
                timer: 10000,
                showConfirmButton: false
            });
        }

        return false;
    }
    if (limpiar_campo(document.querySelector("#txtpassCuentaOtro").value) == "") {
        swal({
            title: "Formato Incorrecto",
            text: "Ingrese Contraseña válida",
            type: "warning",
            timer: 10000,
            showConfirmButton: false
        });
        return false;
    }
    document.querySelector("#txtpassCuentaOtro").value = limpiar_campo(document.querySelector("#txtpassCuentaOtro").value);
    if (limpiar_campo(document.querySelector("#txtpassconfirmCuentaOtro").value) != document.querySelector("#txtpassCuentaOtro").value) {
        swal({
            title: "Formato Incorrecto",
            text: "Las contraseñas no son iguales",
            type: "warning",
            timer: 10000,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtcountryCuentaOtro").value == "") {
        swal({
            title: "Formato Incorrecto",
            text: "Selecciona País",
            type: "warning",
            timer: 10000,
            showConfirmButton: false
        });
        return false;
    }


    /*IMAGEN */
    if (document.querySelector("#txtvoucherCuentaOtro").files.length == 0) {
        showAlertTopEnd("info", "Vacío", "ingrese Voucher del depósito");
        return false;
    }
    if (!(document.querySelector("#txtvoucherCuentaOtro").files[0].type == "image/png" || document.querySelector("#txtvoucherCuentaOtro").files[0].type == "image/jpg" || document.querySelector("#txtvoucherCuentaOtro").files[0].type == "image/jpeg")) {
        showAlertTopEnd("info", "Formato Incorrecto", "Ingrese tipo de arhivo del voucher => png,jpg,jpeg");
        return false;
    }
    //menor a   5 MB
    if (document.querySelector("#txtvoucherCuentaOtro").files[0].size > (5 * 1024 * 1024)) {
        showAlertTopEnd("info", "Tamaño excedido", "el tamaño del archivo tiene que ser menor a 5 MB");
        return false;
    }



    return true;
}

function processAjaxRegisterCompra(form_data) {
    $.ajax({
        url: getHostAPI() + beanRequestComprar.entity_api + "/" + beanRequestComprar.operation,
        type: "POST",
        data: form_data,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json', xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-compra').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-compra").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-compra").attr("aria-valuenow", +Math.round(percentComplete * 100));
                    if (percentComplete === 1) {
                        // $('.progress-bar-compra').addClass('hide');
                        $('.progress-bar-compra').css({
                            width: + '100%'
                        });
                        $(".progress-bar-compra").text("Cargando ... 100%");
                        $(".progress-bar-compra").attr("aria-valuenow", "100");
                    }
                }
            }, false);
            xhr.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-compra').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-compra").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-compra").attr("aria-valuenow", +Math.round(percentComplete * 100));
                }
            }, false);
            return xhr;
        },
    }).done(function (beanCrudResponse) {
        $('#modalCargandoCompra').modal('hide');
        if (beanCrudResponse.messageServer != undefined) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {

                addClass(document.querySelector(".success-compra"), "active pulse-2 m-2 border");
                document.querySelector(".success-compra").setAttribute("style", "background: white;border: 0 none;border-radius: 3px;box-shadow: 0 0 15px 1px rgba(0, 0, 0, 0.4);padding: 20px 30px;box-sizing: border-box;margin: 0 10%;");
                if (document.querySelector("#compraRadios1").checked) {
                    document.querySelector(".next").dispatchEvent(new Event('click'));
                    document.querySelector(".success-compra").innerHTML = `<h2 class="text-center">COMPRA EXITOSA!</h2><p>se envió a tu correo un código de verificación para que puedas acceder al curso.</p>`;
                } else {
                    document.querySelector(".success-compra").innerHTML = `<h2 class="text-center">COMPRA EXITOSA!</h2><p>en un máximo de 24 horas el administrador de Club de Lectura para emprendedores verificará el depósito realizado, y enviará a tu correo un código de verificación para que puedas acceder al curso.</p>`;
                }
                document.querySelectorAll(".form-check").forEach((btn) => {
                    btn.innerHTML = "";
                    removeClass(btn, "border-bottom");
                });
            } else {
                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        } else {
            beanCrudResponse = JSON.parse(beanCrudResponse);
            if (beanCrudResponse.object != undefined) {
                if (beanCrudResponse.object == "error") {
                    showAlertTopEnd("info", "", beanCrudResponse.merchant_message, 10000);
                } else {
                    showAlertTopEnd("success", "", beanCrudResponse.merchant_message, 10000);
                }
            }
        }



    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoCompra').modal('hide');
        showAlertErrorRequest();

    });

}

const removeAccents = (str) => {
    return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
}