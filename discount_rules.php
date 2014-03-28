<?
	$arrayRules = array(
		'rule0' => array('min' => 1, 'max' => 1, 'discount' => 15), 
		'rule1' => array('min' => 2, 'max' => 6, 'discount' => 12),
		'rule2' => array('min' => 7, 'max' => 12, 'discount' => 8)
	);
	
	echo (json_encode($arrayRules));
	
?>