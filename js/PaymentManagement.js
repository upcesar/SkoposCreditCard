//jQuery definition
$(function($){
	rsTrans = {};

	$("#divMsgSelecao").hide();
	$("#btnSearchCollect").button();
	$("#btnRecollect").button();	
	$("#btnBackQuote").button();
	$("#btnRetryPayment").button();
	$("#btnPrint").button();
	$("#btnProcessPayment").button();
	$("#lblCollectAmmount").autoNumeric('init');
	$("#btnProcessPayment").click(
		function(event){
			var validated = validatePayment();
			if(!validated)
				event.preventDefault();
		}
	);
	
	$("#btnRetryPayment").click(
		function(event){
			event.preventDefault();
			$('#frmRetry').submit();
		}
	);

	$("#btnPrint").click(
		function(event){
			event.preventDefault();
			window.print();
		}
	);
	
	$("#btnSearchCollect").click(
		function(event){
			var valid = validateCustomer();
			if(valid){				
				$("#btnSearchCollect").prop('disabled','disabled');
				searchCustomer(true);
			}
			else{
				$("#CustomerData").hide();				
			}
			clearControls();
			
			event.preventDefault();			
		}
	);
	
	$('body').on('change', '#cboPaymentForm', function() {
    	var i = $(this).prop('selectedIndex') - 1; 
    	if(i >= 0){
    		$("#lblCollectDate").html(rsTrans[$(this).val()].DATA_COBRANCA);
    		$("#lblCollectAmmount").autoNumeric('set',rsTrans[$(this).val()].SALDO);
    		$("#AnoUltimaParcela").val(rsTrans[$("#cboPaymentForm").val()].ANO_ULTIMA_PARCELA);
			$("#MesUltimaParcela").val(rsTrans[$("#cboPaymentForm").val()].MES_ULTIMA_PARCELA);    		
			$("#TransacaoAnt").val($("#cboPaymentForm").val());
			
    	}
    	else{
    		$("#lblCollectDate").html("-");
    		$("#lblCollectAmmount").autoNumeric('set', 0);
			$("#AnoUltimaParcela").val('');
			$("#MesUltimaParcela").val('');
			$("#TransacaoAnt").val('');
			$("#TipoVenda").val('RECOBRANCA');
			
    	}
    	$("#TipoVenda").val('RECOBRANCA - TRANSACAO ANTERIOR ' + $("#TransacaoAnt").val());
    	//rsTrans    	
	});
	
	$("#btnRecollect").click(
		function(event){
			
			
			found = $("body").find("#cboPaymentForm").length;
			var i = $("#cboPaymentForm").prop('selectedIndex');
			if(i > 0 | found == 0){				 
				var valid = validateRecollect(found);
				if(valid){
					$("#ValorDocumento").val($("#lblCollectAmmount").autoNumeric('get'));
					$("#ValorDocumentoExibicao").val($("#lblCollectAmmount").html());
					
					$("#MesValidade").val($("#cboExpireMonth").val());
					$("#AnoValidade").val($("#cboExpireYear").val());
					
					if(found > 0)
						$("#NumeroDocumento").val(rsTrans[$("#cboPaymentForm").val()].ORCAMENTO);
					else
						$("#NumeroDocumento").val($('#old_cc').text().trim().substr(5,6).trim());
				}
				else
					event.preventDefault();
			}
			else{
				showError("#divMsgSelecao");
				event.preventDefault();
			}
			
		}
	);


});

function validateRecollect(){
	var validEmptyExpDate =  validateEmptyExpireDate();
	var validExpDate =  validateExpireDate();
	var validLastIntmt = validateExpLastInstallment();
	
	
	return(validEmptyExpDate && validExpDate && validLastIntmt); 
}

function validateExpLastInstallment(){
	var curDate = 0;
	var selectedDate = 0;
	
	expDate = (12 * $("#AnoUltimaParcela").val() ) + $("#MesUltimaParcela").val();
	selectedDate = (12 * $("#cboExpireYear").val() ) + $("#cboExpireMonth").val();
	
	if(selectedDate < expDate){
		$("#divMsgExpireDate").html("Vencimento cart&atilde;o deve ser maior ou igual a " + $("#MesUltimaParcela").val() + "/" + $("#AnoUltimaParcela").val());
		showError("#divMsgExpireDate");
		return false;
	}
	return true;
	
}

