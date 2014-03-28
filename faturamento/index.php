<html>
<?php

require_once '../conf.php';

class ListBiling extends Ometz_Default {
	private $QryOrcamentos = '';
	
	public function QueryBiling($filter){
		
		
		$this->QryOrcamentos  = " SELECT ";
		$this->QryOrcamentos .= " 	CJ_NUM ORCAMENTO, CJ_EMISSAO EMISSAO, CJ_CLIENTE ,  CJ_LOJA, A1_NOME, CJ_STATUS, VZK_DATA DATA, VZK_NOMARQ,  VZK_CAMARQ, VZK_USER ";
		$this->QryOrcamentos .= "	FROM ";
		$this->QryOrcamentos .= "     DB2.SCJ500 SCJ ";
		$this->QryOrcamentos .= "     INNER JOIN DB2.SA1500 SA1 ON (A1_COD = CJ_CLIENTE AND A1_LOJA = CJ_LOJA AND SA1.D_E_L_E_T_ = ' ') ";
		$this->QryOrcamentos .= "     INNER JOIN DB2.VZK500 VZK ON (VZK_CODORC = CJ_NUM AND VZK.D_E_L_E_T_ = ' ') ";		
		$this->QryOrcamentos .= " WHERE CJ_FILIAL = '  ' ";
		$this->QryOrcamentos .=	  $filter;
		$this->QryOrcamentos .= " 	AND SCJ.D_E_L_E_T_ = ' ' ";
		$this->QryOrcamentos .= " 	ORDER BY CJ_NUM ";
		
		//echo $this->QryOrcamentos;
		$response = $this->database->fetchAll($this->QryOrcamentos);
		
		$Itens = '';
		for ($i = 0; $i <= count($response)-1; $i++) {
			$dataorcamento = substr($response[$i]['EMISSAO'],6,2).'/'.substr($response[$i]['EMISSAO'], 4,2).'/'.substr($response[$i]['EMISSAO'], 0,4);	
			$emissao = substr($response[$i]['DATA'],6,2).'/'.substr($response[$i]['DATA'], 4,2).'/'.substr($response[$i]['DATA'], 0,4);
			
			$Itens .= '<tr>';
    		$Itens .= '	<td>'.$response[$i]['ORCAMENTO'].'</td>';
    		$Itens .= '	<td>'.$dataorcamento.'</td>';
    		$Itens .= '	<td>'.$emissao.'</td>';
    		$Itens .= '	<td>'.$response[$i]['A1_NOME'].'</td>';
    		$Itens .= '	<td>'.$response[$i]['VZK_USER'].'</td>';
    		if ($response[$i]['CJ_STATUS'] == 'A'){
    			$Itens .= '	<td>Aberto</td>';
    		}elseif ($response[$i]['CJ_STATUS'] == 'B'){
    			$Itens .= '	<td>Faturado</td>';
    		}elseif ($response[$i]['CJ_STATUS'] == 'S'){
    			$Itens .= '	<td>Cartao Credito</td>';
    		}else{
    			$Itens .= '	<td> </td>';
    		}
    		$Itens .= '	<td><a href="'.trim($response[$i]['VZK_CAMARQ']).trim($response[$i]['VZK_NOMARQ']).'" target="_blank"><i class="icon-zoom-in icon-large"></i></a></td>';
    		$Itens .= '</tr>';
		}
		return $Itens;	
	}

}


$filter = " ";

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
		$filter = " AND CJ_STATUS = 'A' AND CJ_NUM = '".$Orcamento."' ";
	}
}

$ObjItens = new ListBiling();

$Html  = '<head>';
$Html .= '	<title>.:Pagina de Listado de Contratos :.</title>';
$Html .= '	<link charset="utf-8" media="screen" type="text/css" href="../ink/css/ink.css" rel="stylesheet">';
$Html .= '	<script type="text/javascript" src="../ink/js/ink.js"></script>';
$Html .= '	<script type="text/javascript" src="../ink/js/ink.datepicker.pt.js"></script>';
$Html .= '	<script type="text/javascript" src="../ink/js/autoload.js"></script>';
$Html .= '</head>';
$Html .= '<body text="#000000" class="VermelhoGrande">';
$Html .= '	<form name="frmBiling" action="" method="POST" id="frmBiling"  class="ink-form">';
$Html .= '		<table  width="80%" align="center">';		
$Html .= '			<tr>';
$Html .= '				<td  colspan="2" class="FonteFormulario">';
$Html .= '					<p><a href="http://www.ometzgroup.com.br/empresas/skopos"><img src="'.IMG_FOLDER.'logo_company.png" border=0 align="absmiddle" title="Descri&ccedil;&atilde;o da Sua Loja"></a></p>';
$Html .= '				</td>';
$Html .= '			</tr>';
$Html .= '			<tr>';
$Html .= '				<td align="center">';
$Html .= '					<h4>Tela de Visualiza&ccedil;&atilde;o de Contratos</h4>';
$Html .= '				</td>';
$Html .= '			</tr>';
$Html .= '			<tr>';
$Html .= '				<td>';
$Html .= '					<fieldset class="column-group gutters">';
$Html .= '						<table align="right">';
$Html .= '							<tr>';
$Html .= '								<td>Emiss&atilde;o De</td>';
$Html .= '								<td>Emiss&atilde;o At&eacute;</td>';
$Html .= '								<td>N&uacute;mero Or&ccedil;amento</td>';
$Html .= '								<td></td>';
$Html .= '							</tr>';
$Html .= '							<tr>';
$Html .= '								<td><input type="text" id="date1" name="emissaode" class="ink-datepicker" data-format="dd/mm/yyyy"></td>';
$Html .= '								<td><input type="text" id="date" name="emissaoate" class="ink-datepicker" data-format="dd/mm/yyyy"></td>';
$Html .= '								<td><input type="text" id="orcamento" name="orcamento" value="'.$Orcamento.'"></td>';
$Html .= '								<td><input type="submit" class="ink-button" value="Filtrar"></td>';
$Html .= '							</tr>';
$Html .= '						</table>';
$Html .= '				    </fieldset>';
$Html .= '				</td>';
$Html .= '			</tr>';
$Html .= '			<tr>';
$Html .= '				<td class="FonteFormulario">';			
$Html .= '						<table class="ink-table hover ">';
$Html .= '							<thead>';
$Html .= '								<td>N&uacute;mero Or&ccedil;amento</td>';
$Html .= '								<td>Data do Or&ccedil;amento</td>';
$Html .= '								<td>Data de Inclus&atilde;o Documento</td>';
$Html .= '								<td>Cliente</td>';
$Html .= '								<td>Respons&aacute;vel</td>';
$Html .= '								<td>Status</td>';
$Html .= '								<td></td>';
$Html .= '							</thead>';

$Html .= $ObjItens->QueryBiling($filter);

$Html .= '						</table>';
$Html .= '				</td>';
$Html .= '			</tr>';
$Html .= '		</table>';
$Html .= '	</form>';
$Html .= '</body>';

echo $Html;
?>
