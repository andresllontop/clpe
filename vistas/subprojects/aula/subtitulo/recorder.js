var beanPaginationLeccion;
var beanRequestLeccion = new BeanRequest();
let contadorSegundo;
let contador = 0;
let blobGeneral;
let blobGeneralAudio;
/*CAMARA */
var mediaSource;
var mediaRecorder;
var recordedBlobs;
var sourceBuffer;
var gumVideo;
var recordedVideo;
var recordButton;
var playButton;
var playButton;
var downloadButton;
var enviarvideo;

document.addEventListener('DOMContentLoaded', function () {

    beanRequestLeccion.entity_api = 'lecciones';
    beanRequestLeccion.operation = 'add';
    beanRequestLeccion.type_request = 'POST';
    $('#modalCargandoLeccion').on('shown.bs.modal', function () {
        ProcesarAjaxLeccion();
    });

    $("#formularioLeccion").submit(function (event) {
        if (validarFormularioVideo()) {
            $('#modalCargandoLeccion').modal('show');
        }
        event.preventDefault();
        event.stopPropagation();
    });


    $("#ModalFormularioVideo").on('hide.bs.modal', function () {
        stopStream(window.stream);
        gumVideo.parentElement.classList.remove("col-sm-6");
        gumVideo.parentElement.classList.add("col-sm-12");
    });

    if (document.querySelector("#AbrirVideo")) {
        document.querySelector("#AbrirVideo").addEventListener("click", function (e) {
            document.querySelector("#recorded").parentElement.classList.add("d-none");
            document.querySelector("#record").parentElement.classList.remove("col-sm-6");
            document.querySelector("#record").parentElement.classList.add("col-sm-12");
            VideoReproducir(
                document.querySelector("video#gum"),
                document.querySelector("video#recorded"),
                document.querySelector("button#record"),
                document.querySelector("button#play"),
                document.querySelector("button#download-video"),
                document.querySelector("button#enviarVideo")
            );
        });
    }


    if (document.querySelector("#Abrir-VideoModal")) {
        document.querySelector("#Abrir-VideoModal").addEventListener('click', function (e) {
            document.getElementById("container2").style.display = "block";
            AudioReproducir(
                document.querySelector("audio#gum2"),
                document.querySelector("audio#recorded2"),
                document.querySelector("button#record2"),
                document.querySelector("button#play2"),
                document.querySelector("button#enviarAudio")
                // , document.querySelector("button#download2")
            );
        });
    }

});

function VideoReproducir(
    gumvideo,
    recordedvideo,
    recordbutton,
    playbutton,
    downloadbutton,
    enviarVideo
) {
    mediaSource = new MediaSource();
    mediaSource.addEventListener("sourceopen", handleSourceOpen, false);
    gumVideo = gumvideo;
    recordedVideo = recordedvideo;
    recordButton = recordbutton;
    playButton = playbutton;
    downloadButton = downloadbutton;
    enviarvideo = enviarVideo;
    recordButton.onclick = toggleRecording;
    playButton.onclick = play;
    downloadButton.onclick = download;
    document.querySelector("#DownloadVideo").onclick = download;

    navigator.getUserMedia = navigator.getUserMedia ||
        navigator.webkitGetUserMedia ||
        navigator.mozGetUserMedia;

    if (navigator.getUserMedia) {
        try {
            $("#ModalFormularioVideo").modal("show");
            navigator.mediaDevices
                .getUserMedia({
                    audio: true,
                    video: true
                })
                .then(successCallback, errorCallback);
            // navigator.getUserMedia({ audio: true, video: true });
            enviarvideo.onclick = Enviar;
        } catch (error) {
            $("#ModalFormularioVideo").modal("hide");
            console.log("error" + error);
        }

    } else {
        console.log("getUserMedia not supported");
        $("#ModalFormularioVideo").modal("hide");
        swal(
            "NO PUEDE ACCEDER A LA CAMARA DE TU DISPOSITIVO",
            "Se requiere :<br> Firefox 29 o posterior <br>  Chrome 49 o posterior <br>  opera 36 o posterior <br>  chrome para android 71.",
            "info"
        );
        return;

    }


}

function successCallback(stream) {
    //CAMARA ENCENDIAD STREAM
    window.stream = stream;
    gumVideo.srcObject = stream;
}

