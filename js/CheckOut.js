var recurrentPayment = false;  // Global variable.
$(function($){
	//Constrols Definition	
	$("#btnSearch").button();
	//Hide elements while loading
	$("#divMsgQuotation").hide();
	$("#QuotationData").hide();
	$("#PaymentData").hide();
	$("#SaleType").hide();	
	$("#QuoteNotFound").hide();
	$("#divMsgValuePayment").hide();
	$("#divMsgValueFee").hide();
	$("#imgLoading").hide();

	$("#btnSelectQuota").button();	
	$("#btnPay").button();	
		
	$("input[type=text]").click(function() {
	   $(this).select();
	});	
	
	$("#txtNumQuote").val('');
	$("#txtQuantidadeParcelas").val('');
	$("#txtNumQuote").select();
	
	$("#cboExpireMonth").val('');
	$("#cboExpireYear").val('');
	
	$("input").bind("keydown", function (event) {
            var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
			if (keycode == 13) {
                
				if($("#QuotationData").is(":hidden") | $("#txtNumQuote").is(":focus"))
					document.getElementById('btnSearch').click();
				else
					document.getElementById('btnPay').click();
				
				
				return false;
            } else {                
                return true;
            }
        });
		
	
	//Set textboxes to numeric inputs only.
	$("#txtValorDocumento").autoNumeric({aSep: '.', 
										 wEmpty: 'zero',
										 aSign: "R$ ",
										 aDec: ',',
										 mDec: 2,
										 vMin : 0
										});
	
	
	$("#txtQuantidadeParcelas").autoNumeric({aSep: '.', 
											 aDec: ',', 
											 mDec : 0,
											 vMin : 0,
											 vMax : 12
											});

	$("#SaldoRestante").autoNumeric({
										 wEmpty: 'zero',										 
										 aDec: '.',
										 vMin : 0
										});

	$("#FinalPayment").autoNumeric({aSep: '.', 
									 wEmpty: 'zero',
									 aSign: "R$ ",
									 aDec: ',',
									 mDec: 2,
									 vMin : 0
									});

//Sale Type Dialog	

	$( "#dialogFeeNumber" ).dialog({		
			autoOpen: false,
			resizable: false,
			modal: true,
			position:['middle',80],
			width: 'auto',
			open: function(event, ui) { 								
				$(".ui-dialog-titlebar-close").hide(); 				
				$("#msgSelFee").hide();
				
				if( $('#txtQuantidadeParcelas').val() == "" || $('#txtQuantidadeParcelas').val() == "0" )
					$('.optNumParcela').prop('checked', false);
				
				
				var downPayment = calculatePercent($('#txtValorDocumento').autoNumeric('get'), $('#DownPayment').val()) ;
								
				//Calculate fee ammount for each option.				
				$('[name="optFeeNum"]').each(function(){
					
					var selector = "#feeAmmount" + $(this).val().toString();
					var selectorDownPayment = "#downPayment" + $(this).val().toString();
					var principalAmmount = $('#txtValorDocumento').autoNumeric('get');
					recurrentPayment = false;
					if($(this).attr('id').indexOf("Rate") >= 0){						
						expirePeriod = getDifferenceMonth("#CutOffMonth", "#CutOffYear");
						//The difference's gap between 
						curPeriod = parseInt($(this).val()) - 2;  
						selector = "#feeAmmountRate" + $(this).val().toString();
						selectorLabel = "#lblQuoteRate" + $(this).val().toString();
						selectorOpt = "#optFeeRateNum" + $(this).val().toString();
						
						recurrentPayment = true;					
						// Disable and strike when installment is greater than expire date minus today date or
						// Branch is Wise Up Teens. 
						if((expirePeriod < 24 && curPeriod >= expirePeriod ) || $("#EnableRecurring").val() == 0 ){
							$(selectorOpt).attr('disabled','disabled');
							$(selectorLabel).css('text-decoration','line-through');
							$(selectorLabel).css('color','#CFCFCF');
						}
						else{
							$(selectorOpt).removeAttr('disabled');
							$(selectorLabel).css('text-decoration','');
							$(selectorLabel).css('color','');
						}
						principalAmmount = principalAmmount - calculatePercent(principalAmmount, $('#DownPayment').val()) ;						
					}
					else{
						discountValue = checkDiscountRule($(this).val());
						//discountValue = $('#DiscountRegSale').val();
						principalAmmount = principalAmmount - calculatePercent(principalAmmount, discountValue) ;
						
					}
					var feeAmmount = calculateQuoteValue(principalAmmount, $(this).val(), recurrentPayment);
					
					$(selector).autoNumeric();		
					$(selectorDownPayment).autoNumeric();
					$(selector).autoNumeric('set',  feeAmmount);
					$(selectorDownPayment).autoNumeric('set',  downPayment);
					$("#colSelPayment").css('width','150px');
				}
			  );
			  
			  $( "#dialogFeeNumber" ).dialog( "option", "width", 780 );

			},
			
			close: function( event, ui ) {
				$("#txtValorDocumento").select();
			},
			show: {
        		effect: "fade",
        		duration: 500,
      		},
      		hide: {
        		effect: "fade",
        		duration: 500
      		},
			focus: function( event, ui ) {
				$("button").blur();
			},
			buttons: {
				"Selecionar": function() {
					var selectedText = "";
					var discountText = "";
					var curFeeAmount = "";

					var numQuotes = $('[name="optFeeNum"]:checked').val();
					var valPayment = $("#txtValorDocumento").autoNumeric('get');
					var optionID = $('[name="optFeeNum"]:checked').attr('id');
					
					$("#txtQuantidadeParcelas").val(numQuotes);
					
					if (!(numQuotes === undefined)){				  
						// pick between regular sale or recurrent.
						recurrentPayment = (optionID.indexOf("Rate") >= 0 );												
						updatePayment(numQuotes, valPayment);
					  	$( this ).dialog( "close" );
					}
					else{
						$("#msgSelFee").show();
					}
				},
				"Cancelar": function() {
				  $( this ).dialog( "close" );
				}
			  },
			position:['middle',120],
			closeOnEscape: false,
			width: 420
		});
		

	$('[name="optFeeNum"]').change(
		function(event){
			$("#msgSelFee").hide();
		}
	);

	$("#btnSelectQuota").click(
		function(event){
			event.preventDefault();
			var valDoc = $("#txtValorDocumento").autoNumeric('get') !="" ? parseFloat( $("#txtValorDocumento").autoNumeric('get') ) : "";
			var validZeroValDoc = checkNoZeroValue("#divMsgValuePayment", valDoc, "Pagamento");
			var validEmptyExpDate =  validateEmptyExpireDate();
			var validExpDate =  validateExpireDate();
			if(validZeroValDoc && validEmptyExpDate && validExpDate) {			
				var _width = $("#NumColumns").val() * parseInt($(".columnDialog").css("width"));			
				_width = parseInt(_width) * 1.20;										
				$("#dialogFeeNumber").dialog('option','width', _width);
				$("#dialogFeeNumber").dialog( "open" );
			}
			else
				$("#txtValorDocumento").select();
		}
	);		
	
	//Lostfocus
	$("#txtValorDocumento").focusout(function() {		
		updatePayment();
  	});


	//Keyboard event
	$("#txtValorDocumento").keyup(function() {		
		updatePayment();
  	});


	$("#btnPay").click(
		function(event){
			var valid = processPayment();
			if (valid)
				makeCheckOut(); 
			
			event.preventDefault();
			}
	);
		
	
	$("#tdcbalance").click(function(event){		
		$("#txtValorDocumento").val($("#tdcbalance").html());
		$("#txtQuantidadeParcelas").select();				
		updatePayment();
		event.preventDefault();
	});

	$("#btnSearch").click(
		function(event){
			var valid = validateQuotation();
			if(valid)
				searchQuotation();			
			else{
				$("#QuotationData").hide();				
				$("#SaleType").hide();
				$("#PaymentData").hide();
			}
				
			event.preventDefault();
		}
	);
		
});


