<?php

require_once APPLICATION_PATH . "/controllers/IndexController.php";

class SvProject_ParserCli
{
    public function run()
    {
    	$oParser = new SvProject_Parser();
    	
        return $oParser->run();
    }
    
};
