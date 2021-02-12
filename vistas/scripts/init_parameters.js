
class BeanRequest {
    constructor() {
        this.entity_api = "";
        this.operation = "";
        this.type_request = "";
    }
}

document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll('.a-index').forEach(a => {
        a.onclick = function () {
            sendIndex();
        };
    });

    document.querySelectorAll('.a-perfil').forEach(a => {
        a.onclick = function () {
            location.href = getHostFrontEnd() + "app/perfil";
        };
    });
    document.querySelectorAll('.aula-perfil').forEach(a => {
        a.onclick = function () {
            location.href = getHostFrontEnd() + "aula/perfil";
        };
    });

    document.querySelectorAll('.a-help').forEach(a => {
        a.onclick = function () {
            swal({
                title: "Ayuda del sistema",
                text: "Estamos a tu disposicion, cualquier defecto del sistema contactate con el administrador del club de lectura.",
                type: "info",
                timer: 8000,
                showConfirmButton: false
            });
        };
    });

    document.querySelectorAll('.a-close-session').forEach(a => {
        a.onclick = function () {
            swal(
                {
                    title: "¿Estás seguro?",
                    text: "Quieres salir del sistema y cerrar la sesión actual?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#5cb85c",
                    confirmButtonText: "Si, salir",
                    cancelButtonText: "No, cancelar",
                    cancelButtonColor: "#FF5733",
                    animation: "slide-from-top",
                    closeOnConfirm: false
                },
                function () {
                    swal.close();
                    closeSession();
                }
            );
        };
    });



});