function makeCheckOut(){
	
	//Procurando or&ccedil;amento...
	$("#msgWait").html("Enviando dados de pagamento");
	$("#imgLoading").show();
	updatePayment();
	$.ajax({
	  url: "validateWebService.php",
	  dataType:"json"
	  
	})
	.done(function(returnData) {

	  if(returnData.alive)
		$("#frmPedido").submit();
	  else
	  	showMsgInvalidQuote(returnData.strerr);
	})		
	.fail(function(xhr, ajaxOptions, thrownError) {
			var msgerr = "<h1 class = 'mediumError'>Erro carregando a p&aacute;gina solicitada. <br>" + 
									"Status: '" + xhr.status + "<br>" +
									"Detalhe Erro: " + thrownError +
									"</h1>";
			
			showMsgInvalidQuote(msgerr);			
		})
	.always(function (){
			
		});	
}

function validateQuotation(){
	$("#divMsgQuotation").hide();	
		
	if($("#txtNumQuote").val().length == 0){
		$("#divMsgQuotation").html("Deve digitar o n&uacute;mero de Or&ccedil;amento");
		$("#txtNumQuote").select();
		showError("#divMsgQuotation");

		return false;
	}
	return true;
}

function populateCustomerQuote(rs){
	var frow = rs[0];
	var numdoc = "";
	var numQuote = frow["ORCAMENTO"];
	var branch = frow["MT3_CODFIL"] + ' - ' +frow["MT3_FIL"];

	$("#quotenum").html(numQuote);
	
	$("#branch").html(branch);
	
	if(frow["A1_NUMRA"].trim() != "")		
		$("#numra").html(frow["A1_NUMRA"]);
	else
		$("#numra").html("N/A");
	$("#custcode").html(frow["COD_CLIENTE"]);	
	$("#custname").html(frow["CLIENTE"]);	
	$("#doctype").html(frow["TIPODOC"] + ":");
	if(frow["TIPODOC"] == "CPF")
		numdoc = (frow["NUM_DOC"]).formatCPF();
	else
		numdoc = (frow["NUM_DOC"]).formatCNPJ();
	
	$("#docnum").html(numdoc);	
	$("#address").html(frow["ENDERECO"]);
	$("#sumquotation").html((frow["TOTAL_ORCAMENTO"]).formatNumber(2, ',', '.', 'R$'));
	$("#tdcammount").html((frow["TOTAL_CARTAO"]).formatNumber(2, ',', '.', 'R$'));	
	$("#tdcpaid").html((frow["TOTAL_PAGO"]).formatNumber(2, ',', '.', 'R$'));	
	$("#tdcbalance").html((frow["SALDO_RESTANTE"]).formatNumber(2, ',', '.', 'R$'));	
	$("#txtValorDocumento").val((frow["SALDO_RESTANTE"]).formatNumber(2, ',', '.', 'R$'));
	$("#SaldoRestante").val(frow["SALDO_RESTANTE"]);
	$("#EnableRecurring").val(frow["ENABLE_RECURRING"]);
	
	$("#txtNumQuote").val('');					//Clear Quote value for new input.
	$("#NumeroDocumento").val(numQuote);		//Store value for payment process.
	$("#txtQuantidadeParcelas").val('');		//Clear num quotes.
	

}


