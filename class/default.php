<?php
class Ometz_Default
{
	public $model;
	public $database;
	public $validations;

	
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

	public function init(){}
}
