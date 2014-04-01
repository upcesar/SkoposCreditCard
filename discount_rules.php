<?
	class DiscountRule {
			
		private $arrayRules = array();
		
		public function DiscountRule(){
			
			$this->arrayRules = array(
						'rule0' => array('min' => 1, 'max' => 1, 'discount' => 15), 
						'rule1' => array('min' => 2, 'max' => 6, 'discount' => 12),
						'rule2' => array('min' => 7, 'max' => 12, 'discount' => 8)
			);
		}
		
		public function getRulesJSON(){
			return (json_encode($this->arrayRules));
		}
		
		public function getRules($value=""){
			if($value != ""){				
				$num_value = floatval($value);
				
				foreach ($this->arrayRules as $rule) 
					if($num_value >= $rule['min'] & $num_value <= $rule['max'])
						return floatval($rule['discount']);
	
			}
			else 
				return ($this->arrayRules);				
			
		}
	}
		
	
	if(isset($_POST['output']) && $_POST['output'] == "json"){		
		$rule = new DiscountRule();
		echo ($rule->getRulesJSON());		
	}
	
?>