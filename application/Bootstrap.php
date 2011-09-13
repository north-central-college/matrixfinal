<?php


class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
                               

    // allows use of namespace
    public function init(){
		Zend_Session::start();
    }

	
}