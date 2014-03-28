<?php
	require_once '../conf.php';
	
	// QUERY PARA BUSCAR O FILTRO PARA A TELA - ATUALIZAR A QUERY!!!!!!!!!!!!
	
	class ListContracts extends Ometz_Default {
		private $QryArquivos = '';
	
		public function QueryContracts($filter){
			
			$this->QryArquivos  = " SELECT ";
			$this->QryArquivos .= "	VZK_FILIAL , ";
			$this->QryArquivos .= "	VZK_CODORC ORCAMENTO, ";
			$this->QryArquivos .= "	VZK_DATA INCLUSAO, ";
			$this->QryArquivos .= "	VZK_NOMARQ ARQUIVO, ";
			$this->QryArquivos .= "	VZK_CAMARQ CAMINHO, ";
			$this->QryArquivos .= "	VZK_USER RESPONSAVEL";
			$this->QryArquivos .= " FROM ";
			$this->QryArquivos .= "	DB2.VZK500 VZK ";
			$this->QryArquivos .= "	WHERE ";
			$this->QryArquivos .= "		VZK_FILIAL = '  ' ";
			$this->QryArquivos .= $filter;
			$this->QryArquivos .= " 	AND VZK.D_E_L_E_T_ = ' ' ";
			$this->QryArquivos .= " ORDER BY VZK_DATA, VZK_CODORC ";
			
			$response = $this->database->fetchAll($this->QryArquivos);
	
			$Itens = '';
			for ($i = 0; $i <= count($response)-1; $i++) {
				$DataInclusao = substr($response[$i]['INCLUSAO'],6,2).'/'.substr($response[$i]['INCLUSAO'], 4,2).'/'.substr($response[$i]['INCLUSAO'], 0,4);
					
				$Itens .= '<tr>';
				$Itens .= '	<td>'.$response[$i]['ORCAMENTO'].'</td>';
				$Itens .= '	<td>'.$DataInclusao .'</td>';
				$Itens .= '	<td>'.$response[$i]['RESPONSAVEL'].'</td>';
				$Itens .= '	<td>'.$response[$i]['ARQUIVO'].'</td>';
				$Itens .= '</tr>';
	
			}
			return $Itens;
		}
	
	}
	
	
	// REALIZAR FILTROS
	$filter = '';
	
	$DataAtual = date("Ymd");
	$EmissDe = '';
	$EmissAte = '';
	$Orcamento = '';
	
	if (isset($_POST['emissaode']) and isset($_POST['emissaoate'])) {
		if (!empty($_POST['emissaode']) and !empty($_POST['emissaoate'])) {
			$EmissDe = $_POST['emissaode'];
			$EmissAte = $_POST['emissaoate'];
			$EmissaoDe = substr($_POST['emissaode'], 6,4).substr($_POST['emissaode'], 3,2).substr($_POST['emissaode'], 0,2);
			$EmissaoAte = substr($_POST['emissaoate'], 6,4).substr($_POST['emissaoate'], 3,2).substr($_POST['emissaoate'], 0,2);
			$filter .= " AND VZK_DATA BetWeen '".$EmissaoDe."' and '".$EmissaoAte."' ";
		}else{
			$filter .= " AND VZK_DATA >= '".$DataAtual."' ";
		}
	}else {
		$filter .= " AND VZK_DATA >= '".$DataAtual."' ";
	}
	
	if (isset($_POST['orcamento'])){
		if (!empty($_POST['orcamento'])) {
			$Orcamento = $_POST['orcamento'];
			$filter = " AND VZK_CODORC = '".$Orcamento."' ";
		}
	}
	
	
	$ObjItens = new ListContracts();
	

	$Html  = '<head>';
	$Html .= '	<title>.:Pagina de Inclusao de Contratos :.</title>';
	$Html .= '	<link charset="utf-8" media="screen" type="text/css" href="../ink/css/ink.css" rel="stylesheet">';
	$Html .= '	<script type="text/javascript" src="../ink/js/ink.js"></script>';
	$Html .= '	<script type="text/javascript" src="../ink/js/ink.datepicker.pt.js"></script>';
	$Html .= '	<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js "></script>';
	$Html .= '	<script type="text/javascript" src="../ink/js/autoload.js"></script>';
	$Html .= '	<script type="text/javascript" src="../ink/js/ink.modal.js"></script>';
	$Html .= '</head>';
	$Html .= '<body text="#000000" class="VermelhoGrande">';
	$Html .= '	<table  width="80%" align="center">';
	$Html .= '		<tr>';
	$Html .= '			<td  colspan="2" class="FonteFormulario">';
	$Html .= '				<p><a href="http://www.ometzgroup.com.br/empresas/skopos"><img src="'.IMG_FOLDER.'logo_company.png" border=0 align="absmiddle" title="Descri&ccedil;&atilde;o da Sua Loja"></a></p>';
	$Html .= '			</td>';
	$Html .= '		</tr>';
	$Html .= '		<tr>';
	$Html .= '			<td align="center">';
	$Html .= '				<h4>Inclus&atilde;o de Contratos para Aprova&ccedil;&atilde;o</h4>';
	$Html .= '			</td>';
	$Html .= '		</tr>';
	$Html .= '	</table>';
	$Html .= '	<form name="frmUploadfilter" action="" method="POST" id="frmUploadfilter"  class="ink-form" onsubmit="return Ink.UI.FormValidator_1.validate(this);">';
	$Html .= '		<fieldset class="column-group gutters">';
	$Html .= '			<table  width="80%" align="center">';
	$Html .= '				<tr>';
	$Html .= '					<td>';
	$Html .= '						<table align="right">';
	$Html .= '							<tr>';
	$Html .= '								<td>Emiss&atilde;o De</td>';
	$Html .= '								<td>Emiss&atilde;o At&eacute;</td>';
	$Html .= '								<td>N&uacute;mero Or&ccedil;amento</td>';
	$Html .= '								<td></td>';
	$Html .= '								<td></td>';
	$Html .= '							</tr>';
	$Html .= '							<tr>';
	$Html .= '								<td><input type="text" id="date1" name="emissaode" class="ink-datepicker" data-format="dd/mm/yyyy"></td>';
	$Html .= '								<td><input type="text" id="date" name="emissaoate" class="ink-datepicker" data-format="dd/mm/yyyy"></td>';
	$Html .= '								<td><input type="text" id="orcamento" name="orcamento" value=""></td>';
	$Html .= '								<td><input type="submit" class="ink-button" value="Filtrar"></td>';
	$Html .= '								<td><input type="button" id="bModal" class="ink-button orange" value="Incluir"></td>';
	$Html .= '							</tr>';
	$Html .= '						</table>';
	$Html .= '					</td>';
	$Html .= '				</tr>';
	$Html .= '			</table>';
	$Html .= '	    </fieldset>';	
	$Html .= '	</form>';
	$Html .= '	<table align="center" class="ink-table hover" style="width:80%;">';
	$Html .= '		<thead>';
	$Html .= '			<td>N&uacute;mero Or&ccedil;amento</td>';
	$Html .= '			<td>Data de Inclus&atilde;o</td>';
	$Html .= '			<td>Respons&aacute;vel</td>';
	$Html .= '			<td>Nome do Arquivo</td>';
	$Html .= '		</thead>';
	
	$Html .= $ObjItens->QueryContracts($filter);
	
	$Html .= '	</table>';
	
	$Html .= '</body>';

	echo $Html;
	