function searchTransaction() {
	var ser_data = $("#frmCustomerParam").serialize();
	
	$("#msgWait").html("Procurando dados de transa&ccedil;&otilde;es...");
	
	$.ajax(
			{
			url: "searchPendingRecurring.php",
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
				populateTransaction(rs, returnData.reccount);
				$("#ContractData").show();
				$("#txtCustNum").val('');		//Clear Quote value for new input.
				$("#txtNumBrand").val('');		//Store value for payment process.				
			}
			else{
				$("#divMsgContractSearch").html(rs);
				$("#divMsgContractSearch").show();
				$("#custcode").html("");		
			}
			
		})
		.fail(function(xhr, ajaxOptions, thrownError) {
			var msgerr = "<h1 class = 'mediumError'>Erro carregando a p&aacute;gina solicitada. <br>" + 
									"Status: " + xhr.status + "<br>" +
									"Detalhe Erro: " + thrownError +
									"</h1>";
			alert(msgerr);
			$('#divMsgCustomer').html("");
			$('#divMsgCustomer').html(msgerr);
			$("#custcode").html("");
		})
		.always(function() { 						
			$("#imgLoading").hide();
			$("#txtCustNum").select();
			$("#btnSearchCollect").removeAttr('disabled');
			});	
}

function populateTransaction(rs, reccount){
	
	$("#old_cc").html("");
	contentCombo = "";
	
	rsTrans.length = 0;

	
	if(reccount > 1){
		
		contentCombo += '<select id="cboPaymentForm" ><option selected />';
						
		$.each(rs, function(){
			contentCombo += '<option id="tran_' + this.TRANSACAO + '" value = "' + this.TRANSACAO + '">ORC ' + this.ORCAMENTO + ' | CARTAO ' + this.MASK_CC + '</option>';
			rsTrans[this.TRANSACAO] = { "ORCAMENTO": this.ORCAMENTO, 
										"DATA_COBRANCA" : this.DATA_COBRANCA, 
										"MES_ULTIMA_PARCELA" : this.MES_ULTIMA_PARCELA, 
										"ANO_ULTIMA_PARCELA" : this.ANO_ULTIMA_PARCELA,
										"SALDO" : this.SALDO
										};
			}
		);
		
		contentCombo += '</select>';
		
		
	}
	else{
		contentCombo = 'ORC <span id="numOrcamento">' + rs[0].ORCAMENTO + '</span> | CARTAO ' + rs[0].MASK_CC;
		$("#lblCollectDate").html(rs[0].DATA_COBRANCA);		
		$("#lblCollectAmmount").autoNumeric('set', rs[0].SALDO);
		$("#ValorDocumento").val(rs[0].SALDO);
		$("#ValorDocumentoExibicao").val($("#lblCollectAmmount").autoNumeric('get'));
	}
	$("#old_cc").html(contentCombo);
	
}

