<?php
	require_once '../conf.php';
	require_once '../class/formatContent.php';
	
	// QUERY PARA BUSCAR O FILTRO PARA A TELA - ATUALIZAR A QUERY!!!!!!!!!!!!
	
	class ListRecurrence extends Ometz_Default {
		private $QryArquivos = '';
		private $check_select_all = '';
	
		public function get_check_select_all(){
			return $this->check_select_all;
		}
		
		
		public function get_details(){
			
			$f = new formatterContent();
			
			$filter= '';
			
			$bde = isset($_POST['emissaode']) && $f->formatDate($_POST['emissaode'])  > '19691231';
			$bate = isset($_POST['emissaoate']) && $f->formatDate($_POST['emissaoate'])  > '19691231';
			
			
			if($bde & $bate){
				$filter = "SE1.E1_VENCTO BETWEEN '".$f->formatDate($_POST['emissaode'])."' AND '".$f->formatDate($_POST['emissaoate'])."'";
			}
			
			elseif($bde){		
				$filter = "SE1.E1_VENCTO >= '".$f->formatDate($_POST['emissaode'])."'";
				echo ($filter);			
			}
			elseif($bate){		
				$filter = "SE1.E1_VENCTO <= '".$f->formatDate($_POST['emissaoate'])."'";
				echo ($filter);			
			}
			
			if(isset($_POST['nomealuno']) && $_POST['nomealuno'] != ""){
								
				if ($filter != "") {
					$filter .= " AND TRIM(SE1.E1_NOMCLI) LIKE '%".$_POST['nomealuno']."%'";
				}
				else{
					$filter .= " SE1.E1_NOMCLI LIKE '%".$_POST['nomealuno']."%'";
				}
									
			}
			
			if(isset($_POST['numra']) && $_POST['numra'] != ""){
								
				if ($filter != "") {
					$filter .= " AND SE1.E1_NUMRA = '".$_POST['numra']."'";
				}
				else{
					$filter .= " SE1.E1_NUMRA = '".$_POST['numra']."'";
				}
									
			}
			
			
			
			$rs = $this->QueryRecurrence($filter);		
							
			$table_content = '';
			
			$is_all_approved = true;
			
			foreach ($rs as $row ) {
					
				$btn_alter_card='';				
				$checkbox = '';
				$checkSelectAll = '';
				
				if(intval($row["VZL_STATUS"]) == 1){
					$color = 'green';
					$icon = 'icon-ok-sign';					
				}
				else{
					$is_all_approved = false;
					$color = 'red';
					$icon = 'icon-remove-sign';				
					$checkbox = '<input type = "checkbox" class="check-num-record" id = "'.$row["RECNUM"].'"/>';
					
					
					$btn_alter_card = '
						<span class="ink-button orange btnedit" data-id_transacao = "'.$row["TRANSACAO"].'" data-id_cliente = "'.$row["E1_CLIENTE"].'" data-id_loja = "'.$row["E1_LOJA"].'">
							<i class="tooltip icon-edit" data-tip-text="Alterar dados do cart&atilde;o" data-tip-where="mousemove" data-tip-color="orange"></i>
						</span>
					';
					 
					
				}

				
				$table_content .= '<tr>
									<td>'.$checkbox.'</td>
									<td>'.$row["E1_CLIENTE"].'</td>
									<td>'.$row["E1_NUMRA"].'</td>	
									<td>'.$row["E1_NOMCLI"].'</td>
									<td>'.$f->formatDate($row["E1_VENCTO"],'d/m/Y').'</td>	
									<td>'.$f->format_money($row["E1_SALDO"]).'</td>	
									<td style="color:'.$color.';">'.utf8_encode($row["VZL_OBS"]).'</td>	
									<td><span class="ink-badge '.$color.'"><i class="'.$icon.'"></i></span></td>
									<td>'.$btn_alter_card.'</td>		
								</tr>
								';	
			}
			
			if(!$is_all_approved)
				$this->check_select_all = '<input type="checkbox" id="chkSelectAll" value = "">';
			
			return $table_content;
			
		}
		
		
		
		private function QueryRecurrence($filter = ''){
			
			
				
			$where = $filter != '' ? "AND ".$filter : "";
			
			$this->QryArquivos  = 
				" SELECT 
				    SE1.R_E_C_N_O_ RECNUM,
				    SE1.E1_CLIENTE,
				    SE1.E1_LOJA,
				    CASE
				      WHEN SE1.E1_NUMRA != '' THEN E1_NUMRA
				      ELSE 'N/A'
				    END E1_NUMRA,
				    SE1.E1_NOMCLI,
				    SE1.E1_VENCTO,
				    SE1.E1_SALDO,
				    VZL.VZL_OBS,
				    VZL.VZL_STATUS,
				    CASE
				  	  WHEN INSTR(SZ0.Z0_HIST, 'TRASACAO:') > 0  THEN
				  	    TRIM(
				        SUBSTR(SZ0.Z0_HIST, 
				               INSTR(SZ0.Z0_HIST, 'TRASACAO:') + LENGTH('TRASACAO:'), 
				               INSTR(SZ0.Z0_HIST, ';', 1, 2) - (INSTR(SZ0.Z0_HIST, 'TRASACAO:') + LENGTH('TRASACAO:'))
				              )
				            )
				      ELSE ''
					  END AS TRANSACAO
				
				FROM DB2.VZL500 VZL
				INNER JOIN DB2.SE1050 SE1 ON 
				    SE1.E1_FILIAL = VZL.VZL_CODFIL AND 
				    SE1.E1_PREFIXO = VZL.VZL_PREFIX AND
				    SE1.E1_NUM = VZL.VZL_NUM AND
				    SE1.E1_PARCELA = VZL.VZL_PARCEL AND
				    SE1.E1_TIPO = VZL.VZL_TIPO AND
				    SE1.D_E_L_E_T_ = VZL.D_E_L_E_T_ AND
				    VZL.D_E_L_E_T_ <> '*'
				LEFT JOIN 
					  DB2.SZ0500 AS SZ0 ON SE1.E1_NRDOC = SUBSTR(SZ0.Z0_HIST, 36, 14) 
					  
				WHERE 
					E1_TIPO = 'CC' AND
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
				        )
		              OR VZL.VZL_SEQUEN IS NULL
					)				
				
				".$where."

				ORDER BY SE1.E1_VENCTO DESC, SE1.E1_CODORCA DESC, VZL.R_E_C_N_O_ DESC
				
				";
			
			
			//if($where !='')
			//	die($this->QryArquivos);
			
			//echo($this->QryArquivos);
			
			return $this->database->fetchAll($this->QryArquivos);
	
			
		}
	
	}
	
	$obj = new ListRecurrence();


	?>	
	<html lang="pt-BR">
    <head>		
		<title>.:Pagina de Consultas de Recorrências :.</title>		
		<link charset="utf-8" media="screen" type="text/css" href="../ink/css/ink.css" rel="stylesheet">
		<script type="text/javascript" src="../ink/js/ink.js"></script>
		<script type="text/javascript" src="../ink/js/autoload.js"></script>
		<script type="text/javascript" src="../ink/js/ink.datepicker.pt.js"></script>				
		<script type="text/javascript" src="../ink/js/ink.modal.js"></script>
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js "></script>
		<script type="text/javascript" src="../js/recorrencia.js"></script>
	</head>
	<body text="#000000" class="VermelhoGrande">
		
		
	<table  width="80%" align="center">
		<tr>
			<td  colspan="2" class="FonteFormulario">
				<p><a href="<?php echo (BASE_URL); ?>recorrencia/"><img src="<?=IMG_FOLDER ?>logo_company.png" border=0 align="absmiddle" title="Descri&ccedil;&atilde;o da Sua Loja"></a></p>
			</td>
		</tr>
		<tr>
			<td align="center">
				<h4>Acompanhamento de Cobrança da Recorrência Cartâo de Crédito</h4>
			</td>
		</tr>
	</table>


	<form name="frmRecurrenceFilter" action="" method="POST" id="frmUploadfilter" class="ink-form" onsubmit="return Ink.UI.FormValidator_1.validate(this);">
		<fieldset class="column-group gutters">
			<table  width="80%" align="center">
				<tr>
					<td>
						<table align="right">
							<tr>
								<td>Código RA</td>
								<td>Nome Aluno</td>
								<td>Data Cobrança De</td>
								<td colspan="2">Data Cobrança Até</td>
							</tr>
							<tr>
								<td><input type="text" id="numra" name="numra" maxlength="15"></td>
								<td><input type="text" id="nomealuno" name="nomealuno" maxlength="50" size="50"></td>
								<td><input type="text" id="emissaode" name="emissaode" class="ink-datepicker" data-format="dd/mm/yyyy"></td>
								<td><input type="text" id="emissaoate" name="emissaoate" class="ink-datepicker" data-format="dd/mm/yyyy"></td>
								<td><input type="submit" class="ink-button" value="Filtrar"></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
	    </fieldset>	
	</form>
	
	<?php if(isset($_GET['trans_not_found'])) {?>
		<table  width="80%" align="center">
			<tr>
				<td>
					<div class="ink-alert basic error">
					    <button class="ink-dismiss">&times;</button>
					    <p align="center"><b>Erro:</b> Cliente não tem Transacão asociada para alterar cartão.</p>
					</div>						
				</td>
			</tr>
		</table>	
	<?php 
	} 
	$details = $obj->get_details(); 
	?>
	
	<form name="frmPostCartao" id="frmPostCartao" action="<?php echo(BASE_URL.'recorrencia/enviorecorrencia.php'); ?>" method="post">
		<input type="hidden" id="txtNumTrans" name="txtNumTrans" value="" />		        
		<input type="hidden" id="txtCustNum"  name="txtCustNum"  value="" />
		<input type="hidden" id="txtNumBrand" name="txtNumBrand" value="" />		
		<input type="hidden" id="listNumRecord" name="listNumRecord" value="" />
	
		<table align="center" class="ink-table hover" style="width:80%;">
			<thead>
				<td><?php echo ($obj->get_check_select_all()); ?></td>
				<td>C&oacute;digo Cliente</td>
				<td>C&oacute;digo RA</td>
				<td>Nome do Aluno</td>
				<td>Data de Vencimento</td>
				<td>Valor da Parcela</td>
				<td>Observa&ccedil;&otilde;es</td>
				<td>Status</td>
				<td>&nbsp;</td>
			</thead>
	    	<?php echo($details); ?>
		</table>
		
		<table  width="80%" align="center">
			<tr>
				<td>
					<table align="right">
						<tr>
							<td>&nbsp;</td>
						<tr>
							<td><span id = "btnReprocess" class="ink-button orange">Reprocessar</span></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
</body>
</html>