?>

<script>
	var id = 0;
</script>

	<div class="ink-shade fade">
	    <div id="test" class="ink-modal" data-trigger="#bModal" data-width="800px" data-height="500px">
	        <div class="modal-header">
	            <button class="modal-close ink-dismiss"></button>
	            <h4 style="color:orange;">Inclus&atilde;o Arquivo de Contratos</h4>  
	        </div>
	            
	        <div class="modal-body" id="modalContent">
	            <h5>Por favor selecione o arquivo para Upload</h5>
	            <p>Informacoes sobre o tamanho do arquivo que sera realizado o Upload.</p>
	        	<br>
	        	
                <span id = "msgCountQuote" style="display:none; margin-left: 15px; color: red;">Deve ter pelo menos um or&ccedil;amento ativo</span>
                	<form name="frmUploadfile" action="uploadfiles.php" method="POST" id="frmUploadfile"  class="ink-form" enctype="multipart/form-data">	
                 		
			        
                 	<table style="width:80%;">
			        	<tr>
			        		<td>
			        			<label for="responsavel">Respons&aacute;vel pela Inclus&atilde;o (*)</label>
						        <div class="control">
						           	<input type="text" id="inputresponsavel" name="inputresponsavel" class="ink-fv-required">                                    
                                    <span id = "msgResponsavel" style="display:none;color:red; margin-left:15px;"></span>
						        </div>
						        <br>	
					        	<div id="uploadtable"></div>
					        	<br>
			        		</td>
			        	</tr>
			        </table>     
	        </div>
	        <div class="modal-footer">
	            <div class="push-right">
					<input type="button" name="btn" id="btinput" value="Novo Arquivo" class="ink-button" />                               
					<button class="ink-button ink-dismiss" id="btnCancel">Cancelar</button>		   
					<input type="submit" name="btnConfirm" id = "btnConfirm" value="Confirmar" class="ink-button success orange" /> 
	            </div>
            </div>
	    </div>
	</div>
