class Convocatoria {
    constructor(cantidad = 0, fecha = "", descripcion = "", codigo, estado = 0, idconvocatoria = 0) {
        this.idconvocatoria = idconvocatoria;
        this.descripcion = descripcion;
        this.estado = estado;
        this.fecha = fecha;
        this.codigo = codigo;
        this.cantidad = cantidad;
    }
}

class Detalle_Convocatoria {
    constructor(
        iddetalleConvocatoria,
        descripcion,
        tipo
    ) {
        this.iddetalleConvocatoria = iddetalleConvocatoria;
        this.descripcion = descripcion;
        this.tipo = tipo;
    }
}
var listDetalleConvocatoria = [];

