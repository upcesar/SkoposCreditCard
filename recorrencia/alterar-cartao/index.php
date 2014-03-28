<?php

require_once '../../conf.php';

class viewContract extends Ometz_Default
{
	private $rs_cust;
	private $rs_tran;
	private $count_rs_cust = 0;
	private $count_rs_tran = 0;
	private $listYears;
	
	private $last_month_collect = '';
	private $last_day_collect = '';	
	
	
	
	private function fillYear()
	{	
		$curYear = date('Y');
	
		$this->listYears = "<option selected></option>";
		
		for($i= 0; $i <= MAX_YEAR_EXPIRE; $i++) 
		{
			$this->listYears.='<option value="'.strval($curYear + $i).'">'.strval($curYear + $i).'</option>';
		}
	}
			
	private function get_curl_data($url, $data){
		//  Initiate curl
		$ch = curl_init();
		// Disable SSL verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// Will return the response, if false it print the response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Set the url
		curl_setopt($ch, CURLOPT_URL, $url);
		
		//Pass POST variables
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		// Execute
		$result=curl_exec($ch);
				
		
		// Will dump a beauty json :3
		$array = (json_decode($result, true));
				
		
		return ((object) $array);
		
		
	}	
					
	private function get_encoded_data(){
		$data = '';
		foreach($_POST as $name => $value) {
  			$data .= urlencode($name).'='.urlencode($value).'&';
		}
		// chop off last ampersand if there's data.
		if($data != '')
			return substr($data, 0, strlen($data) - 1);
		else
			return '';
		
	}
	
	
	private function search_data(){
		$post_data = $this->get_encoded_data();
				
		$json_cust = $this->get_curl_data(BASE_URL.'recorrencia/alterar-cartao/searchCustomer.php', $post_data);
		$json_tran = $this->get_curl_data(BASE_URL.'recorrencia/alterar-cartao/searchPendingRecurring.php', $post_data);
				
		$this->rs_cust = $json_cust->queryresult;
		$this->rs_tran = $json_tran->queryresult;
		$this->count_rs_cust = $json_cust->reccount;
		$this->count_rs_tran = $json_tran->reccount;
		
		if($this->count_rs_tran <= 0){
			header("Location:".BASE_URL."recorrencia/?trans_not_found");
			exit();
		}

	}
	
	private function has_customer(){
			
		if($this->isPostReceived() && $this->count_rs_cust > 0 )
			return true;
		
		return false;
	}
	
	private function has_transaction(){
			
		if($this->isPostReceived() && $this->count_rs_tran > 0 )
			return true;
		
		return false;
	}
	
	public function init()
	{
		echo('<html>');
		$this->showDefaultHeader();		
		
		if($this->isPostReceived())
			$this->search_data();
		
		$this->fillYear();
				
	}	
	
	/* Customer Data.
	 * 
	 */
	public function get_cust_branch(){
		
		if($this->has_customer())
			return $this->rs_cust[0]["MT3_CODFIL"].' - '.$this->rs_cust[0]["MT3_FIL"];
		
		else
			return "&nbsp;";

	}
	
	public function get_cust_code(){
		
		if($this->has_customer())
			return $this->rs_cust[0]["COD_CLIENTE"];
		
		return "&nbsp;";
		
	}
	
	public function get_cust_num_ra(){
		
		if($this->has_customer() )
			return $this->rs_cust[0]["A1_NUMRA"];
		
		return "&nbsp;";
		
	}

	public function get_cust_doc_type(){
		
		if($this->has_customer() )
			return $this->rs_cust[0]["TIPODOC"];
		
		return "CPF";

	}
	
	public function get_cust_doc_num(){
		
		$value  = "&nbsp;";
		if($this->has_customer() ){
			$format_fn = ".format".$this->get_cust_doc_type()."();"; 			
			$value  = "
			<script>
			$(function($){
				numdoc = ('".$this->rs_cust[0]["NUM_DOC"]."')".$format_fn."
				$('#docnum').html(numdoc);				
				});
						
			</script>
			";

		}
		return $value;

	}