function searchQuotation(){
	// Pad quotation before sending (if numeric), otherwise upercase values.
	var url_param = "";
	if(!isNaN($("#txtNumQuote").val()) ){	
		var padded_quoted = $("#txtNumQuote").val().lpad("0", 6);	
		$("#txtNumQuote").val(padded_quoted);
	}
	else{
		$("#txtNumQuote").val($("#txtNumQuote").val().toUpperCase());
		url_param = "?erp = p8";
	}
	var ser_data = $("#txtNumQuote").serialize();	
	
	$("#divMsgQuotation").hide();
	$("#msgWait").html("Procurando or&ccedil;amento...");
	$("#imgLoading").show();

	$("#QuotationData").hide();
	$("#SaleType").hide();
	$("#PaymentData").hide();

	$.ajax(
			{
			url: "searchQuotation.php" + url_param,
			type: "POST",
			timeout: 600000, 
			data: ser_data, 
			dataType: "json"			
			}

		)
		.done(function(returnData) { 			
			var rs = returnData.queryresult;
			
			var nl2br = function(str, is_xhtml) {
				var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
				return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
			};			
			if(returnData.reccount > 0){
				// Make post-validation of data.
				if(rs[0]["TOTAL_CARTAO"] <= 0){
					showMsgInvalidQuote("Valor pagamento por cartão deve ser maior a zero.");
				}
				else if(rs[0]["TOTAL_ORCAMENTO"] <= 0){
					showMsgInvalidQuote("Valor orçamento deve ser maior a zero.");
				}					
				else if(rs[0]["SALDO_RESTANTE"] <= 0){
					showMsgInvalidQuote("N&atilde;o tem saldo pendente para pagar com cart&atilde;o");
				}
				else if(rs[0]["STATUS"] != 'A' && rs[0]["STATUS"] != 'S'){
					showMsgInvalidQuote("Orçamento deve estar em Aberto.");
				}
				else{
					populateCustomerQuote(rs);
					$("#QuotationData").show();
					$("#PaymentData").show(); //Enable sale type.
				}
			}
			else{
				$("#divMsgQuotation").html(rs);
				$("#divMsgQuotation").show();
				$("#txtNumQuote").select();
			}
		})
		.fail(function(xhr, ajaxOptions, thrownError) {
			var msgerr = "<h1 class = 'mediumError'>Erro carregando a p&aacute;gina solicitada. <br>" + 
									"Status: " + xhr.status + "<br>" +
									"Detalhe Erro: " + thrownError +
									"</h1>";
			alert(msgerr);
			$('#divMsgQuotation').html("");
			$('#divMsgQuotation').html(msgerr);			
		})
		.always(function() { 						
			$("#imgLoading").hide(); 			
			});	
}

