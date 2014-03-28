<?php
//  Preenchimento das Variáveis 
// VARIAVEIS DO CLIENTE
require_once('postvar.php');

?>
<!-- Contrato de Compra no Cartão de Crédito -->
<div id="Header" class="width:100%;">
	<div style="text-align:center;font-size: 14px;">
		<br>
		<h3>CONTRATO DE COMPRA E VENDA DE<br>
			MATERIAL DID&Aacute;TICO - CREDI&Aacute;RIO</h3>
	</div>
</div>
<div id="Content" style="font-size: 12px;">
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		Tendo o aluno adiante identificado e qualificado tomado conhecimento detalhado da
		metodologia de ensino desenvolvida pela <?php echo $Bandeira ?>, e estando ciente das 
		"Caracter&iacute;sticas do Programa" contendo tais informa&ccedil;&otilde;es por escrito 
		e a necessidade da aquisi&ccedil;&atilde;o do material did&aacute;tico exclusivo para 
		atendimento das finalidades do programa, as partes resolvem firmar o presente, 
		segundo as seguintes cl&aacute;usulas:	
	</div>	
	<br>
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		<b>01. COMPRADOR</b><br>
		Nome: <?php echo $NomeCliente ?><br>
		Nacionalidade: <?php echo $NacionalidadeCliente ?><br>
		Profiss&atilde;o: <?php echo $ProfissaoCliente ?><br>
		Estado Civil: <?php echo $EstadoCivilCliente ?><br>
		RG: <?php echo $RGCliente ?><br>
		CPF: <?php echo $CPFCliente ?><br>
		Endere&ccedil;o: <?php echo $EnderecoCliente ?><br>
		Bairro: <?php echo $BairroCliente ?><br>
		Cidade/Estado: <?php echo $CidadeCliente ?> / <?php echo $EstadoCliente ?><br>
		E-mail: <?php echo $EmailCliente ?><br>		
	</div>	
	<br>
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		<b>02. VENDEDORA</b><br>
		Raz&atilde;o: <?php echo $RazaoSocialOmetz ?> , sociedade empres&aacute;ria, 
		sediada na <?php echo $EnderecoOmetz ?>, 
		Bairro <?php echo $BairroOmetz ?>, <?php echo $CidadeOmetz ?> - <?php echo $EstadoOmetz ?>, 
		CEP <?php echo $CepOmetz ?>, inscrita no CNPJ sob o n.&ordm; <?php echo $CNPJOmetz ?>.		
	</div>
	<br>
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		<b>03. PRE&Ccedil;O</b><br>
		O material did&aacute;tico &eacute; constitu&iacute;do por <?php echo $QuantidadeModulos ?> (<?php echo $QuantidadeModulosExtenso ?>) m&oacute;dulo(s) 
		ao custo de <?php echo $ValorModulo ?> (<?php echo $ValorModuloExtenso?> ) cada m&oacute;dulo, 
		perfazendo o total de <?php echo $ValorKit ?> (<?php echo $ValorKitExtenso?>).
	</div>
	<br>
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		<b>04. FORMAS DE AQUISI&Ccedil;&Atilde;O</b><br>
		O material did&aacute;tico pr&oacute;prio ser&aacute; vendido por m&oacute;dulos ou na forma de kit 
		Integral com 50% de desconto, cuja op&ccedil;&atilde;o &eacute; escolhida neste ato pelo Aluno, 
		ap&oacute;s plena ci&ecirc;ncia das condi&ccedil;&otilde;es comerciais estabelecidas pela Editora:
		<br>
		<br>
		<table width="100%">
			<tr>
				<td width="50%" style="text-align:left;">(<?= $chkProduct_2 ?>) M&oacute;dulo I</td>
				<td width="50%" style="text-align:right">ass._________________________________</td>
			</tr>
			<tr>
				<td width="50%" style="text-align:left;">(<?= $chkProduct_3 ?>) M&oacute;dulo II</td>
				<td width="50%" style="text-align:right">ass._________________________________</td>
			</tr>	
			<tr>
				<td width="50%" style="text-align:left;">(<?= $chkProduct_4 ?>) M&oacute;dulo III</td>
				<td width="50%" style="text-align:right">ass._________________________________</td>
			</tr>
			<tr>
				<td width="50%" style="text-align:left;">(<?= $chkProduct_1 ?>) Kit Integral</td>
				<td width="50%" style="text-align:right">ass._________________________________</td>
			</tr>
		</table>
		
	
	</div>
	<br>
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		<b>05. PARCELAMENTO</b><br>
		Sendo de exclusivo interesse do aluno parcelar o valor do material did&aacute;tico adquirido, 
		a VENDEDORA aceita o fracionamento em at&eacute; (<?php echo $QuantidadeParcelas ?>) <?php echo $QuantidadeParcelasExtenso ?> mensais e sucessivas, 
		sendo a primeira no importe de <?php echo $ValorEntrada ?> (<?php echo $ValorEntradaExtenso ?>) com vencimento em  <?php echo $DataVencimento ?>  
		e as remanescentes no valor de <?php echo $ValorParcelas ?> (<?php echo $ValorParcelasExtenso ?>) cada, 
		todo dia <?php echo $DiaCorte ?> de cada m&ecirc;s, iniciando-se em <?php echo $PrimeiraMensalidade ?>.
		<br>
		O pagamento parcelado se far&aacute; mediante d&eacute;bito mensal no Cart&atilde;o de Cr&eacute;dito n.&ordm; <?php echo $NumeroCartaoCredito ?>  , 
		da operadora <?php echo $OperadoraCartao ?>. Em face do fracionamento, as parcelas [est&atilde;o/ser&atilde;o] acrescidas de juros de 1% (um por cento) ao m&ecirc;s.
	</div>
	<br>	
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		<b>06. MULTAS</b><br>
		Em caso de atraso ou inadimpl&ecirc;ncia no pagamento das parcelas na data pactuada, 
		ser&aacute;cobrada a multa de 2% (dois por cento) sobre o valor do d&eacute;bito em aberto, 
		juros de mora de 1% (um por cento) ao m&ecirc;s e corre&ccedil;&atilde;o monet&aacute;ria medida pelo IGPM.
	
	</div>
	<br>	
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		<b>07. VENCIMENTO ANTECIPADO</b><br>
		Em caso de inadimplemento, por tratar-se de venda fracionada concedida por mera liberalidade, 
		a crit&eacute;rio da VENDEDORA poder&aacute; ocorrer o vencimento antecipado do d&eacute;bito, 
		quando ent&atilde;o estar&aacute; autorizada a proceder o apontamento do nome do ALUNO nos &oacute;rg&atilde;os de prote&ccedil;&atilde;o ao cr&eacute;dito, 
		e proceder &agrave; execu&ccedil;&atilde;o do saldo devedor apurado, posto que o presente instrumento 
		&eacute; elaborado de acordo com a legisla&ccedil;&atilde;o nacional que o considerada t&iacute;tulo executivo extrajudicial, 
		similar a uma nota promiss&oacute;ria, dotado das formalidades previstas no inciso II, 
		do artigo 585 do C&oacute;digo de Processo Civil.
	</div>
		<br>	
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		<b>08. MATERIAL DID&Aacute;TICO</b><br>
		A <u><b>devolu&ccedil;&atilde;o do material did&aacute;tico n&atilde;o utilizado (inc&oacute;lume)</b></u> se dar&aacute; mediante a 
		restitui&ccedil;&atilde;o proporcional do pre&ccedil;o correspondente, diretamente pela operadora do 
		cart&atilde;o de cr&eacute;dito indicada na cl&aacute;usula 5, com a reten&ccedil;&atilde;o do percentual de 20% (vinte por cento) 
		sobre o valor a ser restitu&iacute;do, a t&iacute;tulo de despesas operacionais. 
		<u><b>N&atilde;o ser&atilde;o aceitos e reembols&aacute;veis os materiais utilizados</b></u>. 
	
	</div>
		<br>	
	<div style="text-align:left;margin-left:10%;margin-right:10%;">
		<b>09. FORO</b><br>
		Para dirimir qualquer d&uacute;vida ou diverg&ecirc;ncia advinda deste contrato, as partes elegem o foro 
		de Curitiba, renunciando a qualquer outro, por mais privilegiado que seja.
		<br><br>
		<b>E por estarem justos e acordados, firmam o presente instrumento em 2 (duas) 
		vias de igual teor e forma, na presen&ccedil;a de duas testemunhas.</b>
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
