<?php
require_once '../conf.php';
class processPayment extends Ometz_Default
{		
	public function init()
	{
		//$this->showDefaultHeader();
	}	

}
$objPayment =  new processPayment();
?>

<form action='<? echo(PAYMENT_RETURN); ?>' method='post' name='frm'>
<?php

$isPOST = false;

foreach ($_POST as $key => $value) {
    echo "<input type='hidden' name='".htmlentities($key)."' value='".htmlentities($value)."'>";
	$isPOST = true;
}
if ($isPOST == false){
	header('Location: '.BASE_URL);	
	exit;
}

?>
<noscript>
    <p>
        Clicar o bot&atilde;o abaixo se o navegador n&atilde; suporta JavaScript
    </p>
    <input type="submit" value="Enviar Dados"/>
</noscript>        

</form>
<script language="JavaScript">
	document.write("Confirmando pagamento, aguarde...");
    document.frm.submit();
</script>

