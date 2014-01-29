
SELECT 
					ORCAMENTO,
					COD_CLIENTE,
					A1_NUMRA,
					CLIENTE,        
					TIPODOC,        
					NUM_DOC,
					ENDERECO, 
					TOTAL_ORCAMENTO,
					TOTAL_CARTAO,
					TOTAL_PAGO,
					(TOTAL_CARTAO - TOTAL_PAGO) SALDO_RESTANTE
			FROM
(
SELECT 
        SCJ.CJ_NUM ORCAMENTO,
        A1_COD COD_CLIENTE,
        A1_NUMRA,
        A1_NOME AS CLIENTE,        
        Case 
                When A1_PESSOA  = 'F' Then 'CPF'
                Else    'CNPJ'
        End TIPODOC,        
        A1_CGC NUM_DOC,         
        UPPER(A1_END) ENDERECO,
        --UPPER(CAST(A1_END AS VARCHAR(100) CCSID UNICODE )) ENDERECO,
        CJ_VALORD TOTAL_CARTAO, 
        CJ_TOTPAG TOTAL_ORCAMENTO,
        (
        SELECT  
                CASE 
                        WHEN  SUM(Z0_VALCRE) IS NULL THEN 0
                        ELSE    SUM(Z0_VALCRE) 
                END                
        AS SUM_PAYMENT
        FROM SZ0500 AS SZ0
        WHERE 1=1
        AND SZ0.Z0_VALOR > 0 
        AND SZ0.Z0_HIST !=''
        AND SZ0.Z0_DONOCH = '4' 
        AND SZ0.Z0_CODORCA = SCJ.CJ_NUM
        AND D_E_L_E_T_ = ''        
        ) AS TOTAL_PAGO        
FROM 
        SA1500 AS SA1
INNER JOIN  SCJ500   AS SCJ ON SA1.A1_LOJA = SCJ.CJ_LOJA  AND SA1.A1_COD = SCJ.CJ_CLIENTE AND SA1.D_E_L_E_T_ = SCJ.D_E_L_E_T_ AND SA1.D_E_L_E_T_ = ''
) AS Q

WHERE 
        Q.ORCAMENTO in ( '000012','058742', '000336','058908' , '059845')

        
/*
SELECT * FROM SZ0500 AS SZ0
WHERE SZ0.Z0_DONOCH = '4' AND SZ0.Z0_CODORCA = '058908'
*/

/*
SELECT * FROM SCJ500 AS SCJ 
WHERE SCJ.CJ_VALORD > 0
ORDER BY CJ_NUM DESC
*/