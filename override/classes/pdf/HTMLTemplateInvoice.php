<?php

class HTMLTemplateInvoice extends HTMLTemplateInvoiceCore {

	/**
    * Returns the template's HTML header
    * @return string HTML header
    **/
    public function getHeader() {
        
        $this->assignCommonHeaderData();
        $this->smarty->assign('order', $this->order);

        return $this->smarty->fetch($this->getTemplate('header.invoice'));
    }

    /**
    * Returns the template's HTML footer
    * @return string HTML footer
    **/
    public function getFooter() {

        $this->smarty->assign('order', $this->order);
        return $this->smarty->fetch($this->getTemplate('footer.invoice'));
    }
    
}