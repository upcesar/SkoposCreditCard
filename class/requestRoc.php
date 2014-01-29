<?php
class RequestRoc
{
	private $postfields=array();
	
	public function Request(){
		$this->postfields = array('token'=>APPLICATION_TOKEN);
	}
	public function setPostfields($postfields) {
		$this->postfields = $postfields;
	}

	public function getPostfields() {
		return $this->postfields;
	}

	public function get($url)
	{
		$ch = curl_init(SERVICE_ADDRESS.$url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
//		curl_setopt($ch, CURLOPT_HTTPHEADER,array('Host:'.MODULE_ADDRESS)); 
		curl_setopt($ch, CURLOPT_POST, true); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getPostfields());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		
		@$response = simplexml_load_string($response);
		
//		if(empty($response) || $response == "")
//			exit("Nenhum resultado encontrado");	
			
		return $response;
	}

}