<script>
    // There are two ways to run the code...
    // 1 - If you know you have the component and its dependencies already loaded, just do:
    var modal = new Ink.UI.Modal( '#test' );
 
    // 2 - If you're not sure the component or its dependencies are loaded at runtime, do:
    Ink.requireModules( ['Ink.UI.Modal_1'], function(Modal){
        new Modal( '#test' );
    });
</script>
<script>
$(document).ready(function(){
  $("#btinput").click(function(){
	  id++;
    $("#uploadtable").append(
    	'<div> ' +
			'<label for="codigoorcamento">C&oacute;digo Or&ccedil;amento</label><br>' +
			'<div class="control" style="float:left;">' +
				'<input type="text" id="inputorcamento'+id.toString()+'" name="inputorcamento'+id.toString()+'" class="ink-fv-required">' +
			'</div>' +
			'<div class="control-group large-33 medium-33 small-100" style="float:right;">' +
				'<label for="file-input">' +
				'<input id="file-input'+id.toString()+'" name="inputfile'+id.toString()+'" type="file">' +
			'</div>' +
			'<div style="clear:both;"></div>'+
			'<div style="clear:both;display:none;color:red;" id="msg-error'+id.toString()+'">Or&ccedil;amento não pode ser vazio</div>' +
		'</div>');
  });

  $("#btnConfirm").click(function(event){	  
	  var valid = validar();
	  if (!valid)
	  	event.preventDefault();
	  }
  );

});

function validar(){
	var valid = true;
	
	// Validate responsible.
	if ($('#inputresponsavel').val() == ''){
		$('#msgResponsavel').show();
		$('#msgResponsavel').html('Responsavel requerido');
		valid = false;
		}
	else
		$('#msgResponsavel').hide();
	
	// Validate if there any file browser
	if($('#uploadtable').find('div').length == 0) {
		//$('#msgCountQuote').html('Responsavel requerido');
		$('#msgCountQuote').show();
		valid = false;
	}
	else
		$('#msgCountQuote').hide();
			
	//Validate each file browser individually
	var quoteElement = $('#uploadtable').find('div');
	
	quoteElement.each(function(index, element) {
        var i = index + 1;
		var inputorcamento = "#inputorcamento" + i.toString();
		var inputfile = "#file-input" + i.toString();
		var msg_error = "#msg-error" + i.toString();
		
		if($(inputorcamento).val() == '' && $(inputfile).val() == ''){
			$(msg_error).html("Or&ccedil;amento e Arquivo n&atilde;o podem ser vazio");
			$(msg_error).show();
			valid = false;
		}
		
		else if($(inputorcamento).val() == ''){
			$(msg_error).html("Or&ccedil;amento n&atilde;o pode ser vazio");
			$(msg_error).show();
			valid = false;
		}
		else if($(inputfile).val() == ''){
			$(msg_error).html("Arquivo n&atilde;o pode ser vazio");
			$(msg_error).show();
			valid = false;
		}
		else
			$(msg_error).hide();				
    });
	
	
	return (valid);
}
</script>
<script type="text/javascript">
    Ink.requireModules(['Ink.Dom.Event_1'],function(Event){
        var Confirm = Ink.i('confirm');
        Event.observe( Confirm, 'click', function( e ){
            Event.stop(e);                  // Stopping the event and preventing the bubbling
            //var target = Event.element(e);  // Getting the element that triggered the event
            //console.log('The element we clicked was: ', target);
             // Now a POST request:
        	Ink.requireModules(['Ink.Net.Ajax_1'],function(Ajax){
	        	new Ajax('uploadfiles.php',{
		            asynchronous: true,
		            method: 'POST',
		            onSuccess: function( response ){
		                console.log( response.responseText );
	            	}
	        	});
        	});
        });
    });
</script>

