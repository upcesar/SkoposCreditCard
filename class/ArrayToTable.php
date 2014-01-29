<?php
class ArrayToTable
{
	private $_tTable = "<table border='1' id='box-table-a'>";
	private $_cTable = "</table>";
	private $_tHeader = "<thead>";
	private $_cTHeader = "</thead>";
	private $_tHeaderTitles = array();
	
	
	public function makeTable($pRows, $title = null) {
		$content="";
		foreach ($pRows as $tRows) {
			$tHeader = array_keys($tRows);
			if(count($this->_tHeaderTitles) < $tHeader)
				$this->_tHeaderTitles=$tHeader;

			$header = array();
			$content .= "<tr>";

			$y = 0;
			foreach ( $tRows as $tRow ) {
				$tRow = (empty ( $tRow ) || $tRow === " ") ? "&nbsp;" : $tRow;
// 				$html .= "<td class='".$classes[$y]."'>$tRow</td>"; $y++;
				$content .= "<td class=''>$tRow</td>"; $y++;
			}
			$content .= "</tr>";
		}
		if ($title)
			$header[] = "<tr scope='col'><th colspan=".count($this->_tHeaderTitles).">".$title."</th></tr>";

		$header[] = "<tr scope='col'>";
// 		$y = 0;
		$classes = array();
		foreach ($tHeader as $tRow){
			$header[] = "<th>$tRow</th>";
// 			$classes[$y] = strtolower($tRow); $y++;
		}
		$header[] = "</tr>";
		$return =$this->_tTable.$this->_tHeader.implode("", $header).$this->_cTHeader.$content;
		return $return;
	}

}
