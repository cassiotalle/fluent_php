<?php

class FormLib {

  private $tpl = true;
  private $fields = array();
  private $name;
  private $data = array();

  public function __construct() {
    
  }

  public function create($name, $action = null, $class = 'class="forms columnar"', $method = 'post', $atributes = false) {
    if (!array_key_exists($name, App::$model)) {
      App::$model[$name] = Neon::decode_file(MODEL . $name . '.neon');
    }
    if (isset(App::$data[$name]) && is_array(App::$data[$name])) {
      $this->data = App::$data[$name];
    }
    $this->mask();
    // formata action
    if (is_null($action))
      $action = App::$url;
    else {
      if ($action[0] != '/') {
        $action = App::$controller . '/' . $action;
      } else {
        $action = substr($action, 1);
      }
      $action = App::$link . $action;
    }

    echo App::$link;

    $this->name = $name;
    $this->form($name, $method, $action, $class, $atributes);
  }

  private function form($name, $method, $action, $class, $atributes = false) {
    echo "\n" . '<form name="' . $name . '" id="' . $name . '" method="' . $method . '"' . $class . ' action="' . $action . '" ' . $this->atributes($atributes) . ' >' . "\n";
  }

  /**
   * Escreve um label
   * @param type $for
   * @param type $text
   * @return null
   */
  public function label($for, $text) {
    if (isset($text)) {
      return '<label for="' . $for . '">' . $text . '</label>';
    } else
    if (isset(App::$model[$this->name][$for]['title'])) {
      return '<label for="' . $for . '">' . App::$model[$this->name][$for]['title'] . '</label>';
    }
    else
      return null;
  }

  /**
   * Cria um campo no formulário de acordo com o seu tipo
   * @param type $type
   * @param type $name
   * @param type $title
   * @param type $value
   * @param type $atributes
   */
  public function input($type, $name, $title = null, $value = null, $atributes = null) {
    echo $this->label($name, $title);
    if(is_null($value) && isset($this->data[$name])){
      $value = $this->data[$name];
    }
    if ($type == 'textarea') {
      echo '<textarea name="' . $name . '" id="' . $this->name . '[' . $name . ']" ' . $atributes . '>' . nl2br($value) . '</textarea>';
    } else {
      echo '<input type="' . $type . '" name="' . $this->name . '[' . $name . ']" id="' . $name . '" value="' . $value . '" ' . $this->atributes($atributes) . ' />';
    }
    $this->showError($name);
  }

  public function text($name, $title = null, $value = null, $atributes = null) {
    $this->input('text', $name, $title, $value, $atributes);
  }

  public function password($name, $title = null, $value = null, $atributes = null) {
    $this->input('password', $name, $title, $value, $atributes);
  }

  public function hidden($name, $value = null, $atributes = false) {
    $this->input('hidden', $name, null, $value, $atributes);
  }

  public function button($name, $value = null, $atributes = 'class="btn"') {
    $this->input('button', $name, null, $value, $atributes);
  }

  public function submit($value, $atributes = 'class="btn"') {
    echo '<input type="submit" name="submit" id="submit" value="' . $value . '" ' . $atributes . '>';
  }
  
  public function reset($value, $atributes = 'class="btn"') {
    echo '<input type="reset" name="reset" id="reset" value="' . $value . '" ' . $atributes . '>';
  }

  public function radio($name, $title = null, $value = array(), $atributes = null) {
    $this->input('radio', $name, $title, $value, $atributes);
  }

  public function checkbox($name, $title = null, $value = array(), $atributes = null) {
    $this->input('radio', $name, $title, $value, $atributes);
  }

  public function file($name, $title = null, $value = null, $atributes = false) {
    $this->input('file', $name, $title, $value, $atributes);
  }

  public function textarea($name, $title = null, $value = null, $atributes = null) {
    $this->input('textarea', $name, $title, $value, $atributes);
  }
 
  public function captcha($title) {
    $this->setField('captcha');
    include(LIBS . 'componnents/captcha.php');
    $capt = new Captcha();
    echo '<label for="captcha">' . $title . '</label>';
    echo $capt->gif;
    echo '<input type="text" name="captcha" id="captcha" maxlength="4" size="10" />';
    $this->showError('captcha');
  }

