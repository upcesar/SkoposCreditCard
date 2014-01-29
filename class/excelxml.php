<?php
class ExcelXml
{
	private $xml_content="";
	private $sheetCount = 0;

	private $_beginWorkSheet = '<Worksheet ss:Name=@SheetName>';
	private $_beginTable = '<Table ss:ExpandedColumnCount="@colcount" ss:ExpandedRowCount="@rowcount" x:FullColumns="1" x:FullRows="1" ss:DefaultRowHeight="15">'
	private $_closeTable = '</Table>'
	private $_closeWorkSheet = "</Worksheet>";
	private $_tHeader = "<thead>";
	private $_cTHeader = "</thead>";
	private $_tHeaderTitles = array();
	
	
	// Constructor
	public function __construct(){		
				
		$this->initializeXML();		
	}
	
	private function initializeXML(){
		
		$this->xml_content='		
			<?xml version="1.0"?>
			<?mso-application progid="Excel.Sheet"?>
			<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
			 xmlns:o="urn:schemas-microsoft-com:office:office"
			 xmlns:x="urn:schemas-microsoft-com:office:excel"
			 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
			 xmlns:html="http://www.w3.org/TR/REC-html40">
			 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
			  <Author>Ometz Group</Author>
			  <LastAuthor>Ometz Group</LastAuthor>
			  <Created>2013-05-16T19:31:07Z</Created>
			  <Version>14.00</Version>
			 </DocumentProperties>
			 <OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
			  <AllowPNG/>
			 </OfficeDocumentSettings>
			 <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
			  <WindowHeight>10035</WindowHeight>
			  <WindowWidth>22995</WindowWidth>
			  <WindowTopX>480</WindowTopX>
			  <WindowTopY>45</WindowTopY>
			  <ProtectStructure>False</ProtectStructure>
			  <ProtectWindows>False</ProtectWindows>
			 </ExcelWorkbook>
			 <Styles>
			  <Style ss:ID="Default" ss:Name="Normal">
			   <Alignment ss:Vertical="Bottom"/>
			   <Borders/>
			   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
			   <Interior/>
			   <NumberFormat/>
			   <Protection/>
			  </Style>
			 </Styles>
		';
	}
		
	public function clearSheets(){		
		$this->initializeXML();		
	}
	
	public function addSheet($pRows, $pSheetName=""){
		
		$content = '';
		$this->sheetCount++;		
		if($pSheetName=="")
			$pSheetName="Sheet ".strval($this->sheetCount);
		
		
		$this->_beginWorkSheet = str_replace ("@SheetName",$pSheetName, $this->_beginWorkSheet);
		
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
				$content .= "<td class=''>$tRow</td>"; $y++;
			}
			$content .= "</tr>";
		}
		if ($title)
			$header[] = "<tr scope='col'><th colspan=".count($this->_tHeaderTitles).">".$title."</th></tr>";

		$header[] = "<tr scope='col'>";

		foreach ($tHeader as $tRow){
			$header[] = "<th>$tRow</th>";
		}
		$header[] = "</tr>";
		$return =$this->_beginWorkSheet.$this->_tHeader.implode("", $header).$this->_cTHeader.$content;
		return $return;		
		
		
		// Generate a test sheet
		/*
		$this->xml_content.='
		<Worksheet ss:Name="'.$pSheetName.'">
			<Table ss:ExpandedColumnCount="3" ss:ExpandedRowCount="5" x:FullColumns="1" x:FullRows="1" ss:DefaultRowHeight="15">
				<Row ss:AutoFitHeight="0">
					<Cell><Data ss:Type="String">titulo</Data></Cell>
				</Row>
				<Row ss:Index="4" ss:AutoFitHeight="0">
					<Cell><Data ss:Type="String">nome</Data></Cell>
					<Cell><Data ss:Type="String">sobrenome</Data></Cell>
					<Cell><Data ss:Type="String">endereco</Data></Cell>
				</Row>
				<Row ss:AutoFitHeight="0">
					<Cell><Data ss:Type="String">cesar</Data></Cell>
					<Cell><Data ss:Type="String">urdaneta</Data></Cell>
					<Cell><Data ss:Type="String">joao dranka 99 apto 1202</Data></Cell>
				</Row>
			</Table>
		</Worksheet>
		';
		*/
		
		
	}
	
	public function flushXml()	{
		$this->xml_content.= '</Workbook>';
		$data = $this->xml_content;		
		$this->initializeXML();
		return $data;
	}
}