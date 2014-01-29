<?php

require_once('conf.php');

class ValidateWS extends Ometz_Default
{
	public function init(){
		$alive = $this->validations->validateSOAP();
		$strerr = $this->validations->_getErrorMessageWS();
		$arr = array(
				"alive"  	=>  $alive,
				"strerr" 	=>  $strerr
				);

		echo json_encode($arr);
		
	}
}
new ValidateWS();
?>