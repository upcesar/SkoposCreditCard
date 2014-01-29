<html>
<?php

require_once 'conf.php';

class viewQuotation extends Ometz_Default
{
	
	private $listYears;	
	
	private function fillYear()
	{	
		$curYear = date('Y');
	
		$this->listYears = "<option selected></option>";
		
		for($i= 0; $i <= MAX_YEAR_EXPIRE; $i++) 
		{
			$this->listYears.='<option value="'.strval($curYear + $i).'">'.strval($curYear + $i).'</option>';
		}
	}
	
	public function getNumCols($onlyNoInterest = false){
		$ncols = ceil(MAX_QUOTES_NO_INTEREST / NUM_QUOTES_X_COL);

		if(!$onlyNoInterest)
			$ncols += ceil(MAX_QUOTES_NUMBER_INTERES / NUM_QUOTES_X_COL_INTEREST);

		return $ncols;
	}
	
	
					
	public function getMessageRate($wRate){
		return ($wRate)	? "c/j": "s/j";
	}
	
	public function init()
	{
		$this->showDefaultHeader();
		$this->fillYear();
	}	
	
	public function printYears()
	{
		echo $this->listYears;
	}	
}

$objQuotation =  new viewQuotation();
$num_cols = $objQuotation->getNumCols(); 
$num_col_nointerest = $objQuotation->getNumCols(true); 
$num_col_interest = abs($num_col_nointerest - $num_cols);

?>

<body text="#000000" class="VermelhoGrande"> 

