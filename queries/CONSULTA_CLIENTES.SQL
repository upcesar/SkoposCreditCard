
			SELECT 
					MT3.MT3_CODFIL,					
					UPPER(MT3.MT3_FIL) AS MT3_FIL,
					SA1.A1_COD COD_CLIENTE,
					SA1.A1_NUMRA,
					SA1.A1_NOME AS CLIENTE,        
				       CASE 
					   WHEN SX5_NAC.X5_DESCRI IS NOT NULL THEN UPPER(SX5_NAC.X5_DESCRI)
                                           ELSE 'OUTRO' 
                                       END AS NACIONALIDAD,
                                       CASE
						WHEN JA2.JA2_PROFIS IS NOT NULL AND JA2.JA2_PROFIS != '' THEN UPPER(JA2.JA2_PROFIS)
						ELSE 'NAO INFORMADO'
					END AS PROFISSAO,
					CASE 
					         WHEN SX5_EC.X5_DESCRI IS NOT NULL THEN UPPER(SX5_EC.X5_DESCRI)
                                                 ELSE 'OUTRO' 
                                        END AS ESTADO_CIVIL,
                                       SA1.A1_RG RG,
                                       Case 
							When SA1.A1_PESSOA  = 'F' Then 'CPF'
							Else    'CNPJ'
					End TIPODOC,
					SA1.A1_CGC NUM_DOC,
					CASE
						WHEN SA1.A1_END IS NOT NULL THEN UPPER(SA1.A1_END)
						ELSE 'NAO INFORMADO'
					END AS ENDERECO,
					UPPER(SA1.A1_BAIRRO) AS BAIRRO,
					UPPER(SA1.A1_MUN) AS CIDADE,
					UPPER(SA1.A1_EST) AS ESTADO,
					SA1.A1_EMAIL AS E_MAIL					   
        			FROM 
                        DB2.SA1500 AS SA1                                
                                INNER JOIN DB2.JA2500 AS JA2 ON
                                        SA1.A1_COD = JA2.JA2_CLIENT  AND
                                        SA1.A1_LOJA = JA2.JA2_LOJA
                                

			        INNER JOIN DB2.MT3500 AS MT3 ON 
						MT3.MT3_CODEMP = SA1.A1_CEMPANT AND
						MT3.MT3_CODFIL = SA1.A1_MSFIL
						
				LEFT JOIN DB2.SX5500 AS SX5_NAC ON
                                                SX5_NAC.X5_CHAVE = JA2.JA2_NACION AND SX5_NAC.X5_TABELA = '34'
			
				LEFT JOIN DB2.SX5500 AS SX5_EC ON
                                                SX5_EC.X5_CHAVE = JA2.JA2_ECIVIL AND SX5_EC.X5_TABELA = '33'
			
			WHERE SA1.A1_COD = '000054' AND SA1.A1_LOJA = '01'