	public function get_cust_name(){
		
		if($this->has_customer() )
			return $this->rs_cust[0]["CLIENTE"];
		
		return "&nbsp;";		
				
	}
	
	public function get_cust_address(){
		
		if($this->has_customer() )
			return $this->rs_cust[0]["ENDERECO"];
		
		return "&nbsp;";
				
				
	}
	
	public function get_cust_county(){
		
		if($this->has_customer() )
			return $this->rs_cust[0]["BAIRRO"];
		
		return "&nbsp;";
				
				
	}
	
	public function get_cust_city(){
		
		if($this->has_customer() )
			return $this->rs_cust[0]["CIDADE"];
		
		return "&nbsp;";

	}
	
	public function get_cust_state(){
		
		if($this->has_customer() )
			return $this->rs_cust[0]["ESTADO"];
		
		return "&nbsp;";
			
	}
	
	public function get_cust_email(){
		
		if($this->has_customer() )
			return $this->rs_cust[0]["E_MAIL"];
		
		return "&nbsp;";
		

	}
	
	
	/* Get Transactions */
	public function get_tran_num(){
		
		if($this->has_transaction() )
			return str_pad($this->rs_tran[0]["TRANSACAO"], 14, STR_PAD_LEFT);
		
		return "";
	}
	
	public function get_tran_quote(){
		
		if($this->has_transaction() )
			return $this->rs_tran[0]["ORCAMENTO"];
		
		return "&nbsp;";
	}
	
	public function get_tran_payment_data(){
		
		if($this->has_transaction() )
			return "ORC: ".$this->rs_tran[0]["ORCAMENTO"]. " | CARTAO ".$this->rs_tran[0]["MASK_CC"];
		
		return "&nbsp;";
	}
	
	public function get_tran_date(){
		
		if($this->has_transaction() )
			return $this->rs_tran[0]["DATA_COBRANCA"];
		
		return "&nbsp;";
	}	
	
	public function get_tran_installment(){
		
		if($this->has_transaction() )
			return $this->rs_tran[0]["PARCELA"];
		
		return "&nbsp;";
	}	
	
	public function get_tran_amount(){
		
		if($this->has_transaction() )
			return "<script>
			$(function($){
				$('#lblCollectAmmount').autoNumeric('set', ".$this->rs_tran[0]["SALDO"].");
				});
			</script>";
		
		return "&nbsp;";
	}	
	
	
	public function get_year_last_installment(){
		if($this->has_transaction() && count($this->rs_tran) == 1 )
			return strval($this->rs_tran[0]["ANO_ULTIMA_PARCELA"]);
		
		return "";
		
	}
	
	public function get_month_last_installment(){
		if($this->has_transaction() && count($this->rs_tran) == 1 )
			return strval($this->rs_tran[0]["MES_ULTIMA_PARCELA"]);
		
		return "";
		
	}
	
		
	public function isPostReceived(){
		return (isset($_POST) && count($_POST) > 0);
	}
	
	
	public function getTransaction(){
		if ($this->isPostReceived())
			return ($_POST['txtTransaction']);
		else 
			return "&nbsp;";
		
	}

	public function getIdCustomer(){
		if ($this->isPostReceived())
			return ($_POST['txtIdCustomer']);
		else 
			return "&nbsp;";
		
	}

	public function getIdBranch(){
		if ($this->isPostReceived())
			return ($_POST['txtIdBranch']);
		else 
			return "&nbsp;";
		
	}
	
	public function printYears()
	{
		echo $this->listYears;
	}	
	
	
}

$objCollect =  new viewContract();
$format = new formatterContent();

?>

<body text="#000000"> 

