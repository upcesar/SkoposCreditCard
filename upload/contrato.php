<?php
//  Preenchimento das Variáveis 
// VARIAVEIS DO CLIENTE
/*
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

$RazaoSocialOmetz = $_POST["razaosocialometz"];
$EnderecoOmetz = "$_POST["enderecoometz"];
$BairroOmetz = $_POST["bairroometz"];
$CidadeOmetz = $_POST["cidadeometz"];
$EstadoOmetz = $_POST["estadoometz"];
$CepOmetz = $_POST["cepometz"];
$CNPJOmetz = $_POST["cnpjometz"];

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
$Bandeira = "You Move";
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

$RazaoSocialOmetz = "CENTRAL DE PRODUCOES GWUP S/A.";
$EnderecoOmetz = "AV. GETULIO VARGAS, 3812";
$BairroOmetz = "AGUA VERDE";
$CidadeOmetz = "CURITIBA";
$EstadoOmetz = "PARANA";
$CepOmetz = "80240-041";
$CNPJOmetz = "00.000.000/0001-00";

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
$NumeroCartaoCredito = "1234-XXXX-XXXX-5678";
$OperadoraCartao = "VISA";

$DataEmissaoContrato = "17/01/2014";

?>
<!-- Contrato de Compra no Cartão de Crédito -->
<div id="Header" class="width:100%;">
	<div style="text-align:center;">
		<br>
		<h3>CONTRATO DE COMPRA E VENDA DE</h3>
		<h3>MATERIAL DID&AacuteTICO - CREDI&AacuteRIO</h3>
	</div>
</div>
<div id="Content">
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		Tendo o aluno adiante identificado e qualificado tomado conhecimento detalhado da
		metodologia de ensino desenvolvida pela <?php echo $Bandeira ?>, e estando ciente das 
		"Caracter&iacutesticas do Programa" contendo tais informa&ccedil&otildees por escrito 
		e a necessidade da aquisi&ccedil&atildeo do material did&aacutetico exclusivo para 
		atendimento das finalidades do programa, as partes resolvem firmar o presente, 
		segundo as seguintes cl&aacuteusulas:	
	</div>	
	<br>
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		<b>01. COMPRADOR</b><br>
		Nome: <?php echo $NomeCliente ?><br>
		Nacionalidade: <?php echo $NacionalidadeCliente ?><br>
		Profiss&atildeo: <?php echo $ProfissaoCliente ?><br>
		Estado Civil: <?php echo $EstadoCivilCliente ?><br>
		RG: <?php echo $RGCliente ?><br>
		CPF: <?php echo $CPFCliente	 ?><br>
		Endere&ccedilo: <?php echo $EnderecoCliente ?><br>
		Bairro: <?php echo $BairroCliente ?><br>
		Cidade/Estado: <?php echo $CidadeCliente ?> / <?php echo $EstadoCliente ?><br>
		E-mail: <?php echo $EmailCliente ?><br>		
	</div>	
	<br>
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		<b>02. VENDEDORA</b><br>
		Raz&atildeo: <?php echo $RazaoSocialOmetz ?> , sociedade empres&aacuteria, 
		sediada na <?php echo $EnderecoOmetz ?>, 
		Bairro <?php echo $BairroOmetz ?>, <?php echo $CidadeOmetz ?> - <?php echo $EstadoOmetz ?>, 
		CEP <?php echo $CepOmetz ?>, inscrita no CNPJ sob o n.&ordm <?php echo $CNPJOmetz ?>.		
	</div>
	<br>
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		<b>03. PRE&CcedilO</b><br>
		O material did&aacutetico &eacute constitu&iacutedo por <?php echo $QuantidadeModulos ?> (<?php echo $QuantidadeModulosExtenso ?>) m&oacutedulo(s) 
		ao custo de R$ <?php echo $ValorModulo ?> (<?php echo $ValorModuloExtenso?> reais) cada m&oacutedulo, 
		perfazendo o total de R$ <?php echo $ValorKit ?> (<?php echo $ValorKitExtenso?> reais).
	</div>
	<br>
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		<b>04. FORMAS DE AQUISI&Ccedil&AtildeO</b><br>
		O material did&aacutetico pr&oacuteprio ser&aacute vendido por m&oacutedulos ou na forma de kit 
		Integral com 50% de desconto, cuja op&ccedil&atildeo &eacute escolhida neste ato pelo Aluno, 
		ap&oacutes plena ci&ecircncia das condi&ccedil&otildees comerciais estabelecidas pela Editora:
		<br>
		<br>
		<table width="100%">
			<tr>
				<td width="50%" style="text-align:left;">( ) M&oacutedulo I</td>
				<td width="50%" style="text-align:right">ass._________________________________</td>
			</tr>
			<tr>
				<td width="50%" style="text-align:left;">( ) M&oacutedulo II</td>
				<td width="50%" style="text-align:right">ass._________________________________</td>
			</tr>	
			<tr>
				<td width="50%" style="text-align:left;">( ) M&oacutedulo III</td>
				<td width="50%" style="text-align:right">ass._________________________________</td>
			</tr>
			<tr>
				<td width="50%" style="text-align:left;">( ) Kit Integral</td>
				<td width="50%" style="text-align:right">ass._________________________________</td>
			</tr>
		</table>
		
	
	</div>
	<br>
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		<b>05. PARCELAMENTO</b><br>
		Sendo de exclusivo interesse do aluno parcelar o valor do material did&aacutetico adquirido, 
		a VENDEDORA aceita o fracionamento em at&eacute (<?php echo $QuantidadeParcelas ?>) <?php echo $QuantidadeParcelasExtenso ?> parcelas mensais e sucessivas, 
		sendo a primeira no importe de R$ <?php echo $ValorEntrada ?> (<?php echo $ValorEntradaExtenso ?>) com vencimento em    /  /  
		e as remanescentes no valor de R$ <?php echo $ValorParcelas ?> (<?php echo $ValorParcelasExtenso ?>)  cada, 
		todo dia 05 de cada m&ecircs, iniciando-se em <?php echo $PrimeiraMensalidade ?>.
		<br>
		O pagamento parcelado se far&aacute mediante d&eacutebito mensal no Cart&atildeo de Cr&eacutedito n.&ordm <?php echo $NumeroCartaoCredito ?>  , 
		da operadora <?php echo $OperadoraCartao ?>. Em face do fracionamento, as parcelas [est&atildeo/ser&atildeo] acrescidas de juros de 1% (um por cento) ao m&ecircs.
	</div>
	<br>	
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		<b>06. MULTAS</b><br>
		Em caso de atraso ou inadimpl&ecircncia no pagamento das parcelas na data pactuada, 
		ser&aacute cobrada a multa de 2% (dois por cento) sobre o valor do d&eacutebito em aberto, 
		juros de mora de 1% (um por cento) ao m&ecircs e corre&ccedil&atildeo monet&aacuteria medida pelo IGPM.
	
	</div>
	<br>	
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		<b>07. VENCIMENTO ANTECIPADO</b><br>
		Em caso de inadimplemento, por tratar-se de venda fracionada concedida por mera liberalidade, 
		a crit&eacuterio da VENDEDORA poder&aacute ocorrer o vencimento antecipado do d&eacutebito, 
		quando ent&atildeo estar&aacute autorizada a proceder o apontamento do nome do ALUNO nos &oacuterg&atildeos de prote&ccedil&atildeo ao cr&eacutedito, 
		e proceder &agrave execu&ccedil&atildeo do saldo devedor apurado, posto que o presente instrumento 
		&eacute elaborado de acordo com a legisla&ccedil&atildeo nacional que o considerada t&iacutetulo executivo extrajudicial, 
		similar a uma nota promiss&oacuteria, dotado das formalidades previstas no inciso II, 
		do artigo 585 do C&oacutedigo de Processo Civil.
	</div>
		<br>	
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		<b>08. MATERIAL DID&AacuteTICO</b><br>
		A <u><b>devolu&ccedil&atildeo do material did&aacutetico n&atildeo utilizado (inc&oacutelume)</b></u> se dar&aacute mediante a 
		restitui&ccedil&atildeo proporcional do pre&ccedilo correspondente, diretamente pela operadora do 
		cart&atildeo de cr&eacutedito indicada na cl&aacuteusula 5, com a reten&ccedil&atildeo do percentual de 20% (vinte por cento) 
		sobre o valor a ser restitu&iacutedo, a t&iacutetulo de despesas operacionais. 
		<u><b>N&atildeo ser&atildeo aceitos e reembols&aacuteveis os materiais utilizados</b></u>. 
	
	</div>
		<br>	
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		<b>09. FORO</b><br>
		Para dirimir qualquer d&uacutevida ou diverg&ecircncia advinda deste contrato, as partes elegem o foro 
		de Curitiba, renunciando a qualquer outro, por mais privilegiado que seja.
		<br><br>
		<b>E por estarem justos e acordados, firmam o presente instrumento em 2 (duas) 
		vias de igual teor e forma, na presen&ccedila de duas testemunhas.</b>
	</div>
	<br>
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		Data <?php echo $DataEmissaoContrato ?><br>
		<br>
		____________________________________________<br>
		Ass. do Comprador <br>
		<br>
		____________________________________________<br>
		Ass. da Vendedora <br>
		<br>
		Testemunhas<br>
		<table width="100%">
			<tr>
				<td width="50%" style="text-align:left">___________________________</td>
				<td width="50%" style="text-align:left">___________________________</td>
			</tr>
			<tr>
				<td width="50%" style="text-align:left">Nome</td>
				<td width="50%" style="text-align:left">Nome</td>
			</tr>
			<tr>
				<td width="50%" style="text-align:left">RG</td>
				<td width="50%" style="text-align:left">RG</td>
			</tr>
		</table>
	</div>	
</div>
