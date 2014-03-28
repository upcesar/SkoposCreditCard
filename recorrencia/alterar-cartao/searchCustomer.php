
<?php
require_once '../../conf.php';

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
	
	private function countRecord($txtCustNum, $txtBrandNum, $cboFlag)
	{
		$sql_count=" SELECT COUNT(1) CUENTA FROM ";
		$sql_count.= "(".$this->getOriginalQuery($txtCustNum, $txtBrandNum, $cboFlag).") AS Z1 ";

		$response = $this->database->fetchAll($sql_count);
		if($response)
		{
			//echo ($response[0]["CUENTA"]);
			return floatval($response[0]["CUENTA"]);
		}
		else
			return -1;
	}


	private function getQueryWithRecNum($txtCustNum, $txtBrandNum, $cboFlag)
	{
		$sql2=" SELECT ROW_NUMBER() OVER (ORDER BY COD_CLIENTE) AS NUM_REC, Z1.* FROM ";
		$sql2.= "(".$this->getOriginalQuery($txtCustNum, $txtBrandNum, $cboFlag).") AS Z1";
		return($sql2);
	}


	// Set SQL Query here
	private function getOriginalQuery($txtCustNum = "", $txtBrandNum = "", $cboFlag = "")
	{
		$sql3="
			SELECT 
					MT3.MT3_CODFIL,					
					UPPER(MT3.MT3_FIL) AS MT3_FIL,
					SA1.A1_COD COD_CLIENTE,
					SA1.A1_LOJA LOJA,
					CASE WHEN
						SA1.A1_NUMRA !='' THEN SA1.A1_NUMRA
						ELSE 'N/A'
					END	A1_NUMRA,
					SA1.A1_NOME AS CLIENTE,        
				       CASE 
					   WHEN SX5_NAC.X5_DESCRI IS NOT NULL THEN UPPER(SX5_NAC.X5_DESCRI)
                                           ELSE 'OUTRO' 
                                       END AS NACIONALIDADE,
                                       CASE
						WHEN JA2.JA2_PROFIS IS NOT NULL AND JA2.JA2_PROFIS != '' THEN UPPER(JA2.JA2_PROFIS)
						ELSE 'NAO INFORMADO'
					END AS PROFISSAO,
					CASE 
					         WHEN SX5_EC.X5_DESCRI IS NOT NULL THEN UPPER(SX5_EC.X5_DESCRI)
                                                 ELSE 'OUTRO' 
                                        END AS ESTADO_CIVIL,
                                       SA1.A1_RG RG,
                                       Case 
							When SA1.A1_PESSOA  = 'F' Then 'CPF'
							Else    'CNPJ'
					End TIPODOC,
					SA1.A1_CGC NUM_DOC,
					CASE
						WHEN SA1.A1_END IS NOT NULL THEN UPPER(SA1.A1_END)
						ELSE 'NAO INFORMADO'
					END AS ENDERECO,
					UPPER(SA1.A1_BAIRRO) AS BAIRRO,
					UPPER(SA1.A1_MUN) AS CIDADE,
					UPPER(SA1.A1_EST) AS ESTADO,
					SA1.A1_EMAIL AS E_MAIL					   
        			FROM 
                        DB2.SA1500 AS SA1                                
                    LEFT JOIN DB2.JA2500 AS JA2 ON
                            SA1.A1_COD = JA2.JA2_CLIENT  AND
                            SA1.A1_LOJA = JA2.JA2_LOJA
                                

			        INNER JOIN DB2.MT3500 AS MT3 ON 
						MT3.MT3_CODEMP = SA1.A1_CEMPANT AND
						MT3.MT3_CODFIL = SA1.A1_MSFIL
						
				LEFT JOIN DB2.SX5500 AS SX5_NAC ON
                                                SX5_NAC.X5_CHAVE = JA2.JA2_NACION AND SX5_NAC.X5_TABELA = '34'
			
				LEFT JOIN DB2.SX5500 AS SX5_EC ON
                                                SX5_EC.X5_CHAVE = JA2.JA2_ECIVIL AND SX5_EC.X5_TABELA = '33'
		";
		
		$bWhere = false;
		
		if($txtCustNum !=""){
			$sql3.="WHERE SA1.A1_COD = '".$txtCustNum."'";
			$bWhere = true;
		}
		
		if($txtBrandNum !=""){
			if($bWhere)
				$sql3.=" AND SA1.A1_LOJA = '".$txtBrandNum."'";
			else{
				$sql3.="WHERE SA1.A1_LOJA = '".$txtBrandNum."'";
				$bWhere = true;
			}
		}
			
		if($cboFlag !=""){
			if($bWhere)
				$sql3.=" AND SUBSTR(MT3.MT3_FIL,1,". strval(strlen($cboFlag)) .") = '".$cboFlag."'";
			else{
				$sql3.="WHERE SUBSTR(MT3.MT3_FIL,1,". strval(strlen($cboFlag)) .") = '".$cboFlag."'";
				$bWhere = true;
			}
		}
			
					
		
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
		if(isset($_POST['txtCustNum']))
			$txtCustNum = $_POST["txtCustNum"];
		else
			$txtCustNum = "000054"; 

		if(isset($_POST['txtNumBrand']))
			$txtBrandNum = $_POST["txtNumBrand"];
		else
			$txtBrandNum = "01"; 
		
		if(isset($_POST['cboFlag']))
			$cboFlag = $_POST["cboFlag"];
		else
			$cboFlag = ""; 
		
		
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

		$reccount = $this->countRecord($txtCustNum, $txtBrandNum, $cboFlag);
		$pagecount = ceil($reccount / $pagesize);

		$result = "";

		if ($reccount >= 0)
		{	
			$sql="
			SELECT
					MT3_CODFIL,
					MT3_FIL,
					COD_CLIENTE,
					LOJA,
					A1_NUMRA,
					CLIENTE,        
					NACIONALIDADE,
					PROFISSAO,
					ESTADO_CIVIL,
					RG,
					TIPODOC,        
					NUM_DOC,
					ENDERECO, 
					BAIRRO,
					CIDADE,
					ESTADO,
					E_MAIL
			FROM
			(";
			$sql.= $this->getQueryWithRecNum($txtCustNum, $txtBrandNum, $cboFlag);
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