function showMsgInvalidQuote(msg){
	$("#divMsgQuotation").html(msg);					
	$("#txtNumQuote").val('');
	$("#txtNumQuote").select();
	showError("#divMsgQuotation");
}

function checkNoZeroValue(selector, value, msg){
	if(value <= 0){
		$(selector).html(msg + " deve conter um valor maior que zero");
		showError(selector);
		return(false);
	}
	return(true);
}


function showError(layer){
	$(layer).fadeIn("fast",function(){
			$(layer).fadeOut(3800);
			$("#imgLoading").hide();
		});
}

function validatePayment(){
	var numDoc = $("#txtNumQuote").val(),
		valDoc = $("#txtValorDocumento").autoNumeric('get') !="" ? parseFloat( $("#txtValorDocumento").autoNumeric('get') ) : "",
		valFee = $("#txtQuantidadeParcelas").autoNumeric('get') != "" ? parseInt( $("#txtQuantidadeParcelas").autoNumeric('get') ) : "",
		valBalance = $("#SaldoRestante").autoNumeric('get') != "" ? parseFloat( $("#SaldoRestante").autoNumeric('get') ) : "",
		valDocView = $("#txtValorDocumento").val(),
		validZeroValDoc = true,
		validBalance = true,
		valid = true;

	$("#divMsgValuePayment").hide();
	$("#divMsgValueFee").hide();	
	
	validZeroValDoc = checkNoZeroValue("#divMsgValuePayment", valDoc, "Pagamento");
	valid = checkNoZeroValue("#divMsgValueFee", valFee, "Parcela"); 

	valid &= validZeroValDoc;

	if(validZeroValDoc){
		if(valBalance < valDoc){
			$("#divMsgValuePayment").html("Valor Pagamento n&atilde;o deve ser maior ao Saldo");
			$("#txtValorDocumento").select();
			showError("#divMsgValuePayment");
			validBalance = false;
			valid = false;		
		}
	}

	if(!(validZeroValDoc & validBalance))
		$("#txtValorDocumento").select();
	else
		if(!valid)
			$("#txtQuantidadeParcelas").select();

	return (valid);
}

