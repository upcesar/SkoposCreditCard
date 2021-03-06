SELECT 
    VZL.R_E_C_N_O_,
    SE1.E1_CLIENTE,
    SE1.E1_LOJA,
    CASE
      WHEN SE1.E1_NUMRA != '' THEN E1_NUMRA
      ELSE 'NAO INFORMADO'
    END E1_NUMRA,
    SE1.E1_NOMCLI,
    SE1.E1_DTACRED,
    VZL.VZL_OBS,
    VZL.VZL_STATUS,
    CASE
  	  WHEN INSTR(SZ0.Z0_HIST, 'TRASACAO:') >   0  THEN
  	    TRIM(
        SUBSTR(SZ0.Z0_HIST, 
               INSTR(SZ0.Z0_HIST, 'TRASACAO:') + LENGTH('TRASACAO:'), 
               INSTR(SZ0.Z0_HIST, ';', 1, 2) - (INSTR(SZ0.Z0_HIST, 'TRASACAO:') + LENGTH('TRASACAO:'))
              )
            )
      ELSE ''
	  END AS TRANSACAO

FROM DB2.VZL500 VZL
INNER JOIN DB2.SE1050 SE1 ON 
    SE1.E1_FILIAL = VZL.VZL_CODFIL AND 
    SE1.E1_PREFIXO = VZL.VZL_PREFIX AND
    SE1.E1_NUM = VZL.VZL_NUM AND
    SE1.E1_PARCELA = VZL.VZL_PARCEL AND
    SE1.E1_TIPO = VZL.VZL_TIPO
INNER JOIN 
	  DB2.SZ0500 AS SZ0 ON SE1.E1_NRDOC = SUBSTR(SZ0.Z0_HIST, 36, 14) 

ORDER BY SE1.E1_DTACRED DESC, VZL.R_E_C_N_O_ DESC
FETCH FIRST 20 ROWS ONLY