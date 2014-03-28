
<?php
require_once '../conf.php';

class ShowCustomerContrato extends Ometz_Default
{
	private $export="";

	public function init()
	{
		$charset="";		
		// Change to P08 database
		//$this->database->connectToP10();
				
		if($this->validateSOAP())
			$this->makeContent($charset);
		else
			$this->makeContentError();
		
	}


	private function doExportXLS(){
			/** Error reporting */
			/*
			error_reporting(E_ALL);
			ini_set('display_errors', TRUE);
			ini_set('display_startup_errors', TRUE);
			date_default_timezone_set('Europe/London');


			if (PHP_SAPI == 'cli')
				die('This example should only be run from a Web Browser');

			*/

			/** Include PHPExcel */



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

			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow (0,1, $this->export);

			/*
			// Add some data
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('F1', 'Hello')
						->setCellValue('B2', 'world!')
						->setCellValue('C1', 'Hello')
						->setCellValue('D2', 'world!');




			// Miscellaneous glyphs, UTF-8
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A4', 'Miscellaneous glyphs')
						->setCellValue('A5', '�����������������');

			// Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('Simple');

			*/
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);


			// Redirect output to a client�s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="01simple.xlsx"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');
			exit;
		}

	
	private function validateSOAP(){		
		return 	(
					VALIDATE_WS_ONSTART ? 
					$this->validations->validateSOAP() : 
					true
				);
	}
	
	private function countRecord($txtProdCode)
	{
		$sql_count=" SELECT COUNT(1) CUENTA FROM ";
		$sql_count.= "(".$this->getOriginalQuery($txtProdCode).") AS Z1 ";

		$response = $this->database->fetchAll($sql_count);
		if($response)
		{
			//echo ($response[0]["CUENTA"]);
			return floatval($response[0]["CUENTA"]);
		}
		else
			return -1;
	}


	private function getQueryWithRecNum($txtProdCode)
	{
		$sql2=" SELECT ROW_NUMBER() OVER (ORDER BY B1_COD) AS NUM_REC, Z1.* FROM ";
		$sql2.= "(".$this->getOriginalQuery($txtProdCode).") AS Z1";
		return($sql2);
	}


	// Set SQL Query here
	private function getOriginalQuery($txtProdCode = "")
	{
		$sql3="
			SELECT 
  					SB1.B1_COD, SB1.B1_DESC, SB1.B1_PRV1
				FROM DB2.SB1500 AS SB1
				WHERE SB1.D_E_L_E_T_ = ''
		";
		
		if($txtProdCode !="")
			$sql3.="AND SB1.B1_COD = '".$txtProdCode."'";
		
		$sql3 .="		ORDER BY SB1.B1_COD";
		

		return($sql3);

	}

	public function makeContentError(){
		$result = "";
		$result .= $this->validations->_getErrorMessageWS();

		$arrpage = array(
				"reccount"  	=> 0,
				"pagecount"		=> 0,
				"queryresult"	=> $result
				);

		echo json_encode($arrpage);		
	}


	public function makeContent($charset="")
	{
		
		$format = new formatterContent();
		
		$title = 'Busca de Cliente';

		// Init Filters
		if(isset($_POST['cboCourse']))
			$txtProdCode = $_POST["cboCourse"];
		else
			$txtProdCode = "101.00000000352"; 

		
		//Init pager
		if (isset($_GET["numpage"]))
			$numpage = $_GET["numpage"];
		else
			$numpage = "1";

		if($this->export=="xls")
			$pagesize = "100000";

		elseif($this->export=="txt")
			$pagesize = "100";
		else
			$pagesize = "100";

		$minRec = ((intval($numpage) - 1) * intval($pagesize)) + 1;
		$maxRec = intval($numpage) * intval($pagesize);

		$reccount = $this->countRecord($txtProdCode);
		$pagecount = ceil($reccount / $pagesize);

		$result = "";

		if ($reccount >= 0)
		{	
			$sql="
			SELECT * FROM
			(";
			$sql.= $this->getQueryWithRecNum($txtProdCode);
			$sql.= ") AS R ";
			
				
			$sql.= "WHERE R.NUM_REC BETWEEN <minrec> AND <maxrec>";
			$sql = str_replace('<minrec>',strval($minRec),$sql);
			$sql = str_replace('<maxrec>',strval($maxRec),$sql);
			
			/*
			echo($sql);
			exit();
			*/
			$response = $this->database->fetchAll($sql);
			
			if ($response)
			{
				//There's no result for the query.
				if ($reccount > 0){ 				
					//$result = $response; 
					$result = $this->database->decodeResponseUTF8($response);
				}
				else{					
					$result = "Cliente encontrado";
				}				
			}
			else
			{
				$result = ""; //$this->appendDefaultHeader();
				$result.= $this->database->getError();
				if($result=="")
					$result = "Cliente n&atilde;o encontrado";
			}

			$arrpage = array(
				"reccount"  	=> $reccount,
				"pagecount" 	=> $pagecount,
				"queryresult"	=> $result
				);

			//print_r($arrpage); exit();
			
			echo json_encode($arrpage);
		}
		else //There's error connecting to the database.
		{
			$result = ""; //$this->setDefaultHeader();
			$result.= $this->database->getError();

			$arrpage = array(
					"reccount"  	=> $reccount,
					"pagecount"		=> $pagecount,
					"queryresult"	=> $result
					);
	
				echo json_encode($arrpage);											
		}
	}
	
}
new ShowCustomerContrato();
