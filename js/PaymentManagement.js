//jQuery definition
$(function($){
	$("#btnBackQuote").button();
	$("#btnRetryPayment").button();
	$("#btnPrint").button();
	$("#btnProcessPayment").button();
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
	
	

})

function VerificaFormaPagto()
{
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
//-->