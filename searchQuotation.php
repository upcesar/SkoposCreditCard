
<?php
require_once 'conf.php';

class ShowCompetenceByEditor extends Ometz_Default
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
	
	private function countRecord($txtNumQuotation)
	{
		$sql_count=" SELECT COUNT(1) CUENTA FROM ";
		$sql_count.= "(".$this->getOriginalQuery($txtNumQuotation).") AS Z1 ";

		$response = $this->database->fetchAll($sql_count);
		if($response)
		{
			//echo ($response[0]["CUENTA"]);
			return floatval($response[0]["CUENTA"]);
		}
		else
			return -1;
	}


	private function getQueryWithRecNum($txtNumQuotation)
	{
		$sql2=" SELECT ROW_NUMBER() OVER (ORDER BY ORCAMENTO) AS NUM_REC, Z1.* FROM ";
		$sql2.= "(".$this->getOriginalQuery($txtNumQuotation).") AS Z1";
		return($sql2);
	}


	// Set SQL Query here
	private function getOriginalQuery($txtNumQuotation)
	{
		$sql3="
			SELECT 
					CJ_NUM ORCAMENTO,
					MT3.MT3_CODFIL,					
					UPPER(MT3.MT3_FIL) AS MT3_FIL,
					SA1.A1_COD COD_CLIENTE,
					SA1.A1_NUMRA,
					SA1.A1_NOME AS CLIENTE,        
					Case 
							When SA1.A1_PESSOA  = 'F' Then 'CPF'
							Else    'CNPJ'
					End TIPODOC,
					SA1.A1_CGC NUM_DOC,
					CASE
						WHEN SA1.A1_END IS NOT NULL THEN UPPER(SA1.A1_END)
						ELSE 'NAO INFORMADO'
					END AS ENDERECO,
					SCJ.CJ_VALORD TOTAL_CARTAO, 
					SCJ.CJ_TOTPAG TOTAL_ORCAMENTO,
					(
					SELECT  
						CASE
							WHEN  SUM(SZ0.Z0_VALCRE) IS NULL THEN 0
							ELSE    SUM(SZ0.Z0_VALCRE) 
						END AS SUM_PAYMENT
					FROM DB2.SZ0500 AS SZ0
					WHERE SZ0.Z0_DONOCH IN ('4', '5') 
					AND SZ0.Z0_CODORCA = SCJ.CJ_NUM
					AND SZ0.D_E_L_E_T_ = ''
					) + SCJ.CJ_DESCONT AS TOTAL_PAGO,
					SCJ.CJ_STATUS STATUS
        			FROM 
                        DB2.SA1500 AS SA1
        			INNER JOIN  DB2.SCJ500 AS SCJ ON 
						SA1.A1_LOJA = SCJ.CJ_LOJA  AND SA1.A1_COD = SCJ.CJ_CLIENTE AND
						SA1.D_E_L_E_T_ = SCJ.D_E_L_E_T_ AND SA1.D_E_L_E_T_ = ''                        
			        INNER JOIN DB2.MT3500 AS MT3 ON 
						MT3.MT3_CODEMP = SCJ.CJ_CEMPANT AND
						MT3.MT3_CODFIL = SCJ.CJ_MSFIL
		";
		
		if($txtNumQuotation !="")
			$sql3.="WHERE SCJ.CJ_NUM = '".$txtNumQuotation."'";

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
		
		$title = 'Busca de Orcamentos';

		// Init Filters
		if(isset($_POST['txtNumQuote']))
			$txtNumQuotation = $_POST["txtNumQuote"];
		else
			$txtNumQuotation = "058742";			

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

		$reccount = $this->countRecord($txtNumQuotation);
		$pagecount = ceil($reccount / $pagesize);

		$result = "";

		if ($reccount >= 0)
		{	
			$sql="
			SELECT
					ORCAMENTO,
					MT3_CODFIL,
					MT3_FIL,
					COD_CLIENTE,
					A1_NUMRA,
					CLIENTE,        
					TIPODOC,        
					NUM_DOC,
					ENDERECO, 
					TOTAL_ORCAMENTO,
					TOTAL_CARTAO,
					TOTAL_PAGO,
					(TOTAL_CARTAO - TOTAL_PAGO) SALDO_RESTANTE,
					STATUS
			FROM
			(";
			$sql.= $this->getQueryWithRecNum($txtNumQuotation);
			$sql.= ") AS R ";
			
				
			$sql.= "WHERE R.NUM_REC BETWEEN <minrec> AND <maxrec>";
			$sql = str_replace('<minrec>',strval($minRec),$sql);
			$sql = str_replace('<maxrec>',strval($maxRec),$sql);
			
			$response = $this->database->fetchAll($sql);
			
			if ($response)
			{
				//There's no result for the query.
				if ($reccount > 0){ 				
					//$result = $response; 
					$result = $this->database->decodeResponseUTF8($response);
				}
				else{					
					$result = "Or&ccedil;amento n&atilde;o encontrado";
				}				
			}
			else
			{
				$result = ""; //$this->appendDefaultHeader();
				$result.= $this->database->getError();
				if($result=="")
					$result = "Or&ccedil;amento n&atilde;o encontrado";
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
new ShowCompetenceByEditor();
