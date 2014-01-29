
SELECT 
       Q.DATA_PAGAMENTO AS "Data Pagamento",
	Q.COD_AUTORIZACAO AS "Cod. Autorizacao",
	Q.TRASACAO AS "Cod. Trasacao",
	Q.VALOR_PAGAMENTO AS "Valor Pagamento",
	Q.NUM_PARCELA AS "Num Parcelas",
	Q.VALOR_PARCELA AS "Valor Parcela"
FROM
(
SELECT  
        SZ0.Z0_CODORCA AS ORCAMENTO,
        SZ0.Z0_DATA AS DATA_PAGAMENTO,        
        
        CASE 
           WHEN INSTR(SZ0.Z0_HIST, 'COD AUTORIZACAO:') > 0  THEN 
                   TRIM(
                   SUBSTR(SZ0.Z0_HIST, 
                                INSTR(SZ0.Z0_HIST, 'COD AUTORIZACAO:') + LENGTH('COD AUTORIZACAO:'), 
                                INSTR(SZ0.Z0_HIST, ';') - (INSTR(SZ0.Z0_HIST, 'COD AUTORIZACAO:') + LENGTH('COD AUTORIZACAO:'))
                                )
                    )
           ELSE ''
        END AS COD_AUTORIZACAO,   

        CASE 
           WHEN INSTR(SZ0.Z0_HIST, 'TRASACAO:') > 0  THEN 
                   TRIM(
                   SUBSTR(SZ0.Z0_HIST, 
                                INSTR(SZ0.Z0_HIST, 'TRASACAO:') + LENGTH('TRASACAO:'), 
                                INSTR(SZ0.Z0_HIST, ';', 1, 2) - (INSTR(SZ0.Z0_HIST, 'TRASACAO:') + LENGTH('TRASACAO:'))
                                )
                    )
           ELSE ''
        END AS TRASACAO,   
                                                
        SZ0.Z0_VALCRE AS VALOR_PAGAMENTO,
        SZ0.Z0_PARCELA AS NUM_PARCELA,
        SZ0.Z0_VALOR AS VALOR_PARCELA

FROM DB2.SZ0500 AS SZ0
WHERE 1=1 
AND SZ0.Z0_DONOCH = '4' 
AND D_E_L_E_T_ = ''  
) AS Q
WHERE  1=1
--AND Q.ORCAMENTO = '058908'
--AND TRASACAO = '73508255014694'
