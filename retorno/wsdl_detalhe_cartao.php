<?php
require_once '../conf.php';

class INCLUI {
  public $SZ0_CODORCA; // string
  public $SZ0_VENCTO; // string
  public $NZ0_VALENT; // float
  public $NZ0_VALCRE; // float
  public $SZ0_PARCELA; // string
  public $NZ0_VALOR; // float
  public $SZ0_DONOCH; // string
  public $SZ0_HIST; // string
  public $SZ0_EMITENT; // string
  public $NCJ_DESCONT; // float
}

class INCLUIRESPONSE {
  public $INCLUIRESULT; // string
}


/**
 * WS_DETALHE_CARTAO class
 * 
 * Web Service para pagamento por cartão de crédito 
 * 
 * @author    {author}
 * @copyright {copyright}
 * @package   {package}
 */
class WS_DETALHE_CARTAO extends SoapClient {

  private static $classmap = array(
                                    'INCLUI' => 'INCLUI',
                                    'INCLUIRESPONSE' => 'INCLUIRESPONSE',
                                   );

  public function WS_DETALHE_CARTAO($wsdl = P10_WSDL, $options = array()) {
    foreach(self::$classmap as $key => $value) {
      if(!isset($options['classmap'][$key])) {
        $options['classmap'][$key] = $value;
      }
    }

    parent::__construct($wsdl, $options);
  }

  /**
   * Inclusão do pagamento por cartão de crédito 
   *
   * @param INCLUI $parameters
   * @return INCLUIRESPONSE
   */
  public function INCLUI(INCLUI $parameters) {
    return $this->__soapCall('INCLUI', array($parameters),       array(
            'uri' => P10_WSDL, //'http://187.94.60.35:8002/ws/WS_DETALHE_CARTAO.apw',
            'soapaction' => ''
           )
      );
  }

}

?>
