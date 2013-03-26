<?

class Validate {

  public static $errorList = array();
  private $add = array();
  public $data;
  private $validate_user;
  function __construct() {
    $this->validate_user = Neon::decode_file(PATH.'fluent'.DS.'validate.neon');
  }
  
  /**
   * Grupo de tipo de dados
   */

  /**
   * Valida e-mail
   * @param type $field
   * @param type $email
   * @param type $message
   * @return boolean
   */
  public function email($field, $email, $message = null) {
    $pattern = "/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/";
    if (preg_match($pattern, $email))
      return true;
    else {
      if (isset($message)) {
        self::$errorList[$field] = $message;
      } else {
        self::$errorList[$field] = $this->validate_user['validation_mensages']['email'];
      }
      return false;
    }
  }

  /**
   * Valida url
   * @param type $field
   * @param type $value
   * @param type $message
   * @return boolean
   */
  public function url($field, $value, $message = null) {
    if (preg_match("/^http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?$/i", $value)) {
      return true;
    } else {
      if (isset($message)) {
        self::$errorList[$field] = $message;
      } else {
        self::$errorList[$field] = $this->validate_user['validation_mensages']['url'];
      }
      return false;
    }
  }

  /**
   * Verifica se variável é um número inteiro
   * @param type $field
   * @param type $number
   * @param type $message
   * @return boolean
   */
  public function int($field, $number, $message = null) {
    if (is_int($number)) {
      return true;
    } else {
      if (isset($message)) {
        self::$errorList[$field] = $message;
      } else {
        self::$errorList[$field] = $this->validate_user['validation_mensages']['int'];
      }
      return false;
    }
  }

  /**
   * Verifica se a variável é um número decimal
   * @param type $field
   * @param type $number
   * @param type $message
   * @return boolean
   */
  public function decimal($field, $number, $message = null) {
    if (preg_match('!^\d+.\d\d$!', $number)) {
      return true;
    } else {
      if (isset($message)) {
        self::$errorList[$field] = $message;
      } else {
        self::$errorList[$field] = $this->validate_user['validation_mensages']['decimal'];
      }
      return false;
    }
  }

  /**
   * Faz a validação de um número de CPF de acordo com o seu dígito verificador.
   * @param type $field
   * @param type $cpf
   * @param type $message
   * @return boolean
   */
  public function cpf($field, $cpf, $message = null) {
    $sinais = array(".", "-");
    $cpf = str_replace($sinais, "", $cpf);
    $cpf_validar = substr($cpf, 0, 9);
    $soma = 0;
    $n = 11;

    for ($i = 0; $i <= 9; $i++) {
      $n = $n - 1;
      $soma = $soma + (substr($cpf_validar, $i, 1) * $n);
    };
    $resto = $soma % 11;
    if ($resto < 2) {
      $cpf_validar = $cpf_validar . "0";
    } else {
      $cpf_validar = $cpf_validar . (11 - $resto);
    };
    //Segunda parte da validação do CPF
    $soma = 0;
    $n = 12;
    for ($i = 0; $i <= 10; $i++) {
      $n = $n - 1;
      $soma = $soma + (substr($cpf_validar, $i, 1) * $n);
    };
    $resto = $soma % 11;
    if ($resto < 2) {
      $cpf_validar = $cpf_validar . "0";
    } else {
      $cpf_validar = $cpf_validar . (11 - $resto);
    }
    if ($cpf_validar == $cpf) {
      return true;
    } else {
      if (isset($message)) {
        self::$errorList[$field] = $message;
      } else {
        self::$errorList[$field] = $this->validate_user['validation_mensages']['cpf'];
      }
      return false;
    };
  }

  /**
   * Verifica se valo é uma data válida
   * @param type $field
   * @param type $value
   * @param type $message
   * @return boolean
   */
  public function date($field, $value, $message = null) {
    $m = (int) $value[4] . $value[5];
    $d = (int) $value[6] . $value[7];
    $y = (int) $value[0] . $value[1] . $value[2] . $value[3];
    if (checkdate($m, $d, $y)) {
      return true;
    } else {
      if (isset($message)) {
        self::$errorList[$field] = $message;
      } else {
        self::$errorList[$field] = $this->validate_user['validation_mensages']['date'];
      }
      return false;
    }
  }

  /**
   * Faz a validação do valor gerado pela imagem captcha.
   * @param type $value
   * @param type $message
   * @return boolean
   */
  function captcha($value, $message = 'Código de segurança inválido.') {
    if ($_SESSION['captcha'] == $value) {
      unset($_SESSION['captcha']);
      return true;
    } else {
      unset($_SESSION['captcha']);
      self::$errorList['captcha'] = $message;
      return false;
    }
  }

  /**
   * Validação
   * @param type $data
   * @param type $data_return
   */
  private function redlink(&$data, &$data_return) {
    if (array_key_exists('redlink', App::$model)) {
      $k = App::$model['redlink']['Key'];
      if (array_key_exists($k, $data) && isset($data[$k])) {
        $data_return['redlink'] = simpleString($data[$k], '_', -1);
      }
    }
  }

