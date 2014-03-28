<html>
<?php

ini_set('default_socket_timeout', 180);

require_once '../conf.php';
require_once 'wsdl_detalhe_cartao.php';

class returnPayment extends Ometz_Default
{	
	private $hasValues;			//If values has passed by post
	private $status; 			//Transaction status: Aproved / Denied.
	private $transCode;			//Authorization code given by cc brand.	
	private $transCodeAnt;		//Previous Authorization code given by cc brand.	
	private $authCode;			//Transaction number given by credit card brand	
	private $maskedCreditCard;	//Masked Credit Card Number (up to 19 digits)
	private $docNumber;			//Quote number	
	private $ammount;			//Amount charged to credit card (numercic).
	private $viewAmmount;		//Amount charged to credit card (for diaplaying in view).
	private $feeNumbers;		//Number of fees.
	private $viewFeeNumbers;	//Number of fees (for diaplaying in view).
	private $valPaydown;		//Paydown value
	private $valTotalCred;		//Total ammount of credit.
	private $feeAmmount;		//Fee ammount depending upon sale type.
	private $expiredate;		//Expire date.
	private $codSaleType;		//Sale Type Code.
	private $discountValue;					// Discount value when regular sale applies.
	private $result;			//Web Service Response (REST).
	private $soapMessage;		//Web Service Response (SOAP). 
	
	private function checkVarPOST() {
		if (isset($_POST)){
			$this->hasValues = true;
			$this->codSaleType = $this->getCodSaleType();
			$this->status = $this->getStatus();
			$this->transCode = $this->getTransCode();
			$this->authCode = $this->getAuthCode();
			$this->maskedCreditCard = $this->getMaskedCreditCard();
			$this->docNumber = $this->getDocNumber();
			$this->ammount = $this->getAmmount();
			$this->viewAmmount = $this->getViewAmmount();
			$this->feeNumbers = $this->getFeeNumbers();
			$this->viewFeeNumbers = $this->getViewFeeNumbers();
			$this->valPaydown = $this->getPayDownAmmount();
			$this->valTotalCred = $this->getTotalCreditAmmount();			
			$this->expiredate = $this->getExpireDate();
			$this->discountValue = $this->getDiscountValue();
			$this->valTotalCred = $this->applyDiscountCreditAmmount();
			$this->feeAmmount = $this->getFeeAmmount();
			$this->transCodeAnt = $this->getTransCodeAnt();
		}		
	}
	
	private function makeTestSOAP(){
		header('Content-Type: text/plain'); 
		echo("test payment\r\n");
		$this->hasValues = true;
		$this->codSaleType = "4";
		$this->status = "True";
		$this->transCode = "036218";
		$this->authCode = "73522174974737";
		$this->maskedCreditCard = "518767******6833";
		$this->docNumber = "062991";
		$this->feeNumbers = "4";
		$this->valPaydown = 0;
		$this->valTotalCred = 5;
		$this->feeAmmount = 1.25;
		$this->expiredate = "112018";
		$this->discountValue = 0;
		$this->addPaymentSOAP();
		//$this->addPaymentNUSOAP();
		echo("payment saved...<br>");
		echo($this->soapMessage);
		exit();
	}
	
	private function addPaymentREST(){
		echo($this->status);
		if($this->status == "True"){
			echo("Executing...<br>");
			$hist = "COD AUTORIZACAO: ".$this->authCode."; TRANSACAO: ".$this->transCode.";";
			$data = array(
					'SZ0_CODORCA' 	=> $this->docNumber, 
					'NZ0_VALOR' 	=> $this->ammount, 
					'SZ0_HIST'		=> $hist, 
					'SZ0_PARCELA' 	=> $this->feeNumbers		
					);

			echo("parameters...<br>");

			print_r($data);
			

			$ch = curl_init();				
			curl_setopt($ch, CURLOPT_URL, ERP_URL_WS);		
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //POST DATA);
			
			
			$result = curl_exec($ch);
			
			if ($result === false) {
				echo("<br>");
				die(curl_error($ch));
			}

			print_r($result);
			curl_close($ch);		
	
		}
	}
		
