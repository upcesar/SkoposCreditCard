
$(function($){

	$("#btnSearchCust").button();
	$("#ContractData").hide();
	$("#divMsgContractSearch").hide();
	$("#btnPrintContract").button();

	$("#divCreditFlag").hide();
	$("#divMsgCreditCard").hide();
	$("#divSchoolFlag").hide();
	
	$("#divMsgIssueDate").hide();
	$("#productChosser").hide();
	

	$(".ammountProduct").autoNumeric();

	$("#txtCustNum").val('');		//Clear Quote value for new input.
	$("#txtNumBrand").val('');		//Store value for payment process.
	$("#txtCustNum").select();

	$( "#dataemissaocontrato" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "dd/mm/yy"
    });
			

	$("#firstCCNum").autoNumeric(
		{aSep: '', 
		 mDec : 0,
		 vMin : 0,
		 vMax : 9999
		}

	);

	$("#lastCCNum").autoNumeric(
		{aSep: '', 
		 mDec : 0,
		 vMin : 0,
		 vMax : 9999
		}
	);


	$("#txtValorDocumento").autoNumeric('set', 0);

	$("#btnSearchCust").click(
		function(event){
			
			var valid = validateCustomer();
			if(valid)
				searchCustomer();
			else{
				$("#CustomerData").hide();				
			}
			clearControls();
			event.preventDefault();
		}
	);
		

	$( "#dialogPrint" ).dialog({		
			autoOpen: false,
			resizable: false,
			modal: true,
			position:['middle',80],
			width: 'auto',
			open: function(event, ui) { 								
				var ser_data = $("input").serialize();
				$(".ui-dialog-titlebar-close").hide(); 											  
			  	$( "#dialogPrint" ).dialog( "option", "width", 750 );
			  	$( "#dialogPrint" ).dialog( "option", "height", 600 );
			  
			  	// Load contract from template.
				$.ajax(
						{
						url: "contract_template.php",
						type: "POST",
						timeout: 600000, 
						data: ser_data, 
						dataType: "html"						
						}
			
					)
					.done(function(returnData) { 			
						$("#textContract").html(returnData);
					})
					.fail(function(xhr, ajaxOptions, thrownError) {
						var msgerr = "<h1 class = 'mediumError'>Erro carregando a p&aacute;gina solicitada. <br>" + 
												"Status: " + xhr.status + "<br>" +
												"Detalhe Erro: " + thrownError +
												"</h1>";

						$("#textContract").html(msgerr);
					})
					.always(function() { 						
						});
			},
			
			close: function( event, ui ) {

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
				// $("button").blur();
			},
			buttons: {
				"Imprimir": function() {
					printContract();
				},
				"Cancelar": function() {
				  $( this ).dialog( "close" );
				}
			  },
			position:['middle',40],
			closeOnEscape: false,
		});

	$("#btnPrintContract").click(
		function(event){
			
			//Validate data
			var valid = validateContract();
			if(valid){
				// Hardcoded				
				var selectedKit = $("#cboFlag").val() == "Y " ? "#product_101_00000000351" : "#product_101_00000001062";			
				
				// Find by product			
				var valorModulo = 	$(selectedKit).find('.ammountProduct').autoNumeric('get');
				
				var valorEntrada = 0;
				var valorParcela = $('#outputFeeAmmount').autoNumeric('get');
				var operadoraCartao = $('#cboCCIssuer').val();
				//$('#cboFlag > [value = "YT"]').text()
				var bandeiraEscola = $('#cboFlag > [value = "'+ $('#cboFlag').val() + '"]').text();
				
				if($('body').find('#outputDownPayment').length > 0)
					valorEntrada = $('#outputDownPayment').autoNumeric('get');
				
				$('#valormodulo').val(valorModulo);
				$('#valorentrada').val(valorEntrada);
				$('#valorparcelas').val(valorParcela);
				$('#operadoracartao').val(operadoraCartao);
				$('#bandeira').val(bandeiraEscola);
								
				$("#dialogPrint").dialog( "open" );
			}
			event.preventDefault();
		}
	);

	
	$('.chkProduct').change(
		function(event){
			checkProduct();
		}
	);
	
	$("#cboFlag").change(
			function(event){								
				
				if($( this ).val () != "" ){				
					$("#productChosser").show();
					showProductList($( this ).val());
				}
				else
					$("#productChosser").hide();
				
				$('.chkProduct').prop('checked', false);
				checkProduct();				
			}
	);

					
}).bind("keyup keydown", function(e){
    if(e.ctrlKey && e.keyCode == 80){
        return printContract();
    }
});;


