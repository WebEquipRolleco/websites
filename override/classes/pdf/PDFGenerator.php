<?php

class PDFGenerator extends PDFGeneratorCore {

	public function writePage() {

		$this->SetHeaderMargin(5);
		$this->SetFooterMargin(30);
		// [/Activis] Modified
		$this->setMargins(10, 30, 10);
		// [/Activis]
		$this->SetAutoPageBreak(true, 31);
	
		$this->AddPage();
	
		/* $this->writeHTML($this->content, true, false, true, false, '');*/
		$this->writeHTML($this->content, false, false, false, false, '');
	}
	
}