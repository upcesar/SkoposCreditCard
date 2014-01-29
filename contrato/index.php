<html>
<?php

require_once '../conf.php';

class viewContract extends Ometz_Default
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
	
	public function listAvailablesCurses(){
		$sql = "SELECT 
  					SB1.B1_COD, SB1.B1_DESC, SB1.B1_PRV1
				FROM DB2.SB1500 AS SB1
				WHERE SB1.B1_COD IN (
					'101.00000000001',
					'101.00000000351',
					'101.00000000352',
					'101.00000000353') 
				AND SB1.D_E_L_E_T_ = ''
				ORDER BY SB1.B1_COD";
		
		$option = "";
		$x = 0;
		$response = $this->database->fetchAll($sql);
		foreach($response as $row){
			$nameCheck = $row["B1_COD"] == "101.00000000001" ? "": "";
			$x++;
			$option .= '<div id="product_'.str_replace(".","_", $row["B1_COD"]).'">	
							<span class="descriptionProduct"><input type="checkbox" name="chkProduct_'.$x.'" class="chkProduct" value = "'.$row["B1_COD"].'" >'.$row["B1_DESC"].'</span>
							<span data-a-sign="R$ " data-a-dec="," data-a-sep="." class = "ammountProduct">'.$row["B1_PRV1"].'</span>
						</div>';			
		}
		return $option;
	}
}