function printContract(){
	var opt = { mode : "iframe", 
				popClose : true };
	
	if($( "#dialogPrint" ).dialog( "isOpen" ) ) {
		$( "#dialogPrint" ).dialog( "close" );		
		$( "#textContract" ).printArea( opt );
		return false;
	}
	return true;

}

function validateContract(){
		

	var valDoc = $("#txtValorDocumento").autoNumeric('get') !="" ? parseFloat( $("#txtValorDocumento").autoNumeric('get') ) : "";
	var validZeroValDoc = checkNoZeroValue("#divMsgValuePayment", valDoc, "Pagamento");
	var validEmptyExpDate =  validateEmptyExpireDate();
	var validExpDate =  validateExpireDate();
	var isvalid = true; 

	if($("#dataemissaocontrato").val()=="") {
		showError("#divMsgIssueDate");
		isvalid = false;
	}	


	if(!(validZeroValDoc && validEmptyExpDate && validExpDate)) {
		isvalid = false;
	}	

	if($("#cboFlag").val()==""){
		showError("#divSchoolFlag");
		isvalid = false;
	}
		
	// Validate credit card flag.
	if($("#cboCCIssuer").val()==""){
		showError("#divCreditFlag");
		isvalid = false;
	}
	
	//Validate credit card
	if($("#firstCCNum").val()=="" || $("#lastCCNum").val()==""){
		showError("#divMsgCreditCard");
		isvalid = false;
	}
	
	// Validate installment
	if($("#txtQuantidadeParcelas").val()=="" || $("#txtQuantidadeParcelas").val()=="0"){
		showError("#divMsgValueFee");
		isvalid = false;
	}
		
	return (isvalid);

}