	private function chooseCutOffDate(){
		if ($this->codSaleType == "4"){
			$varDate = new DateTime();
			return $varDate->format('Ymd'); 
		}
		else
			return $this->getCutOffDate();
	}
	
	private function get_unique_receive_acc($trans_id){
		$sql = "SELECT 
				  E1_FILIAL AS FILIAL, E1_PREFIXO AS PREFIXO, 
				  E1_NUM AS NUMERO, SE1.E1_TIPO AS TIPO, 
				  SE1.E1_PARCELA AS PARCELA
								
				  FROM DB2.SE1050 SE1 
				
				  INNER JOIN DB2.VZL500 VZL ON
				      SE1.E1_FILIAL = VZL.VZL_CODFIL AND 
				      SE1.E1_PREFIXO = VZL.VZL_PREFIX AND
				      SE1.E1_NUM = VZL.VZL_NUM AND
				      SE1.E1_PARCELA = VZL.VZL_PARCEL AND
				      SE1.E1_TIPO = VZL.VZL_TIPO AND
				          VZL.D_E_L_E_T_ <> '*'
				      
				  WHERE E1_TIPO = 'CC'
				   	AND E1_SALDO > 0 
				   	AND E1_NRDOC <> '' 
				   	AND E1_CODORCA <> ''
				  	AND SE1.E1_DTACRED <> ''
				  	AND SE1.D_E_L_E_T_ <> '*'					
				  	AND SE1.E1_NRDOC = '".$trans_id."'
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
		
		$rs = $this->database->fetchAll($sql);
		
		return count($rs) > 0 ? $rs[0] : false; 
		
	}
	
	private function recollectSOAP(){
		if($this->status == "True"){
			if($this->validations->validateSOAP()){
				$rowRecorrencia = $this->get_unique_receive_acc($this->getTransCodeAnt());				
				if($rowRecorrencia != false) {
					$wsdl = new WS_DETALHE_CARTAO();
					$data = new RECORRENCIA();				
					$data->SVZL_CODEMP = "05"; // string (hardcoded temporally)
					$data->SVZL_CODFIL = strval($rowRecorrencia['FILIAL']); // string
					$data->SVZL_PREFIX = strval($rowRecorrencia['PREFIXO']); // string
					$data->SVZL_NUM = strval($rowRecorrencia['NUMERO']); // string
					$data->SVZL_PARCEL = strval($rowRecorrencia['PARCELA']); // string
					$data->SVZL_TIPO = strval($rowRecorrencia['TIPO']); // string
					$data->SVZL_STATUS = strval($this->status); // string
					$data->SVZL_APROVACAO = str_pad($this->getAuthCode(), 6, STR_PAD_LEFT); // string
					$data->SVZL_TRANSACAO = str_pad($this->getTransCode(), 14, STR_PAD_LEFT); // string
					$data->SVZL_OBS = "Cobrança OK - Alteração manual do cartão."; // string

					$response = $wsdl->RECORRENCIA($data);
						
					$soapMessage = $response->RECORRENCIARESULT;
					
					$wsdl->RECORRENCIA($data);
				}
			}
			else{
				$this->soapMessage = $this->validations->_getErrorMessageWS();
			}
		}
	}
	
