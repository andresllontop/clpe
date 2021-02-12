let contador = 0;
let blobGeneral;
let blobGeneralAudio;
let ajax_load =
    "<div class='progress'>" +
    "<div id='progress_id' class='progress-bar progress-bar-striped active' " +
    "role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 45%'>" +
    "n/100</div></div>";
$(".FormularioAjax").submit(function (e) {
    e.preventDefault();
    let form = $(this);
    let metodo = form.attr("method");
    let formdata = new FormData(this);
    if (contador === 0) {
        swal(
            "Ocurrió un error inesperado",
            "Debes grabar tu video para poder guardar Informacion",
            "error"
        );
    } else {
        if (contador === 1) {
            formdata.append("Video-reg", blobGeneral);
            formdata.append("accion", "save");
        } else {
            formdata.append("accion", "save");
        }

        ProcesarAjax(metodo, formdata);
    }
});
$(".FormularioAjaxAudio").submit(function (e) {
    e.preventDefault();
    let form = $(this);
    let metodo = form.attr("method");
    let formdata = new FormData(this);
    ("data");
    if (contador === 0) {
        swal(
            "Ocurrió un error inesperado",
            "Debes grabar tu video para poder guardar Informacion",
            "error"
        );
    } else {
        if (contador === 1) {
            formdata.append("Audio-reg", blobGeneralAudio);
            formdata.append("accion", "save");
        } else {
            formdata.append("accion", "save");
        }
        $("#cargarpagina").html(ajax_load);
        ProcesarAjaxAudio(metodo, formdata);
    }
});
var elemento = document.querySelector("#AbrirVideo");
if (elemento) {
    elemento.addEventListener("click", function (ev) {
        $("#ModalFormularioVideo").modal("show");
        VideoReproducir(
            document.querySelector("video#gum"),
            document.querySelector("video#recorded"),
            document.querySelector("button#record"),
            document.querySelector("button#play"),
            // document.querySelector("button#download"),
            document.querySelector("button#enviarVideo")
        );
    });
}
// document.querySelector("#AbrirVideo").addEventListener("click", function (ev) {
//     $("#ModalFormularioVideo").modal("show");
//     VideoReproducir(
//         document.querySelector("video#gum"),
//         document.querySelector("video#recorded"),
//         document.querySelector("button#record"),
//         document.querySelector("button#play"),
//         // document.querySelector("button#download"),
//         document.querySelector("button#enviarVideo")
//     );
// });

