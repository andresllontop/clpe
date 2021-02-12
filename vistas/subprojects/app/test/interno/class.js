class Test {
    constructor(cantidad = 0, nombre = "", descripcion = "", sub = "", subcodigo = "", titulo = undefined, idtest = 0) {
        this.idtest = idtest;
        this.descripcion = descripcion;
        this.nombre = nombre;
        this.sub = sub;
        this.subcodigo = subcodigo;
        this.tipo = 2;
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
        this.test = new Test(0, "", "", "", "", titulo);

    }
}
var listDetalleTest = [];

