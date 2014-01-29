<?php

class paymentDetailsERP extends Ometz_Default{
		
	public function init(){
		
	}


	public function addPaymentREST($objPayment){
		echo($this->status);
		if($this->status == "True"){
			echo("Executing...<br>");
			$hist = "COD AUTORIZACAO: ".$objPayment->authCode."; TRASACAO: ".$this->transCode.";";
			$data = array(
					'sZ0_CODORCA' 	=> $objPayment->docNumber, 
					'nZ0_VALOR' 	=> $objPayment->ammount, 
					'sZ0_HIST'		=> $hist, 
					'sZ0_PARCELA' 	=> $objPayment->feeNumbers		
					);

			echo("parameters...<br>");

			print_r($data);

			$ch = curl_init();				
			curl_setopt($ch, CURLOPT_URL, ERP_URL_WS);		
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //POST DATA);
			
			
			$result = curl_exec($ch);
			
			if ($result === false) {
				echo("<br>");
				die(curl_error($ch));
			}

			print_r($result);
			curl_close($ch);		
	
		}
	}
	
	public function addPaymentSOAP($objPayment){
		
	}
}

?>