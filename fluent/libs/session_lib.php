<?php

/**
 * Classe LibSession faz a interface com sessões.
 */
class SessionLib {

  /**
   * Inicia a sesseção que permite o acesso a variavel global $_SESSION
   */
  public function __construct() {
    session_start();
  }

  /**
   * Seta um sesseção na variavel global $_SESSION['flash']
   * @access public
   * @param string $mensage
   */
  public function setFlash($mensage, $name = 'flash') {
    $_SESSION['flash'][$name] = $mensage;
  }

  /**
   * Verifica se uma Session existe
   * @param string $key : nome da session que será verificada
   * @return boolean
   */
  public function check($key) {
    if (isset($_SESSION[$key]))
      return true;
    return false;
  }

  /**
   * Retorna a variavel que foi setada pela função setFlash()
   * @return string
   */
  public function flash() {
    return '<div id="flashMessage">' . $_SESSION['flash'] . '</div>';
  }

  /**
   * Destroi a session flash, esta funcão é utilizada para apagar a session flash antiga.
   */
  public static function destructFlash() {
    if (isset($_SESSION['flash'])) {
      //mantem sessão flash em caso de redirecionamento
      //App::$reaload_flash = $_SESSION['flash'];
      unset($_SESSION['flash']);
    }
  }

  /**
   * Apaga todas as sessões existentes
   */
  public function destroy() {

    session_destroy();
  }

}

?>