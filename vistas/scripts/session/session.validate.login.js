console.log("PathName: " + window.location.pathname);
if (window.location.pathname == getContextAPP()) {
    window.location = getHostAPP() + getContextAPP() + "index";
}
if (!(window.location.pathname.includes(getContextAPP() + "app") || window.location.pathname.includes(getContextAPP() + "aula") || window.location.pathname.includes(getContextAPP() + "api"))) {
    if (Cookies.get("clpe_token") != undefined) {
        if (parseJwt(Cookies.get("clpe_token"))) {
            sendIndex();
        }
    }
}