<form name="frmPedido" action="<? echo(PAYMENT_GATEWAY); ?>" method="POST" id="frmPedido">

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
				<legend>DADOS DA COMPRA</legend>
                <input type="hidden" id="ValorTotalCredito" name="ValorTotalCredito" value="">
                <input type="hidden" id="ValorDocumento" name="ValorDocumento" value="">
                <input type="hidden" id="QuantidadeParcelas" name="QuantidadeParcelas" value="">
                <input type="hidden" id="QuantidadeParcelasExibicao" name="QuantidadeParcelasExibicao" value="">
                <input type="hidden" id="ValorParcela" name="ValorParcela" value="50">
                <input type="hidden" id="NumeroDocumento" name="NumeroDocumento" value="">
                <input type="hidden" id="FormaPagto" name="FormaPagto" value="">
                <input type="hidden" id="ValorDocumentoExibicao" name="ValorDocumentoExibicao" value="">
                <input type="hidden" id="SaldoRestante" name="SaldoRestante" value="">
                <input type="hidden" id="TipoVenda" name="TipoVenda" value="">
                <input type="hidden" id="CodTipoVenda" name="CodTipoVenda" value="">
                <input type="hidden" id="NumColumns" name="NumColumns" value="<?= $num_cols ?>">
                <input type="hidden" id="MaxQuoteNoRate" name="MaxQuoteNoRate" value="<?= MAX_QUOTES_NO_INTEREST; ?>">
                <input type="hidden" id="RatePerc" name="RatePerc" value="<?= RATE_PERC; ?>">
                <input type="hidden" id="ServerMonth" name="ServerMonth" value="<? echo(date('m')); ?>">
				<input type="hidden" id="ServerYear" name="ServerYear" value="<? echo (date('Y')); ?>">
                <input type="hidden" id="CutOffMonth" name="CutOffMonth" value="<? echo($objQuotation->getCutOffDate('m')); ?>">
				<input type="hidden" id="CutOffYear" name="CutOffYear" value="<? echo ($objQuotation->getCutOffDate('Y')); ?>">
                <input type="hidden" id="DiscountRegSale" name="DiscountRegSale" value="<?= DISC_REG_SALE_PERC ?>">
				<input type="hidden" id="DownPayment" name="DownPayment" value="<?= DOWN_PAYMENT ?>">
                <input type="hidden" id="FinalPayment" name="FinalPayment" value="">
				<input type="hidden" id="AnoValidade" name="AnoValidade" value="">
			    <input type="hidden" id="MesValidade" name="MesValidade" value="">

				<div class="PurchaseData">
                
                    <label for="lblQuotation" class="textlabel"><b>N&Uacute;MERO OR&Ccedil;AMENTO:</b></label>                    
                    <input type="text" id="txtNumQuote" name="txtNumQuote" size="20" maxlength="6" autocomplete="off" class="TextBoxCompras">                    
                    <span class="marginButton"><button id="btnSearch"><img src="img/find.png" width="16px" height="16px" style="padding-right:5px;margin-top:-2px">Busca</button></span>
                    <span id="imgLoading">
                    	<img src="img/loading.gif" />&nbsp;
                        <span id="msgWait">Procurando or&ccedil;amento...</span>
                    </span>
                    <div id="divMsgQuotation" class="smallError">Deve digitar o n&uacute;mero de Or&ccedil;amento</div>					
                </div>
				
				<div id="QuoteNotFound">				
				</div>
                
                <div id="QuotationData">
                    <hr />
                    
					<!-- QUOTE NUMBER -->                    
                    <div class="PurchaseData">                
                        <b class="textlabel">N&Uacute;MERO OR&Ccedil;AMENTO:</b>
                        <span id="quotenum" class="CustomerData"></span>
                        
                    </div>    					
                    
                    <!-- BRANCH CODE AND BRANCH NAME -->                    
                    <div class="PurchaseData">                
                        <b class="textlabel">FILIAL:</b>
                        <span id="branch" class="CustomerData"></span>
                        
                    </div>    
                    
                    <!-- CUSTOMER NUMBER -->                    
                    <div class="PurchaseData">                
                        <b class="textlabel">N&Uacute;MERO CLIENTE:</b>
                        <span id="custcode" class="CustomerData"></span>
                        
                    </div>                    

					<!-- NUM RA -->                    
                    <div class="PurchaseData">                
                        <b class="textlabel">N&Uacute;MERO RA:</b>
                        <span id="numra" class="CustomerData"></span>                        
                    </div>                    
					
                    <!-- DOCUMENT -->
                    <div class="PurchaseData">                
                        <b id="doctype" class="textlabel">CPF:</b>
                        <span id="docnum" class="CustomerData"></span>
                    </div>

                    <!-- CUSTOME NAME -->
                    <div class="PurchaseData">                
                        <b class="textlabel">NOME CLIENTE:</b>
                        <span id="custname" class="CustomerData"></span>
                    </div>
                    
                    <!-- ADDRESS -->
                    <div class="PurchaseData">                
                        <b class="textlabel">ENDERE&Ccedil;O:</b>
                        <span id="address" class="CustomerData"></span>
                    </div>
                    
                    <hr />

                    <!-- QUOTATION -->
                    <div class="PurchaseData">                
                        <b class="textlabel">TOTAL OR&Ccedil;AMENTO:</b>
                        <span id="sumquotation" class="CustomerData"></span>
                    </div>                    

				</div>                                
			</fieldset>
			<br />
            
            <div id="PaymentData">
                <fieldset>
                    <legend>DADOS DO PAGAMENTO</legend>

                    <!-- CREDIT CARD OVERALL AMMOUNT-->
                    <div class="PurchaseData">                
                        <b class="textlabel">TOTAL PAGAR CART&Atilde;O:</b>
                        <span id="tdcammount" class="CustomerData"></span>
                    </div>                    

					<!-- CREDIT CARD BALANCE -->
                    <div class="PurchaseData">                
                        <b class="textlabel">TOTAL PAGO POR CART&Atilde;O:</b>
                        <span id="tdcpaid" class="CustomerData"></span>
                    </div>       
										
                    <hr />
                                        
					<!-- CREDIT CARD BALANCE -->
                    <div class="PurchaseData">                
                        <b class="textlabel">SALDO RESTANTE:</b>
                        <span class="CustomerData"><a href="#" id="tdcbalance"></a></span>
                    </div>                                                            

                    <hr />

                    <!-- CURRENT CREDIT CARD PAYMENT VALUE -->
                    <div class="PurchaseData">
                        <label for="lblQuotation" class="textlabel"><b>VALOR PAGAMENTO:</b></label>
                        <input type="text" id="txtValorDocumento" name="txtValorDocumento" size="20" class="TextBoxCompras">
                        <span id="divMsgValuePayment" class="smallError">Deve digitar o valor para Pagamento</span>						
                    </div>
                    
					<div class="PurchaseData">
                        <label for="lblExpire" class="textlabel"><b>VENCIMENTO CART&Atilde;O:</b></label>
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
							<?= $objQuotation->printYears();?>
						</select>

                        <span id="divMsgExpireDate" style="display:none;" class="smallError">Deve selecionar m&egrave;s e ano de vencimento</span>
					</div>                    
                    
                    <!-- FEE NUMBERS -->
                    
                    <div class="PurchaseData">
                        
                        <input type="hidden" id="txtQuantidadeParcelas" name="txtQuantidadeParcelas" size="2" class="TextBoxCompras" maxlength="2" readonly >
                        <span style="margin-left:75px">
                        <button id="btnSelectQuota">
                            <img src="img/fee_number.png" width="20px" height="20px">&nbsp;Selecionar Parcelas
                        </button>
                        </span>
                        &nbsp;&nbsp;                        
                        <button id="btnPay">
                            <img src="img/checkout.png" width="22px" height="22px" style="padding-right:5px;margin-top:-2px">Realizar Compra
                        </button>

                        
                        <span id="divMsgValueFee" class="smallError">Deve digitar o valor para N&uacute;mero de Parcelas</span>						
                    </div>
                    
                    <div class="PurchaseData">
                        <label for="lblPay" class="textlabel">&nbsp;</label>
                        <span class="">
                        </span>
                        <span id = "outputPayment" class="msgPayment">                        	
                        </span>

					</div>                    
                    
                </fieldset>
            </div>


            
		</td>
	</tr>
