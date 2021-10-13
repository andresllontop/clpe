function closeSession() {
  let keys = keysCOOKIES();
  for (let i = 0; i < keys.length; i++) {
    Cookies.remove(keys[i]);
  }
  if (Cookies.get('clpe_libro') != undefined) {
    Cookies.remove('clpe_libro');
  }

  //REDIRECCIONAMOS EL LOGIN
  location.href = getHostAPP() + getContextAPP() + "index";
}
function keysCOOKIES() {
  var keys = ['clpe_token', 'clpe_user'];
  return keys;
}
function closeCOOKIESNiubiz() {
  let keys = keysCOOKIESNiubiz();
  for (let i = 0; i < keys.length; i++) {
    Cookies.remove(keys[i]);
  }
}

function keysCOOKIESNiubiz() {
  var keys = ['clpe_niubiz', 'clpe_empresa_compra', 'clpe_niubiz_date'];
  return keys;
}

function setCookieSessionNiubiz(test) {
  if (test != undefined) {
    Cookies.set('clpe_niubiz', test);
  }

}
function setCookieSessionLibro(test) {
  if (test != undefined) {
    Cookies.set('clpe_libro', test);
  }

}
function parseJwt(token) {
  try {
    var base64Url = token.split('.')[1];
    var base64 = base64Url.replace('-', '+').replace('_', '/');
    JSON.parse(window.atob(base64));

    return true;
  } catch (error) {
    //console.log('Error el token no es valido');
    return true;
  }
  //return JSON.parse(window.atob(base64));
}

function setCookieSession(token, user) {

  Cookies.set('clpe_token', token);
  Cookies.set('clpe_user', user);
}

function sendIndex() {
  let user = Cookies.getJSON('clpe_user');

  if (user == undefined) {
    closeSession();
    return;
  }

  switch (parseInt(user.tipo_usuario)) {
    case 1:
      //APP
      location.href = getHostFrontEnd() + 'app/index';
      break;
    case 2:
      //CLIENTE
      location.href = getHostFrontEnd() + 'aula/index';
      break;
  }
}

function getIdAreaUserSession() {
  let url = window.location.href;
  if (url.includes('obstetricia')) return 4;

  if (url.includes('psicopedagogia')) return 6;

  if (url.includes('social')) return 7;
}

function setUrlFotoUserSession(url_foto) {
  document.querySelectorAll('.dt-avatar').forEach((img) => {
    img.setAttribute('src', url_foto);
  });
}
