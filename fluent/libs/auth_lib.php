<?php

/* sergio cardoso da silva

  rua franklin lewis gemmel, 54;
 */

class AuthLib {

  public $message;
  public $user = 'email';
  public $pass = 'senha';

  /**
   * Tipo de permissão de acesso do usuário
   * @var <type> 
   */
  public $accss;

  public function __construct() {
    //$this->testLogin();
  }

  /**
   * Faz a validação do usuário e senha.
   * @param string $user
   * @param string $pass
   * @return boolean
   */
  public function login($table, $user, $pass, $fields = null) {
    $user = trim($user);
    $pass = convert_pass($pass);

    if ($user = data($table)->where("$this->user = '$user' and $this->pass = '$pass'")->exec()) {
      $this->logout();
      $user = $user[0];
      unset($user[$pass]);
      $_SESSION['auth'] = $user;
      set_flash('Login realizado com sucesso.', 'success');
      if (isset($_SESSION['auth_url'])) {
        $a = $_SESSION['auth_url'];
        unset($_SESSION['auth_url']);
        redirect($a);
      } else {
        redirect(App::$auth_url);
      }
    } else {
      set_flash('Usuário ou senha inválidos', 'alert');
      return false;
    }
  }

  /**
   * Verifica se o usuário está logado, caso não esteja ele é redirecionado
   * para a página de login que é definida no arquivo routers.php
   */
  public function check() {
    if (!isset($_SESSION['auth']['id'])) {
      $_SESSION['auth_url'] = App::$url;
      redirect(App::$login_url);
    } else {
      return true;
    }
  }

  public function getUser() {
    if (isset($_SESSION['auth'])) {
      return $_SESSION['auth'];
    } else {
      return false;
    }
  }

  /**
   * Destroi a SESSION auth, forçando assim o logof do usuário.
   */
  public function logout() {
    unset($_SESSION['auth']);
  }

  public function exists() {
    if (isset($_SESSION['auth']['id']) && !isset($_SESSION['auth']['usuario'])) {
      return $_SESSION['auth'];
    } else {
      return false;
    }
  }

}

?>