function errorCallback(error) {
    $("#ModalFormularioVideo").modal("hide");
    console.log("navigator.getUserMedia error: ", error);
    swal(
        "NO PUEDE ACCEDER A LA CAMARA DE TU DISPOSITIVO",
        "Se requiere :<br> Firefox 29 o posterior <br>  Chrome 49 o posterior <br>  opera 36 o posterior <br>  chrome para android 71.",
        "info"
    );
}

function handleSourceOpen(event) {
    sourceBuffer = mediaSource.addSourceBuffer('video/webm; codecs="vp8"');
}

function handleDataAvailable(event) {
    if (event.data && event.data.size > 0) {
        recordedBlobs.push(event.data);
    }
}

function handleStop(event) { }

function toggleRecording() {
    if (recordButton.dataset.camara === "comenzar") {
        startRecording();
    } else {
        stopRecording();
        recordButton.innerHTML = '<i class="zmdi  zmdi-play-circle zmdi-hc-4x text-danger"></i>';
        recordButton.dataset.camara = "comenzar";
        recordButton.dataset.originalTitle = "INICIAR GRABACIÓN";
        playButton.disabled = false;
        playButton.classList.remove("d-none");
        // downloadButton.disabled = false;
    }
}
// The nested try blocks will be simplified when Chrome 47 moves to Stable
function startRecording() {

    let options = { mimeType: "video/webm;codecs=vp9", bitsPerSecond: 921600 };
    recordedBlobs = [];
    try {
        mediaRecorder = new MediaRecorder(window.stream, options);
    } catch (e0) {
        try {
            options = { mimeType: "video/webm;codecs=vp8", bitsPerSecond: 921600 };
            mediaRecorder = new MediaRecorder(window.stream, options);
        } catch (e1) {
            try {
                options = "video/mp4";
                mediaRecorder = new MediaRecorder(window.stream, options);
            } catch (e2) {
                //   alert("MediaRecorder no soportado para estos navegadores.");
                console.error("MediaRecorder no soportado para estos navegadores.", e2);
                return;
            }
        }
    }

    recordButton.innerHTML = '<i class="zmdi  zmdi-stop zmdi-hc-4x text-danger"></i>';
    recordButton.dataset.camara = "detener";
    recordButton.dataset.originalTitle = "FINALIZAR GRABACIÓN";
    recorded.parentElement.classList.add("d-none");
    gumVideo.parentElement.classList.remove("col-sm-6");
    gumVideo.parentElement.classList.add("col-sm-12");
    playButton.disabled = true;
    downloadButton.disabled = true;
    document.querySelector("#DownloadVideo").disabled = true;
    removeClass(document.querySelector("#DownloadVideo"), "d-none");
    mediaRecorder.onstop = handleStop;
    mediaRecorder.ondataavailable = handleDataAvailable;
    mediaRecorder.start(10); // collect 10ms of data

    clearInterval(contadorSegundo);
    var n = 1;
    var nm = 0;
    var l = document.getElementById("number");
    contadorSegundo = setInterval(function () {
        if (n == 60) {
            nm++;
            n = 0;
        }
        if ((n + "").length == 1) {
            l.innerHTML = nm + ":0" + n;
        } else {
            l.innerHTML = nm + ":" + n;
        }



        n++;
    }, 1000);
}

function stopRecording() {
    clearInterval(contadorSegundo);
    mediaRecorder.stop();
    recordedVideo.controls = true;
    contador = 1;
    downloadButton.disabled = false;
    document.querySelector("#DownloadVideo").disabled = false;
}

function play() {
    let superBuffer = new Blob(recordedBlobs, { type: "video/webm" });
    recordedVideo.src = window.URL.createObjectURL(superBuffer);
    recorded.parentElement.classList.remove("d-none");
    gumVideo.parentElement.classList.remove("col-sm-12");
    gumVideo.parentElement.classList.add("col-sm-6");
}

