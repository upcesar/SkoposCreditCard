<?php

setlocale(LC_MONETARY, "pt_BR");

require_once('../conf.php');

// print_r($_POST);

$obj = new Ometz_Default();

$formatter = new formatterContent();

$RazaoSocialOmetz = "CENTRAL DE PRODUCOES GWUP S/A.";
$EnderecoOmetz = "R WILLIAM BOOTH, 2501";
$BairroOmetz = "BOQUEIRAO";
$CidadeOmetz = "CURITIBA";
$EstadoOmetz = "PARANA";
$CepOmetz = "81730-080";
$CNPJOmetz = "01.959.772/0002-07";
$Bandeira = strtoupper($_POST["bandeira"]);

$NomeCliente = $_POST["nomecliente"];
$NacionalidadeCliente = $_POST["nacionalidadecliente"];
$ProfissaoCliente = $_POST["profissaocliente"];
$EstadoCivilCliente = $_POST["estadocivilcliente"];
$RGCliente = $_POST["rgcliente"];
$CPFCliente = $_POST["cpfcliente"];
$EnderecoCliente = $_POST["enderecocliente"];
$BairroCliente = $_POST["bairrocliente"];
$CidadeCliente = $_POST["cidadecliente"];
$EstadoCliente = $_POST["estadocliente"];
$EmailCliente = $_POST["emailcliente"];

$QuantidadeModulos = $_POST["quantidademodulos"];
$QuantidadeModulosExtenso = $formatter->valorPorExtenso(intval($QuantidadeModulos,false));
$ValorModulo = $_POST["valormodulo"];
$ValorKit = $QuantidadeModulos * $ValorModulo;
$ValorModuloExtenso = $formatter->valorPorExtenso(floatval($ValorModulo), true);
$ValorModulo = money_format('%.2n', $ValorModulo);

$ValorKitExtenso = $formatter->valorPorExtenso(floatval($ValorKit), true);
$ValorKit = money_format('%.2n', $ValorKit);

$QuantidadeParcelas = intval($_POST["txtQuantidadeParcelas"]) + 1;
$QuantidadeParcelasExtenso = $formatter->valorPorExtenso(intval($QuantidadeParcelas),false, false,"parcela","parcelas");
$ValorEntrada = $_POST["valorentrada"];
$ValorEntradaExtenso = $formatter->valorPorExtenso(floatval($ValorEntrada), true);
$ValorEntrada = money_format('%.2n', $ValorEntrada);

$DataEmissaoContrato = $_POST["dataemissaocontrato"];
$DataVencimento = $DataEmissaoContrato; 
$DiaCorte = $obj->getCutOffDate("d", $DataEmissaoContrato); //"05";

$ValorParcelas = $_POST["valorparcelas"];
$ValorParcelasExtenso = $formatter->valorPorExtenso(floatval($ValorParcelas), true);
$ValorParcelas = money_format('%.2n', $ValorParcelas);
$PrimeiraMensalidade = $obj->getCutOffDate("d/m/Y", $DataEmissaoContrato); //"05";
$NumeroCartaoCredito = $_POST["firstCCNum"]."-XXXX-XXXX-".$_POST["lastCCNum"];
$OperadoraCartao = $_POST["operadoracartao"];



$chkProduct_1 = isset($_POST["chkProduct_1"]) ? "X" : "&nbsp;";
$chkProduct_2 = isset($_POST["chkProduct_2"]) ? "X" : "&nbsp;";
$chkProduct_3 = isset($_POST["chkProduct_3"]) ? "X" : "&nbsp;";
$chkProduct_4 = isset($_POST["chkProduct_4"]) ? "X" : "&nbsp;";

/*
$RazaoSocialOmetz = $_POST["razaosocialometz"];
$EnderecoOmetz = $_POST["enderecoometz"];
$BairroOmetz = $_POST["bairroometz"];
$CidadeOmetz = $_POST["cidadeometz"];
$EstadoOmetz = $_POST["estadoometz"];
$CepOmetz = $_POST["cepometz"];
$CNPJOmetz = $_POST["cnpjometz"];
$Bandeira = $_POST["bandeira"];

$NomeCliente = $_POST["nomecliente"];
$NacionalidadeCliente = $_POST["nacionalidadecliente"];
$ProfissaoCliente = $_POST["profissaocliente"];
$EstadoCivilCliente = $_POST["estadocivilcliente"];
$RGCliente = $_POST["rgcliente"];
$CPFCliente = $_POST["cpfcliente"];
$EnderecoCliente = $_POST["enderecocliente"];
$BairroCliente = $_POST["bairrocliente"];
$CidadeCliente = $_POST["cidadecliente"];
$EstadoCliente = $_POST["estadocliente"];
$EmailCliente = $_POST["emailcliente"];

$QuantidadeModulos = $_POST["quantidademodulos"];
$QuantidadeModulosExtenso = $_POST["quantidademodulosextenso"];
$ValorModulo = $_POST["valormodulo"];
$ValorModuloExtenso = $_POST["valormoduloextenso"];
$ValorKit = $_POST["valorkit"];
$ValorKitExtenso = $_POST["valorkitextenso"];

$QuantidadeParcelas = $_POST["quantidadeparcelas"];
$QuantidadeParcelasExtenso = $_POST["quantidadeparcelasextenso"];
$ValorEntrada = $_POST["valorentrada"];
$ValorEntradaExtenso = $_POST["valorentradaextenso"];
$ValorParcelas = $_POST["valorparcelas"];
$ValorParcelasExtenso = $_POST["valorparcelasextenso"];
$PrimeiraMensalidade = $_POST["primeiramensalidade"];
$NumeroCartaoCredito = $_POST["numerocartaocredito"];
$OperadoraCartao = $_POST["operadoracartao"];

$DataEmissaoContrato = $_POST["dataemissaocontrato"];

*/

/*

$NomeCliente = "FULANO DE TAL";
$NacionalidadeCliente = "BRASILEIRO";
$ProfissaoCliente = "DESENVOLVEDOR";
$EstadoCivilCliente = "SOLTEIRO";
$RGCliente = "10.111.000-6";
$CPFCliente = "000.000.000-00";
$EnderecoCliente = "AV. GETULIO VARGAS, 3812";
$BairroCliente = "AGUA VERDE";
$CidadeCliente = "CURITIBA";
$EstadoCliente = "PARANA";
$EmailCliente = "TESTE@TESTE.COM.BR";

$QuantidadeModulos = "1";
$QuantidadeModulosExtenso = "um";
$ValorModulo = "350,00";
$ValorModuloExtenso = "Trezentos e Cinquenta";
$ValorKit = "2500,00";
$ValorKitExtenso = "Dois Mil e Quinhentos Reais";

$QuantidadeParcelas = "12";
$QuantidadeParcelasExtenso = "Doze";
$ValorEntrada = "300,00";
$ValorEntradaExtenso = "Trezentos Reais";
$ValorParcelas = "250,00";
$ValorParcelasExtenso = "Duzentos e Cinquenta Reais";
$PrimeiraMensalidade = "16/01/2014";
$NumeroCartaoCredito = "1234515376";
$OperadoraCartao = "VISA";

$DataEmissaoContrato = "17/01/2014";
*/
?>