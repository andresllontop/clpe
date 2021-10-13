<?PHP

interface IAuth
{
    public function autenticar($usuario);
    public function estaAutenticado();
    public function destruir();
    public function usuario();
}
class Auth implements IAuth
{
    private $cookie = 'usuario';
    private $cookieToken = 'token';
    private $tiempo = 1; // Expresado en horas

    public function autenticar($usuario)
    {
        if (!is_object($usuario)) {
            throw new Exception("Fallo autenticación");
        } else if (empty($usuario->getId())) {
            throw new Exception("Fallo autenticación");
        }
        if ($usuario->getTipo() == 1) {
            $extraParaElToken = array(
                "idusuario" => $usuario->getId(),
                "usuario" => $usuario->getUsuario(),
                "tipo" => $usuario->getTipo(),
                "codigo" => $usuario->getCodigo(),
                "accion" => "",
                "tiempo" => (time() + (3600 * $this->tiempo)),
            );
        } elseif ($usuario->getTipo() == 2) {
            $extraParaElToken = array(
                "idusuario" => $usuario->getId(),
                "usuario" => $usuario->getUsuario(),
                "tipo" => $usuario->getTipo(),
                "codigo" => $usuario->getCodigo(),
                "libro" => $usuario->getLibroCode(),
                "accion" => "",
                "tiempo" => (time() + (3600 * $this->tiempo)),
            );
        }

        $json = array(
            $this->cookie => $usuario->__toString(),
            $this->cookieToken => $this->token(json_encode($extraParaElToken)),
        );

        return $json;
    }

    public function estaAutenticado()
    {
        if (!empty($_COOKIE[$this->cookie])) {
            $json = json_decode($_COOKIE[$this->cookie]);

            if (empty($json)) {
                throw new Exception("No esta autenticado");
            }

            if (empty($json->Token)) {
                throw new Exception("No esta autenticado");
            }

            $extraParaElToken = $json->id . $json->usuario;

            if ($json->Token !== $this->token($extraParaElToken)) {
                throw new Exception("No esta autenticado");
            }
        } else {
            throw new Exception("No esta autenticado");
        }
    }

    public function destruir()
    {
        $this->estaAutenticado();

        unset($_COOKIE[$this->cookie]);
        setcookie($this->cookie, null, -1);
    }

    public function usuario()
    {
        $this->estaAutenticado();
        return json_decode($_COOKIE[$this->cookie]);
    }

    private function token($extra)
    {
        // return sha1("hola" . $extra);
        // echo ($this->encryption($extra));
        return ($this->encryption($extra));
    }
    private function encryption($string)
    {
        $output = false;
        $key = hash('sha256', TOKEN_SECRET_KEY);
        $iv = substr(hash('sha256', TOKEN_SECRET_IV), 0, 16);
        $output = openssl_encrypt($string, TOKEN_METHOD, $key, 0, $iv);
        $output = base64_encode($output);

        return $output;
    }

}
