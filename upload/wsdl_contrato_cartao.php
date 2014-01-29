<?php

require_once '../conf.php';

class INCLUI {
  public $SVZK_CODORC; // string
  public $SVZK_NOMARQ; // string
  public $SVZK_CAMARQ; // string
  public $SVZK_USER; // string
}

class INCLUIRESPONSE {
  public $INCLUIRESULT; // string
}


/**
 * WS_CONTRATO_CARTAO class
 * 
 * Web Service para enlace de contrato digitalizados 
 * 
 * @author    {author}
 * @copyright {copyright}
 * @package   {package}
 */
class WS_CONTRATO_CARTAO extends SoapClient {

  private static $classmap = array(
                                    'INCLUI' => 'INCLUI',
                                    'INCLUIRESPONSE' => 'INCLUIRESPONSE',
                                   );

  public function WS_CONTRATO_CARTAO($wsdl = P10_WSDL_CONTRACT, $options = array()) {
    foreach(self::$classmap as $key => $value) {
      if(!isset($options['classmap'][$key])) {
        $options['classmap'][$key] = $value;
      }
    }
    parent::__construct($wsdl, $options);
  }

  /**
   * Inclusão de informação de contratos digitalizados 
   *
   * @param INCLUI $parameters
   * @return INCLUIRESPONSE
   */
  public function INCLUI(INCLUI $parameters) {
    return $this->__soapCall('INCLUI', array($parameters),       array(
            'uri' => P10_WSDL_CONTRACT,
            'soapaction' => ''
           )
      );
  }

}

?>
