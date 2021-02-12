class Respuesta {
    constructor(tipo = 2) {
        this.idrespuesta = 0;
        this.cuenta = user_session.codigo;
        this.test = testSelected.idtest;
        this.tipo = tipo;

    }
}

class Detalle_Respuesta {
    constructor(
        iddetallerespuesta,
        descripcion,
        pregunta,
        subtitulo,
        test,
        tipo = 2
    ) {
        this.iddetallerespuesta = iddetallerespuesta;
        this.pregunta = pregunta;
        this.descripcion = descripcion;
        this.subtitulo = subtitulo;
        this.test = test;
        this.respuesta = new Respuesta(tipo);

    }
}
var listDetalleRespuesta = [];

