<?php	
	ini_set('default_socket_timeout', 600);
	require_once '../conf.php';
	require_once '../retorno/wsdl_detalhe_cartao.php';
	
    // CLASSE PARA BUSCAR OS TITULOS DE RECORRENCIA E ENVIAR PARA COBRANÇA
	class ListRecurrence extends Ometz_Default {
		private $QryRecorrencia = '';
	
		private function QryRecurrencePOST(){
			
			$this->QryRecorrencia  = "
				SELECT 
				  	SE1.E1_FILIAL, SE1.E1_PREFIXO, SE1.E1_TIPO, SE1.E1_NUM, SE1.E1_CODORCA,
					SE1.E1_EMISSAO, SE1.E1_VENCTO, SE1.E1_PARCELA AS PARCELA, 				
				  	SE1.E1_CLIENTE, SE1.E1_LOJA, SE1.E1_NUMRA, SE1.E1_NOMCLI, 
					SE1.E1_NRDOC,  SE1.E1_SALDO 
				FROM DB2.SE1050 AS SE1
				WHERE SE1.R_E_C_N_O_ IN (".strval($_POST['listNumRecord']).")";
										
		}
		
		private function QryAllRecurrence(){
			date_default_timezone_set("America/Sao_Paulo");	
			if (isset($_GET['DataInicio']))
				$DataInicio = strval($_GET['DataInicio']);
			else
				$DataInicio = "";
			
			if (isset($_GET['DataFinal']))
				$DataFinal = strval($_GET['DataFinal']);
			else
				$DataFinal = "20141231"; //strval(date("Ymd"));
			
			$interval_date = "";
			
			if($DataInicio != '' && $DataFinal != '')
				$interval_date = "AND E1_VENCTO BETWEEN '".$DataInicio."' AND '".$DataFinal."'";
			elseif($DataFinal != '')
				$interval_date = "AND E1_VENCTO <= '".$DataFinal."'";
			
			
			$this->QryRecorrencia  = " 				
				SELECT DISTINCT E1_FILIAL, E1_PREFIXO, SE1.E1_TIPO, E1_NUM, E1_CODORCA,
				E1_EMISSAO, SE1.E1_VENCTO,
				SE1.E1_PARCELA AS PARCELA, 				
				E1_CLIENTE, E1_LOJA, E1_NUMRA, E1_NOMCLI, 
				E1_NRDOC,  E1_SALDO
				
				FROM DB2.SE1050 SE1 
				
				LEFT JOIN DB2.VZL500 VZL ON
				    SE1.E1_FILIAL = VZL.VZL_CODFIL AND 
				    SE1.E1_PREFIXO = VZL.VZL_PREFIX AND
				    SE1.E1_NUM = VZL.VZL_NUM AND
				    SE1.E1_PARCELA = VZL.VZL_PARCEL AND
				    SE1.E1_TIPO = VZL.VZL_TIPO AND
		            VZL.D_E_L_E_T_ <> '*'
            
			 	WHERE E1_TIPO = 'CC'
				 	".$interval_date." 
				 	AND E1_SALDO > 0 
				 	AND E1_NRDOC <> '' 
				 	AND E1_CODORCA <> ''
					AND SE1.E1_DTACRED <> ''
					AND SE1.D_E_L_E_T_ <> '*'					
					AND (VZL.VZL_STATUS = '2' OR VZL.VZL_STATUS IS NULL)
					AND 
					(VZL.VZL_SEQUEN IN
						(
						SELECT MAX(VZL2.VZL_SEQUEN)
	          			FROM DB2.VZL500 AS VZL2
	          			WHERE 
	            			SE1.E1_FILIAL = VZL2.VZL_CODFIL AND 
			  			    SE1.E1_PREFIXO = VZL2.VZL_PREFIX AND
			  			    SE1.E1_NUM = VZL2.VZL_NUM AND
			  			    SE1.E1_PARCELA = VZL2.VZL_PARCEL AND
			  			    SE1.E1_TIPO = VZL2.VZL_TIPO AND            
	            			SE1.D_E_L_E_T_ = VZL2.D_E_L_E_T_
	      					AND VZL2.D_E_L_E_T_ <> '*'
	        			) OR VZL.VZL_SEQUEN IS NULL
					)
        
				
			 	ORDER BY SE1.E1_NUM, SE1.E1_FILIAL , SE1.E1_PARCELA			 	
			 ";
			 
			 
		}
		
		public function get_post_status(){
			if (isset($_POST['listNumRecord']) && $_POST['listNumRecord'] != '' && $_POST['listNumRecord'] != 'ALL')
				return true;
			
			return false;
		}
		
		public function QryRecurrence($filter){
			
			$list_record = '';
			
			if($this->get_post_status()) 
				$this->QryRecurrencePOST();
			else
				$this->QryAllRecurrence();
			
			//die($this->QryRecorrencia);
			
			
			$response = $this->database->fetchAll($this->QryRecorrencia);
			
			
			//GRAVA ARRAY COM OS VALORES PARA ENVIAR VIA POST PARA COBREBEM
			$Items = Array();
			
			for ($i = 0; $i <= count($response)-1; $i++) {
					
				$Items[$i] = Array(
					"FILIAL" => $response[$i]['E1_FILIAL'], 
					"PREFIXO" => $response[$i]['E1_PREFIXO'],
					"NUMERO" => $response[$i]['E1_NUM'],
					"ORCAMENTO" => $response[$i]['E1_CODORCA'],
					"PARCELA" => $response[$i]['PARCELA'],
					"TIPO" => $response[$i]['E1_TIPO'],
					"CLIENTE" => $response[$i]['E1_CLIENTE'],
					"LOJA" => $response[$i]['E1_LOJA'],
					"TRANSACAO" => $response[$i]['E1_NRDOC'],
					"VALORDOCUMENTO" => $response[$i]['E1_SALDO'],
					"QUANTIDADEPARCELAS" => 1);
			}
			return $Items;
		}

		public function curl_download($Url,$fields_string){		 	
									
		 	$this->write_log('enviorecorrencia', 'Començo envio dados cobrebem - '.$Url, 'INFO');	
		    // is cURL installed yet?
		    if (!function_exists('curl_init')){
		        die('Sorry cURL is not installed!');
		    }
		    
		    // OK cool - then let's create a new cURL resource handle
		    $ch = curl_init();
		 
		    // Now set some options (most are optional)
		 
		    // Set URL to download
		    curl_setopt($ch, CURLOPT_URL, $Url);
		  
		    // Include header in result? (0 = yes, 1 = no)
		    curl_setopt($ch, CURLOPT_HEADER, 0);
		 
		 	curl_setopt($ch, CURLOPT_POST, 5);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
		    
		    // Should cURL return or print out the data? (true = return, false = print)
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		 
		    // Timeout in seconds
		    curl_setopt($ch, CURLOPT_TIMEOUT, 999);
		 
		    // Download the given URL, and return output
		    $timeFirst  = strtotime(strval(date('Y-m-d H:i:s')));
		    $output = curl_exec($ch);
			$timeSecond  = strtotime(strval(date('Y-m-d H:i:s')));
			$diff_seconds = $timeSecond - $timeFirst;
		 
		    // Close the cURL resource, and free system resources
		    curl_close($ch);
		 
		    $this->write_log('enviorecorrencia', 'Finalizado envio dados cobrebem - '.$diff_seconds. ' segundos', 'INFO');
		    
		    return $output;
			
		}
				
		
		public function save_recurring_payments($returnAPVF, $rowRecorrencia){
				
			$this->write_log('enviorecorrencia', 'Inicio envio log ERP-TOTVS', 'INFO');
			
			$wsdl = new WS_DETALHE_CARTAO();
			$data = new RECORRENCIA();
						
			$data->SVZL_CODEMP = "05"; // string (hardcoded temporally)
			$data->SVZL_CODFIL = strval($rowRecorrencia['FILIAL']); // string
			$data->SVZL_PREFIX = strval($rowRecorrencia['PREFIXO']); // string
			$data->SVZL_NUM = strval($rowRecorrencia['NUMERO']); // string
			$data->SVZL_PARCEL = strval($rowRecorrencia['PARCELA']); // string
			$data->SVZL_TIPO = strval($rowRecorrencia['TIPO']); // string
			$data->SVZL_STATUS = strval($returnAPVF->TransacaoAprovada); // string
			$data->SVZL_APROVACAO = str_pad($returnAPVF->CodigoAutorizacao, 6, STR_PAD_LEFT); // string
			$data->SVZL_TRANSACAO = str_pad($returnAPVF->Transacao, 14, STR_PAD_LEFT); // string
			$data->SVZL_OBS = utf8_decode(strval($returnAPVF->ResultadoSolicitacaoAprovacao)); // string
			
			$timeFirst  = strtotime(strval(date('Y-m-d H:i:s')));
			$response = $wsdl->RECORRENCIA($data);
			$timeSecond  = strtotime(strval(date('Y-m-d H:i:s')));
			$diff_seconds = $timeSecond - $timeFirst;
				
			$soapMessage = $response->RECORRENCIARESULT;
			
			
			$this->write_log('enviorecorrencia', 'Finalizado envio dados ERP-TOTVS - '.$diff_seconds. 'segundos', 'INFO');

			/*
			echo("<br><hr>");
			print_r($rowRecorrencia);
			echo("<br><hr>");
			print_r($returnAPVF);
			echo("<br><hr>");
			print_r($soapMessage);
			echo("<br><hr>");

				
			echo 'STATUS: '.$returnAPVF->TransacaoAprovada.'<br>';
			echo 'RETORNO OPERADORA: '.$returnAPVF->ResultadoSolicitacaoAprovacao.'<br>';
			echo 'NUMERO TRANSACAO: '.$returnAPVF->Transacao.'<br>';
			echo 'DOCUMENTO: '.$returnAPVF->NumeroDocumento.'<br>';
			echo '<br><hr>';			 
			 
			 */
			
		}		
	}
	

