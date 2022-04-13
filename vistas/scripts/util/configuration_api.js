/*HOST BACKEND */
function getHostAPI() {
  return getHostAPP() + getContextAPP() + "api/";
}

/* HOST FRONTED*/
function getHostAPP() {
  return 'http://clpe.local/';
  //  return 'https://clpe5.com/'
  ;
}
/* */
function getContextAPP() {
  return 'clpe/';
  //  return '';
}

function getHostFrontEnd() {
  return getHostAPP() + getContextAPP();
}