function searchCustomer(withTransaction){
	//Pad quotation before sending
	var padded_cust = $("#txtCustNum").val().lpad("0", 6);
	var padded_brand = $("#txtNumBrand").val().lpad("0", 2);
		
	withTransaction = (withTransaction === undefined) ? false : withTransaction; 
	
	$("#txtCustNum").val(padded_cust);
	$("#txtNumBrand").val(padded_brand);

	var ser_data = $("#frmCustomerParam").serialize();	
	
	$("#divMsgQuotation").hide();
	$("#msgWait").html("Procurando cliente...");
	$("#imgLoading").show();

	$("#ContractData").hide();

	$.ajax(
			{
			url: "searchCustomer.php",
			type: "POST",
			timeout: 600000, 
			data: ser_data, 
			dataType: "json"
			
			}

		)
		.done(function(returnData) { 			
			var rs = returnData.queryresult;
			
			var nl2br = (function(str, is_xhtml) {
				var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
				return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
			});	
					
			if(returnData.reccount > 0){			
				
				populateCustomer(rs);
				
				if(withTransaction == false){
					$("#ContractData").show();
					$("#txtCustNum").val('');		//Clear Quote value for new input.
					$("#txtNumBrand").val('');		//Store value for payment process.
				}					
				else			
					searchTransaction();
					
					
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
			if(withTransaction == false){ 						
				$("#imgLoading").hide();
				$("#txtCustNum").select();
			}
			});	



}

function showProductList(flag){
	var selectorModules = "";
	if(flag.toUpperCase()  =="YT"){
		$("#youmoveProd").hide();
		$("#youmoveteensProd").css('display','table');
		$("#quantidademodulos").val("3");
		selectorModules = '#youmoveteensProd';
		
	}
	else{
		$("#youmoveProd").css('display','table');
		$("#youmoveteensProd").hide();
		selectorModules = '#youmoveProd';		
	}
	
	var countModules = $(selectorModules).children().length - 1;
	$("#quantidademodulos").val(countModules);
	
}

function validateCustomer(){
	var msg = "";
	$("#divMsgContractSearch").hide();	
	if($("#txtCustNum").val().length == 0 && $("#txtNumBrand").val().length == 0){
		msg = "Deve digitar o n&uacute;mero de Cliente e Loja";
		$("#txtCustNum").select();
	}
	else if ($("#txtCustNum").val().length == 0){
		msg = "Deve digitar o n&uacute;mero de Cliente";
		$("#txtCustNum").select();
	}	
	else if ($("#txtNumBrand").val().length == 0){
		msg = "Deve digitar o n&uacute;mero Loja";
		$("#txtNumBrand").select();
	}
	
	if(msg != ""){
		$("#divMsgContractSearch").html(msg);
		showError("#divMsgContractSearch");
		return false;
	}

	return true;

}

function populateCustomer(rs){
	var frow = rs[0];
	var numdoc = "";
	var branch = frow["MT3_CODFIL"] + ' - ' + frow["MT3_FIL"];
	
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
	$("#county").html(frow["BAIRRO"]);
	$("#city_state").html(frow["CIDADE"] + ' / ' + frow["ESTADO"]);
	

	// Populate hidden tags
	$("#nomecliente").val(frow["CLIENTE"]);
	$("#nacionalidadecliente").val(frow["NACIONALIDADE"]);
	$("#profissaocliente").val(frow["PROFISSAO"]);
	$("#estadocivilcliente").val(frow["ESTADO_CIVIL"]);
	$("#rgcliente").val(frow["RG"]);
	$("#cpfcliente").val(numdoc);
	$("#enderecocliente").val(frow["ENDERECO"]);
	$("#bairrocliente").val(frow["BAIRRO"]);
	$("#cidadecliente").val(frow["CIDADE"]);	
	$("#estadocliente").val(frow["ESTADO"]);		
	$("#emailcliente").val(frow["E_MAIL"]);
}

function checkProduct(){
	checked_kit = false;
	ammount = 0;
	$('.chkProduct').each(function(){
		var productCode = $(this).val();
		var checked = $(this).prop('checked');
		var divProduct = $('#product_' + productCode.toString().replace('.','_') );

		// KIT YOUMOVE OR KIT YOU MOVE TEENS
		if(checked && (productCode == '101.00000000001' || productCode == '101.00000001020') ){
			checked_kit = true;					
			
			ammount = parseFloat(divProduct.find('.ammountProduct').autoNumeric('get') );
		}
		else{
			if(checked_kit){
				$(this).prop('checked', false);
			}
			else{
				if(checked){
					
					ammount += parseFloat( divProduct.find('.ammountProduct').autoNumeric('get') );
				}
			}
		}
		
	});
	$("#txtValorDocumento").autoNumeric("set", ammount);
	updatePayment();	
}

function clearControls(){
	
	$('#cboFlag').val("");	
	$('.chkProduct').prop('checked','false');	
	$('.ammountDialog').prop('checked','false');
	checkProduct();
	
	$('.optNumParcela').prop('checked','false');
	
	$('#cboCCIssuer').val("");
	$('#firstCCNum').val("");
	$('#lastCCNum').val("");
	$('#cboExpireMonth').val("");
	$('#cboExpireYear').val("");
	
	
	
	$('#dataemissaocontrato').val(
		$('#ServerDay').val() + "/" + 
		$('#ServerMonth').val() + "/" +
		$('#ServerYear').val() 
	);
	
	
	$("#productChosser").hide();
	$("#youmoveProd").hide();
	$("#youmoveteensProd").hide();

	
		
	$('.chkProduct').prop('checked', false);
	checkProduct();					
}