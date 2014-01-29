<!--
Exemplo de captura dos parâmetros retornados pelo Aprova Fácil - Parâmetros capturados separadamente por POST em PHP.
Este exemplo foi construido pela equipe da Cobre Bem Tecnologia
-->


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<center>,
<font size=3 face=Verdana color=darkblue>Exemplo de P&aacute;gina de Retorno em PHP: <br>Parâmetros capturados separadamente por POST</font>
<p>
<table width=65%  border="0"><tr><td>
<fieldset>
<legend><font size=2 face=Verdana color=#666666>Par&acirc;metros Retornados:</font></legend>
<table>
<?php 
if (isset($_POST))
{
  echo("<tr><td><font size=2 face=Verdana color=darkblue>Transacao: </font></td><td><font size=2 face=Verdana color=red>" . $_POST['Transacao'] . "</font></td>");
  echo("<tr><td><font size=2 face=Verdana color=darkblue>TransacaoAprovada: </font></td><td><font size=2 face=Verdana color=red>" . $_POST['TransacaoAprovada'] . "</font></td>");
  echo("<tr><td><font size=2 face=Verdana color=darkblue>CodigoAutorizacao: </font></td><td><font size=2 face=Verdana color=red>" . $_POST['CodigoAutorizacao'] . "</font></td>");
  echo("<tr><td><font size=2 face=Verdana color=darkblue>ValorDocumento: </font></td><td><font size=2 face=Verdana color=red>" . $_POST['ValorDocumento'] . "</font></td>");
  echo("<tr><td><font size=2 face=Verdana color=darkblue>ResultadoSolicitacaoAprovacao: </font></td><td><font size=2 face=Verdana color=red>" . urldecode($_POST['ResultadoSolicitacaoAprovacao']) . "</font></td>");
  echo("<tr><td><font size=2 face=Verdana color=darkblue>CartaoMascarado: </font></td><td><font size=2 face=Verdana color=red>" . $_POST['CartaoMascarado'] . "</font></td>");
  echo("<tr><td><font size=2 face=Verdana color=darkblue>NumeroDocumento: </font></td><td><font size=2 face=Verdana color=red>" . $_POST['NumeroDocumento'] . "</font></td>");
  echo("<tr><td><font size=2 face=Verdana color=darkblue>QuantidadeParcelas: </font></td><td><font size=2 face=Verdana color=red>" . $_POST['QuantidadeParcelas'] . "</font></td>");
}
?>
</table>	
</fieldset>
</td></tr></table>
</center>
