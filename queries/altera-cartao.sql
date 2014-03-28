SELECT 
  SE1.E1_CLIENTE,
  SZ0.Z0_CODORCA,
  CASE
	  WHEN INSTR(SZ0.Z0_HIST, 'TRASACAO:') > 0  THEN
	    TRIM(
      SUBSTR(SZ0.Z0_HIST, 
             INSTR(SZ0.Z0_HIST, 'TRASACAO:') + LENGTH('TRASACAO:'), 
             INSTR(SZ0.Z0_HIST, ';', 1, 2) - (INSTR(SZ0.Z0_HIST, 'TRASACAO:') + LENGTH('TRASACAO:'))
            )
          )
      ELSE ''
    END AS TRANSACAO,

  CASE
	  WHEN INSTR(SZ0.Z0_EMITENT, 'CRE:') > 0  THEN
	    TRIM(
      SUBSTR(SZ0.Z0_EMITENT, 
             INSTR(SZ0.Z0_EMITENT, 'CRE:') + LENGTH('CRE:'), 
             INSTR(SZ0.Z0_EMITENT, ';') - (INSTR(SZ0.Z0_EMITENT, 'CRE:') + LENGTH('CRE:'))
            )
          )
      ELSE ''
    END AS MASK_CC,
  MIN(SE1.E1_DTACRED) E1_DTACRED, MIN(SE1.E1_PARCELA) E1_PARCELA, SE1.E1_SALDO
FROM 
  DB2.SE1050 AS SE1
INNER JOIN 
  DB2.SZ0500 AS SZ0 ON SE1.E1_NRDOC = SUBSTR(SZ0.Z0_HIST, 36, 14) 
WHERE 1=1 
AND SE1.E1_DTACRED <> ''
AND SZ0.Z0_EMITENT <> ''
AND SZ0.Z0_DONOCH = '5'
AND SE1.E1_CLIENTE = '078605'
AND SE1.E1_LOJA = '01'

GROUP BY  SE1.E1_CLIENTE, SZ0.Z0_CODORCA, SZ0.Z0_HIST, SZ0.Z0_EMITENT, SE1.E1_SALDO