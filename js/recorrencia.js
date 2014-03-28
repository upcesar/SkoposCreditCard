$(function($){

	$("#btnReprocess").hide();
	$("#chkSelectAll").removeAttr('checked');
	$(".check-num-record").removeAttr('checked');
	
	
	$(".btnedit").click(
		function(event){
			$('#txtNumTrans').val($(this).data('id_transacao').toString() );
			$('#txtCustNum').val(pad($(this).data('id_cliente').toString(), 6) );
			$('#txtNumBrand').val(pad($(this).data('id_loja'), 2) );
			$('#listNumRecord').val('');
			
			
			$("#frmPostCartao").attr('action', 'alterar-cartao/');			
			$("#frmPostCartao").submit();			
		}
	);
	
	$("#btnReprocess").click(
		function(event){
			getReprocessSelected();			
			
			$("#frmPostCartao").attr('action', 'enviorecorrencia.php');			
			$("#frmPostCartao").submit();			
		}
	);
	
	$("#chkSelectAll").change(
		function(event){
			if($(this).attr('checked')){
				$('.check-num-record').attr('checked', 'checked');
				$("#btnReprocess").show();				
			}
			else{
				$('.check-num-record').removeAttr('checked');
				$("#btnReprocess").hide();
			}
			
		}
	);
	
	$(".check-num-record").change(		
		function(event){
			var allSelected = true;
			var noneSelected = true;
			
			if($(this).attr('checked') !== undefined)
				$('#btnReprocess').show();
			
			
			$('.check-num-record').each(function(){			
				if($(this).attr('checked') === undefined){
					$("#chkSelectAll").removeAttr('checked');
					allSelected = false;
					//return false;
				}
				else{
					noneSelected = false;
				}
				
			});
			
			if(allSelected){
				$("#chkSelectAll").attr('checked', 'checked');
			}
			
			if(noneSelected){
				$("#btnReprocess").hide();
			}
					
		}
	);
	
	
	
}
);

function pad (str, max) {
  str = str.toString();
  return str.length < max ? pad("0" + str, max) : str;
}

 

function getReprocessSelected(){
	var listRecord = '';
	$('#txtNumTrans').val('');
	$('#txtCustNum').val('');
		
	$('.check-num-record').each(function(){			
			if($(this).attr('checked') !== undefined){
				listRecord += $(this).attr('id') + ',';
			}
		}
	);

	if(listRecord != '')
		listRecord = listRecord.substr(0, listRecord.lastIndexOf(','));	
	
	
	$('#listNumRecord').val(listRecord);	
}
