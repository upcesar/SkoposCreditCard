
SELECT Q.*, SCJ.CJ_VALORD FROM  
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
                WHERE SZ0.Z0_DONOCH = '4' 
                AND SZ0.Z0_CODORCA = '058908' --SCJ.CJ_NUM
                AND D_E_L_E_T_ = ''  
                

UNION 
SELECT  
                SZ0.Z0_CODORCA AS ORCAMENTO,
                '---' AS DATA_PAGAMENTO,                
                '---' AS COD_AUTORIZACAO,   
                'TOTAL ->' AS TRASACAO,                                                           
                SUM(SZ0.Z0_VALCRE) AS VALOR_PAGAMENTO,
                0 AS NUM_PARCELA,
                0 AS VALOR_PARCELA
        
                FROM DB2.SZ0500 AS SZ0
                WHERE SZ0.Z0_DONOCH = '4' 
                AND SZ0.Z0_CODORCA = '058908' --SCJ.CJ_NUM
                AND D_E_L_E_T_ = ''  
                GROUP BY SZ0.Z0_CODORCA

) AS Q

INNER JOIN SCJ500 SCJ ON Q.ORCAMENTO = SCJ.CJ_NUM
WHERE (SCJ.CJ_VALORD > Q.VALOR_PAGAMENTO) -- AND Q.TRASACAO = 'TOTAL ->')
