<?php
class HomeController extends Controller{
  //public $db = false;
  public $libs = array('Form');
  public $js = array('jquery');
  public $css = array();
  
  function index(){
    $this->load_lib("paginate");
    set('ola',"Olá mundo via variável\n");
    //$this->data->entrada->fields('nome','data')->where('data > 20070101')->exec();
    //$this->data->exec('select * from ? where id > 10',$talela);
    //$this->data->entrada->selectById('10');
    $dados = array('usuario'=>array(
        'nome'=>'Cássio Talle e Silva', 
        'email'=>'cassiolandia@gmailcom', 
        'datanascimento'=>'28/02/2013', 
        'site'=>'www.bookserie.com.br'));
    data('usuario')->create($dados)->exec();
  }
  
  function access(){
    $this->data->entrada->fields('nome','sobrenome')->exec();
    
  }
}
?>
