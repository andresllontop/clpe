$(document).ready(function() {
  listarinfo($("#codigo").text());
  listararbolincial($("#codigo").text());
  let codigoAntiguo = "";
  let contador = 0;
  let contador1 = 0;
  let contador2 = 0;
  let espacio = "&nbsp;&nbsp;";
  $(".nav-red > #btn-red").css({ background: "#39a030", color: "white" });
  $("#accion-economico").css("display", "none");
  $(".nav-red > #btn-red").click(function() {
    $("#accion-economico").css("display", "none");
    $("#accion-red").css("display", "block");
    $(".nav-red > #btn-red").css({ background: "#39a030", color: "white" });
    $(".nav-red > #btn-economico").css({
      background: "#fff",
      color: "#000"
    });
  });
  $(".nav-red > #btn-economico").click(function() {
    $("#accion-red").css("display", "none");
    $("#accion-economico").css("display", "block");
    $(".nav-red > #btn-economico").css({
      background: "#39a030",
      color: "white"
    });
    $(".nav-red > #btn-red").css({
      background: "#fff",
      color: "#000"
    });
  });
  listareconomico();
  listareconomicoMes();

  function listarinfo(code) {
    $.ajax({
      type: "POST",
      url: url + "ajax/arbolAjax.php",
      data: {
        accion: "datos",
        codigo: code
      },
      success: function(respuesta) {
        // console.log(respuesta);
        let admin = JSON.parse(respuesta);
        let html = "";
        contador1++;
        for (var key in admin) {
          html += `<tr>
            <td class="text-center" style="border-right:1px solid #ddd;">Codigo del Patrocinador</td>
            <td class="text-center">${admin[key].Cuenta_Codigo}</td>
            </tr>
            <tr>
            <td class="text-center" style="border-right:1px solid #ddd;">Nombre del Patrocinador</td>
            <td class="text-center">${admin[key].AdminNombre}  ${
            admin[key].AdminApellido
          }</td>
            </tr>
            <tr>
            <td class="text-center" style="border-right:1px solid #ddd;">Numero de Celular</td>
            <td class="text-center">${admin[key].AdminTelefono} </td>
            </tr>
            <tr>
            <td class="text-center" style="border-right:1px solid #ddd;">Correo Electronico</td>
            <td class="text-center">${admin[key].email} </td>
            </tr>
            `;
        }

        $(".RespuestaLista").html(html);
        if (contador1 == 1) {
          addEventsButtonsAdmin();
        }
      },
      error: function(e) {
        swal(
          "Ocurrió un error inesperado",
          "Por favor recargue la página",
          "error"
        );
      }
    });
    return false;
  }
  function listararbolincial(code) {
    $.ajax({
      type: "POST",
      url: url + "ajax/arbolAjax.php",
      data: {
        accion: "datos",
        codigo: code
      },
      success: function(respuesta) {
        // console.log(respuesta);
        let admin = JSON.parse(respuesta);
        let html2 = "";
        contador2++;
        for (var key in admin) {
          html2 = `
          <div class="text-left editar-arbol" cuenta="${
            admin[key].Cuenta_Codigo
          }" style=" width:100%;border-bottom:1px solid #ddd;cursor:pointer;padding-left:14px;">
          <i class="zmdi zmdi-arrow-left-bottom bg-primary text-light" style="width:20px; padding-left:5px;"></i>&nbsp;&nbsp;
          ${admin[key].AdminNombre} ${
            admin[key].AdminApellido
          } &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ${
            admin[key].Cuenta_Codigo
          }</div>
          <div id="${admin[key].Cuenta_Codigo}"></div>`;
          codigoAntiguo = admin[key]["Cuenta_Codigo"];
        }
        $("#RespuestaArbol").html(html2);
        if (contador2 == 1) {
          addEventsButtonsAdmin();
        }
      },
      error: function(e) {
        swal(
          "Ocurrió un error inesperado",
          "Por favor recargue la página",
          "error"
        );
      }
    });
    return false;
  }
  function listararbol(cuentacodigo) {
    $.ajax({
      type: "POST",
      url: url + "ajax/arbolAjax.php",
      data: {
        accion: "listarDato",
        codigo: cuentacodigo
      },
      success: function(respuesta) {
        // console.log(respuesta);
        if (respuesta !== "ninguno") {
          let admin = JSON.parse(respuesta);
          let html2 = "";
          espacio = espacio + espacio;
          contador++;
          for (var key in admin) {
            if (admin[key].hijo == null) {
            } else {
              html2 +=
                `
              <div class="text-left editar-arbol" cuenta="${
                admin[key].Cuenta_Codigo
              }" style=" width:100%;border-bottom:1px solid #ddd;cursor:pointer;padding-left:14px;">` +
                espacio +
                `<i class="zmdi zmdi-arrow-left-bottom bg-primary text-light" style="width:20px; padding-left:5px;"></i>&nbsp;&nbsp;
              ${admin[key].AdminNombre}  ${
                  admin[key].AdminApellido
                } &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ${
                  admin[key].Cuenta_Codigo
                }</div>
              <div id="${admin[key].Cuenta_Codigo}"></div>`;
            }
            codigoAntiguo = admin[key]["padre"];
          }
          $("#" + codigoAntiguo).html(html2);
          if (contador == 1) {
            addEventsButtonsArqui();
          }
        }
      },
      error: function(e) {
        swal(
          "Ocurrió un error inesperado",
          "Por favor recargue la página",
          "error"
        );
      }
    });
    return false;
  }
  function addEventsButtonsAdmin() {
    $("#RespuestaArbol > .editar-arbol").each(function(index, value) {
      $(this).click(function() {
        var css = $(this)
          .parent()
          .parent()
          .find("div #" + $(this).attr("cuenta"))
          .text();
        if (css == "") {
          listararbol($("#codigo").text());

          // $(this)
          //   .find("i")
          //   .removeClass("bg-primary");
          // $(this)
          //   .find("i")
          //   .addClass("bg-danger");
        } else {
          // $(this)
          //   .find("i")
          //   .removeClass("bg-danger");
          // $(this)
          //   .find("i")
          //   .addClass("bg-primary");
          $("#" + $(this).attr("cuenta")).html("");
          location.reload(true);
        }
      });
    });
  }
  function addEventsButtonsArqui() {
    $("#RespuestaArbol > div >.editar-arbol").each(function(index, value) {
      $(this).click(function() {
        var css2 = $(this)
          .parent()
          .parent()
          .find("div #" + $(this).attr("cuenta"))
          .text();

        if (css2 == "") {
          listararbol($(this).attr("cuenta"));
          listarinfo($(this).attr("cuenta"));
          // $(this)
          //   .find("i")
          //   .removeClass("bg-primary");
          // $(this)
          //   .find("i")
          //   .addClass("bg-danger");
        } else {
          // $(this)
          //   .find("i")
          //   .removeClass("bg-danger");
          // $(this)
          //   .find("i")
          //   .addClass("bg-primary");
          $("#" + $(this).attr("cuenta")).html("");
        }
      });
    });
  }
  function listareconomico() {
    $.ajax({
      type: "POST",
      url: url + "ajax/patrocinadoreconomicoAjax.php",
      data: {
        accion: "datos",
        codigo: $("#codigo").text(),
        tipo: "patrocinador"
      },
      success: function(respuesta) {
        // console.log(respuesta);
        let admin = JSON.parse(respuesta);
        let html = "";
        let html2 = "";
        let mes = Array();
        let totalmonto = 0;
        let totalPagado = 0;
        let totalNopagado = 0;
        // let cantidadnivel = Array();
        // for (let index = 0; index < 6; index++) {
        //   cantidadnivel[index] = 0;
        // }
        for (var key in admin) {
          html += `<tr>
                <td class="text-center" style="border-right:1px solid #ddd;">Nivel ${
                  admin[key].nivel
                }</td>
                <td class="text-center">${admin[key].porcentaje}%</td>
                <td class="text-center">$ ${(admin[key].porcentaje * 50) /
                  100}</td>
                <td class="text-center" style="border-right:1px solid #ddd;">${
                  admin[key].total_nopagado
                }</td>
                <td class="text-center" >$ ${(admin[key].total_nopagado *
                  admin[key].porcentaje *
                  50) /
                  100}</td>
                </tr>
                `;
          totalmonto +=
            (admin[key]["total_nopagado"] * admin[key]["porcentaje"] * 50) /
            100;
          totalPagado +=
            (admin[key]["total_pagado"] * admin[key]["porcentaje"] * 50) / 100;
          totalNopagado +=
            (admin[key]["total_nopagado"] * admin[key]["porcentaje"] * 50) /
            100;
          mes = admin[key]["fecha"];
        }
        var res = mes.split("-");
        let datom = Mes(res[0]);

        html += `<tr>
        <td class="text-center" colspan="4" style="border-right:1px solid #ddd;">Monto Total</td>
        <td class="text-center" >$ ${totalmonto}</td>
        </tr>
        `;
        html2 += `<tr>
        <td class="text-center" >$ ${totalPagado}</td>
        <td class="text-center" >$ ${totalPagado}</td>
        <td class="text-center" >$ ${totalNopagado}</td>
        <td class="text-center" >$ ${totalmonto}</td>
        </tr>
        `;
        $(".RespuestaListaEconomico").html(html);
        $(".RespuestaListaPresupuesto").html(html2);
        $("#titulo-mes").html("Monto obtenido del Presente Mes de " + datom);
      },
      error: function(e) {
        swal(
          "Ocurrió un error inesperado",
          "Por favor recargue la página",
          "error"
        );
      }
    });
    return false;
  }
  function listareconomicoMes() {
    $.ajax({
      type: "POST",
      url: url + "ajax/patrocinadoreconomicoAjax.php",
      data: {
        accion: "datos",
        codigo: $("#codigo").text(),
        tipo: "patrocinador"
      },
      success: function(respuesta) {
        // console.log(respuesta);
        let admin = JSON.parse(respuesta);
        let html2 = "";
        let mes = Array();
        let totalmonto = 0;
        let totalPagado = 0;
        let totalNopagado = 0;
        // let cantidadnivel = Array();
        // for (let index = 0; index < 6; index++) {
        //   cantidadnivel[index] = 0;
        // }
        for (var key in admin) {
          //   console.log(admin[key]["nivel"]);
          totalmonto +=
            (admin[key]["total_nopagado"] * admin[key]["porcentaje"] * 50) /
            100;
          totalPagado +=
            (admin[key]["total_pagado"] * admin[key]["porcentaje"] * 50) / 100;
          totalNopagado += parseFloat(admin[key]["monto"]);
          mes = admin[key]["fecha"];
        }
        var res = mes.split("-");
        let datom = Mes(res[0]);
        // console.log("mes : "+datom);
        html2 += `<tr>
        <td class="text-center" >${datom}</td>
        <td class="text-center" >${res[1]}</td>
        <td class="text-center" >$ ${totalPagado}</td>
        <td class="text-center" >$ ${totalNopagado}</td>
        <td class="text-center" >$ ${totalmonto}</td>
        </tr>
        `;
        $(".RespuestaListaPresupuesto").html(html2);
      },
      error: function(e) {
        swal(
          "Ocurrió un error inesperado",
          "Por favor recargue la página",
          "error"
        );
      }
    });
    return false;
  }
  function Mes(valor) {
    switch (valor) {
      case "1":
        return "Enero";
        break;
      case "2":
        return "Febrero";
        break;
      case "3":
        return "Marzo";
        break;
      case "4":
        return "Abril";
        break;
      case "5":
        return "Mayo";
        break;
      case "6":
        return "Junio";
        break;
      case "7":
        return "Julio";
        break;
      case "8":
        return "Agosto";
        break;
      case "9":
        return "Setiembre";
        break;
      case "10":
        return "Octubre";
        break;
      case "11":
        return "Noviembre";
        break;
      case "12":
        return "Diciembre";
        break;
    }
  }
});
