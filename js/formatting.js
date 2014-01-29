$(function($){

	Number.prototype.formatNumber = function(c, d, t, m){
		var n = this,
			c = isNaN(c = Math.abs(c)) ? 2 : c,
			d = d == undefined ? "." : d,
			t = t == undefined ? "," : t,
			m = m == undefined ? "" : m.trim() + " ",
			s = n < 0 ? "-" : "",
			i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
			j = (j = i.length) > 3 ? j % 3 : 0;
		
		return s + m + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	};
	
	String.prototype.formatNumber = function(c, d, t, m){
		return (parseInt(this)).formatNumber(c, d, t, m);
	}

	String.prototype.lpad = function(padString, length, sep_thousand, minlength) {
		var str = this,
			strint = "";

		sep_thousand = sep_thousand == undefined ? "" : sep_thousand;
		minlength = minlength == undefined ? 0 : minlength;

		while (str.length < length ){
			strint = str.substring(0,str.length - minlength);
			if( (sep_thousand != "") & (strint.length % 4 == 3) & (strint.length > minlength - 1) & (str.length < length - 1) ){
				str = sep_thousand + str;
			}
			else
				str = padString + str;
		}
		return str;
	}

	//CPF Validations
	Number.prototype.formatCPF = function(){
		var n = (this),
			k = "",
			s = "NaN";
		
		if(n < Math.pow(10,11)){		
			n = (this / 100);						
			
			var	k = (n.formatNumber(2,'-','.')).toString(),
				s = k.lpad('0', 14, '.', 3);
		}

		return s;
	};
	
	String.prototype.formatCPF = function(){
		return (parseInt(this)).formatCPF();
	}
	
	//CNPJ Validations (14 digits)
	// Examples: 
	// 03847655000198
	// 14218835000127
	// 11177299000170	
	
	// 03.847.655/0001-98
	// 14.218.835/0001-27
	// 11.177.299/0001-70
	
	Number.prototype.formatCNPJ = function(){

		var n = (this),
			k = "",
			strn = "",
			s = "NaN",
			numbers = new Array();
		
		if(n < Math.pow(10, 14)){		
			strn = n.toString().lpad('0', 14);
			numbers[0] = strn.substr(0,2).lpad('0', 2) + ".";
			numbers[1] = strn.substr(2,3).lpad('0', 3) + ".";
			numbers[2] = strn.substr(5,3).lpad('0', 3) + "/";
			numbers[3] = strn.substr(8,4).lpad('0', 4) + "-";
			numbers[4] = strn.substr(12,2).lpad('0', 2);
			s = "";
			numbers.forEach(function(item){
				s += item;
			});						
		}

		return s;
	}
	
	String.prototype.formatCNPJ = function(){
		return (parseInt(this)).formatCNPJ();
	}
	
	
}
)