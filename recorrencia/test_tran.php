<?php
require_once '../conf.php';

print_r ($_POST);

?>
<html>
	<body>

			<form action="https://www.aprovafacil.com/cgi-bin/APFW/skoposeditora/APC" method="post">
				Numero Documento:
				<input type="text" name="NumeroDocumento" />
				<br>			
				Valor Documento:
				<input type="text" name="ValorDocumento" />
				<br>
				Quantidade Parcela:
				<input type="text" name="QuantidadeParcelas" />
				<br>
				Transacao:
				<input type="text" name="TransacaoAnterior" />
				<br>
				<input type="submit" value="Processar">
		</form>

	</body>
</html>
