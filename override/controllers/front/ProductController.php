<?php

class ProductController extends ProductControllerCore {

    public function initContent() {

    	if(Tools::getIsset('dl_pdf'))
    		return $this->downloadPDF();

    	parent::initContent();
    }

    private function downloadPDF() {

    	$pdf = new PDF(array('product'=>$this->product), 'Product', $this->context->smarty);
    	die($pdf->render());
    }
}