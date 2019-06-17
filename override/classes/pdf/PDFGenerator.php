<?php

class PDFGenerator extends PDFGeneratorCore {

	public function writePage() {

		$this->SetHeaderMargin(5);
		$this->SetFooterMargin(18);
		// [/Activis] Modified
		$this->setMargins(10, 30, 10);
		// [/Activis]
		$this->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
	
		$this->AddPage();
	
		/* $this->writeHTML($this->content, true, false, true, false, '');*/
		$this->writeHTML($this->content, false, false, false, false, '');
	}
	
}