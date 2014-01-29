<?php
class Request
{
	private $postfields=array();
	
	public function Request(){
		$this->postfields = array('token'=>APPLICATION_TOKEN);
	}
	public function setPostfields($postfields) {
		$this->postfields = $postfields;
		return $this;
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
		
		// if(empty($response) || $response == "")
		// 	exit("Nenhum resultado encontrado");	
			
		return $response;
	}
		public function getJava($url)
		{
			
			// echo JBOSS_SERVICE_ADDRESS.$url;exit;
			$ch = curl_init(JBOSS_SERVICE_ADDRESS.$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Accept: application/xml"));

			// curl_setopt($curl, CURLOPT_HEADER, true);
			// curl_setopt($curl, CURLOPT_FILETIME, true);
			// curl_setopt($curl, CURLOPT_NOBODY, true);

			// curl_setopt($ch, CURLOPT_HEADER, 0);
	//		curl_setopt($ch, CURLOPT_HTTPHEADER,array('Host:'.MODULE_ADDRESS)); 
			// curl_setopt($ch, CURLOPT_POST, false); 
			// curl_setopt($curl_handle, CURLOPT_HTTPGET, TRUE);
			// curl_setopt($ch, CURLOPT_POSTFIELDS, null);
			// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
// echo $response;exit;
			@$response = simplexml_load_string($response);
			return $response;
		}
}