function download() {
    if (recordedBlobs == undefined) {
        return;
    }
    let blob = new Blob(recordedBlobs, { type: "video/webm" });
    let url = window.URL.createObjectURL(blob);
    let a = document.createElement("a");
    a.style.display = "none";
    a.href = url;
    a.download = "video-grabado.mp4";
    document.body.appendChild(a);
    a.click();
    setTimeout(function () {
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }, 100);
    // Enviar();
}
function Enviar() {
    if (contador != 1) {
        swal(
            "Ocurrió un error inesperado",
            "Debes grabar tu video para poder enviar el video",
            "error"
        );
    } else {
        //  let blob = new Blob(recordedBlobs, { type: "video/webm" });
        let blob = new Blob(recordedBlobs, { type: "video/mp4" });
        blobGeneral = blob;
        stopStream(window.stream);
        $("#ModalFormularioVideo").modal("hide");
        swal({
            title: "VIDEO GRABADO",
            text: "Para continuar con el registro haga clic en aceptar.",
            confirmButtonColor: "#2ca441",
            confirmButtonText: "Aceptar",
            imageUrl: getHostFrontEnd() + 'vistas/assets/img/video.png',
            closeOnConfirm: false
        },
            function () {
                swal.close();
            });
    }
}
function stopStream(stream) {
    try {
        for (let track of stream.getTracks()) {
            track.stop();
        }
    } catch (error) {
        swal(
            "NO PUEDE ACCEDER A LA CAMARA DE TU DISPOSITIVO",
            "Se requiere :<br> Firefox 29 o posterior <br>  Chrome 49 o posterior <br>  opera 36 o posterior <br>  chrome para android 71.",
            "info"
        );
    }

}
function ProcesarAjaxAudio(metodo, formdata) {
    swal({
        title: "TAREA REGISTRADA",
        text: "Para pasar a la siguiente lección haga clic en aceptar.",
        showCancelButton: true,
        confirmButtonColor: "#2ca441",
        cancelButtonClass: "btn-danger",
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar",
        imageUrl: getHostFrontEnd() + 'vistas/assets/img/enviada-carta.png',
        closeOnConfirm: false
    },
        function () {
            swal("Deleted!", "Your imaginary file has been deleted.", "success");
        });
    $.ajax({
        type: metodo,
        url: url + "ajax/videousuarioAjax.php",
        data: formdata,
        cache: false,
        contentType: false,
        processData: false, // modificar el valor de xhr a nuestro gusto
        xhr: function () {
            // obtener el objeto XmlHttpRequest nativo
            let xhr = $.ajaxSettings.xhr();
            // añadirle un controlador para el evento onprogress
            xhr.onprogress = function (evt) {
                // calculamos el porcentaje y nos quedamos sólo con la parte entera
                let porcentaje = Math.floor((evt.loaded / evt.total) * 100);
                // actualizamos el texto con el porcentaje mostrado
                $("#progress_id").text(porcentaje + "%");
                // actualizamos la cantidad avanzada en la barra de progreso
                $("#progress_id").attr("aria-valuenow", porcentaje);
                $("#progress_id").css("width", porcentaje + "%");
            };
            // devolvemos el objeto xhr modificado
            return xhr;
        },
        success: function (data) {
            $("#cargarpagina").html("");
            swal(
                {
                    title: JSON.parse(data).Titulo,
                    text: JSON.parse(data).Texto,
                    type: JSON.parse(data).Tipo,
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    closeOnConfirm: false,
                    confirmButtonText: "Aceptar"
                },
                function () {
                    contador = 0;
                    location.reload();
                }
            );
        },
        error: function (e) {
            e;
            swal(
                "Ocurrió un error inesperado",
                "Por favor recargue la página",
                "error" + e
            );
        }
    });
    return false;
}
function AudioReproducir(
    gumvideo,
    recordedvideo,
    recordbutton,
    playbutton,
    // downloadbutton,
    enviarVideo
) {
    mediaSource = new MediaSource();
    mediaSource.addEventListener("sourceopen", handleSourceOpen, false);
    mediaRecorder;
    recordedBlobs;
    sourceBuffer;
    gumVideo = gumvideo;
    recordedVideo = recordedvideo;
    recordButton = recordbutton;
    playButton = playbutton;
    // let downloadButton = downloadbutton;
    enviarvideo = enviarVideo;
    recordButton.onclick = toggleRecording;
    playButton.onclick = play;
    // downloadButton.onclick = download;

    let constraints = {
        audio: true
    };

    navigator.mediaDevices
        .getUserMedia(constraints)
        .then(successCallback, errorCallback);
    enviarvideo.onclick = Enviar;

    function successCallback(stream) {
        window.stream = stream;
        gumVideo.srcObject = stream;
    }

    function errorCallback(error) {
        console.log("navigator.getUserMedia error: ", error);
        swal(
            "No puedes acceder al audio",
            "Graba tu audio desde un dispositivo externo y envialo desde tu PC",
            "error"
        );
        let ob = document.getElementById("container2");
        ob.style.display = "none";
        var html = `<p class="text-left">Subir Audio:</p>
    <input type="file" clas=" material-control" name="Audio-reg" id="">
    `;
        $("#RespondeAudio").html(html);
        contador = 2;
    }

    function handleSourceOpen(event) {
        sourceBuffer = mediaSource.addSourceBuffer('audio/webm; codecs="vp8"');
    }

    function handleDataAvailable(event) {
        if (event.data && event.data.size > 0) {
            recordedBlobs.push(event.data);
        }
    }

    function handleStop(event) { }

    function toggleRecording() {
        if (recordButton.dataset.camara === "comenzar") {
            startRecording();
        } else {
            stopRecording();
            recordButton.innerHTML = '<i class="zmdi  zmdi-play-circle zmdi-hc-fw"></i> <span>GRABAR</span>';
            playButton.disabled = false;
            // downloadButton.disabled = false;
        }
    }
    // The nested try blocks will be simplified when Chrome 47 moves to Stable
    function startRecording() {
        let options = { mimeType: "video/webm;codecs=vp9", bitsPerSecond: 100000 };
        recordedBlobs = [];
        try {
            mediaRecorder = new MediaRecorder(window.stream, options);
        } catch (e0) {
            try {
                options = { mimeType: "video/webm;codecs=vp8", bitsPerSecond: 100000 };
                mediaRecorder = new MediaRecorder(window.stream, options);
            } catch (e1) {
                try {
                    options = "audio/mp3";
                    mediaRecorder = new MediaRecorder(window.stream, options);
                } catch (e2) {
                    alert("MediaRecorder is not supported by this browser.");
                    console.error("Exception while creating MediaRecorder:", e2);
                    return;
                }
            }
        }

        recordButton.innerHTML = '<i class="zmdi zmdi-stop zmdi-hc-fw"></i> <span>FINALIZAR GRABACIÓN</span>';
        playButton.disabled = true;
        // downloadButton.disabled = true;
        mediaRecorder.onstop = handleStop;
        mediaRecorder.ondataavailable = handleDataAvailable;
        mediaRecorder.start(10); // collect 10ms of data
    }

    function stopRecording() {
        mediaRecorder.stop();
        recordedVideo.controls = true;
        contador = 1;
    }

    function play() {
        let superBuffer = new Blob(recordedBlobs, { type: "audio/webm" });
        recordedVideo.src = window.URL.createObjectURL(superBuffer);
    }

    function download() {
        let blob = new Blob(recordedBlobs, { type: "audio/webm" });
        let url = window.URL.createObjectURL(blob);
        let a = document.createElement("a");
        a.style.display = "none";
        a.href = url;
        a.download = "test.webm";
        document.body.appendChild(a);
        a.click();
        setTimeout(function () {
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }, 100);
        // Enviar();
    }
    function Enviar() {
        if (contador != 1) {
            swal(
                "Ocurrió un error inesperado",
                "Debes grabar tu Audio para poder Poder seguir con la clase",
                "error"
            );
        } else {
            let blob = new Blob(recordedBlobs, { type: "audio/webm" });
            stopStream(window.stream);
            blobGeneralAudio = blob;
            swal(
                "Selecciona 'Guarda Audio' Para Enviar la Informacion",
                "",
                "success"
            );
        }
    }
    function stopStream(stream) {
        for (let track of stream.getTracks()) {
            track.stop();
        }
    }
}
function ProcesarAjaxLeccion() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestLeccion.operation == 'add'
    ) {

        try {
            json = {
                comentario: document.querySelector("#txtComentarioLeccion").value,
                subtitulo: subtituloSelected.codigo,
                cuenta: user_session.codigo
            };
            if (blobGeneral == undefined || blobGeneral == null || blobGeneral == "") {
                let dataFot = $("#txtVideoLeccion").prop("files")[0];
                form_data.append("txtVideoLeccion", dataFot);

            } else {
                form_data.append("txtVideoLeccion", blobGeneral);
            }

            form_data.append("class", JSON.stringify(json));
        } catch (error) {
            showAlertTopEnd("error", "Ingresa Datos correctos de la lección", "");
            console.log(error);
            return;
        }

    } else {
        form_data = null;
    }

    switch (beanRequestLeccion.operation) {
        case 'add':

            break;
        default:

            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestLeccion.entity_api + "/" + beanRequestLeccion.operation +
            parameters_pagination,
        type: beanRequestLeccion.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-video').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-video").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-video").attr("aria-valuenow", +Math.round(percentComplete * 100));
                    if (percentComplete === 1) {
                        // $('.progress-bar-video').addClass('hide');
                        $('.progress-bar-video').css({
                            width: + '100%'
                        });
                        $(".progress-bar-video").text("Cargando ... 100%");
                        $(".progress-bar-video").attr("aria-valuenow", "100");
                    }
                }
            }, false);
            xhr.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-video').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-video").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-video").attr("aria-valuenow", +Math.round(percentComplete * 100));
                }
            }, false);
            return xhr;
        },
        contentType: ((beanRequestLeccion.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {
        $('#modalCargandoLeccion').modal('hide');

        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {

                /* swal({
                     title: "TAREA REGISTRADA!",
                     text: "Para pasar a la siguiente lección haga click en aceptar.",
                     showCancelButton: true,
                     confirmButtonColor: "#2ca441",
                     cancelButtonClass: "btn-danger",
                     confirmButtonText: "Aceptar",
                     cancelButtonText: "Cancelar",
                     imageUrl: getHostFrontEnd() + 'vistas/assets/img/enviada-carta.png',
                     closeOnConfirm: false,
                     closeOnCancel: false
                 },
                     function (isConfirm) {
 
                         if (isConfirm) {
                             swal.close();
                             beanRequestSubtitulo.operation = 'updatestado';
                             beanRequestSubtitulo.type_request = 'POST';
                             $('#modalCargandoSubtitulo').modal('show');
                         } else {
                             swal("Seleccionaste Cancelar", "No avanzaste a la siguiente Lección.");
 
                         }
 
 
                     });*/

                document.querySelector("#modalSwallTitulo").innerText = "TAREA REGISTRADA!";
                document.querySelector("#modalSwallContenido").innerText = "Para pasar a la siguiente lección haga click en aceptar.";
                $('#modalSwallMensaje').modal('show');

                //AGREGANDO EVENTO CLICK
                document.querySelector('#modalSwallAceptar').onclick = function () {
                    $('#modalSwallMensaje').modal('hide');
                    beanRequestSubtitulo.operation = 'updatestado';
                    beanRequestSubtitulo.type_request = 'POST';
                    $('#modalCargandoSubtitulo').modal('show');

                };
                document.querySelector('#modalSwallCancelar').onclick = function () {
                    $('#modalSwallMensaje').modal('hide');
                    swal("Seleccionaste Cancelar", "No avanzaste a la siguiente Lección.");
                };

            } else if (beanCrudResponse.messageServer.toLowerCase() == 'general') {
                if (beanRequestSubtitulo.operation == 'updatestado') {
                    window.location.reload();
                } else {
                    /* swal({
                         title: "CUESTIONARIO DEL CAPÍTULO POR REALIZAR",
                         text: "Para responder el siguiente cuestionario del Capítulo haga click en aceptar.",
                         showCancelButton: true,
                         confirmButtonColor: "#2ca441",
                         cancelButtonClass: "btn-danger",
                         confirmButtonText: "Aceptar",
                         cancelButtonText: "Cancelar",
                         imageUrl: getHostFrontEnd() + 'vistas/assets/img/enviada-carta.png',
                         closeOnConfirm: false,
                         closeOnCancel: false
                     },
                         function (isConfirm) {
                             $("#modalCargandoLeccion").modal('hide');
                             if (isConfirm) {
                                 swal.close();
                                 document.querySelector("#modalCargandoSubtitulo .progress-bar").innerText = "Cargando Cuestionario ... ";
                                 beanRequestSubtitulo.operation = 'updatestado';
                                 beanRequestSubtitulo.type_request = 'POST';
                                 $('#modalCargandoSubtitulo').modal('show');
                             } else {
                                 swal("Seleccionaste Cancelar", "No avanzaste a responder tus cuestionarios");
 
                             }
 
                         });*/
                    document.querySelector("#modalSwallTitulo").innerText = "CUESTIONARIO DEL CAPÍTULO POR REALIZAR";
                    document.querySelector("#modalSwallContenido").innerText = "Para responder el siguiente cuestionario del Capítulo haga click en aceptar.";
                    $('#modalSwallMensaje').modal('show');
                    //AGREGANDO EVENTO CLICK
                    document.querySelector('#modalSwallAceptar').onclick = function () {
                        $('#modalSwallMensaje').modal('hide');
                        document.querySelector("#modalCargandoSubtitulo .progress-bar").innerText = "Cargando Cuestionario ... ";
                        beanRequestSubtitulo.operation = 'updatestado';
                        beanRequestSubtitulo.type_request = 'POST';
                        $('#modalCargandoSubtitulo').modal('show');

                    };
                    document.querySelector('#modalSwallCancelar').onclick = function () {
                        $('#modalSwallMensaje').modal('hide');
                        swal("Seleccionaste Cancelar", "No avanzaste a responder tus cuestionarios");
                    };
                }

            } else if (beanCrudResponse.messageServer.toLowerCase() == 'interno') {
                if (beanRequestSubtitulo.operation == 'updatestado') {
                    window.location.reload();
                } else {
                    /*
                    swal({
                        title: "CUESTIONARIO DEL CAPÍTULO POR REALIZAR",
                        text: "Para responder el siguiente cuestionario de reforzamiento del Capítulo haga click en aceptar.",
                        showCancelButton: true,
                        confirmButtonColor: "#2ca441",
                        cancelButtonClass: "btn-danger",
                        confirmButtonText: "Aceptar",
                        cancelButtonText: "Cancelar",
                        imageUrl: getHostFrontEnd() + 'vistas/assets/img/enviada-carta.png',
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                        function (isConfirm) {
                            $("#modalCargandoLeccion").modal('hide');
                            if (isConfirm) {
                                swal.close();
                                document.querySelector("#modalCargandoSubtitulo .progress-bar").innerText = "Cargando Cuestionario ... ";
                                beanRequestSubtitulo.operation = 'updatestado';
                                beanRequestSubtitulo.type_request = 'POST';
                                $('#modalCargandoSubtitulo').modal('show');
                            } else {
                                swal("Seleccionaste Cancelar", "No avanzaste a responder tus cuestionarios");

                            }

                        });*/
                    document.querySelector("#modalSwallTitulo").innerText = "CUESTIONARIO DEL CAPÍTULO POR REALIZAR";
                    document.querySelector("#modalSwallContenido").innerText = "Para responder el siguiente cuestionario de reforzamiento del Capítulo haga click en aceptar.";
                    $('#modalSwallMensaje').modal('show');

                    //AGREGANDO EVENTO CLICK
                    document.querySelector('#modalSwallAceptar').onclick = function () {
                        $('#modalSwallMensaje').modal('hide');
                        document.querySelector("#modalCargandoSubtitulo .progress-bar").innerText = "Cargando Cuestionario ... ";
                        beanRequestSubtitulo.operation = 'updatestado';
                        beanRequestSubtitulo.type_request = 'POST';
                        $('#modalCargandoSubtitulo').modal('show');

                    };
                    document.querySelector('#modalSwallCancelar').onclick = function () {
                        $('#modalSwallMensaje').modal('hide');
                        swal("Seleccionaste Cancelar", "No avanzaste a responder tus cuestionarios");
                    };
                }

            } else if (beanCrudResponse.messageServer.toLowerCase() == 'fin') {
                showAlertTopEnd("info", "TAREA REGISTRADA FIN DEL CURSO", "haga clic en aceptar");

            } else {
                showAlertTopEnd("info", "INFORMACIÓN!", beanCrudResponse.messageServer);
            }
        }

    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoLeccion').modal("hide");
        showAlertErrorRequest();

    });

}

var validarFormularioVideo = () => {
    document.querySelector('#txtComentarioLeccion').value = limpiar_campo(
        document.querySelector('#txtComentarioLeccion').value

    );

    if (blobGeneral == undefined || blobGeneral == null || blobGeneral == "") {
        /*IMAGEN */
        if (document.querySelector("#txtVideoLeccion").files.length == 0) {
            showAlertTopEnd("info", "Graba video de tu comentario", "");
            return false;
        }

        if (!(document.querySelector("#txtVideoLeccion").files[0].type == "video/mp4" || document.querySelector("#txtVideoLeccion").files[0].type == "video/webm" || document.querySelector("#txtVideoLeccion").files[0].type == "video/ogg" || document.querySelector("#txtVideoLeccion").files[0].type == "video/quicktime")) {
            showAlertTopEnd("info", "Formato Incorrecto", "Ingrese tipo de arhivo video => mp4, webm, ogg, mov");
            return false;
        }
        //menor a   500 MB
        if (document.querySelector("#txtVideoLeccion").files[0].size > (500 * 1024 * 1024)) {
            showAlertTopEnd("info", "Tamaño excedido", "el tamaño del archivo tiene que ser menor a 500MB KB");
            return false;
        }

    }



    return true;
}
