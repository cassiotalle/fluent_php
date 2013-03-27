<?php

/*
 * Lib de upload de arquivo
 */

class UploadLib {

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
   * Variável que define se o arquivo será excluído ao final do carregamento
   * da página por se trar de um arquivo temporário.
   * @var boolean
   */
  private $temp;

  /**
   * Carrega o dados da array() $_FILES.
   * @var array
   */
  public $file;

  /**
   * Pega os erros socorridos com o envio dos arquivos;
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
  public function upFile($dir, $file = 'FILES', $temp = 0) {
    $this->uploadDir = $dir;
    if ($file == 'FILES') {
      $this->file = $_FILES;
    } else {
      $this->file = $file;
    }
    $list = array_keys($this->file);
    foreach ($list as $key) {
      if ($this->file[$key]['error'] == 0) {
        $this->file[$key]['upload_in'] = WEBROOT . $dir . $this->file[$key]['name'];
        if (!move_uploaded_file($this->file[$key]['tmp_name'], $this->file[$key]['upload_in'])) {
          $this->errors = array('file', $this->file[$key]['name'], 'error', '5');
        }
      } else {
        $this->errors = array('file', $this->file[$key]['name'], 'error', $this->file[$key]['error']);
      }
    }

    if ($this->errors) {
      return false;
    } else {
      $this->setMsgErrors();
      return true;
    }
  }

  /**
   * Carrega as mensagens de erro na variável $this->msgErrors, caso exista
   * algum.
   */
  function setMsgErrors() {
    $this->msgErrors['1'] = 'O arquivo no upload é maior do que o limite definido';
    $this->msgErrors['2'] = 'O arquivo ultrapassa o limite de tamanho em MAX_FILE_SIZE que foi especificado no formulário HTML.';
    $this->msgErrors['3'] = 'o upload do arquivo foi feito parcialmente.';
    $this->msgErrors['4'] = 'Não foi feito o upload do arquivo.';
    $this->msgErrors['5'] = 'Erro ao final do processo.';
  }

  /**
   * Excui o arquivo temporário;
   */
  function __destruct() {
    if ($this->temp) {
      unlink($this->uploadFile);
    }
  }

}

?>
