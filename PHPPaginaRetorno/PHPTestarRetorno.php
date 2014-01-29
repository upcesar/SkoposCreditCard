<!--
Exemplo de captura dos parâmetros retornados pelo Aprova Fácil - Parâmetros capturados separadamente por GET e POST em PHP.
Este exemplo foi construido pela equipe da Cobre Bem Tecnologia
-->


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<center>
<font size=3 face=Verdana color=darkblue>Exemplo de P&aacute;gina de Retorno em PHP: <br>Par&acirc;metros capturados separadamente por GET e POST</font>
<p>
<table width=63%  border="0"><tr><td>
<fieldset>
<legend><font size=2 face=Verdana color=#666666>Par&acirc;metros Retornados:</font></legend>
<table>
<?php 
if (isset($_GET) || isset($_POST))
{ 
 foreach($_GET as $Param => $valor)
 {
  if ($Param == 'ResultadoSolicitacaoAprovacao')
  {
   echo("<tr><td><font size=2 face=Verdana color=darkblue>" . $Param . ": </font></td><td><font size=2 face=Verdana color=red>" . urldecode($valor) . " </font></td>");
  }
  else
  {
   echo("<tr><td><font size=2 face=Verdana color=darkblue>" . $Param . ": </td><td><font size=2 face=Verdana color=red>" . $valor . " </td>");
  }
 }
 foreach($_POST as $Param => $valor)
 {
  if ($Param == 'ResultadoSolicitacaoAprovacao')
  {
   echo("<tr><td><font size=2 face=Verdana color=darkblue>" . $Param . ": </font></td><td><font size=2 face=Verdana color=red>" . urldecode($valor) . " </font></td>");
  }
  else
  {
   echo("<tr><td><font size=2 face=Verdana color=darkblue>" . $Param . ": </td><td><font size=2 face=Verdana color=red>" . $valor . " </td>");
  }
 }
}
?>
</table>	
</fieldset>
</td></tr></table>
</center>