  private function mask() {
    if (isset(App::$model[$this->name])) {
      $data = "\n" . '<script type="text/javascript">
                    jQuery(function($){';
      foreach (App::$model[$this->name] as $model) {
        if (array_key_exists($model['type'], App::$mask['string'])) {
          $data.='$("#' . $model['Field'] . '").mask("' . App::$mask['string'][$model['type']] . '");' . "\n";
        } elseif (array_key_exists($model['type'], App::$mask['numeric'])) {
          $data.='$("#' . $model['Field'] . '").maskMoney({symbol:"' . App::$mask['numeric'][$model['type']] . '",decimal:",",thousands:"."})' . "\n";
        }
      }
      $data .= '})</script>';
      App::$head[] = $data;
    }
  }

// Funções para lista...

  /**
   * Implementa elementos selecionados em um List, checkbox e radio
   * @param type $type
   * @param type $name
   * @param type $selected
   */
  public function listField($type, $name, $selected = null) {
    $values = explode('_', $name);
    $table = $values[0];
    $field = $values[1];
    if (Db::query('select id, ' . $field . ' from ' . $table . ' order by ' . $field)) {
      $array = Db::getData(0);
      foreach ($array as $a) {
        $v[] = $a[0];
      }
      $opt_chek['radio'] = '" checked="checked';
      $opt_chek['checkbox'] = '" checked="checked';
      $opt_chek['select'] = '" selected="selected';
      if (array_key_exists($name, App::$data))
        $selected = App::$data[$name];
      if (is_array($selected)) {
        foreach ($selected as $s) {
          $key = array_search($s, $v);
          if (is_int($key)) {
            $array[$key][0].=$opt_chek[$type];
          }
        }
      } elseif (isset($selected)) {
        $key = array_search($selected, $v);
        if (is_int($key)) {
          $array[$key][0].=$opt_chek[$type];
        }
      }

      $this->$type($name, $array);
    }
  }

  public function select($name, $value) {
    $this->setField($name);
    $field = '<select name="' . $name . '" id="' . $name . '"  >';
    foreach ($value as $v) {
      $field .= '<option id="' . $v[0] . '" value="' . $v[0] . '" >' . $v[1] . '</option>';
    }
    $field.='</select>';
    echo $field;
    echo $this->showError($name);
  }

  /**
   * Captcha
   * Gera uma imagem para validação de formulário.
   */
// fim das funções de select

  private function atributes($at) {
    if ($at) {
      if (is_array($at)) {
        foreach ($at as $k => $v) {
          $atributos.=$k . '="' . $v . '"';
        }
        return $atributos;
      } else {
        return $at;
      }
    }
    else
      return '';
  }

  private function showError($name) {
    if (array_key_exists($name, Validate::$error_list)) {
      echo '<span class="errorform">' . Validate::$error_list[$name] . '</span>';
    }
  }

  public function loadData(array $data) {
    App::$data = $data[0];
  }

  public function end() {
    echo '</form>';
  }

  public function setField($name) {
    $this->fields[] = $name;
  }

  public function ajaxSubmit($value, $efect = null) {
    echo '<input type="button" id="ajaxSubmit" value="' . $value . '" />';
    echo $this->generateAjax($efect);
  }

  private function generateAjax($efect = null) {
    $code = "<script type=\"text/javascript\" charset=\"utf-8\">
            \n$(function(){
        //Dispara o Submit ao clicar no input com id = #calc
        $('#ajaxSubmit').click(function(){";

    foreach ($this->fields as $f) {
      $code .= "\nvar " . $f . " = $('#" . $f . "').val();";
      $listFields[] = $f . ': ' . $f;
    }

    $code .="\n$.post('" . App::$url . App::$controller . "/" . App::$action . "',{";
    $code .=implode(',', $listFields) . "},function(data){\n";
    $code .="$('#containerForm').html(data);\n";
    if (isset($efect)) {
      $code .="$('#containerForm')." . $efect . "(1000);";
    }
    $code .="});});});\n</script>";
    return $code;
  }

}

?>