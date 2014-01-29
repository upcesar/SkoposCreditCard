<?php

$urlapf = "https://www.meusite.com.br/cgi-bin/CBEAPF/APC?" .
          'NumeroCartao=' . $HTTP_POST_VARS['NumeroCartao'] .
          '&MesValidade=' . $HTTP_POST_VARS['MesValidade'] .
          '&AnoValidade=' . $HTTP_POST_VARS['AnoValidade'] .
          '&CodigoSeguranca=' . $HTTP_POST_VARS['CodigoSeguranca'] .
          '&QuantidadeParcelas=' . $HTTP_POST_VARS['QuantidadeParcelas'] .
          '&ValorDocumento=' . $HTTP_POST_VARS['ValorDocumento'];

$resultadotransacao = file($urlapf);

if (substr($resultadotransacao[2], 0, 4) == 'True') {
    $Transacao = substr($resultadotransacao[11], 0, 14);
    $CodigoAutorizacao = substr($resultadotransacao[8], 0, 6);

// Transação Aprovada deve ser confirmada após salvar os dados no seu banco de dados
    $urlapf = "https://www.meusite.com.br/cgi-bin/CBEAPF/CAP?" .
              'Transacao=' . $Transacao;
    $resultadotransacao = file($urlapf);

}
else {
    echo "Não Aprovado";
}

?>
