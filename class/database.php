<?php

class Database
{
	private $database = 'T9P5XC10';
	private $user = 'USR_GWI';
	private $password = 'Gw1Db2@c355*';
	private $hostname = '187.94.60.35';
	private $port = '50002';


	private $conn;
	private $err = "";
	
	private function setupConfig() {
		date_default_timezone_set("America/Sao_Paulo");
		ini_set('max_execution_time', 600);	
	}


	private function connectDataBase(){
		$this->setupConfig();
		if (isset($conn) && $conn != false) db2_close($conn);
		$this->err = "";
		$conn_string = "DRIVER={IBM DB2 ODBC DRIVER};DATABASE={$this->database};HOSTNAME={$this->hostname};PORT={$this->port};PROTOCOL=TCPIP;UID={$this->user};PWD={$this->password};";
		$this->conn = db2_connect($conn_string, '', '');
		//$this->conn = db2_connect($database, '', '');
		if(!$this->conn){
			$this->err = "<div class='smallerrormsg'>Erro na conexão com Banco do dados</div>";
		}
		//db2_close($conn);
		
	}
	
	public function __construct(){
		
		$this->connectToP10();		
	}

	public function connectToP10(){
		if(AMBIENTE_DB == 'PRO')
			$this->connectToP10_PRO();
		else
			$this->connectToP10_HOM();
	}
	
	public function connectToP10_PRO(){
		$this->user = 'USR_GWI';
		$this->password = 'Gw1Db2@c355*';
		$this->connectToHostP10();
	}


	public function connectToP10_HOM(){
		$this->database = 'T9P5XCT1';
		$this->user = 'USR_GWIH';
		$this->password = 'Gw1Db2@c355*h';
		$this->hostname = '187.94.62.41';
		$this->port = '50002';
		$this->connectDataBase();
	}

	public function connectToHostP10(){
		$this->database = 'T9P5XC10';
		$this->hostname = '187.94.60.35';
		$this->port = '50002';
		$this->connectDataBase();
	}
	
	public function connectToP10_WBI(){
		$this->user = 'WBI';
		$this->password = '@c3550b1t0tv5#';
		$this->connectToHostP10();
	}
	
	
	public function connectToP08(){
		if(AMBIENTE_DB == 'PRO')
			$this->connectToP08_PRO();
		else
			$this->connectToP08_HOM();
	}
	
	public function connectToP08_PROD(){
		$this->database = 'T9P5XC';
		$this->user = 'WBI';
		$this->password = '@c3550b1t0tv5#';
		$this->hostname = '187.94.60.35';
		$this->port = '50002';	
		$this->connectDataBase();
	}
	
	public function connectToP08_HOM(){
		$this->database = 'T9P5XC_H';
		$this->user = 'wbi2';
		$this->password = 't9p5xch0m0';
		$this->hostname = '187.94.62.41';
		$this->port = '50002';	
		$this->connectDataBase();
	}

	public function getError(){
		return $this->err;
	}
	
	public function fetchAll($sql){
		$i=0;
		
		if(!$this->conn)
			return false;
		
		$result = db2_exec($this->conn, $sql);
		
		if(!$result){			
			
			$sqlhtml = htmlentities($sql);
			$sqlhtml = htmlspecialchars($sqlhtml);
			
			$this->err = "
				<div class = 'smallerrormsg'>
				<p>Erro na Query</p>";
			
			$this->err.= "
					<p> ".$sql."</p>
				  	<p>database: ".$this->database."</p>
				  	<p>user: ".$this->user."</p>
				  	<p>password: ".$this->password."</p>
				  	<p>hostname: ".$this->hostname."</p>
				  	<p>port: ".$this->port."</p>			
			";
			
			$this->err.= "</div>";
			
			return false;
			
		}
				
		$merge=array();
		while($row = db2_fetch_assoc($result)){
		
			$i++;
			$merge[]=$row;
		}		
		return $merge;

	}
	
	public function decodeResponseUTF8($response){		
		foreach($response as &$tRow){
			$tCols = array_keys($tRow);
			foreach($tRow as &$tValue){
				if(gettype($tValue)=="string"){
					$tValue = utf8_encode(trim($tValue));
				}
				
			}
		}
		return ($response);
	}
	
	public function fetchHtmlExcel($sql, $pTitle = null) {
		//Copied from ArrayTable. It's temporal until we make a better solution for
		//Excel exportation.
		$_tTable = "<table border='1' id='box-table-a'>";
		$_cTable = "</table>";
		$_tHeader = "<thead>";
		$_cTHeader = "</thead>";
		$_tHeaderTitles = array();
		$format = new formatterContent();		
		
		$rs = db2_exec($this->conn, $sql);
		$content="";		
		
		

		
		while($tRows = db2_fetch_assoc($rs)){		
						
			$tHeader = array_keys($tRows);			
			//print_r($tHeader); return;
						
			if(count($_tHeaderTitles) < $tHeader)
				$_tHeaderTitles=$tHeader;

			$header = array();
			$content .= "<tr>";

			$y = 0;
			foreach ( $tRows as $tRow ) {
				$tRow = (empty ( $tRow ) || $tRow === " ") ? "&nbsp;" : $tRow;												
				$tRow = $format->formatField($rs, $tHeader[$y], $tRow);
				$content .= "<td class=''>$tRow</td>"; $y++;				
			}
			$content .= "</tr>";						
		}		
						
		
		if ($pTitle){						
			$header[] = "<tr scope='col'><th colspan=".count($_tHeaderTitles).">".$pTitle."</th></tr>";						
			$filename= strtolower(str_replace(" ","_",$pTitle));			
		}
		else
			$filename='download';
		
		
		
		$header[] = "<tr scope='col'>";

		$classes = array();
		foreach ($tHeader as $tRow){
			$header[] = "<th>$tRow</th>";
		}
		$header[] = "</tr>";
				
		$rethtml = $_tTable.$_tHeader.implode("", $header).$_cTHeader.$content;
		
		$downloadTokenValue = $_POST["downloadExcelToken"];		
		setcookie("fileDownloadToken", $downloadTokenValue, time()+3600);

		header("Content-type: application/msexcel;");
		header("Content-type: application/force-download");
		header("Content-Disposition: attachment; filename=".$filename.".xls");
		header("Pragma: no-cache");

		echo $rethtml;
	}
		
	public function fetchXmlExcel_Old($sql, $pTitle=""){
		/** Error reporting */
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('Europe/London');

		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');

		/** Include PHPExcel */
		//require_once '../Classes/PHPExcel.php';

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
									 ->setLastModifiedBy("Maarten Balliauw")
									 ->setTitle("Office 2007 XLSX Test Document")
									 ->setSubject("Office 2007 XLSX Test Document")
									 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test result file");


		// Add some data
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'Hello')
					->setCellValue('B2', 'world!')
					->setCellValue('C1', 'Hello')
					->setCellValue('D2', 'world!');

		// Miscellaneous glyphs, UTF-8
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A4', 'Miscellaneous glyphs')
					->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Simple');


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);


		// Redirect output to a client’s web browser (Excel2007)
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="01simple.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;	
	}

}
