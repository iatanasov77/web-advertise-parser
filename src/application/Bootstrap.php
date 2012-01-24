<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	
    protected function _initAppAutoload()
    {
    	$sNameSpace = "";
		if (defined(‘APP_NAMESPACE_HACK’) && APP_NAMESPACE_HACK == 1) {
			$sNameSpace = "App";
		}
		
 		$moduleLoader = new Zend_Application_Module_Autoloader(array(
			'namespace' => $sNameSpace,
			'basePath' => APPLICATION_PATH));
 		
 		return $moduleLoader;
    }
    
    protected function _initDoctrine()
    {   	
        $this->getApplication()->getAutoloader()
            ->pushAutoloader(array('Doctrine', 'autoload'));
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));
        
        $doctrineConfig = $this->getOption('doctrine');
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(
          Doctrine::ATTR_MODEL_LOADING,
          $doctrineConfig['model_autoloading']
        );

        Doctrine_Core::loadModels($doctrineConfig['models_path']);

        $conn = Doctrine_Manager::connection($doctrineConfig['dsn'],'doctrine');
        $conn->setAttribute(Doctrine::ATTR_USE_NATIVE_ENUM, true);
        
        // Set Charset Encoding
        /* */
        $manager->setAttribute(Doctrine::ATTR_DEFAULT_TABLE_CHARSET,'utf8');
        $manager->setAttribute(Doctrine::ATTR_DEFAULT_TABLE_COLLATE,'utf8_unicode_ci');
        $manager->setCollate('utf8_unicode_ci');
        $manager->setCharset('utf8');
      	//Doctrine_Query::query('SET NAMES utf8');
      	$conn->execute('SET NAMES utf8');
        
        return $conn;
    }
    
	/*     */
    protected function _initLayout()
    {
    	//Initialise Zend_Layout's MVC helpers
		Zend_Layout::startMvc(array('layoutPath' => SITE_ROOT.'/application/views', 'layout' => 'layout'));
		$view = Zend_Layout::getMvcInstance()->getView();
		$view->doctype('XHTML1_TRANSITIONAL');
    }

};