	private function addPaymentSOAP(){
		if($this->status == "True"){
			if($this->validations->validateSOAP()){
				$wsdl = new WS_DETALHE_CARTAO();
				$data = new INCLUI();
	
				
				$data->SZ0_CODORCA  = $this->docNumber;
				$data->SZ0_VENCTO   = $this->chooseCutOffDate();						
				$data->NZ0_VALENT 	= $this->valPaydown;
				$data->NZ0_VALCRE 	= $this->valTotalCred;
				$data->SZ0_PARCELA  = $this->feeNumbers;		
				$data->NZ0_VALOR 	= $this->feeAmmount;
				$data->SZ0_DONOCH	= $this->getCodSaleType();
				$data->SZ0_HIST 	= "COD AUTORIZACAO: ".$this->authCode."; TRASACAO: ".$this->transCode.";";
				$data->SZ0_EMITENT  = "CRE: ".$this->maskedCreditCard.";VEN: ".$this->expiredate.";";
				$data->NCJ_DESCONT	= $this->discountValue;

				
				$response = $wsdl->INCLUI($data);
				
				$this->soapMessage = $response->INCLUIRESULT;
			}
			else
				$this->soapMessage = $this->validations->_getErrorMessageWS();
		}		
	}					

	public function init()
	{

		/*
		if(isset($_GET["test"])){
			$this->makeTestSOAP();		
			exit;
		}
		*/
				
		$this->showDefaultHeader();
		$this->checkVarPOST();			

						
		if($this->hasPOST() == false) {
			header('Location: '.BASE_URL);	
			exit;
		}
		
		// Recollect		
		if($this->getCodSaleType() == "6")
			$this->recollectSOAP();
		
		else 
			$this->addPaymentSOAP();	//Add payment details using SOAP.	
		
		$this->verifySavedData();
		
		
	}
				
			
	public function hasPOST() {
		return $this->hasValues;
	}	

	//Transaction status: Aproved / Denied
	public function getStatus() {
		if (isset($_POST["TransacaoAprovada"]))		
			return htmlentities($_POST["TransacaoAprovada"]) ;
		else {
			$this->hasValues = false;
			return $this->status;
		}
	}			

	//Get sale type: Regular / Automatic Debit
	public function getCodSaleType() {
		if (isset($_POST["CodTipoVenda"]))
			return $_POST["CodTipoVenda"];
		else {
			$this->hasValues = false;
			return $this->codSaleType;
		}
	}			


	//Authorization code given by cc brand.
	public function getTransCode() {
		if (isset($_POST["Transacao"]))
			return $_POST["Transacao"];
		else {
			$this->hasValues = false;
			return $this->transCode;
		}
	}
	
	//Authorization code given by cc brand.
	public function getTransCodeAnt() {
		if (isset($_POST["TipoVenda"]))
			return str_replace("RECOBRANCA - TRANSACAO ANTERIOR ", "", urldecode($_POST["TipoVenda"]));
		else {
			$this->hasValues = false;
			return $this->transCodeAnt;
		}
	}			
			
	//Transaction number given by credit card brand
	public function getAuthCode() {
		if (isset($_POST["CodigoAutorizacao"]))		
			return $_POST["CodigoAutorizacao"];
		else {
			$this->hasValues = false;
			return $this->authCode;
		}
	}			

	//Masked Credit Card Number
	public function getMaskedCreditCard() {
		if (isset($_POST["CartaoMascarado"]))
			return str_pad($_POST["CartaoMascarado"], 19," ", STR_PAD_LEFT);
		else {
			$this->hasValues = false;
			return $this->maskedCreditCard;
		}
	}				
		
	//Quote number
	public function getDocNumber() {
		if (isset($_POST["NumeroDocumento"]))
			return $_POST["NumeroDocumento"];
		else {
			$this->hasValues = false;
			return $this->docNumber;
		}
	}					

	//Amount charged to credit card.
	public function getAmmount() {		
		if (isset($_POST["ValorDocumento"])){
			return floatval($_POST["ValorDocumento"]);			
		}
		else {
			$this->hasValues = false;
			return $this->ammount;
		}
	}
	
	// Amount charged to credit card (for diaplaying in view).
	public function getViewAmmount() {		
		if (isset($_POST["ValorDocumentoExibicao"]))			
			return rawurldecode($_POST["ValorDocumentoExibicao"]);
		else {
			$this->hasValues = false;
			return $this->viewAmmount;
		}		
	}						

