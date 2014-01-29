<html>
<?php
require_once '../conf.php';

class viewUnderConstruction extends Ometz_Default
{		
	public function init()
	{
		$this->showDefaultHeader();
	}		
}

$objUnderConst =  new viewUnderConstruction();

?>

<body text="#000000" class="VermelhoGrande"> 

<form name="frmPedido" action="<? echo(PAYMENT_GATEWAY); ?>" method="POST" id="frmPedido">

<table  width="50%" align="center">
	<tr >
		<td colspan="2" class="FonteFormulario">
			<p><a href="<? echo(BASE_URL); ?>"><img src="<?php echo (IMG_FOLDER); ?>logo_company.png" border=0 align="absmiddle" title="Descri&ccedil;&atilde;o da Sua Loja"></a></p>
		</td>
	</tr>
	<tr>
		<td class="FonteFormulario">
            <img class="img-construction" alt="Web site em contru&ccedil;&atilde;o" src="<? echo(BASE_URL); ?>img/Website_Under_Construction.gif" />                        
		</td>
	</tr>
    <tr align="center">
		<td class="FonteFormulario">
            <a href="<? echo(BASE_URL); ?>">Voltar para Inicio</a>
		</td>
	</tr>

</table>
</form>
</body>
</html>