function getDifferenceMonth(selectorMonth, selectorYear){
  	
	var expireMonth = parseInt($("#cboExpireMonth").val());
	var expireYear = parseInt($("#cboExpireYear").val());
	
    var curMonth = parseInt($(selectorMonth).val());
    var curYear = parseInt($(selectorYear).val());
	
	var curDate = (curYear * 12) + curMonth;
	var expireDate = (expireYear * 12) + expireMonth;
	
	return (expireDate - curDate);	
}

function validateExpireDate(){
	var numMonth = getDifferenceMonth("#ServerMonth","#ServerYear");
	if(numMonth < 0){
		$("#divMsgExpireDate").html("Vencimento cart&atilde;o deve ser maior ou igual a " + $("#ServerMonth").val() + "/" + $("#ServerYear").val());
		showError("#divMsgExpireDate");
		return (false);
	}
	return (true);
}

function validateEmptyExpireDate(){
	var expireMonth = $("#cboExpireMonth").val();
	var expireYear = $("#cboExpireYear").val();

	if( expireYear == "" & expireMonth == ""){
		$("#divMsgExpireDate").html("Deve selecionar mês e ano no vencimento");
		showError("#divMsgExpireDate");
		return(false);
	}
	
	if( expireYear == "" ){
		$("#divMsgExpireDate").html("Deve selecionar ano no vencimento");
		showError("#divMsgExpireDate");
		return(false);
	}

	if( expireMonth == ""){
		$("#divMsgExpireDate").html("Deve selecionar mes no vencimento");
		showError("#divMsgExpireDate");
		return(false);
	}


	return(true);
	
}


function setBillingType(selector, valSaleType){	
	var SaleTypeText;
	
	if(valSaleType == "0"){
		SaleTypeText = "Venda Regular";
		valSaleType = "4";
	}
	else {
		SaleTypeText = "Venda Recorrente";
		valSaleType = "5";
	}
	
	$("#CodTipoVenda").val(valSaleType);
	$("#TipoVenda").val(SaleTypeText);
}

function processPayment()
{	
	if(validatePayment()){
													
		valFeeView = $("#txtQuantidadeParcelas").val();
		valTotalCredit = $("#txtValorDocumento").autoNumeric('get');
		valFeeAmmount = $("#outputFeeAmmount").autoNumeric('get');
		valExpireMonth = $("#cboExpireMonth").val();
		valExpireYear = $("#cboExpireYear").val();
				
		if($("#CodTipoVenda").val() == "4"){		
			//valDocView = $("#txtValorDocumento").val() + " - Desconto " + $("#DiscountRegSale").val() +"% = " + $("#FinalPayment").val();
			valDocView = $("#outputTotalAmmount").text();
			valDoc = $("#FinalPayment").autoNumeric('get');
			valFee = $("#txtQuantidadeParcelas").val();
			// valTotalCredit = valDoc;
		}
		else{
			//valDocView = "Entrada de " + $("#FinalPayment").val();
			valDocView = $("#outputDownPayment").text();
			valDownPayment = calculatePercent( $("#txtValorDocumento").autoNumeric('get') , $("#DownPayment").val());
			valDoc = valDownPayment; // + calculateQuoteValue(valRemaining, $("#txtQuantidadeParcelas").val(), true);
			valFee = 1;			
		}		
			
		$("#ValorDocumentoExibicao").val(valDocView);
		$("#QuantidadeParcelasExibicao").val(valFeeView);
		
		$("#ValorDocumento").val(parseFloat(valDoc).toFixed(2));		
		$("#QuantidadeParcelas").val(valFee);
		
		$("#ValorTotalCredito").val(valTotalCredit);
		$("#ValorParcela").val(valFeeAmmount);

		$("#MesValidade").val(valExpireMonth);
		$("#AnoValidade").val(valExpireYear);

			
		return (true);
	}
	return (false);
		
}


function calculateQuoteValue(totalAmmount, quoteNum, withRate){
	var pv = parseFloat(totalAmmount);
	var n = parseInt(quoteNum);
	var i = parseFloat($("#RatePerc").val()) / 100;
	var quoteVal = 0;
	
	if (withRate){
		// French system.
		quoteVal = pv * i * ( Math.pow( 1 + i ,n )) / ( Math.pow( 1 + i , n ) - 1);	
	}
	else{
		quoteVal = pv /  n;
	}
	
	return(quoteVal.toFixed(2));
	
}

