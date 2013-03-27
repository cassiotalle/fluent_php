<?php

include_once(CORE . 'validate.php');

class FormLib {

  private $tpl = true;
  private $fields = array();
  private $name;
  private $field_type = array(
      'text' => 'text',
      'password' => 'password',
      'date' => 'date',
  );
  private $data = array();
  private $validate = array();

  public function __construct() {
    
  }

  public function create($name, $action = null, $method = 'post', $atributes = false) {
    if (!array_key_exists($name, App::$model)) {
      App::$model[$name] = Neon::decode_file(MODEL . $name . '.neon');
    }
    $this->mask();
    if (is_null($action))
      $action = App::$url;
    $this->name = $name;
    $this->form($name, $method, $action, $atributes);
  }

  private function form($name, $method, $action, $atributes = false) {
    echo "\n" . '<form name="' . $name . '" id="' . $name . '" method="' . $method . '" action="' . $action . '" ' . $this->atributes($atributes) . ' >' . "\n";
  }

  public function label($for, $text) {
    if (isset($text)) {
      return '<label for="' . $for . '">' . $text . '</label>';
    }
    else
      return null;
  }

  private function mask() {
    if (check_array(App::$model[$this->name])) {
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

  public function field($name, $legend = false) {
    if (array_key_exists($name, App::$model[$thi->name])) {
      if (array_key_exists(App::$model[$thi->name][$name]['type'], $this->field_type)) {
        $this->loadInput($this->field_type[App::$model[$thi->name][$name]['type']], $name);
      } else {

        $this->input($name, 'text', App::$model[$thi->name][$name]['Title'], null, 'maxlength="' . App::$model[$thi->name][$name]['max_len'] . '" size="' . $size . '"');
      }

      if ($legend) {
        echo '<span id="form_legend"' . App::$model[$name]['Legend'];
      }
    } else {
      echo "Erro, campo " . $name . "inesistente";
    }
  }

  private function loadInput($type, $name) {
    switch ($type) {
      case 'date': $this->input($name, 'text', App::$model[$name]['Title'], null, 'maxlength="10" size="10"');
        break;
      case 'text': $this->text($name, App::$model[$name]['Title']);
        break;
      case 'password': $this->input($name, 'password', App::$model[$name]['Title'], null, 'maxlength="16" size="16"');
        break;
    }
  }

  public function hidden($name, $value = '', $atributes = false) {
    $this->setField($name);
    if (array_key_exists($name, App::$data))
      $value = App::$data[$name];
    echo '<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" ' . $this->atributes($atributes) . ' /> ';
    $this->showError($name);
  }

  public function input($name, $type = 'text', $title = null, $value = null, $atributes = null) {
    $this->setField($name);
    if (isset($title))
      echo $this->label($name, $title);
    if (array_key_exists($name, App::$data))
      $value = App::$data[$name];
    echo '<input type="' . $type . '" name="' . $name . '" id="' . $name . '" value="' . $value . '" ' . $this->atributes($atributes) . ' />';
    $this->showError($name);
  }

  public function file($name, $title = '', $value = '', $atributes = false) {
    $this->setField($name);
    if (isset($title))
      echo $this->label($name, $title);
    if (array_key_exists($name, App::$data))
      $value = App::$data[$name];
    echo '<input type="file" name="' . $name . '" id="' . $name . '" value="' . $value . '" ' . $this->atributes($atributes) . ' />';
    $this->showError($name);
  }

  public function text_area($name, $title = '', $value = '', $atributes = null) {
    $this->setField($name);
    if (isset($title))
      echo $this->label($name, $title);
    if (array_key_exists($name, App::$data))
      $value = App::$data[$name];
    echo '<textarea name="' . $name . '" id="' . $name . '" ' . $atributes . '>' . $value . '</textarea>';
    $this->showError($name);
  }

  public function submit($value) {
    echo '<input type="submit" name="submit" id="submit" value="' . $value . '">';
  }

// Funções para lista...

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

  public function checkbox($name, array $value) {
    $this->setField($name);
    $field = null;
    $br = "&nbsp;";
    if (count($value) > 3)
      $br = '<br />';
    foreach ($value as $v) {
      $field .= '<input type="checkbox" name="' . $name . '" id="' . $name . '" value="' . $v[0] . '" ' . $this->atributes($atributes) . ' >' . $this->label($name, $v[1]) . $br;
    }
    echo $field;
    echo $this->showError($name);
  }

  public function radio($name, $value) {
    $this->setField($name);
    $field = null;
    $br = "&nbsp;";
    if (count($value) > 3)
      $br = '<br />';
    foreach ($value as $v) {
      $field .= '<input type="radio" name="' . $name . '" id="' . $name . '" value="' . $v[0] . '" >' . $this->label($name, $v[1]) . $br;
    }
    echo $field;
    echo $this->showError($name);
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
  public function captcha($title) {
    $this->setField('captcha');
    include(LIBS . 'componnents/captcha.php');
    $capt = new Captcha();
    echo '<label for="captcha">' . $title . '</label>';
    echo $capt->gif;
    echo '<input type="text" name="captcha" id="captcha" maxlength="4" size="10" />';
    $this->showError('captcha');
  }

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
    if (array_key_exists($name, Validate::$errorList)) {
      echo '<span class="errorform">' . Validate::$errorList[$name] . '</span>';
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