</table>


<div id="dialogFeeNumber" title="Op&ccedil;&otilde;es de pagamento">

		<? 
			$numColRate = 0;
			for($k = 0; $k <= $num_cols - 1; $k++) {?>			
			
				<? 
					$minquote = 1;

					if($k < $num_col_interest - 1){						
						$nextcol = NUM_QUOTES_X_COL;
						$labelInterest = "";
						$num_quotes_no_rate = 0;
						$divColID = "";
						$entrada = 0;
					}
					else{
						$nextcol = NUM_QUOTES_X_COL_INTEREST;
						$labelInterest = "Rate";						
						$num_quotes_no_rate = NUM_QUOTES_X_COL;
						$numColRate++;
						$divColID = $labelInterest.strval($numColRate);
						$entrada = -1;
					}

					$start_index = (($nextcol * $k) + $minquote) - $num_quotes_no_rate;				// Begin Fee
					$end_index   = (($nextcol * ($k + 1)) + ($minquote - 1)) - $num_quotes_no_rate; 	// End Fee					

					// Subtract position with down payent.
					
					$start_index += $entrada;
					$end_index   += $entrada; 	
					

					//$first_line = false;
				?>
           
           <div id="colSelPayment<?= $divColID ?>" class="columnDialog">
           
				<? for($i = $start_index; $i <= $end_index; $i++) { 							
					if(!($minquote == MIN_QUOTES_INTEREST && $i > MAX_QUOTES_NUMBER_INTERES  )) { ?>
                         <div style="height:5px">
                         <?
						 /*
						 if( $i == 1 && $labelInterest == "Rate" && $first_line == false) {
						 	$i--;
							$first_line = true;
							echo("----<br>");
							continue;
						 }
						 */
						 if($i >= MIN_QUOTES_INTEREST || $k < $num_col_interest - 1) {?>						
                            <input name="optFeeNum" id="optFee<?= $labelInterest ?>Num<?= $i ?>" type="radio" value="<?= $i ?>" class="optNumParcela" />
                            <label for = "optFee<?= $labelInterest ?>Num<?= $i ?>" id = "lblQuote<?= $labelInterest.strval($i) ?>">
                                <? if ($labelInterest == "Rate") {?>
                                	Ent&nbsp;<span class="ammountDialog" id="downPayment<?= $i ?>" data-a-sign="R$ " data-a-dec="," data-a-sep="."></span>
                                    &nbsp;+&nbsp;
                                <? } ?>
                                <?= $i.' x '; ?>  
                                <span class="ammountDialog" id="feeAmmount<?= $labelInterest.$i ?>" data-a-sign="R$ " data-a-dec="," data-a-sep="."></span>
                                <span id="lblMsgRate"><?= ($i > 1 || $labelInterest == "Rate") ? $objQuotation->getMessageRate($labelInterest == "Rate" ) : '';  ?></span>
                            </label>
							</span>
                         <? }
                         else{ ?>
                            ----
                         <? } ?>
                         </div>
                         <br>
                    <? } 
                	else
                    	break; 
                 } ?>
			</div>
		<? } ?>	

	
	<div id="msgSelFee" class="smallerror" style="float:right;margin-top:10px;">
    	Deve selecionar uma op&ccedil;&atilde;o para pagamento
    </div>
</div>

</form>
</body>
</html>
