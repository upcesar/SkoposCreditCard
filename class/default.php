<?php
class Ometz_Default
{
	public $model;
	public $database;
	public $validations;

	
	/**
	 * Get log file location in the server 
	 */
	private function get_url_log(){
		$url_log = str_replace('httpdocs', 'logs', $_SERVER['DOCUMENT_ROOT']);
		
		//Create or map to YEAR folder
		if(!is_dir($url_log)){
			mkdir($url_log);
		}
			
		//Check year folder, if it doesn't exist, create it
		if(!is_dir($url_log.strval(date('Y')) )){
			mkdir($url_log.strval(date('Y')));
		}
		
		//Check month folder, if it doesn't exist, create it
		if(!is_dir($url_log.strval(date('Y/m')) )){
			mkdir($url_log.strval(date('Y/m')));
		}

		//Check day folder, if it doesn't exist, create it
		if(!is_dir($url_log.strval(date('Y/m/d')) )){
			mkdir($url_log.strval(date('Y/m/d')));
		}
		
		return ($url_log.strval(date('Y/m/d')).'/log_cartao_'.strval(date('Ymd')).'.txt');
		
	}
	
	public function Ometz_Default() {

		$this->model=new Request();
		$this->database = new Database();
		$this->validations = new Validations();
		$this->init();
	}
	
	public function showDefaultHeader()
	{
		$header = $this->appendDefaultHeader();
		echo $header;

	}
	
	public function appendDefaultHeader(){
		
		$header= '
		<head>			
			<title>.:Pagina de Pagamento Cart&atilde;o :.</title>
			<link charset="utf-8" media="screen" type="text/css" href="'.BASE_URL.'css/payment_design.css" rel="stylesheet">
			<link charset="utf-8" media="screen" type="text/css" href="'.BASE_URL.'css/contract.css" rel="stylesheet">
			<link charset="utf-8" media="screen" type="text/css" href="'.BASE_URL.'css/jquery-ui-1.10.3.custom.css" rel="stylesheet">
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">			
			<script language="javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>
			<script language="javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
			<script language="javascript" src="'.BASE_URL.'js/autoNumeric.js"></script>
			<script language="javascript" src="'.BASE_URL.'js/PaymentManagement.js"></script>			
			<script language="javascript" src="'.BASE_URL.'js/CheckOut.js"></script>			
			<script language="javascript" src="'.BASE_URL.'js/formatting.js"></script>			
			<script language="javascript" src="'.BASE_URL.'js/contract.js"></script>
			<script language="javascript" src="'.BASE_URL.'js/jquery.PrintArea.js"></script>
		</head>
		';

		/*
		<header>
			<title>Ometz Intranet :: Relatorios Controlador&iacute;a</title>
			<link rel="icon" href="'.BASEURL.'img/favicon.ico" type="image/x-icon">
			<link href="'.BASEURL.'img/favicon.ico" rel="shortcut icon" />
			<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
			<script language="javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>
			<script language="javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
			<script language="javascript" src="'.BASEURL.'js/default.js"></script>
			<script language="javascript" src="'.BASEURL.'js/jcalendar.js"></script>
			<script language="javascript" src="'.BASEURL.'js/jfilters.js"></script>
			<script language="javascript" src="'.BASEURL.'js/jquery.cookie.js"></script>
			<link charset="utf-8" media="screen" type="text/css" href="'.BASEURL.'css/jcalendar.css" rel="stylesheet">
			<link charset="utf-8" media="screen" type="text/css" href="'.BASEURL.'css/jfilters.css" rel="stylesheet">
			<link charset="utf-8" media="screen" type="text/css" href="'.BASEURL.'css/jquery-ui-1.10.3.custom.css" rel="stylesheet">
			<link charset="utf-8" media="screen" type="text/css" href="'.BASEURL.'css/box-table.css" rel="stylesheet">			
		</header>
		';
		*/
		
		return $header;
		
	}
	
	public function makeComboWeek(){
		$response = $this->model->get("HWS/season?_action=indexAction&count=9999999&offset=0");
		$html="<p><label>Selecione a Temporada: </label><select name='season' onChange=getWeeksBySeason(this.value)>";
		$html.="<option value=''>Selecione</option>";
		foreach ($response->object->season as $season) {
			$html.="<option value='".$season->PK_SEASON."'>".$season->TITLE."</option>";
		}
		$html.="</select></p>";
		$html.="<p><label>Selecione a Semana: </label><select name='comboWeek' id='comboWeek' ><option>Selecione</option></select></p>";
		return $html;
	}
	
	//	Calculate Cut Off Date
	public function getCutOffDate($dateFormat = "", $dateValue = ""){		
		
		if($dateValue != ""){
			$dateValue = str_replace("/","-", $dateValue);
			$varDate = new DateTime($dateValue);
		}
		else
			$varDate = new DateTime();
			
		$varCurDay = intval($varDate->format('j'));
		$addMonth = "1";
		$cutoffDay = "";
				

		//Calculate cutoff date depending of current date
		if($varCurDay <= intval(CUT_OFF_1))
			$cutoffDay = str_pad(CUT_OFF_1,2);
		
		if($varCurDay > intval(CUT_OFF_1) && $varCurDay <= intval(CUT_OFF_2) )
			$cutoffDay = str_pad(CUT_OFF_2,2);
			
		if($varCurDay > intval(CUT_OFF_2) && $varCurDay <= intval(CUT_OFF_3) )
			$cutoffDay = str_pad(CUT_OFF_3, 2);
		
		if($varCurDay > intval(CUT_OFF_3)){
			$cutoffDay = str_pad(CUT_OFF_1, 2);
			$addMonth = "2";
		}
		
		$interval = new DateInterval('P'.$addMonth.'M');		
		$varDate->add($interval);
		
		$varDate->setDate($varDate->format('Y'), $varDate->format('m'), $cutoffDay);
				
		
		if($dateFormat != "")
			return $varDate->format($dateFormat);
		else
			return $varDate->format('Ym').$cutoffDay;
	}

	/**
	 * Write log to a TXT file locate in /log folder for further diagnostics.
	 */
	public function write_log($module, $description, $type){
		$url = $this->get_url_log();
		$str_content = '';
		
		if(!file_exists ( $url )){
			$str_content .= "Date_Time\t\tModule\t\t\t\tDescription\t\t\t\t\t\tType\r\n";			
			$str_content .= str_repeat("-", 3 * strlen($str_content))."\r\n";
		}
		
		
						
		if(strlen($module) > 30)
			$module = substr($module, 0, 30).'... ';
		else
			$module = $module.str_repeat(' ', 30 - strlen($module));

		if(strlen($description) > 70)
			$description = substr($description, 0, 70).'... ';
		else
			$description = $description.str_repeat(' ', 70 - strlen($description));

		
		$str_content .= strval(date('Y-m-d H:i:s')).str_repeat(' ', 5).$module.$description.$type."\r\n";
		
		/*
		unlink($url);
		echo($str_content);
		exit();
		*/	
		$handle = fopen($url,'a');
		
		fwrite($handle, $str_content);
		
		fclose($handle);
		 
	}
	
	public function init(){}
}
