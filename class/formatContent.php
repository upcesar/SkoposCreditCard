<?php
	class formatterContent
	{

		public function formatField($pRowSet, $pFieldName, $pValue){

			$varNum = str_replace(",","",$pValue);
			$varNum = str_replace(".","",$varNum);
			if(is_numeric($varNum)){		
				$fieldType = db2_field_type($pRowSet, $pFieldName);
				if(strtolower($fieldType) == "string")
					return '="'.$pValue.'"';
				else
					return number_format($pValue, 2, ",","");														
			}
			else
				return $pValue;
		}
		
		public function formatDate($pDate)
		{
			date_default_timezone_set("America/Sao_Paulo");
			$varDate = $pDate; //$_POST["dtpTo"];
			$varDateR = str_replace('/', '-', $varDate);
			$ret = date('Ymd', strtotime($varDateR));
			return ($ret);
		}
		
		function valorPorExtenso($valor=0, $usamoeda = false, $masculino = true, $unidadesingular = "", $unidadeplural = "") {
						
			if($usamoeda){			
				$unidadesingular = "real";
				$unidadeplural = "reais";
				$cent_singular = "centavo";
				$cent_plural = "centavos";				
			}
			else{
				$cent_singular = "";
				$cent_plural = "";
			}
			
			$umgen = $masculino ? "um" : "uma";
			
			$singular = array($cent_singular, $unidadesingular, "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
			$plural = array($cent_plural, $unidadeplural, "mil", "milhões", "bilhões", "trilhões","quatrilhões");
		 
			$c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
			$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
			$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
			$u = array("", $umgen, "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
		 
			$z=0;
			
			$rt = "";
		 
			$valor = number_format($valor, 2, ".", ".");
			$inteiro = explode(".", $valor);
			for($i=0;$i<count($inteiro);$i++)
				for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
					$inteiro[$i] = "0".$inteiro[$i];
		 
			// $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;) 
			$fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
			for ($i=0;$i<count($inteiro);$i++) {
				$valor = $inteiro[$i];
				$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
				$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
				$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";
			
				$r = $rc.(($rc && ($rd || $ru)) ? " " : "").$rd.(($rd && $ru) ? " e " : "").$ru;
				$t = count($inteiro)-1-$i;
				$r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
				if ($valor == "000")$z++; elseif ($z > 0) $z--;
				if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t]; 
				if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
			}
		 
			return($rt ? $rt : "zero");
		}

	}
?>