
<?php
require_once '../../conf.php';

class ConsultaTransacaoCartao extends Ometz_Default
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


	private function validateSOAP(){		
		return 	(
					VALIDATE_WS_ONSTART ? 
					$this->validations->validateSOAP() : 
					true
				);
	}
	
	private function countRecord($txtCustNum, $txtBranchNum, $txtNumTrans)
	{
		$sql_count=" SELECT COUNT(1) CUENTA FROM ";
		$sql_count.= "(".$this->getOriginalQuery($txtCustNum, $txtBranchNum, $txtNumTrans).") AS Z1 ";
		

		$response = $this->database->fetchAll($sql_count);
		if($response)
		{
			//echo ($response[0]["CUENTA"]);
			return floatval($response[0]["CUENTA"]);
		}
		else
			return -1;
	}


	private function getQueryWithRecNum($txtCustNum, $txtBranchNum, $txtNumTrans)
	{
		$sql2=" SELECT ROW_NUMBER() OVER (ORDER BY Z1.TRANSACAO) AS NUM_REC, Z1.* FROM ";
		$sql2.= "(".$this->getOriginalQuery($txtCustNum, $txtBranchNum, $txtNumTrans).") AS Z1";
		return($sql2);		
	}


	// Set SQL Query here
	private function getOriginalQuery($txtCustNum = "", $txtBranchNum = "", $txtNumTrans = "")
	{
		$sql3="
				SELECT 
				  CASE
					  WHEN INSTR(SZ0.Z0_HIST, 'TRASACAO:') > 0  THEN
					    TRIM(
				      SUBSTR(SZ0.Z0_HIST, 
				             INSTR(SZ0.Z0_HIST, 'TRASACAO:') + LENGTH('TRASACAO:'), 
				             INSTR(SZ0.Z0_HIST, ';', 1, 2) - (INSTR(SZ0.Z0_HIST, 'TRASACAO:') + LENGTH('TRASACAO:'))
				            )
				          )
				      ELSE ''
				    END AS TRANSACAO,
				
				  SZ0.Z0_CODORCA AS ORCAMENTO,
				  
				  CASE
					  WHEN INSTR(SZ0.Z0_EMITENT, 'CRE:') > 0  THEN
					    TRIM(
				      SUBSTR(SZ0.Z0_EMITENT, 
				             INSTR(SZ0.Z0_EMITENT, 'CRE:') + LENGTH('CRE:'), 
				             INSTR(SZ0.Z0_EMITENT, ';') - (INSTR(SZ0.Z0_EMITENT, 'CRE:') + LENGTH('CRE:'))
				            )
				          )
				      ELSE ''
				    END AS MASK_CC,
				  MIN(SE1.E1_DTACRED) DATA_COBRANCA, 
				  MAX(SE1.E1_DTACRED) DATA_ULTIMA_PARCELA, 
				  MIN(SE1.E1_PARCELA) PARCELA, 
				  SE1.E1_SALDO SALDO
				FROM 
				  DB2.SE1050 AS SE1
				INNER JOIN 
				  DB2.SZ0500 AS SZ0 ON SE1.E1_NRDOC = SUBSTR(SZ0.Z0_HIST, 36, 14) 
				WHERE 1=1 
				AND SE1.E1_DTACRED <> ''
				AND SZ0.Z0_EMITENT <> ''
				AND SZ0.Z0_DONOCH = '5'
				";
		
		$bWhere = false;
		
		if($txtCustNum !=""){
			$sql3.="AND SE1.E1_CLIENTE = '".$txtCustNum."'";
			$bWhere = true;
		}
		
		if($txtBranchNum !=""){
			$sql3.=" AND SE1.E1_LOJA = '".$txtBranchNum."'";			
		}
		
		
		if($txtNumTrans !=""){
			$sql3.=" AND SZ0.Z0_HIST LIKE '%TRASACAO: ".$txtNumTrans."%'";			
		}
		
		//AND SZ0.Z0_HIST LIKE '%TRASACAO: 73528242290595%'
			
		$sql3 .= " GROUP BY  SZ0.Z0_CODORCA, SZ0.Z0_HIST, SZ0.Z0_EMITENT, SE1.E1_SALDO"; 
		
				
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
			$txtCustNum = "078605"; 

		if(isset($_POST['txtNumBrand']))
			$txtBranchNum = $_POST["txtNumBrand"];
		else
			$txtBranchNum = "01"; 
		
		if(isset($_POST['txtNumTrans']))
			$txtNumTrans = $_POST["txtNumTrans"];
		else
			$txtNumTrans = ""; 
		
		
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

		$reccount = $this->countRecord($txtCustNum, $txtBranchNum, $txtNumTrans);
		$pagecount = ceil($reccount / $pagesize);

		$result = "";

		if ($reccount >= 0)
		{	
			$sql="
			SELECT				  
				  TRANSACAO,				
				  ORCAMENTO,
				  MASK_CC,
				  SUBSTR(DATA_COBRANCA,7,2) || '/' || SUBSTR(DATA_COBRANCA,5,2) || '/' || SUBSTR(DATA_COBRANCA,1,4) DATA_COBRANCA, 
				  SUBSTR(DATA_ULTIMA_PARCELA,7,2) || '/' || SUBSTR(DATA_ULTIMA_PARCELA,5,2) || '/' || SUBSTR(DATA_ULTIMA_PARCELA,1,4) DATA_ULTIMA_PARCELA,
				  SUBSTR(DATA_ULTIMA_PARCELA,5,2) MES_ULTIMA_PARCELA,
				  SUBSTR(DATA_ULTIMA_PARCELA,1,4) ANO_ULTIMA_PARCELA,
				  PARCELA, 
				  SALDO
			FROM
			(";
			$sql.= $this->getQueryWithRecNum($txtCustNum, $txtBranchNum, $txtNumTrans);
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
					$result = "Transacao nao encontrado";
				}				
			}
			else
			{
				$result = ""; //$this->appendDefaultHeader();
				$result.= $this->database->getError();
				if($result=="")
					$result = "Transacao nao encontrado";
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
new ConsultaTransacaoCartao();