$objContract =  new viewContract();
$format = new formatterContent();
$num_cols = $objContract->getNumCols(); 
$num_col_nointerest = $objContract->getNumCols(true); 
$num_col_interest = abs($num_col_nointerest - $num_cols);

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
            <input type="hidden" name="nomecliente" id="nomecliente" value = "">
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
                                     
            <input type="hidden" name="quantidademodulos" id="quantidademodulos" value = "3">
            <input type="hidden" name="valormodulo" id="valormodulo" value = "">
            <input type="hidden" name="valorkit" id="valorkit" value = "">
            <input type="hidden" name="valorentrada" id="valorentrada" value = "">
            <input type="hidden" name="valorparcelas" id="valorparcelas" value = "">
            <input type="hidden" name="primeiramensalidade" id="primeiramensalidade" value = "">
            <input type="hidden" name="numerocartaocredito" id="numerocartaocredito" value = "">
            <input type="hidden" name="operadoracartao" id="operadoracartao" value = "">
 			 
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
			<input type="hidden" id="CutOffMonth" name="CutOffMonth" value="<? echo($objContract->getCutOffDate('m')); ?>">
			<input type="hidden" id="CutOffYear" name="CutOffYear" value="<? echo ($objContract->getCutOffDate('Y')); ?>">
			<input type="hidden" id="DiscountRegSale" name="DiscountRegSale" value="<?= DISC_REG_SALE_PERC ?>">
			<input type="hidden" id="DownPayment" name="DownPayment" value="<?= DOWN_PAYMENT ?>">
			<input type="hidden" id="FinalPayment" name="FinalPayment" value="">
			<input type="hidden" id="AnoValidade" name="AnoValidade" value="">
		    <input type="hidden" id="MesValidade" name="MesValidade" value="">
 			
 			

            <fieldset>
				<legend>DADOS DO CLIENTE</legend>
                
				<div class="PurchaseData">
                	<label for="lblQuotation" class="textlabel"><b>N&Uacute;MERO CLIENTE / LOJA:</b></label>                    
                    <input type="text" id="txtCustNum" name="txtCustNum" size="20" maxlength="6" autocomplete="off" class="TextBoxCompras">
                    <input type="text" id="txtNumBrand" name="txtNumBrand" size="2" maxlength="2" autocomplete="off" class="TextBoxCompras">
                    
                    <span class="marginButton"><button id="btnSearchCust"><img src="<?= BASE_URL ?>img/find.png" width="16px" height="16px" style="padding-right:5px;margin-top:-2px">Busca</button></span>
                    <span id="imgLoading">
                    	<img src="<?= BASE_URL ?>img/loading.gif" />&nbsp;
                        <span id="msgWait">Procurando cliente...</span>
                    </span>                    
                </div>                
                
                <div id="divMsgContractSearch" class="smallError">Deve digitar o n&uacute;mero de Cliente</div>
				
                <div id="ContractData">                                        
                    <hr />
                                                            					 					                    
                    <!-- BRANCH CODE AND BRANCH NAME -->                    
                    <div class="PurchaseData">                
                        <b class="textlabel">FILIAL:</b>
                        <span id="branch" class="CustomerData">&nbsp;</span>
                        
                    </div>    
                    
                    <!-- CUSTOMER NUMBER -->                    
                    <div class="PurchaseData">                
                        <b class="textlabel">N&Uacute;MERO CLIENTE:</b>
                        <span id="custcode" class="CustomerData">&nbsp;</span>                        
                    </div>                    

					<!-- NUM RA -->                    
                    <div class="PurchaseData">                
                        <b class="textlabel">N&Uacute;MERO RA:</b>
                        <span id="numra" class="CustomerData">&nbsp;</span>                        
                    </div>                    
					
                    <!-- DOCUMENT -->
                    <div class="PurchaseData">                
                        <b id="doctype" class="textlabel">CPF:</b>
                        <span id="docnum" class="CustomerData">&nbsp;</span>
                    </div>

                    <!-- CUSTOME NAME -->
                    <div class="PurchaseData">                
                        <b class="textlabel">NOME CLIENTE:</b>
                        <span id="custname" class="CustomerData">&nbsp;</span>
                    </div>
                    
                    <!-- ADDRESS -->
                    <div class="PurchaseData">                
                        <b class="textlabel">ENDERE&Ccedil;O:</b>
                        <span id="address" class="CustomerData">&nbsp;</span>
                    </div>

                    <!-- COUNTY -->
                    <div class="PurchaseData">                
                        <b class="textlabel">BAIRRO:</b>
                        <span id="county" class="CustomerData">&nbsp;</span>
                    </div>

                    <!-- CITY / STATE -->
                    <div class="PurchaseData">                
                        <b class="textlabel">CIDADE / ESTADO:</b>
                        <span id="city_state" class="CustomerData">&nbsp;</span>
                    </div>
                   
                    
                    <hr />

                    <!-- CONTRACT ISSUE DATE -->
                    <div class="PurchaseData">                
                        <b class="textlabel">DATA EMISS&Atilde;O CONTRATO:</b>
                        <span id="levels_ammount" class="CustomerData">
							<input type="text" id="dataemissaocontrato" name = "dataemissaocontrato" value="<? echo ( date('d/m/Y') ); ?>">
                        </span>
                    </div>


                    <!-- LEVELS LIST -->
                    <div class="PurchaseData">                
						<b class="textlabel">MODULO / CURSO:</b>
                        <span id="num_levels2" class="CustomerData">                            
                            <div style="display:table">
                                <?php echo ($objContract->listAvailablesCurses()); ?>
                            </div>
                        </span>
                    </div>



                    <!-- LEVELS AMMOUNT -->
                    <div class="PurchaseData">                
                        <b class="textlabel">TOTAL MODULOS:</b>
                        <span id="levels_ammount" class="CustomerData">
	                        <input type="text" autocomplete="off" name="txtValorDocumento" id="txtValorDocumento" size="20" readonly>
                        </span>
                    </div>

                    
					<!-- EXPIRE CREDIT CARD -->
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
							<?= $objContract->printYears();?>
						</select>

                        <span style="margin-left:75px">
	                        <button id="btnSelectQuota">
	                            <img src="<?php echo(BASE_URL); ?>img/fee_number.png" width="20px" height="20px">&nbsp;Selecionar Parcelas
	                        </button>
                        </span>


                        <div id="divMsgExpireDate" style="display:none;" class="smallError">Deve selecionar m&egrave;s e ano de vencimento</div>
                        <div id = "outputPayment" class="msgPayment"></div>
					</div>                    
                    
                    <div class="PurchaseData">                        
                        <input type="hidden" id="txtQuantidadeParcelas" name="txtQuantidadeParcelas" size="2" class="TextBoxCompras" maxlength="2" readonly >
                        
                        <span id="divMsgValueFee" class="smallError">Deve digitar o valor para N&uacute;mero de Parcelas</span>						
                    </div>                    
                    
                    <!-- CREDIT CARD FLAG / BRAND -->
                    <div class="PurchaseData">                
                        <b class="textlabel">BANDEIRA CART&Atilde;O:</b>
                        <span id="levels_ammount" class="CustomerData">
                        	<select id="cboCCIssuer" name = "cboCCIssuer">
                            	<option selected value=""></option>
                                <option value="VISA">VISA</option>
                                <option value="MASTER CARD">MASTER CARD</option>
                                <option alue="AMEX">AMEX</option>                                
                            </select>
                        </span>
                    </div>

                    <!-- CREDIT CARD NUMBER -->
                    <div class="PurchaseData">                
                        <b class="textlabel">N&Uacute;MERO CART&Atilde;O:</b>
                        <span id="levels_ammount" class="CustomerData">
                        	<input type="text" name="firstCCNum" id="firstCCNum" maxlength="4" size="4">
                            ********
                            <input type="text" name="lastCCNum" id="lastCCNum" maxlength="4" size="4">                        
                        
                        </span>
                    </div>

                    
                    <hr />
                    
                    <div class="PurchaseData">                
                        <b class="textlabel">&nbsp;</b>
                        <span id="printContract" class="CustomerData">
                        	<button id="btnPrintContract">
                            	<img src="<?= BASE_URL ?>img/print.png" width="16px" height="16px" style="padding-right:5px;margin-top:5px">
                                Imprimir Contrato
                            </button>
                        </span>
                    </div>
                    
                    
                    
				</div>                                
			</fieldset>
			<br />
            
		</td>
	</tr>
</table>


<div id="dialogPrint" title="Impress&atilde;o de contrato">
    <div id="textContract" style="border:5px" >				
	</div>	    
</div>


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
                            <input disabled name="optFeeNum" id="optFee<?= $labelInterest ?>Num<?= $i ?>" type="radio" value="<?= $i ?>" class="optNumParcela" />
                            <label for = "optFee<?= $labelInterest ?>Num<?= $i ?>" style="text-decoration:line-through; color:#CFCFCF" id = "lblQuote<?= $labelInterest.strval($i) ?>">
                                <? if ($labelInterest == "Rate") {?>
                                	Ent&nbsp;<span class="ammountDialog" id="downPayment<?= $i ?>" data-a-sign="R$ " data-a-dec="," data-a-sep="."></span>
                                    &nbsp;+&nbsp;
                                <? } ?>
                                <?= $i.' x '; ?>  
                                <span class="ammountDialog" id="feeAmmount<?= $labelInterest.$i ?>" data-a-sign="R$ " data-a-dec="," data-a-sep="."></span>
                                <span id="lblMsgRate"><?= ($i > 1 || $labelInterest == "Rate") ? $objContract->getMessageRate($labelInterest == "Rate" ) : '';  ?></span>
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

</body>
</html>