<table  width="50%" align="center">
	<tr >
		<td colspan="2" class="FonteFormulario">
			<p>
            	<a href="http://www.ometzgroup.com.br/empresas/skopos"><img src="<?php echo (IMG_FOLDER); ?>logo_company.png" border=0 align="absmiddle" title="Descri&ccedil;&atilde;o da Sua Loja"></a>
				<div class="version">Vers&atilde;o: <? echo(VERSION); ?></div>                
            </p>
		</td>
	</tr>
	<tr>
		<td class="FonteFormulario">			
 			 			
            <fieldset>
				<legend>RECOBRAN&Ccedil;A DE PARCELAS</legend>
                <? if(!$objCollect->isPostReceived()) {?>
                <form action="" method="post" id="frmCustomerParam" >
					<div class="PurchaseData" id = "CustomerParams">
	                	
		                	<label for="lblQuotation" class="textlabel"><b>N&Uacute;MERO CLIENTE / LOJA:</b></label>                    
		                    <input type="text" id="txtCustNum" name="txtCustNum" size="20" maxlength="6" autocomplete="off" class="TextBoxCompras">
		                    <input type="text" id="txtNumBrand" name="txtNumBrand" size="2" maxlength="2" autocomplete="off" class="TextBoxCompras">
		                    
		                    <span class="marginButton"><button id="btnSearchCollect"><img src="<?= BASE_URL ?>img/find.png" width="16px" height="16px" style="padding-right:5px;margin-top:-2px">Busca</button></span>
		                    <span id="imgLoading">
		                    	<img src="<?= BASE_URL ?>img/loading.gif" />&nbsp;
		                        <span id="msgWait">Procurando cliente...</span>
		                    </span>
	                </div>

	           	</form>                    

                <div id="divMsgContractSearch" class="smallError">Deve digitar o n&uacute;mero de Cliente</div>
				
                
                	<div id="ContractData">
                	<hr />
                
                <?}?>                 
                    
                    
				                                                            					 					                    
                    <!-- BRANCH CODE AND BRANCH NAME -->                    
                    <div class="PurchaseData">                
                        <b class="textlabel">FILIAL:</b>
                        <span id="branch" class="CustomerData"><?php echo ($objCollect->get_cust_branch()); ?></span>
                        
                    </div>    
                    
                    <!-- CUSTOMER NUMBER -->                    
                    <div class="PurchaseData">                
                        <b class="textlabel">N&Uacute;MERO CLIENTE:</b>
                        <span id="custcode" class="CustomerData"><?php echo ($objCollect->get_cust_code()); ?></span>                        
                    </div>                    

					<!-- NUM RA -->                    
                    <div class="PurchaseData">                
                        <b class="textlabel">N&Uacute;MERO RA:</b>
                        <span id="numra" class="CustomerData"><?php echo ($objCollect->get_cust_num_ra()); ?></span>
                    </div>                    
					
                    <!-- DOCUMENT -->
                    <div class="PurchaseData">                
                        <b id="doctype" class="textlabel"><?php echo ($objCollect->get_cust_doc_type()); ?>:</b>
                        <span id="docnum" class="CustomerData">&nbsp;</span>
                        <?php echo ($objCollect->get_cust_doc_num()); ?>
                    </div>

                    <!-- CUSTOME NAME -->
                    <div class="PurchaseData">                
                        <b class="textlabel">NOME CLIENTE:</b>
                        <span id="custname" class="CustomerData"><?php echo ($objCollect->get_cust_name()); ?></span>
                    </div>
                    
                    <!-- ADDRESS -->
                    <div class="PurchaseData">                
                        <b class="textlabel">ENDERE&Ccedil;O:</b>
                        <span id="address" class="CustomerData"><?php echo ($objCollect->get_cust_address()); ?></span>
                    </div>

                    <!-- COUNTY -->
                    <div class="PurchaseData">                
                        <b class="textlabel">BAIRRO:</b>
                        <span id="county" class="CustomerData"><?php echo ($objCollect->get_cust_county()); ?></span>
                    </div>

                    <!-- CITY / STATE -->
                    <div class="PurchaseData">                
                        <b class="textlabel">CIDADE / ESTADO:</b>
                        <span id="city_state" class="CustomerData"><?php echo ($objCollect->get_cust_state()); ?></span>
                    </div>
                   
                    
                    <hr />

                    <!-- OLD CREDIT CARD -->
                    <div class="PurchaseData">                
                        <b class="textlabel">DADOS PAGAMENTO:</b>
                        <span id="old_cc" class="CustomerData">
                        	<?php echo ($objCollect->get_tran_payment_data()); ?>
                        </span>
                    </div>


                    <!-- INSTALLMENT DATE -->
                    <div class="PurchaseData">                
                        <b class="textlabel">DATA COBRAN&Ccedil;A:</b>
                        <span id="levels_ammount" class="CustomerData">
							<span id="lblCollectDate"><?php echo ($objCollect->get_tran_date()); ?></span>							
                        </span>
                    </div>

	                
	                <!-- CURRENT INSTALLMENT -->
	                <div class="PurchaseData">
						<label for="lblFlag" class="textlabel"><b>VALOR PARCELA:</b></label>
	                	<span id="schoolSection" class="CustomerData">		                	
		                	<span id="lblCollectAmmount" data-a-sign="R$ " data-a-dec="," data-a-sep="."></span>
		                	<?php echo ($objCollect->get_tran_amount()); ?>
	                	</span>
					</div>
                    
                    
                    <!--LEVELS AMMOUNT -->
                    <div class="PurchaseData" style="display: none;">                
                        <b class="textlabel">TOTAL MODULOS:</b>
                        <span id="levels_ammount" class="CustomerData">
	                        <input type="text" autocomplete="off" name="txtValorDocumento" id="txtValorDocumento" size="20">
	                        <span id="divMsgValuePayment" class="smallError">Pagamento deve conter um valor maior que zero</span>
                        </span>
                    </div>

                    
                    <div class="PurchaseData">                        
                        <input type="hidden" id="txtQuantidadeParcelas" name="txtQuantidadeParcelas" size="2" class="TextBoxCompras" maxlength="2" readonly >
                    </div>                    
                    
                    
                    <div id="divMsgSelecao" class="smallError">Deve selecionar o pagamento a alterar</div>
                    
                    <hr />
                    
                    <div class="PurchaseData">
                        <label for="lblExpire" class="textlabel"><b>VENCIMENTO NOVO CART&Atilde;O:</b></label>
						<select id="cboExpireMonth" name="cboExpireMonth" class="TextBoxCompras">
							<option selected></option>
							<option value="01">01</option>
							<option value="02">02</option>
							<option value="03">03</option>
							<option value="04">04</option>
							<option value="05">05</option>
							<option value="06">06</option>
							<option value="07">07</option>
							<option value="08">08</option>
							<option value="09">09</option>
							<option value="10">10</option>
							<option value="11">11</option>
							<option value="12">12</option>
						</select>

						<select id="cboExpireYear" name="cboExpireYear" class="TextBoxCompras">
							<?= $objCollect->printYears();?>
						</select>

                        <span id="divMsgExpireDate" style="display:none;" class="smallError">Deve selecionar m&egrave;s e ano de vencimento</span>
					</div>         
                    
                    <hr />
                    
                    <form action="<? echo(PAYMENT_GATEWAY); ?>" method="post" id="frmRepay">
			            <!--input type="hidden" name="nomecliente" id="nomecliente" value = "">
			            <input type="hidden" name="nacionalidadecliente" id="nacionalidadecliente" value = "">
			            <input type="hidden" name="profissaocliente" id="profissaocliente" value = "">
			            <input type="hidden" name="estadocivilcliente" id="estadocivilcliente" value = "">
			            <input type="hidden" name="rgcliente" id="rgcliente" value = "">
			            <input type="hidden" name="cpfcliente" id="cpfcliente" value = "">
			            <input type="hidden" name="enderecocliente" id="enderecocliente" value = "">
			            <input type="hidden" name="bairrocliente" id="bairrocliente" value = "">
			            <input type="hidden" name="cidadecliente" id="cidadecliente" value = "">
			            <input type="hidden" name="estadocliente" id="estadocliente" value = "">
			            <input type="hidden" name="emailcliente" id="emailcliente" value = "">
			            <input type="hidden" name="selectedkit" id="selectedkit" value = "">
			                                     
			            <input type="hidden" name="bandeira" id="bandeira" value = "3">
			            <input type="hidden" name="quantidademodulos" id="quantidademodulos" value = "3">
			            <input type="hidden" name="valormodulo" id="valormodulo" value = "">
			            <input type="hidden" name="valorkit" id="valorkit" value = "">
			            <input type="hidden" name="valorentrada" id="valorentrada" value = "">
			            <input type="hidden" name="valorparcelas" id="valorparcelas" value = "">
			            <input type="hidden" name="primeiramensalidade" id="primeiramensalidade" value = "">
			            <input type="hidden" name="numerocartaocredito" id="numerocartaocredito" value = "">
			            <input type="hidden" name="operadoracartao" id="operadoracartao" value = "" -->
			 			 
						<input type="hidden" id="ValorTotalCredito" name="ValorTotalCredito" value="">
						<input type="hidden" id="ValorDocumento" name="ValorDocumento" value="<?php echo ($objCollect->get_tran_amount()); ?>">
						<input type="hidden" id="QuantidadeParcelas" name="QuantidadeParcelas" value="1">
						<input type="hidden" id="QuantidadeParcelasExibicao" name="QuantidadeParcelasExibicao" value="1">
			            <input type="hidden" id="ValorParcela" name="ValorParcela" value="">
						<input type="hidden" id="NumeroDocumento" name="NumeroDocumento" value="">
						<input type="hidden" id="FormaPagto" name="FormaPagto" value="">
						<input type="hidden" id="ValorDocumentoExibicao" name="ValorDocumentoExibicao" value="">
						<input type="hidden" id="SaldoRestante" name="SaldoRestante" value="">
						<input type="hidden" id="TipoVenda" name="TipoVenda" value="RECOBRANCA - TRANSACAO ANTERIOR <?php echo($objCollect->get_tran_num()); ?>">
						<input type="hidden" id="CodTipoVenda" name="CodTipoVenda" value="6">
						<input type="hidden" id="ServerMonth" name="ServerMonth" value="<? echo(date('m')); ?>">
						<input type="hidden" id="ServerYear" name="ServerYear" value="<? echo (date('Y')); ?>">
						<input type="hidden" id="ServerDay" name="ServerDay" value="<? echo (date('d')); ?>">
						<input type="hidden" id="FinalPayment" name="FinalPayment" value="">
						<input type="hidden" id="AnoValidade" name="AnoValidade" value="">
					    <input type="hidden" id="MesValidade" name="MesValidade" value="">
					    <input type="hidden" id="AnoUltimaParcela" name="AnoUltimaParcela" value="<?php echo ($objCollect->get_year_last_installment()); ?>">
					    <input type="hidden" id="MesUltimaParcela" name="MesUltimaParcela" value="<?php echo ($objCollect->get_month_last_installment()); ?>">
					    <input type="hidden" id="TransacaoAnt" name="TransacaoAnt" value="<?php echo($objCollect->get_tran_num()); ?>">
					    <input type="hidden" id="EnderecoIPComprador" name="EnderecoIPComprador" value="RecorrENtE">

                    	
	                    <div class="PurchaseData">                
	                        <b class="textlabel">&nbsp;</b>
	                        <span id="printContract" class="CustomerData">
	                        	<button id="btnRecollect">
	                            	<img src="<?= BASE_URL ?>img/checkout.png" width="24px" height="24px" style="padding-right:5px;margin-top:5px">
	                                Realizar Cobran&ccedil;a
	                            </button>
	                        </span>
	                    </div>
                    </form>
                    
                    
				</div>                                
			</fieldset>
			<br />
            
		</td>
	</tr>
</table>



</body>
</html>
