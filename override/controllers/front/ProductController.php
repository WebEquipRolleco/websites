<?php

class ProductController extends ProductControllerCore {

    public function initContent() {

    	if(Tools::getIsset('dl_pdf'))
    		return $this->downloadPDF();

    	if(Tools::getIsset('dl_demo'))
    		return $this->downloadDemo();

    	parent::initContent();
    }

    /**
    * Télécharge le PDF produit (avec prix)
    **/
    private function downloadPDF() {

    	$pdf = new PDF(array('product'=>$this->product), PDF::TEMPLATE_PRODUCT, $this->context->smarty);
    	die($pdf->render());
    }

    /**
    * Télécharge le PDF démo produit (sans prix)
    **/
    private function downloadDemo() {

    	$pdf = new PDF(array('product'=>$this->product), PDF::TEMPLATE_PRODUCT_DEMO, $this->context->smarty);
    	die($pdf->render());
    }

}