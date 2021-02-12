class Recurso {
    constructor(cantidad = 0,
        nombre = "",
        descripcion = "",
        titulo = undefined,
        idrecurso = 0) {
        this.idrecurso = idrecurso;
        this.descripcion = descripcion;
        this.nombre = nombre;
        this.tipo = 1;
        this.titulo = titulo;
        this.cantidad = cantidad;
    }
}

class Detalle_Recurso {
    constructor(
        iddetallerecurso,
        nombre,
        tipo,
        file,
        subtitulo = undefined
    ) {
        this.iddetallerecurso = iddetallerecurso;
        this.nombre = nombre;
        this.tipo = tipo;
        this.file = file;
        this.recurso = new Recurso(0, "", "", subtitulo);

    }
}
var listDetalleRecurso = [];

