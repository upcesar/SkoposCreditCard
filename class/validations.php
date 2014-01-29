<?

class Validations{

	public function _getErrorMessageWS(){
		return "Erro ao conectar com o WebService do ERP. Favor entrar em contato com o suporte técnico.";
	}
	
	public function validateSOAP($url=""){
		if($url == "")
			$url = P10_WSDL;
		
		$handle = curl_init($url);

		curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
		
		$response = curl_exec($handle);
		$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
				
		if($httpCode != 200) {
			/* You don't have a WSDL Service is down. exit the function */
			return false;
		}
		
		curl_close($handle);
		return true;
	}
	
}

?>