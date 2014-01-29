<?php

require_once '../conf.php';
require_once 'wsdl_contrato_cartao.php';

class ContractWS extends Ometz_Default {	
	
	public $orcamento;
	public $responsavel = $_POST['inputresponsavel'];
	public $nomearquivo = $_FILES['inputfile'.strval($id)]['name'];
	
	private 
	
	public function addContract(){
			$ws = new WS_CONTRATO_CARTAO();
	}
}

?>