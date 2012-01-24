<?php

class Model_Ads extends Model_Base_Ads
{
	
	public function postSave($event)
	{
		//file_put_contents('/tmp/debug', var_dump($event->getQuery())."\n");
	}
	
	public static function getProjectUrls($projectId) {
		$resultset = Doctrine_Query::create()
		    ->select('parser_project_ads_url')
		    ->from('Model_Ads')
		    ->where('parser_project_id = ?', $projectId)
		    ->fetchArray();
		    
		return $resultset;
	}
	//=============================================================================
	
};
