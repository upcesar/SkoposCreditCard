SELECT
    SE1.E1_ADM,
    SE1.E1_NRDOC,
    SE1.E1_DTACRED,
    REPLACE(CHAR(DATE(CURRENT DATE),ISO),'-','') TODAY,
    '   |   ' SEPARATOR,
    SE1.*
FROM
    DB2.SE1050 AS SE1
/*
INNER JOIN 
DB2.SZ0500 AS SZ0
ON SZ0.Z0_DONOCH IN ('4', '5') 
AND SZ0.Z0_HIST = SCJ.CJ_NUM
AND D_E_L_E_T_ = ''        
*/
WHERE 1=1
AND    SE1.E1_TIPO='CC'
AND SE1.E1_NRDOC !=''
AND SE1.E1_DTACRED <= REPLACE(CHAR(DATE(CURRENT DATE),ISO),'-','')
AND SE1.E1_DTACRED != ''
AND SE1.D_E_L_E_T_ = ''
// AND SE1.E1_EMIS1 LIKE '201312%'
// AND SE1.E1_NRDOC = '73520743256078'
