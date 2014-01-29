<?php
require_once '../conf.php';
require_once 'wsdl_contrato_cartao.php';


$Html = '';
$Itens = '';

$id = 1;

//Valida WS
$objValidWs = new Validations();
$ws_valid = $objValidWs->validateSOAP(P10_WSDL_CONTRACT); 

while(isset($_POST['inputorcamento'.strval($id)])){
	
	$orcamento = $_POST['inputorcamento'.strval($id)];
	$responsavel = $_POST['inputresponsavel'];
	$nomearquivo = $_FILES['inputfile'.strval($id)]['name'];
			
	$Data = date("Ymd");
	
	$DataInclusao = date("d/m/Y"); //substr($Data, 6,4).substr($Data, 3,2).substr($Data, 0,2);
	
		
	// SALVA O ARQUIVO NA PASTA DO SERVER
	$allowedExts = array("gif", "jpeg", "jpg", "png");
	$temp = explode(".", $_FILES['inputfile'.strval($id)]['name']);
	$extension = end($temp);
	if ((($_FILES['inputfile'.strval($id)]['type'] == "image/gif")
			|| ($_FILES['inputfile'.strval($id)]['type'] == "image/jpeg")
			|| ($_FILES['inputfile'.strval($id)]['type'] == "image/jpg")
			|| ($_FILES['inputfile'.strval($id)]['type'] == "image/pjpeg")
			|| ($_FILES['inputfile'.strval($id)]['type'] == "image/x-png")
			|| ($_FILES['inputfile'.strval($id)]['type'] == "image/png"))
			//&& ($_FILES['inputfile'.strval($id)]['size'] < 20000)
			&& in_array($extension, $allowedExts))
	{
		if ($_FILES['inputfile'.strval($id)]['error'] > 0)
		{
			$Itens .= '		<tr>';
			$Itens .= '			<td>'.$orcamento.'</td>';
			$Itens .= '			<td>'.$DataInclusao.'</td>';
			$Itens .= '			<td>'.$responsavel.'</td>';	
			$Itens .= '			<td>'.$nomearquivo.'</td>';
			$Itens .= '			<td><span class="ink-badge red"><i class="icon-remove-sign"></i></span></td>';
			$Itens .= '		</tr>';
		}
		else
		{
						
			// Check file root folder.
			if(!is_dir(UPLOAD_ROOT_FOLDER)){
				mkdir(UPLOAD_ROOT_FOLDER);
			}
			
			//Check year folder, if it doesn't exist, create it
			if(!is_dir(UPLOAD_ROOT_FOLDER.strval(date('Y')) )){
				mkdir(UPLOAD_ROOT_FOLDER.strval(date('Y')));
			}
			
			//Check month folder, if it doesn't exist, create it
			if(!is_dir(UPLOAD_ROOT_FOLDER.strval(date('Y/m')) )){
				mkdir(UPLOAD_ROOT_FOLDER.strval(date('Y/m')));
			}

			//Check day folder, if it doesn't exist, create it
			if(!is_dir(UPLOAD_ROOT_FOLDER.strval(date('Y/m/d')) )){
				mkdir(UPLOAD_ROOT_FOLDER.strval(date('Y/m/d')));
			}

			
			$camarq = UPLOAD_ROOT_FOLDER.strval(date("Y/m/d"))."/";	

			
			
			if (file_exists($camarq.$_FILES['inputfile'.strval($id)]['name']))
			{
				$Itens .= '		<tr>';
				$Itens .= '			<td>'.$orcamento.'</td>';
				$Itens .= '			<td>'.$DataInclusao.'</td>';
				$Itens .= '			<td>'.$responsavel.'</td>';
				$Itens .= '			<td>'.$nomearquivo.'</td>';
				$Itens .= '			<td><span class="ink-badge red"><i class="icon-remove-sign"></i></span></td>';
				$Itens .= '		</tr>';
			}
			else
			{
								
				move_uploaded_file($_FILES['inputfile'.strval($id)]['tmp_name'],
				$camarq . $_FILES['inputfile'.strval($id)]['name']);
				//echo "Stored in: " . "/upload/" . $_FILES['inputfile'.strval($id)]['name'];
				
				
			
				// GRAVAR NA TABELA OS DADOS DO ARQUIVO ANEXADO.
				if($ws_valid){
						$wsdl = new WS_CONTRATO_CARTAO();
						$data = new INCLUI();
						
						$data->SVZK_CODORC  = $orcamento;
						$data->SVZK_NOMARQ  = $nomearquivo;
						$data->SVZK_CAMARQ 	= BASE_URL."/upload/".$camarq;
						$data->SVZK_USER 	= $responsavel;
			
						
						$response = $wsdl->INCLUI($data);
						
						$soapMessage = $response->INCLUIRESULT;
						
						
				}
				else
					$soapMessage = "FALSE";
				
				if(strtoupper($soapMessage) == 'TRUE')
					$icon = '<span class="ink-badge green"><i class="icon-ok-sign"></i></span>';									
				else
					$icon = '<span class="ink-badge red"><i class="icon-remove-sign"></i></span>';


				$Itens .= '		<tr>';
				$Itens .= '			<td>'.$orcamento.'</td>';
				$Itens .= '			<td>'.$DataInclusao.'</td>';
				$Itens .= '			<td>'.$responsavel.'</td>';
				$Itens .= '			<td>'.$nomearquivo.'</td>';
				$Itens .= '			<td>'.$icon.'</td>';
				$Itens .= '		</tr>';
				
				
			}
		}
	}
	else
	{
		$Itens .= '		<tr>';
		$Itens .= '			<td>'.$orcamento.'</td>';
		$Itens .= '			<td>'.$DataInclusao.'</td>';
		$Itens .= '			<td>'.$responsavel.'</td>';
		$Itens .= '			<td>'.$nomearquivo.'</td>';
		$Itens .= '			<td><span class="ink-badge red"><i class="icon-remove-sign"></i></span></td>';
		$Itens .= '		</tr>';
	}
	
	$id++;
}

$Html  = '<head>';
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
$Html .= '				<h4>Status inclus&atilde;o de anexos aos contratos</h4>';
$Html .= '			</td>';
$Html .= '		</tr>';
$Html .= '	</table>';
$Html .= '	<table align="center" class="ink-table hover" style="width:80%;">';
$Html .= '		<thead>';
$Html .= '			<td>N&uacute;mero Or&ccedil;amento</td>';
$Html .= '			<td>Data de Inclus&atilde;o</td>';
$Html .= '			<td>Respons&aacute;vel</td>';
$Html .= '			<td>Nome do Arquivo</td>';
$Html .= '			<td>Status</td>';
$Html .= '		</thead>';


$Html .= $Itens;

$Html .= '		<tr>';
$Html .= '			<td colspan=5 align="center"><a href="index.php"><i class="icon-circle-arrow-left"></i> Voltar ao menu inicial</a></td>';
$Html .= '		</tr>';

$Html .= '	</table>';

if(!$ws_valid) // Se tiver fora o WS, mostar a mensagem	
	$Html .= '	<div style="text-align:center; color:#F00">'.$objValidWs->_getErrorMessageWS().'</div>';

$Html .= '</body>';

echo $Html;

?>
<div style="text-align:center; color:#F00"></>