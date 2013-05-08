<?php

/*
 * Lib de upload de arquivo
 */

class Upload {

  /**
   * Caminho do diretório onde o arquivo será salvo
   * @var string
   */
  public $uploadDir;

  /**
   * Caminho completo e nome do arquivo que será salvo a partir do upload.
   * @var string
   */
  public $uploadFile;

  /**
   * Carrega o dados da array() $_FILES.
   * @var array
   */
  public $file;

  /**
   * Pega os erros ocorridos com o envio dos arquivos;
   * @var array;
   */
  public $errors = false;

  /**
   *
   */
  public $msgErrors;

  /**
   * Função que faz o upload do arquivo
   * @param string $dir : Caminho onde os arquivos de uoload seram salvos
   * @param array $file : Array proveniente da array Global $_FILE[];
   * @param boolean $temp : Indica se se trata de um arquivo temporário, que
   * será excluído ao final do carregamento do arquivo.
   * @return boolean
   */
  public function upFile($name, $table, $list) {
    foreach ($list as $key) {
      $ext = strtolower(end(explode('.', ($_FILES[$table]['name'][$key]))));
      $this->file[$key] = App::$model[$table][$key]['dir'] . $name . '.' . $ext;
      if (check_array(App::$model[$table][$key]['type'] == 'file')) {
        if (!move_uploaded_file($_FILES[$table]['tmp_name'][$key], App::$model[$table][$key]['dir'] . $name . '.' . $ext)) {
          Validate::$error_list[$key] = $msg[5];
        }
      } else {
        if (check_array(App::$model[$table][$key]['thumb'])) {
          $k = array_keys(App::$model[$table][$key]['thumb']);
          $img = App::setIstance('Img', 'Lib');
          foreach ($k as $j) {
            $img->openImage($_FILES[$table]['tmp_name'][$key], $ext);
            $img->resizeImage(App::$model[$table][$key]['thumb'][$j][0], App::$model[$table][$key]['thumb'][$j][1]);
            $img->saveImage(App::$model[$table][$key]['dir'] . $name . $j . $j . '.' . $ext);
          }
        } elseif (check_array(App::$model[$table][$key]['resize'])) {
          $img = App::setIstance('Img', 'Lib');
          $img->openImage($_FILES[$table]['tmp_name'][$key], $ext);
          $img->resizeImage(App::$model[$table][$key]['resize'][0], App::$model[$table][$key]['resize'][1]);
          $img->saveImage(App::$model[$table][$key]['dir'] . $name . $j . '.' . $ext);
          pr(App::$model[$table][$key]['dir'] . $name . $j . '.' . $ext);
          pr($_FILES[$table]['tmp_name'][$key]);
          exit();
        } else {
          if (!move_uploaded_file($_FILES[$table]['tmp_name'][$key], App::$model[$table][$key]['dir'] . $name . '.' . $ext)) {
            Validate::$error_list[$key] = $msg[5];
          }
          exit();
        }
      }
    }
    unset($_FILES[$table]);
  }

  public function process($table, $list) {
    if (isset($_FILES[$table])) {
      $msg['1'] = 'O arquivo no upload é maior do que o limite definido';
      $msg['2'] = 'O arquivo ultrapassa o limite de tamanho em  que foi especificado no formulário HTML.';
      $msg['3'] = 'o upload do arquivo foi feito parcialmente.';
      $msg['4'] = 'Não foi feito o upload do arquivo.';
      $msg['5'] = 'Erro ao fazer o upload do arquivo.';
      foreach ($list as $key) {
        if ($_FILES[$table]['error'][$key] != 0) {
          if (App::$model[$table][$k]['not_null'] == true && $_FILES[$table]['error'][$key] == 4) {
            Validate::$error_list[$key] = 'Campo obrigatório.';
          } elseif ($_FILES[$table]['error'][$key] != 4) {
            Validate::$error_list[$key] = $msg[$_FILES[$table]['error'][$key]];
          }else{
            
          }
          continue;
        } else {
          $this->size($key, $table);
          $this->format($key, $table);
        }
      }
    }
  }

  public function size($file, $table) {
    if ((isset(App::$model[$table][$file]['max_size'])) && ($_FILES[$table]['size'][$file] < App::$model[$table][$file]['max_size'])) {
      Validate::$error_list[$file] = "Arquivo muito grande, maior do que o permitido " . round($_FILES[$table]['size'][$file] / 1204, 2) . "MB.";
    }
  }

  public function format($file, $table) {
    if (isset(App::$model[$table][$file]['format'])) {
      $type = explode("/", $_FILES[$table]['type'][$file]);
      $type = $type[count($type) - 1];
      if (!array_search($type, App::$model[$table][$file]['format'])) {
        Validate::$error_list[$file] = "Somente estes formador de arquivo são permitidos (" . concat_array(App::$model[$table][$file]['format']) . ")";
      }
    }
  }

}

?>