function calculatePercent(baseAmmount, percentValue){
	
	return(parseFloat(baseAmmount) * (parseFloat(percentValue) / 100));

}

function updateMessageAmmount(numQuotes, curFeeSTAC, feeAmmount){

	var feeNumber = $("#txtQuantidadeParcelas").val();
	if(feeNumber != ""){
		var selectedText = "";
		var prefixSaleText = "";
		var totalAmmount = "";

		var selectedPag = ""; // $('label[for="' + optionID +'"]').text().trim();
		var msgRate = recurrentPayment ? " c/j" : " s/j";
		var principal = 0;
		
		if(recurrentPayment){
			prefixSaleText = 'Entrada de <span id="outputDownPayment" data-a-sign="R$ " data-a-dec="," data-a-sep=".">' 
							+  '</span> + ';			
		}
		else {
			prefixSaleText = 'Desconto de ' + $("#DiscountRegSale").val() + '% em ';
		}
				
		
		selectedText = prefixSaleText + numQuotes.toString() + 
		   			   ' x <span id="outputFeeAmmount" data-a-sign="R$ " data-a-dec="," data-a-sep="."></span>' 
					   + msgRate 
					   + '&nbsp;=&nbsp;<span id="outputTotalAmmount" data-a-sign="R$ " data-a-dec="," data-a-sep="."></span>';
		
		
		//Output the selection to the main page when Total ammount is greater than zero.
		
		$("#outputPayment").html(selectedText);
		$("#outputFeeAmmount").autoNumeric();
		$("#outputFeeAmmount").autoNumeric('set',  feeAmmount);
		
		if(recurrentPayment){
			$("#outputDownPayment").autoNumeric();
			$("#outputDownPayment").autoNumeric('set',  curFeeSTAC);
			totalAmmount = curFeeSTAC + (parseInt(numQuotes) * parseFloat(feeAmmount ) );
		}
		else{
			totalAmmount = curFeeSTAC;
		}
		$("#outputTotalAmmount").autoNumeric();
		$("#outputTotalAmmount").autoNumeric('set',  totalAmmount);
		
	
		if(curFeeSTAC > 0 & feeAmmount > 0 ){
			$("#outputPayment").show();
		}
		else
			$("#outputPayment").hide();
	}
}

function updatePayment(numQuotes, valPayment) {

	if(numQuotes === undefined)
		numQuotes = $('#txtQuantidadeParcelas').val();
	
	if(valPayment === undefined)
		valPayment = $("#txtValorDocumento").autoNumeric('get');		
		

	if(recurrentPayment) {
		curFeeSTAC = calculatePercent(valPayment, $("#DownPayment").val()); 		
		principalAmmount = valPayment - curFeeSTAC;				
		setBillingType(this, "1");
	}
	else {
		principalAmmount = valPayment - calculatePercent(valPayment, $("#DiscountRegSale").val());
		curFeeSTAC = principalAmmount;
		setBillingType(this, "0");
		$('#DiscountRegSale').val(checkDiscountRule(numQuotes));
	}

	feeAmmount = calculateQuoteValue(principalAmmount, numQuotes, recurrentPayment);
	
	// Store current installment for sending to STAC page.
	$("#FinalPayment").autoNumeric( 'set', curFeeSTAC );

	updateMessageAmmount(numQuotes, curFeeSTAC, feeAmmount);
	
}

function checkDiscountRule(installmentNum){

	valDiscount = 0;


	$.ajax({
	  url: "discount_rules.php",
	  async: false,
	  dataType:"json"
	  
	})
	.done(function(returnData) {
		
		$.each(returnData, function(){
			if(installmentNum >= this.min & installmentNum <= this.max){
				valDiscount = this.discount;
				return false;
			}
		});
		
	})		
	.fail(function(xhr, ajaxOptions, thrownError) {
			valDiscount = $('#DiscountRegSale').val();
		})
	.always(function(){
		});
	
	

	
	return valDiscount;
	
}