	//Number of fees.
	public function getFeeNumbers() {
		if (isset($_POST["QuantidadeParcelasExibicao"])){
			if (intval($this->codSaleType) == 1){
				$qp = intval($_POST["QuantidadeParcelasExibicao"]);
				return strval($qp - 1);
			}
			else
				return $_POST["QuantidadeParcelasExibicao"];
		}
		else {
			$this->hasValues = false;
			return $this->feeNumbers;
		}
	}


	//Number of fees (for diaplaying in view).
	public function getViewFeeNumbers() {
		if (isset($_POST["QuantidadeParcelasExibicao"]))
			return $_POST["QuantidadeParcelasExibicao"];
		else {
			$this->hasValues = false;
			return $this->viewFeeNumbers;
		}
	}

	//Paydown calculated if Sale Type is automatic debit. Otherwise, becomes 0
	public function getPayDownAmmount() {
		if ($this->codSaleType == "5")
			return floatval($this->ammount);
		else
			return 0;
	}

	// Aply discount to Credit Ammount
	public function applyDiscountCreditAmmount() {
		if($this->codSaleType == "4")
			return ($this->valTotalCred - $this->discountValue);
		else
			return ($this->valTotalCred);
	}
	
	//Total Credit Ammount
	public function getTotalCreditAmmount() {		
		if (isset($_POST["ValorTotalCredito"]))
			return $_POST["ValorTotalCredito"];
		else {
			$this->hasValues = false;
			return $this->valTotalCred;
		}
	}

	//Discount Value
	public function getDiscountValue() {
		if($this->codSaleType == "4") {
			$baseAmmount = floatval( $this->valTotalCred );
			$discountApply = DISC_REG_SALE_PERC / 100;
			return ( round ($baseAmmount * $discountApply, 2) );
		}
		else
			return 0;
	}


	//Total Fee Ammount
	public function getFeeAmmount() {	
		if (isset($_POST["ValorParcela"]))
			return floatval($_POST["ValorParcela"]);
		else{
			$this->hasValues = false;
			return $this->feeAmmount;
		}
	}

	//Total Fee Ammount
	public function getExpireDate() {	
		$month = "";
		$year = "";

		if (isset($_POST["MesValidade"]))
			$month = $_POST["MesValidade"];

		if (isset($_POST["AnoValidade"]))
			$year = $_POST["AnoValidade"];
		
		if($month != "" && $year != "")
			return $month.$year;
		else {
			$this->hasValues = false;
			return $this->expiredate;
		}
	}
	
	private function send_mail_sz0(){
		
		$message = "Dados para inserir na SZ0:<br>			
			SZ0_CODORCA  = ".$this->docNumber."<br>
			SZ0_VENCTO   = ".$this->chooseCutOffDate()."<br>						
			NZ0_VALENT 	= ".$this->valPaydown."<br>
			NZ0_VALCRE 	= ".$this->valTotalCred."<br>
			SZ0_PARCELA  = ".$this->feeNumbers."<br>
			NZ0_VALOR 	= ".$this->feeAmmount."<br>
			SZ0_DONOCH	= ".$this->getCodSaleType()."<br>
			SZ0_HIST 	= \"COD AUTORIZACAO: ".$this->authCode."; TRASACAO: ".$this->transCode."\";<br>
			SZ0_EMITENT  = \"CRE: ".$this->maskedCreditCard.";VEN: ".$this->expiredate."\";<br>
		";


		//Create a new PHPMailer instance
		$mail = new PHPMailer();
		//Tell PHPMailer to use SMTP
		$mail->isSMTP();
		//Set the hostname of the mail server
		$mail->Host = "smtp.mindset.net.br";
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = 587;
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication
		$mail->Username = "teste@ometzgroup.com.br";
		//Password to use for SMTP authentication
		$mail->Password = "1q2w3e4r";
		//Set who the message is to be sent from
		$mail->setFrom('cartaoskopos@ometzgroup.com.br', 'Webmaster Cartao Skopos');
		//Set an alternative reply-to address
		$mail->addReplyTo('cartaoskopos@ometzgroup.com.br', 'Webmaster Cartao Skopos');
		//Set who the message are to be sent to
		$mail->addAddress('joab.rodrigues@ometzgroup.com.br', 'Joab Rodrigues');
		$mail->addAddress('cesar.urdaneta@ometzgroup.com.br', 'Cesar Urdaneta');
		//Set the subject line
		$mail->Subject = 'Dados tabela SZ0 - cartoaskopos.';
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($message);
		//Replace the plain text body with one created manually
		$mail->AltBody = $message;
				
		//send the message, check for errors
		if (!$mail->send()) {
		    echo "Mailer Error: " . $mail->ErrorInfo;
		} 
		/*
		else {
		    echo "Message sent!";
		}
			*/	
		
	}
	