  /**
   * 
   * @param type $field
   * @param type $value
   * @param type $value2
   * @param type $message
   * @return boolean
   */
  public function equal($field, $value, $value2, $message) {
    if ($value == $value2) {
      return true;
    } else {
      self::$errorList[$field] = $message;
      return false;
    }
  }

  /**
   * 
   * @param type $field
   * @param type $value
   * @param type $message
   * @param type $table
   * @return boolean
   */
  public function unique($field, $value, $value2, $message) {
    if (!isset($table))
      $table = App::$current_table;
    Db::query('SELECT ' . $field . ' FROM ' . $table . ' where ' . $field . ' = "' . $value . '";');
    if (!Db::numRows()) {
      return true;
    } else {
      self::$errorList[$field] = $message;
    }
  }

  /**
   * 
   * @param type $field
   * @param type $value
   * @param type $value2
   * @param type $message
   * @return boolean
   */
  public function max($field, $value, $value2, $message) {
    if ($value > $value2) {
      self::$errorList[$field] = $message;
      return false;
    } else {
      return true;
    }
  }

  /**
   * 
   * @param type $field
   * @param type $value
   * @param type $value2
   * @param type $message
   * @return boolean
   */
  public function min($field, $value, $value2, $message) {
    if ($value < $value2) {
      return true;
    } else {
      self::$errorList[$field] = $message;
      return false;
    }
  }

  /**
   * 
   * @param type $field
   * @param type $value
   * @param type $message
   * @return boolean
   */
  public function notnull($field, $value, $message = null) {
    if (strlen($value) < 1 || is_null($value)) {
      if (isset($message)) {
        self::$errorList[$field] = $message;
      } else {
        self::$errorList[$field] = $this->validate_user['validation_mensages']['not_null'];
      }
      return false;
    } else {
      return true;
    }
  }

  /**
   * 
   * @param type $field
   * @param type $value
   * @param type $max
   * @return boolean
   */
  public function maxlen($field, $value, $max, $message = null) {
    $len = strlen($value);
    if ($len > $max) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * 
   * @param type $field
   * @param type $value
   * @param type $min
   * @return boolean
   */
  public function minlen($field, $value, $min, $message = null) {
    $len = strlen($value);
    if ($len < $min) {
      return false;
    } else {
      return true;
    }
  }

  public function process($table, $data) {
    $model = Neon::decode_file(PATH . 'data' . DS . $table . '.neon');  
    if ($model) {
      $data_return = array();
      $keys = array_keys($model);
 
      foreach ($keys as $k) {
        if(array_key_exists('type',$model[$k])){
        $type = $model[$k]['type'];
        $this->$type($k, $data[$k]);
        
        if(array_key_exists('validate', $model[$k]) && check_array($model[$k]['validate'])){
          $kvs = array_keys($model[$k]['validate']);
          foreach ($kvs as $kv){
            if(is_array($model[$k]['validate'][$kv]) && count($model[$k]['validate'][$kv]) == 2){
              $mensage = $model[$k]['validate'][$kv][1];
            }
            else{
              $mensage = $this->validate_user['validation_mensages'][$kv];
            }
            $this->$kv($k, $data[$k], $model[$k]['validate'][$kv], $mensage);
          }
        }
        
      }

        if ($valid && array_key_exists($k, $this->add) && array_key_exists($k, $data)) {
          $function = $this->add[$k][0];
          if (array_key_exists($k, $data_return)) {
            $v = $data_return[$k];
          } else {
            $v = $data[$k];
          }
          if (isset($this->add[$k][1])) {
            $this->$function($k, $v, $this->add[$k][1], $this->add[$k][2]);
          } else {
            $this->$function($k, $v, $this->add[$k][2]);
          }
        }
      }
    }

    if (check_array(self::$errorList)) {
      App::$data = $data;
      return false;
    } else {
      $this->data = $data_return;
      return true;
    }
  }

  private function filter($value, $type) {
    switch ($type) {
      case 'date':
        $p = explode("/", $value);
        if (count($p) == 3) {
          $value = $p[2] . $p[1] . $p[0];
        }
        break;
      case 'decimal':
        $value = moeda($value);
        break;
      case 'euro':
        $value = moeda($value);
        break;
      case 'real':
        $value = moeda($value);
        break;
      case 'dollar':
        $value = moeda($value);
        break;
      case 'password':
        if (strlen($value) > 0) {
          $value = convert_pass($value);
        }
        break;
    }
    return $value;
  }

  private function defealtValidade($field, $type, $value) {
    $types = array(
        'euro' => 'decimal',
        'dollar' => 'decimal',
        'real' => 'decimal',
        'date' => 'date',
        'email' => 'email',
        'cpf' => 'cpf',
        'int' => 'int',
        'url' => 'url'
    );
    if (array_key_exists($type, $types)) {
      return $this->$types[$type]($field, $value);
    }
    else
      return true;
  }

  public function add($type, $field, $value, $msg = null) {
    $this->add[$field] = array($type, $value, $msg);
  }

}

?>