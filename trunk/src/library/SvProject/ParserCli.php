<?php

require_once APPLICATION_PATH . "/controllers/IndexController.php";

class SvProject_ParserCli
{
    public function run()
    {
    	$oParser = new SvProject_Parser();
    	
    	if(count($_SERVER['argv']) > 1) {
    		$projects = explode(',', $_SERVER['argv'][1]);
    		foreach($projects as $p) {
    			$oParser->run($p);
    		}
    	}
    	else {
    		$oParser->run();
    	}
    	
        return true;
    }
    
};
