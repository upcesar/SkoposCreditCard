<?php
/*
 * Constants
 */
define("VERSION","3.0.0");
define("CLASS_PATH",realpath(dirname( __FILE__ ).'/../class/'));
//define("SERVICE_ADDRESS","http://ws.mindset.net.br/");
define("JBOSS_SERVICE_ADDRESS","http://192.168.20.10:8080/");
// define("SERVICE_ADDRESS","http://edersom.web.local/WebCore_Ws/htdocs/");

define('APPLICATION_TOKEN', 'e5e6bce3709766173a7578917acf7a1c324315381bd8554130e6dcb2362c17e4ace4254090b30f9c5ac0f6682d2024b9bb6e18407694dd9f4ed724a5d8ca4087');


//Dias de cortes (cutoff day)
define('CUT_OFF_1', '05');  		// Primeiro
define('CUT_OFF_2', '15');			// Segundo
define('CUT_OFF_3', '25');			// Terceiro

define('MAX_YEAR_EXPIRE', 10);			// For credit card chooser.

define('MAX_QUOTES_NUMBER_INTERES', 17);	// Maximum number of quote to select.
define('MAX_QUOTES_NO_INTEREST', 12);		// Maximum number of quote without rate charges.

define('MIN_QUOTES_INTEREST', 1);			// Minimum number of quote when applying rate charges.

define('NUM_QUOTES_X_COL', 12);				// Maximum number of quote per column.
define('NUM_QUOTES_X_COL_INTEREST', 12);	// Maximum number of quote per column.

define('NUM_BLOCK', 1);						// Selection blocks number in rows.
define('RATE_PERC', 1);					// % Rate ammount applied to quotes

define ('DISC_REG_SALE_PERC', 10);		// % Discount when regular sale is chosen.
define ('DOWN_PAYMENT', 20);			// % down payment on recurrent sale.

//Enviroment Setting
define ('AMBIENTE_DB', 'HOM');

define ('BASE_URL','http://cesar.web.mindset/cartao_skopos/dev/');
define ('IMG_FOLDER',BASE_URL.'img/');

define('UPLOAD_ROOT_FOLDER', 'files/');
define('UPLOAD_MAX_SIZE', 3145728);	//  Maximum file size for uploading expressed in byte, that means 3 MB.


define('VALIDATE_WS_ONSTART', false);

if(AMBIENTE_DB == 'HOM'){
	define ('P10_WSDL','http://187.94.60.37:8002/ws/WS_DETALHE_CARTAO.apw?WSDL');
	define ('PAYMENT_GATEWAY', 'https://teste.aprovafacil.com/cgi-bin/STAC/skoposeditora/');
	define ('RECURRING_GATEWAY', 'https://teste.aprovafacil.com/cgi-bin/APFW/skoposeditora/APC');
}
else{
	define ('P10_WSDL','http://187.94.60.35:8002/ws/WS_DETALHE_CARTAO.apw?WSDL');
	define ('PAYMENT_GATEWAY', 'https://www.aprovafacil.com/cgi-bin/STAC/skoposeditora/');
	define ('RECURRING_GATEWAY', 'https://teste.aprovafacil.com/cgi-bin/APFW/skoposeditora/APC');
}

//CONSTANT FOR CONTRACT WEB SERVICE.
define ('P10_WSDL_CONTRACT', str_replace('WS_DETALHE_CARTAO','WS_CONTRATO_CARTAO', P10_WSDL));

set_include_path(CLASS_PATH.PATH_SEPARATOR.get_include_path());
//define ('PAYMENT_GATEWAY', BASE_URL.'processpayment/');

define ('PAYMENT_RETURN', BASE_URL.'retorno/');


// Libraries section
require_once 'class/request.php';
require_once 'class/database.php';
require_once 'class/validations.php';
require_once 'class/ArrayToTable.php';
require_once 'class/default.php';
require_once 'class/formatContent.php';
require_once 'class/phpmailer/PHPMailerAutoload.php';

?>