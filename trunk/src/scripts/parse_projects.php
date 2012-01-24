<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'parserCLI'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path()
)));
                                         
                                         
require_once 'Zend/Application.php';
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

//only load resources we need for script, in this case db and mail
$application->getBootstrap()->bootstrap(array('appautoload' , 'doctrine'));  // 'appautoload' , 

$config = $application->getOptions();
$cli = new SvProject_ParserCli($config);
$cli->run($_SERVER['argv']);

?>