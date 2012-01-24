<?php

class SvProject_Parser
{
	
	/**
	 * 
	 * @param Model_ParserProject $oProject
	 * @param string $adsUrl
	 */
	static function AbsoluteUrl($oProject, $adsUrl) {
    	if(!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $adsUrl)) {
	    	$urlParts = parse_url($oProject->url);
	    	$adsUrl = $urlParts['scheme']. "://" . $urlParts['host'] . "/" . $adsUrl;
	    }
	    
	    return $adsUrl;
    }
    
	/**
	 * 
	 */
	function run($projectId=0, $preview=FALSE) {
		$fetchedFields = array();
		
		if($projectId) {
			$oProject = Doctrine_Core::getTable('Model_ParserProject')->findOneBy('id', $projectId);
    		if( !$oProject || empty($oProject->url) ) {
    			throw new Exception('Project ID not exists or the Project URL is empty.');
    		}
    		
			$fetchedFields[$oProject->id] = $this->parseProject($oProject);
		}
		else {
			$fetchedFields = $this->parseAll();
		}
		
		if($preview) {
			return $fetchedFields;
		}
		
		foreach($fetchedFields as $pid => $ff) {
			$oProject = Doctrine_Core::getTable('Model_ParserProject')->findOneBy('id', $pid);
    		if( !$oProject || empty($oProject->url) ) {
    			throw new Exception('Project ID not exists or the Project URL is empty.');
    		}
    		
			$this->_saveAdd($ff, $oProject);
		}
	}
	
	/**
	 * Parse All active projects
	 */
	private function parseAll() {
		$ads = array();
		
		$q = Doctrine_Query::create()
		  ->from('Model_ParserProject')
		  ->where('active=1');
		$projects = $q->execute();
		
		foreach($projects as $pr) {
			$ads[$pr->id]  = $this->parseProject($pr);
		}
		
		return $ads;
	}
	
	/**
     * Parse Project and fetch new Ads content
     * 
     * @param Model_ParserProject $oProject
     * @return array Return an array with all new fetched ads 
     */
    function parseProject($oProject) {
    	$fetchedFields = array();
    	
    	$pUrls = $oProject->getInternalUrls();
    	
    	/**
    	 * All Saved Ads URLS for this projects
    	 */
    	$sUrls = Model_Ads::getProjectUrls($oProject->id);

    	$allAds = array();
    	foreach($pUrls as $addUrl) {
    		/*
    		 * If Add is already saved (exsits)
    		 */
    		if(in_array($addUrl, $sUrls))
    			continue;
    			
    		$allAds[] = $this->parseAdd($addUrl, $oProject);
    	}

 		return $allAds;
    }
    
    /**
     * Parse an Add Fields
     * 
     * @param string $url
     * @param Model_ParserProject $oProject
     * 
     * @return array()		Associative array with all parsed data
     */
    private function parseAdd($url, $oProject) {
    	$add = array();
    	$add['parser_project_ads_url'] = $url;
    	
    	$html = $oProject->getUrlContent($url);
    	if(empty($html))
    		return array();
    		
    	$dom = new Zend_Dom_Query($html);
    	
    	foreach($oProject->fieldsAds as $f) {
    		
    		if(empty($f->xquery)) {
    			$add[$f->fields_caption] = '';
    		}
    		else {
	    		$res = $dom->query($f->xquery);
	    		if($res->current()) {
	    			$regex = '/_[1-9]$/';
	    			if (preg_match($regex, $f->fields_caption)) {
	    				$caption = preg_replace($regex, '', $f->fields_caption);
	    				$add[$caption] .= '<br />' . trim(strip_tags($res->current()->ownerDocument->saveXML($res->current())));
	    			}
	    			else {
	    				$add[$f->fields_caption] = trim(strip_tags($res->current()->ownerDocument->saveXML($res->current())));
	    			}
	    		}
	    		else {
	    			$add[$f->fields_caption] = '';
	    		}
    		}
    	}
    	
    	$add['pictures'] = array();
    	foreach($oProject->fieldsAdsPictures as $fap) {
    		if(!empty($fap->xquery)) {
	    		$res = $dom->query($fap->xquery);
		    	if($res->current() && ($res->current()->nodeName == 'img')) {	
					if(!empty($fap->regex)) {
						$picture = preg_replace($fap->regex, $fap->replace, $res->current()->getAttribute('src'));
					}
					else {
						$picture = $res->current()->getAttribute('src');
					}
		    		$add['pictures'][] = $picture;
				}
    		}
    	}

    	return $add;
    }
    
    /**
     * 
     * @param $pictureRemote
     */
    function savePicture($pictureRemote, $picSource)
    {
    	$pictureField['remote'] = $pictureRemote;
    	$fileParts = explode('.', $pictureField['remote']);
    	$ext = $fileParts[count($fileParts)-1];
    	
    	/**
    	 * Build Picture File Name
    	 */
    	$pictureField['local'] = sprintf("parser-%s-d_pic.%s", uniqid(time()), $ext);
    	
    	/**
    	 * Settings
    	 */
    	$thmbWidth = 70;
    	$bigThmbHeight = 300;
    	$imgCompression = 75;
    	
		$docRoot = '/home/vtorarak/public_html';

    	$picPath 		= $docRoot . '/images/listings/' . $pictureField['local'];				// Original Size
    	$thmbPath 		= $docRoot . '/images/listings/thmb/' . $pictureField['local'];			// 93 X 70
    	$bigThmbPath 	= $docRoot . '/images/listings/bigThmb/' . $pictureField['local'];		// 400 X 300
    	
    	if($picFile = @fopen($picPath, 'w')) {
    		$ok = @fwrite($picFile, $picSource);
			fclose($picFile);
			
			if(!$ok) {
				return FALSE;
			}
    	}
    	else {
    		return FALSE;
    		
    		// The Picture File is not created and must generate an exception
    	    throw new Exception('Cannot create picture file. ("' . $picPath . '") Possible reasons are wrong path or missing permissions, may be.');
    	}
		
    	
    	/**
    	 * Resize Pictures
    	 */
    	/**
         * Create source GD image object
         */
            $imageInfo = getimagesize($picPath);
            $imgType = $imageInfo[2];
            $srcImage = NULL;
            if( $imgType == IMAGETYPE_JPEG ) {
                $srcImage = imagecreatefromjpeg($picPath);
            } elseif( $imgType == IMAGETYPE_GIF ) {
                $srcImage = imagecreatefromgif($picPath);
            } elseif( $imgType == IMAGETYPE_PNG ) {
                $srcImage = imagecreatefrompng($picPath);
            }
            $srcWidth = imagesx($srcImage);
            $srcHeight = imagesy($srcImage);
            
            /**
             * Create thumbnail
             */
            $thmbRatio = $thmbWidth / $srcWidth;
            $thmbHeight = $srcHeight * $thmbRatio;
            
            $thmbImage = imagecreatetruecolor($thmbWidth, $thmbHeight);
            imagecopyresampled($thmbImage, $srcImage, 0, 0, 0, 0, $thmbWidth, $thmbHeight, $srcWidth, $srcHeight);
            
          /**
           * Save Thubnail
           */
            if( $imgType == IMAGETYPE_JPEG ) {
                imagejpeg($thmbImage, $thmbPath, $imgCompression);
            } elseif( $imgType == IMAGETYPE_GIF ) {
                imagegif($thmbImage, $thmbPath);
            } elseif( $imgType == IMAGETYPE_PNG ) {
                imagepng($thmbImage, $thmbPath);
            }
     
    
            /**
             * Create big thumbnail
             */
            $bigThmbRatio = $bigThmbHeight / $srcHeight;
            $bigThmbWidth = $srcWidth * $bigThmbRatio;
            
            $bigThmbImage = imagecreatetruecolor($bigThmbWidth, $bigThmbHeight);
            imagecopyresampled($bigThmbImage, $srcImage, 0, 0, 0, 0, $bigThmbWidth, $bigThmbHeight, $srcWidth, $srcHeight);
            
            /**
           * Save big Thubnail
           */
            if( $imgType == IMAGETYPE_JPEG ) {
                imagejpeg($bigThmbImage, $bigThmbPath, $imgCompression);
            } elseif( $imgType == IMAGETYPE_GIF ) {
                imagegif($bigThmbImage, $bigThmbPath);
            } elseif( $imgType == IMAGETYPE_PNG ) {
                imagepng($bigThmbImage, $bigThmbPath);
            }
            
            
    	return $pictureField['local'];
    }
    
    /**
     * 
     * @param array $fetchedFields
     */
    private function _saveAdd($fetchedFields, $oProject)
    {
    	foreach($fetchedFields as $add) {  		
    	    $oAds = new Model_Ads();
    	    $oAds->parser_project_id = $oProject->id;
    	    foreach($add as $k => $v) {
	    		//if($k == 'pictures') continue;
	    		if(!isset($oAds->$k)) continue;
	    		
	    		if($k == 'price') {
	    		    $v = doubleval(str_replace(",", "", $v));
	    		}
	    		
	    		$oAds->$k = $v;
	    		//$oAds->$k = iconv('CP1251', 'UTF-8', , $v);
	    	}
	    	
	    	$oAds->user_id = $oProject->user_id;
	    	$oAds->category_id = $oProject->category_id;
	    	
	    	$oAds->package_id = 1;
	    	$oAds->usr_pkg = 1;
	    	$oAds->user_approved = 1;
	    	
	    	$oAds->date_added = date("Y-m-d H:i:s");
    	    $secondsPerDay = 60 * 60 * 24;
    	    $oAds->date_expires = date("Y-m-d H:i:s", (time()+30*$secondsPerDay));
    	    $oAds->save();
	    	
	    	/*   
	    	 * Process and Save Pictures
	    	 */
	    	/* */
    	    $n = 0;
    	    $aPictureIds = array();
    	    foreach($add['pictures'] as $pic) {
    	    	if($pic == $oProject->nopic) {
    	    		continue;
    	    	}
    	    	
    	    	$picSource = $oProject->getUrlContent($pic);
    	    	
	    		// $pic  sadarja remote url
	    		if($localFile = $this->savePicture($pic, $picSource)) {
	    			$oAdsPicture = new Model_AdsPicture();
	    			$oAdsPicture->ad_id = $oAds->id;
	    			$oAdsPicture->picture = $localFile;
	    			$oAdsPicture->order_no = ++$n;
	    			$oAdsPicture->save();
	    			
	    			//$aPictureIds[] = $oAdsPicture->id;
	    			//$oAdsPicture->link('ads', array($oAds->id));
	    		}
        	}
        	
    	}
    }

};
