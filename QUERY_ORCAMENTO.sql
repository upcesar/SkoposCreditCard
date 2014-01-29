SELECT 
        CJ_NUM ORCAMENTO,
        A1_COD COD_CLIENTE,
        A1_NUMRA,
        A1_NOME AS CLIENTE,        
        Case 
                When A1_PESSOA  = 'F' Then 'CPF'
                Else    'CNPJ'
        End TIPODOC,        
        A1_CGC NUM_DOC,         
        A1_END,
        CJ_VALORD TOTAL_CARTAO, 
        CJ_TOTPAG TOTAL_ORCAMENTO
FROM 
        SA1500 AS SA1
INNER JOIN  SCJ500   AS SCJ ON SA1.A1_LOJA = SCJ.CJ_LOJA  AND SA1.A1_COD = SCJ.CJ_CLIENTE AND SA1.D_E_L_E_T_ = SCJ.D_E_L_E_T_ AND SA1.D_E_L_E_T_ = ''
WHERE 
        SCJ.CJ_NUM in ( '058742', '000336' )

