class Test {
    constructor(cantidad = 0, nombre = "", descripcion = "", titulo = undefined, idtest = 0) {
        this.idtest = idtest;
        this.descripcion = descripcion;
        this.nombre = nombre;
        this.sub = null;
        this.subcodigo = null;
        this.tipo = 1;
        this.titulo = titulo;
        this.cantidad = cantidad;
    }
}

class Detalle_Test {
    constructor(
        iddetalletest,
        descripcion,
        subtitulo,
        titulo = undefined
    ) {
        this.iddetalletest = iddetalletest;
        this.descripcion = descripcion;
        this.subtitulo = subtitulo;
        this.test = new Test(0, "", "", titulo);

    }
}
var listDetalleTest = [];

