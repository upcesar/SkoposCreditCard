
<?php
require_once 'conf.php';

class SearchQuotation extends Ometz_Default
{
	private $export="";

	public function init()
	{
		$charset="";		
		// Change to P08 database		
		
		
		if(!is_numeric($this->getNumQuote())){
			$this->database->connectToP08();
		}
		
		
		if($this->validateSOAP())
			$this->makeContent($charset);
		else
			$this->makeContentError();
		
	}

	private function get_erp($txtNumQuote = ""){
		
		$erp = "P10";

		if(isset($_GET["erp"]))			
			$erp = strval($_GET["erp"]);			
		else if (!is_numeric($txtNumQuote))
			$erp = "P08";
		
		return strtoupper($erp);
		
	}

	private function getNumQuote(){
		if(isset($_POST['txtNumQuote']))
			$txtNumQuotation = $_POST["txtNumQuote"];
		else
			$txtNumQuotation = $this->get_default_num_quote();
		
		return $txtNumQuotation;
				
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

	private function getOriginalQuery($txtNumQuotation){
		
		// Query P8 if Quotation nomber is alphanumeric 	
		if(is_numeric($this->getNumQuote())){
			$sql3= $this->getOriginalQueryP10($txtNumQuotation);
			$field_num_quote = "SCJ.CJ_NUM";
		}
		else{
			$sql3= $this->getOriginalQueryP08($txtNumQuotation);
			$field_num_quote = "SZ6.Z6_NUM";
		}
		
		if($txtNumQuotation !="")
			$sql3.="WHERE ".$field_num_quote." = '".$txtNumQuotation."'";

		return($sql3);

	}
	
	// Set SQL Query here for P10
	private function getOriginalQueryP10($txtNumQuotation){
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
					SCJ.CJ_STATUS STATUS,					
					CASE 
						WHEN SCJ.CJ_FILINC < 'A0' AND SCJ.CJ_CODEMP = '30' THEN 0
						ELSE 1
					END ENABLE_RECURRING
        			FROM 
                        DB2.SA1500 AS SA1
        			INNER JOIN  DB2.SCJ500 AS SCJ ON 
						SA1.A1_LOJA = SCJ.CJ_LOJA  AND SA1.A1_COD = SCJ.CJ_CLIENTE AND
						SA1.D_E_L_E_T_ = SCJ.D_E_L_E_T_ AND SA1.D_E_L_E_T_ = ''                        
			        INNER JOIN DB2.MT3500 AS MT3 ON 
						MT3.MT3_CODEMP = SCJ.CJ_CEMPANT AND
						MT3.MT3_CODFIL = SCJ.CJ_MSFIL
		";
		
		return $sql3;
	}
	
	
	private function getOriginalQueryP08($txtNumQuotation)
	{
		$sql3="
			SELECT 
					Z6_NUM ORCAMENTO,
					
					CASE 
			            WHEN NOT MT3.MT3_CODFIL IS NULL THEN MT3.MT3_CODFIL 
			            ELSE 'N/A'
			        END AS MT3_CODFIL,
					
					CASE 
			            WHEN NOT MT3.MT3_FIL IS NULL THEN UPPER(MT3.MT3_FIL) 
			            ELSE 'NAO ATRIBUIDO'
			        END AS MT3_FIL,					
					
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
					SZ6.Z6_VALORD TOTAL_CARTAO, 
					SZ6.Z6_PRV01 + SZ6.Z6_PRV02 + SZ6.Z6_PRV03 TOTAL_ORCAMENTO,
					(
					SELECT  
						CASE
							WHEN  SUM(SZ0.Z0_VALCRE) IS NULL THEN 0
							ELSE    SUM(SZ0.Z0_VALCRE) 
						END AS SUM_PAYMENT
					FROM DB2.SZ0010 AS SZ0
					WHERE SZ0.Z0_DONOCH IN ('4', '5') 
					AND SZ0.Z0_CODORCA = SZ6.Z6_NUM
					AND SZ0.D_E_L_E_T_ = ''
					) + SZ6.Z6_DESCONT AS TOTAL_PAGO,
					SZ6.Z6_STATUS STATUS,					
					0 AS ENABLE_RECURRING
        			FROM 
                        DB2.SA1010 AS SA1
        			INNER JOIN  DB2.SZ6010 AS SZ6 ON 
						SA1.A1_LOJA = SZ6.Z6_LOJA  AND SA1.A1_COD = SZ6.Z6_CLIENTE AND
						SA1.D_E_L_E_T_ = SZ6.D_E_L_E_T_ AND SA1.D_E_L_E_T_ = ''                        
			        LEFT JOIN DB2.MT3010 AS MT3 ON 
						MT3.MT3_CODEMP = SZ6.Z6_CODEMP AND
						MT3.MT3_CODFIL = SZ6.Z6_MSFIL			
		";
				
		//die($sql3);
		
		return($sql3);

	}
		
	private function get_default_num_quote(){
			
		return $this->get_erp() == "P08" ? "AAIL06" : "058742";
				
	}
	
	public function makeContentError(){
		$result = "";
		$result .= $this->validations->_getErrorMessageWS();
		
		$arrpage = array(
				"erp"			=> $this->get_erp($this->getNumQuote()),
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
		$txtNumQuotation = $this->getNumQuote();
		
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
					STATUS,
					ENABLE_RECURRING
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
				"erp"			=> $this->get_erp(),
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
					"erp"			=> $this->get_erp(),
					"reccount"  	=> $reccount,
					"pagecount"		=> $pagecount,
					"queryresult"	=> $result
					);
	
				echo json_encode($arrpage);											
		}
	}
	
}
new SearchQuotation();
