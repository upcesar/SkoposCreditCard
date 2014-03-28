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
	$allowedExts = array("gif", "jpeg", "jpg", "png", "pdf");
	$temp = explode(".", $_FILES['inputfile'.strval($id)]['name']);
	$extension = end($temp);
	
	
	if ((($_FILES['inputfile'.strval($id)]['type'] == "image/gif")
			|| ($_FILES['inputfile'.strval($id)]['type'] == "image/jpeg")
			|| ($_FILES['inputfile'.strval($id)]['type'] == "image/jpg")
			|| ($_FILES['inputfile'.strval($id)]['type'] == "image/pjpeg")
			|| ($_FILES['inputfile'.strval($id)]['type'] == "image/x-png")			
			|| ($_FILES['inputfile'.strval($id)]['type'] == "image/png")
			|| ($_FILES['inputfile'.strval($id)]['type'] == "application/pdf")
			|| ($_FILES['inputfile'.strval($id)]['type'] == "application/x-pdf")
			|| ($_FILES['inputfile'.strval($id)]['type'] == "application/vnd.pdf")
			|| ($_FILES['inputfile'.strval($id)]['type'] == "text/pdf")
			|| ($_FILES['inputfile'.strval($id)]['type'] == "text/x-pdf"))
			&& ($_FILES['inputfile'.strval($id)]['size'] <= UPLOAD_MAX_SIZE)
			&& in_array($extension, $allowedExts))
	{
		if ($_FILES['inputfile'.strval($id)]['error'] > 0)
		{
			$Itens .= '		<tr>';
			$Itens .= '			<td>'.$orcamento.'</td>';
			$Itens .= '			<td>'.$DataInclusao.'</td>';
			$Itens .= '			<td>'.$responsavel.'</td>';	
			$Itens .= '			<td>'.$nomearquivo.'</td>';
			$Itens .= '			<td><span style="color:red;">Erro do servidor ao subir o arquivo.</span></td>';
			$Itens .= '			<td><span class="ink-badge red"><i class="icon-remove-sign" title="Erro do servidor ao subir o arquivo."></i></span></td>';			
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
				$Itens .= '			<td><span style="color:red;">Arquivo ja existe no servidor</span></td>';
				$Itens .= '			<td><span class="ink-badge red"><i class="icon-remove-sign" title="Arquivo ja existe no servidor"></i></span></td>';
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
				
				if(strtoupper($soapMessage) == 'TRUE'){
					$message = "Arquivo subido com sucesso";
					$icon = '<span class="ink-badge green"><i class="icon-ok-sign" title="Arquivo subido com sucesso"></i></span>';
					$color = 'green';					
				}									
				else{
					$message = $soapMessage;
					$icon = '<span class="ink-badge red"><i class="icon-remove-sign" title="'.$soapMessage.'"></i></span>';
					$color = 'red';
				}

				$Itens .= '		<tr>';
				$Itens .= '			<td>'.$orcamento.'</td>';
				$Itens .= '			<td>'.$DataInclusao.'</td>';
				$Itens .= '			<td>'.$responsavel.'</td>';
				$Itens .= '			<td>'.$nomearquivo.'</td>';				
				$Itens .= '			<td><span style="color:'.$color.';">'.$message.'</span></td>';
				$Itens .= '			<td>'.$icon.'</td>';
				$Itens .= '		</tr>';
				
				
			}
		}
	}
	else{
		
		$message = "";
		
		// Show invalid extension files.		
		if(!in_array($extension, $allowedExts)){
			$message .= "Extens&otilde;es do arquivo permitidas: ". strtoupper(implode(", ", $allowedExts));
		}
		

		if($_FILES['inputfile'.strval($id)]['size'] > UPLOAD_MAX_SIZE){
			$sizeFile = floatval(UPLOAD_MAX_SIZE);
			$imeasure = 0;
			$smeasure = "byte";
			$plural = "s";
			while ($sizeFile >= 1024){
				$sizeFile = floatval($sizeFile / 1024); 
				$imeasure++;
				switch ($imeasure) {
					case 1:
						$smeasure = "Kb";
						break;					
					case 2:
						$smeasure = "Mb";
						$plural = "'s";
						break;					
					case 3:
						$smeasure = "Gb";
						$plural = "'s";
						break;
					default:						
						break;
				}
			}
			$smeasure = $smeasure.($sizeFile > 1 ? $plural : "");
			
			if($message != ""){
				$message .="\r\n";
			}
			
			$message .= "Tamanho maximo permitido para upload de arquivos &eacute; de ".strval($sizeFile)." ".$smeasure;
		}
		
		
		$Itens .= '		<tr>';
		$Itens .= '			<td>'.$orcamento.'</td>';
		$Itens .= '			<td>'.$DataInclusao.'</td>';
		$Itens .= '			<td>'.$responsavel.'</td>';
		$Itens .= '			<td>'.$nomearquivo.'</td>';
		$Itens .= '			<td><span style="color:red;">'.str_replace('\r\n', '<br>', $message).'</span></td>';
		$Itens .= '			<td><span class="ink-badge red"><i class="icon-remove-sign" title="'.$message.'"></i></span></td>';		
		$Itens .= '		</tr>';
	}
	
	$id++;
}

$Html  = '<head>';
$Html .= '	<title>.:Pagina de Upload de Contratos :.</title>';
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
$Html .= '			<td>Mensagem</td>';
$Html .= '			<td>Status</td>';
$Html .= '		</thead>';


$Html .= $Itens;

$Html .= '		<tr>';
$Html .= '			<td colspan=6 align="center"><a href="index.php"><i class="icon-circle-arrow-left"></i> Voltar ao menu inicial</a></td>';
$Html .= '		</tr>';

$Html .= '	</table>';

if(!$ws_valid) // Se tiver fora o WS, mostar a mensagem	
	$Html .= '	<div style="text-align:center; color:#F00">'.$objValidWs->_getErrorMessageWS().'</div>';

$Html .= '</body>';

echo $Html;

?>
<div style="text-align:center; color:#F00"></>