	private function verifySavedData(){
		
		$sql= "
			SELECT 1					 
			FROM DB2.SZ0500 AS SZ0
			WHERE SZ0.Z0_CODORCA  = '".$this->docNumber."'
			AND SZ0.Z0_HIST LIKE '%TRASACAO: ".$this->transCode."%'
		";
		
		//die($sql);
				
		$rs = $this->database->fetchAll($sql);
		
		//Send E-Mail when data is not found.
		if (count($rs) == 0)	
			$this->send_mail_sz0();

	}
	
	public function showErrorSoapMsg(){
			
		$pos = !strpos($this->getSoapMessage(), "sucesso");
		$pos2 = !strpos($this->getSoapMessage(), "OK");		
		
		if($pos && $pos2){
			$show_error = true;
			echo('	<br>
					<font color="#FF0000">'.$this->getSoapMessage().'
					</font>');
			
			return true;
		}
		return false;
	}
	
	//Web Service message
	public function getSoapMessage(){
		return $this->soapMessage;
	} 	
}

$objPayment =  new returnPayment();

?>

<body text="#000000" class="VermelhoGrande"> 

<table  width="50%" align="center">
	<tr >
		<td colspan="2" class="FonteFormulario">
			<p>
				<a href="http://www.ometzgroup.com.br/empresas/skopos"><img src="<?php echo (IMG_FOLDER); ?>logo_company.png" border=0 align="absmiddle" title="Descri&ccedil;&atilde;o da Sua Loja"></a>
			</p>
			<div class="version">Vers&atilde;o: <? echo(VERSION); ?></div>
		</td>        
	</tr>
    <tr>
		<td class="FonteFormulario">
        	