// let obj1 = document.getElementById("ventanaModalVideo");
// obj1.style.display = "block";
var el = document.querySelector("#Abrir-VideoModal");
if (el) {
    el.addEventListener('click', function (ev) {
        let obj1 = document.getElementById("container2");
        obj1.style.display = "block";
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
// document.querySelector("#Abrir-VideoModal").addEventListener("click", function (ev) {
// let obj1 = document.getElementById("container2");
// obj1.style.display = "block";
// AudioReproducir(
//     document.querySelector("audio#gum2"),
//     document.querySelector("audio#recorded2"),
//     document.querySelector("button#record2"),
//     document.querySelector("button#play2"),
//     document.querySelector("button#enviarAudio")
//     // , document.querySelector("button#download2")
// );
// });
function VideoReproducir(
    gumvideo,
    recordedvideo,
    recordbutton,
    playbutton,
    // downloadbutton,
    enviarVideo
) {
    let mediaSource = new MediaSource();
    mediaSource.addEventListener("sourceopen", handleSourceOpen, false);
    let mediaRecorder;
    let recordedBlobs;
    let sourceBuffer;
    let gumVideo = gumvideo;
    let recordedVideo = recordedvideo;
    let recordButton = recordbutton;
    let playButton = playbutton;
    // let downloadButton = downloadbutton;
    let enviarvideo = enviarVideo;
    recordButton.onclick = toggleRecording;
    playButton.onclick = play;
    // downloadButton.onclick = download;

    navigator.getUserMedia = navigator.getUserMedia ||
        navigator.webkitGetUserMedia ||
        navigator.mozGetUserMedia;

    if (navigator.getUserMedia) {
        navigator.mediaDevices
            .getUserMedia({
                audio: true,
                video: true
            })
            .then(successCallback, errorCallback);
        // navigator.getUserMedia({ audio: true, video: true });
        enviarvideo.onclick = Enviar;

    } else {
        console.log("getUserMedia not supported");
        swal(
            "No puedes acceder a la camara",
            "Graba tu Video desde un dispositivo externo y envialo desde tu PC",
            "error"
        );
        $("#ModalFormularioVideo").modal("hide");
        let ob = document.getElementById("AbrirVideo");
        ob.style.display = "none";
        var html = `<p class="text-left">Subir video:</p>
        <input type="file" clas=" material-control" name="Video-reg" id="Video-reg">
        `;
        $(".RespondeVideo").html(html);
        contador = 2;
    }


    function successCallback(stream) {
        window.stream = stream;
        gumVideo.srcObject = stream;
    }

    function errorCallback(error) {
        console.log("navigator.getUserMedia error: ", error);
        swal(
            "No puedes acceder a la camara",
            "Graba tu Video desde un dispositivo externo y envialo desde tu PC",
            "error"
        );
        $("#ModalFormularioVideo").modal("hide");
        let ob = document.getElementById("AbrirVideo");
        ob.style.display = "none";
        var html = `<p class="text-left">Subir video:</p>
        <input type="file" clas=" material-control" name="Video-reg" id="Video-reg">
        `;
        $(".RespondeVideo").html(html);
        contador = 2;
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
        if (recordButton.textContent === "Comenzar Grabacion") {
            startRecording();
        } else {
            stopRecording();
            recordButton.textContent = "Comenzar Grabacion";
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
                    options = "video/mp4";
                    mediaRecorder = new MediaRecorder(window.stream, options);
                } catch (e2) {
                    alert("MediaRecorder no soportado para estos navegadores.");
                    console.error("Exception while creating MediaRecorder:", e2);
                    return;
                }
            }
        }

        recordButton.textContent = "Detener Grabacion";
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
        let superBuffer = new Blob(recordedBlobs, { type: "video/webm" });
        recordedVideo.src = window.URL.createObjectURL(superBuffer);
    }

    function download() {
        let blob = new Blob(recordedBlobs, { type: "video/webm" });
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
                "Debes grabar tu video para poder enviar el video",
                "error"
            );
        } else {
            let blob = new Blob(recordedBlobs, { type: "video/webm" });
            blobGeneral = blob;
            swal(
                "Envio Exitosamente",
                "Se envio el video al formulario, para continuar selecciona el boton guardar",
                "success"
            );
            stopStream(window.stream);
            $("#ModalFormularioVideo").modal("hide");
            var html = "<p> Video Grabado </p>";
            $(".RespondeVideo").html(html);
        }
    }
    function stopStream(stream) {
        for (let track of stream.getTracks()) {
            track.stop();
        }
    }
}
function ProcesarAjax(metodo, formdata) {
    $("#cargarpagina").html(ajax_load);
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
    let mediaSource = new MediaSource();
    mediaSource.addEventListener("sourceopen", handleSourceOpen, false);
    let mediaRecorder;
    let recordedBlobs;
    let sourceBuffer;
    let gumVideo = gumvideo;
    let recordedVideo = recordedvideo;
    let recordButton = recordbutton;
    let playButton = playbutton;
    // let downloadButton = downloadbutton;
    let enviarvideo = enviarVideo;
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
        if (recordButton.textContent === "Comenzar Grabacion") {
            startRecording();
        } else {
            stopRecording();
            recordButton.textContent = "Comenzar Grabacion";
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

        recordButton.textContent = "Detener Grabacion";
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
function ProcesarAjaxAudio(metodo, formdata) {
    $.ajax({
        type: metodo,
        url: url + "ajax/declaracionAjax.php",
        data: formdata,
        cache: false,
        contentType: false,
        processData: false,
        // modificar el valor de xhr a nuestro gusto
        xhr: function () {
            // obtener el objeto XmlHttpRequest nativo
            let xhr = $.ajaxSettings.xhr();
            // añadirle un controlador para el evento onprogress
            xhr.onprogress = function (evt) {
                // calculamos el porcentaje y nos quedamos sólo con la parte entera
                let porcentaje = Math.floor((evt.loaded / evt.total) * 100);
                // actualizamos el texto con el porcentaje mostrado
                $("#progress_id").text(porcentaje + "/100");
                // actualizamos la cantidad avanzada en la barra de progreso
                $("#progress_id").attr("aria-valuenow", porcentaje);
                $("#progress_id").css("width", porcentaje + "%");
            };
            // devolvemos el objeto xhr modificado
            return xhr;
        },
        success: function (data) {
            data;
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