function VerificaFormaPagto() {
	
	var objForm = document.getElementById('frmPedido');
	var objDivNumeroCartao = document.getElementById('divNumeroCartao');
	var objDivSelCard = document.getElementById('divSelCard');
	var objDivMesVal = document.getElementById('divMesVal');
	var objDivAnoVal = document.getElementById('divAnoVal');
	var objDivCodSeg = document.getElementById('divCodSeg');
	var objDivNumParc = document.getElementById('divNumParc');
	var objDivDadosCartao = document.getElementById('divDadosCartao');
	var objdivMsgCreditNum = document.getElementById('divMsgCreditNum');

	document.getElementById('NumeroCartao').maxLength = 20;
	document.getElementById('CodigoSeguranca').maxLength = 3;
	
	//Set default
	objDivDadosCartao.style.display = 'block';
	objDivNumeroCartao.style.display = 'block';
	objdivMsgCreditNum.style.display = 'none';
	objDivMesVal.style.display = 'block';
	objDivAnoVal.style.display = 'block';
	objDivCodSeg.style.display = 'block';


	//Hide layers related with images.
	document.getElementById("img_visa").style.display = "none";
	document.getElementById("img_mastercard").style.display = "none";
	document.getElementById("img_amex").style.display = "none";
	/*
	document.getElementById("img_diners").style.display = "none";		
	document.getElementById("img_hipercard").style.display = "none";
	document.getElementById("img_aura").style.display = "none";
	*/
	
	//Clear security code whether the value has changed.
	document.getElementById('CodigoSeguranca').value = '';
	
    switch (objForm.Bandeira.options[objForm.Bandeira.selectedIndex].value)
    {
		case 'VISA':
         //Visa
			//objdivMsgCreditNum.style.display = 'block';
			//document.getElementById('NumeroCartao').maxLength = 6;
			document.getElementById("img_visa").style.display = "block";
			document.forms[0].FormaPagto.value = 'VISA';			
			break;
		case 'MASTERCARD':
			//Master
			document.forms[0].FormaPagto.value = 'MASTERCARD';
			document.getElementById("img_mastercard").style.display = "block";
			break;
		/*
		case 'DINERS':
			//Diners
			document.getElementById("img_diners").style.display = "block";			
			document.forms[0].FormaPagto.value = 'DINERS';
			break;
		*/
		case 'AMEX':
			//Amex
			document.getElementById("img_amex").style.display = "block";
			document.getElementById('CodigoSeguranca').maxLength = 4;
			document.forms[0].FormaPagto.value = 'AMEX';
			break;		
		/*
		case 'HIPERCARD':
			//Hipercard
			document.getElementById("img_hipercard").style.display = "block";			
			document.forms[0].FormaPagto.value = 'HIPERCARD';
         break;		
		case 'AURA':		
			//Aura
			document.getElementById("img_aura").style.display = "block";			
			document.forms[0].FormaPagto.value = 'AURA';
			break;		
		*/
		default:
			objDivDadosCartao.style.display = 'none';
			objDivNumeroCartao.style.display = 'none';
			objdivMsgCreditNum.style.display = 'none';
			objDivMesVal.style.display = 'none';
			objDivAnoVal.style.display = 'none';
			objDivCodSeg.style.display = 'none';
			document.forms[0].FormaPagto.value = '';
			Bandeira.focus();
			return(false);
    }
}


/*
function EfetuaPOST()
{
	var objForm = document.getElementById('frmPedido');
	var mpg_popup;
	
	if ((objForm.Bandeira.options[objForm.Bandeira.selectedIndex].value) == "VISA")
	{
		window.location = "https://teste.aprovafacil.com/cgi-bin/STAC/klinos/APC/";						   
		mpg_popup = window.open("", "mpg_popup","toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=0,resizable=0,screenX=0,screenY=0,left=0,top=0,width=800,height=600");
		document.getElementById('frmPedido').target = 'mpg_popup';
		//window.location = "http://www.seusite.com.br/htmls/redirecionaCBP.html";
		
		return(true);
	}
	else
	{
		document.getElementById('frmPedido').target = '_self';
		return(true);		
	} 	 
	
	return(validate());		
}
*/

function validatePayment()
{
	var validated = true;
	
	$("#divMsgCreditNum").hide();
	$("#divMsgMonth").hide();
	$("#divMsgYear").hide();
	$("#divMsgSecNum").hide();
	
	// Credit card nomber must have all digits
	if($("#NumeroCartao").val().length < $("#NumeroCartao").attr("maxLength")){
		$("#NumDigitCC").html($("#NumeroCartao").attr("maxLength"));
		$("#divMsgCreditNum").show();
		validated = false;
	}
	
	// Months must have been chosen.
	if($("#MesValidade").val().length == 0){
		$("#divMsgMonth").show();
		validated = false;	
	}

	// Year must have been chosen.
	if($("#AnoValidade").val().length == 0){
		$("#divMsgYear").show();
		validated = false;	
	}
	
	// Security code must have all digits
	if($("#CodigoSeguranca").val().length < $("#CodigoSeguranca").attr("maxLength"))
	{
		$("#NumDigitSec").html($("#CodigoSeguranca").attr("maxLength"));
		$("#divMsgSecNum").show();
		validated = false;
	}
	

	return (validated);
}



function teclado(qtdeCaracter, tecla)
{
	var quantidade, tecla;
	quantidade = document.getElementById(qtdeCaracter);

	if (quantidade.maxLength > quantidade.value.length)
	  quantidade.value = quantidade.value + tecla;
}

function backspace(qtdeCaracter)
{
	var quantidade = document.getElementById(qtdeCaracter);	
	quantidade.value = quantidade.value.substring(0, quantidade.value.length - 1);	
}
