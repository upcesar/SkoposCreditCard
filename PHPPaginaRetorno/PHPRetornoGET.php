<!--
Exemplo de captura dos parâmetros retornados pelo Aprova Fácil - Parâmetros capturados separadamente por GET em PHP.
Este exemplo foi construido pela equipe da Cobre Bem Tecnologia
-->


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<center>,
<font size=3 face=Verdana color=darkblue>Exemplo de P&aacute;gina de Retorno em PHP: <br>Parâmetros capturados separadamente por GET</font>
<p>
<table width=65%  border="0"><tr><td>
<fieldset>
<legend><font size=2 face=Verdana color=#666666>Par&acirc;metros Retornados:</font></legend>
<table>
<?php 
if (isset($_GET))
{
  echo("<tr><td><font size=2 face=Verdana color=darkblue>Transacao: </font></td><td><font size=2 face=Verdana color=red>" . $_GET['Transacao'] . "</font></td>");
  echo("<tr><td><font size=2 face=Verdana color=darkblue>TransacaoAprovada: </font></td><td><font size=2 face=Verdana color=red>" . $_GET['TransacaoAprovada'] . "</font></td>");
  echo("<tr><td><font size=2 face=Verdana color=darkblue>CodigoAutorizacao: </font></td><td><font size=2 face=Verdana color=red>" . $_GET['CodigoAutorizacao'] . "</font></td>");
  echo("<tr><td><font size=2 face=Verdana color=darkblue>ValorDocumento: </font></td><td><font size=2 face=Verdana color=red>" . $_GET['ValorDocumento'] . "</font></td>");
  echo("<tr><td><font size=2 face=Verdana color=darkblue>ResultadoSolicitacaoAprovacao: </font></td><td><font size=2 face=Verdana color=red>" . urldecode($_GET['ResultadoSolicitacaoAprovacao']) . "</font></td>");
  echo("<tr><td><font size=2 face=Verdana color=darkblue>CartaoMascarado: </font></td><td><font size=2 face=Verdana color=red>" . $_GET['CartaoMascarado'] . "</font></td>");
  echo("<tr><td><font size=2 face=Verdana color=darkblue>NumeroDocumento: </font></td><td><font size=2 face=Verdana color=red>" . $_GET['NumeroDocumento'] . "</font></td>");
  echo("<tr><td><font size=2 face=Verdana color=darkblue>QuantidadeParcelas: </font></td><td><font size=2 face=Verdana color=red>" . $_GET['QuantidadeParcelas'] . "</font></td>");
}
?>
</table>	
</fieldset>
</td></tr></table>
</center>