			<fieldset>
				<legend>RESULTADO DA TRANSA&Ccedil;&Atilde;O</legend>
				<? if($objPayment->getStatus() == "True") {?>					
                    <div id="txtTransaction">
                    	<div id="imgTransaction">
                        	<img src="<? echo(IMG_FOLDER); ?>aprovado.png">
                        </div>
                        <p class="textStatus">
                        	<? if($objPayment->getCodSaleType() != '6') {?>
	                        	A compra foi feita com sucesso.
	                            
	                        <? }
							else { ?>
								A alteração do cartão foi feita com sucesso.
							<? } 
							$objPayment->showErrorSoapMsg();
							?>
							
                        <br><br>
                        Segue os dados de aprova&ccedil;&atilde;o do cart&atilde;o de cr&eacute;dito:
                        </p>

                    </div>

                    <hr />
                    <div class="PurchaseData">                
                        <b class="textlabel">N&Uacute;MERO OR&Ccedil;AMENTO:</b>
                        <span id="quotenum" class="CustomerData"><?php echo($objPayment->getDocNumber()); ?></span>                            
                    </div>
                    <div class="PurchaseData">                
                        <b class="textlabel">N&Uacute;MERO TRANSA&Ccedil;&Atilde;O:</b>
                        <span id="numtran" class="CustomerData"><?php echo($objPayment->getTransCode()); ?></span>                            
                    </div>
                    <div class="PurchaseData">                
                        <b class="textlabel">C&Oacute;DIGO AUTORIZA&Ccedil;&Atilde;O:</b>
                        <span id="authcode" class="CustomerData"><?php echo($objPayment->getAuthCode()); ?></span>
                        
                    </div>
                    <div class="PurchaseData">                
                        <b class="textlabel">N&Uacute;MERO CART&Atilde;O:</b>
                        <span id="numcc" class="CustomerData"><?php echo($objPayment->getMaskedCreditCard()); ?></span>
                        
                    </div>
                    <div class="PurchaseData">                                                            
                        <b class="textlabel">VALOR TRANSA&Ccedil;&Atilde;O:</b>
                        <span id="txtValorDocumentoExibicao" class="CustomerData"><?php echo($objPayment->getViewAmmount()); ?></span>
                    </div>
                    <div class="PurchaseData">                
                        <b class="textlabel">N&Uacute;MERO PARCELAS:</b>
                        <span id="txtQuantidadeParcelas" class="CustomerData"><?php echo($objPayment->getViewFeeNumbers()); ?></span>
                        
                    </div>

                    <div class="buttonsetApproved">
                        <a id="btnPrint" href="#">Imprimir</a>
                        <a id="btnBackQuote" href="<? echo(BASE_URL); ?>">Voltar para Or&ccedil;amento</a>
                    </div>	
                    

                    <div class="PurchaseData">
                        <p>&nbsp;</p>
                    </div>

				<? }
                else {
                ?>			    	
                    <form name="frmRetry" action="<? echo(PAYMENT_GATEWAY); ?>" method="POST" id="frmRetry">
                        <input type="hidden" id="ValorTotalCredito" name="ValorTotalCredito" value="<?= $objPayment->getTotalCreditAmmount(); ?>">
                        <input type="hidden" id="ValorDocumento" name="ValorDocumento" value="<?= $objPayment->getAmmount(); ?>">
                        <input type="hidden" id="QuantidadeParcelas" name="QuantidadeParcelas" value="<?= $objPayment->getFeeNumbers(); ?>">
                        <input type="hidden" id="QuantidadeParcelasExibicao" name="QuantidadeParcelasExibicao" value="<?= $objPayment->getViewFeeNumbers(); ?>">
                        <input type="hidden" id="NumeroDocumento" name="NumeroDocumento" value="<?= $objPayment->getDocNumber(); ?>">
                        <input type="hidden" id="FormaPagto" name="FormaPagto" value="">
                        <input type="hidden" id="ValorDocumentoExibicao" name="ValorDocumentoExibicao" value="<?= $objPayment->getViewAmmount(); ?>">
                        <input type="hidden" id="SaldoRestante" name="SaldoRestante" value="">
                        <input type="hidden" id="TipoVenda" name="TipoVenda" value="">
                        <input type="hidden" id="CodTipoVenda" name="CodTipoVenda" value="<?= $objPayment->getCodSaleType(); ?>">
                    
                    <div id="txtTransaction">
                    	<div id="imgTransaction">
                        	<img src="<? echo(IMG_FOLDER); ?>negado.png">
                        </div>
                        <span class="textStatus">
                        	<p>
                            	O cart&atilde;o de cr&eacute;dito inserido foi recusado. Tente a transa&ccedil;ao com outro cart&atilde;o.
                            </p>

                            <div class="buttonsetDenied">
                                <span class="buttonsetDeniedElement">
                                    <a id="btnPrint" class="buttonsetDenied" href="#">Imprimir</a>
                                </span>
                                <span class="buttonsetDeniedElement">
                                    <a id="btnRetryPayment" class="buttonsetDenied" href="#">Tentar com outro Cart&atilde;o</a>
                                </span>
                            	<span class="buttonsetDeniedElement">
                                	<a id="btnBackQuote" class="buttonsetDenied" href="<? echo(BASE_URL); ?>">Voltar para Or&ccedil;amento</a>
                                </span>
                            </div>	
                            
                        </span>

                    </div>
                    </form>
                    
				<? } 
				?>   


			</fieldset>			

            
		</td>
	</tr> 
         
</table>
</body>
</html>