// BUSCAR OS DADOS PARA COBRANÇA
$ObjRecorrencia = new ListRecurrence();
$ObjRecorrencia->write_log('enviorecorrencia', 'Començo Processo', 'INFO');
$filter = '';


$ReturnRecorrencia = $ObjRecorrencia->QryRecurrence($filter);
$count = count($ReturnRecorrencia);
$ObjRecorrencia->write_log('enviorecorrencia', 'Dados lidos. '.strval($count).' registros.', 'INFO');


for ($i = 0; $i < $count; $i++) {
		
	// CRIAR ARRAY PARA ENVIAR PARA COBRANÇA
	$Items = Array('NumeroDocumento' => $ReturnRecorrencia[$i]['ORCAMENTO'], 
			'ValorDocumento' => $ReturnRecorrencia[$i]['VALORDOCUMENTO'], 
			'QuantidadeParcelas' => $ReturnRecorrencia[$i]['QUANTIDADEPARCELAS'],
			'CodigoSeguranca' => 555,
			'ResponderEmUTF8' => 'S',
			'TransacaoAnterior' => $ReturnRecorrencia[$i]['TRANSACAO']);
	
	$fields_string = '';
	foreach($Items as $key=>$value) {
 		$fields_string .= $key.'='.$value.'&'; 
	}
	$fields_string = trim(substr($fields_string, 0,strlen($fields_string) - 2));
	 	

 	
 	$str_returnAPVF = $ObjRecorrencia->curl_download(RECURRING_GATEWAY, $fields_string);
	
	// var_dump($str_returnAPVF);
	
	$str_returnAPVF = utf8_encode ($str_returnAPVF);
	$returnAPVF = simplexml_load_string($str_returnAPVF);
	
	// print_r($returnAPVF);
	
	// echo("<br>");
	
	// TRATAR O RETORNO DA COBREBEM
   	// CHAMAR O WS PARA A ALTERAÇÃO DO TITULO EM QUESTÃO
   	$ObjRecorrencia->save_recurring_payments($returnAPVF, $ReturnRecorrencia[$i]);
   	
   	// echo("Registro ".$i." processado...<br>");
	
}
/*
if(!($ObjRecorrencia->get_post_status() || (isset($_POST['listNumRecord']) && $_POST['listNumRecord'] == 'ALL'))){
	echo("Pronto...");
	exit();
}
 
 */

header("Location: ".BASE_URL."recorrencia/");
?>