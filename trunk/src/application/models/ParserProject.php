<?php


class Model_ParserProject extends Model_Base_ParserProject
{

	/**
	 * 
	 */
	function getFieldsetId()
	{
		$cat = $this->category->toArray();
		return (int)$cat['Fieldset']['id'];
	}
	
	/**
	 * 
	 * @param string $fieldName
	 */
	function getField($fieldName)
	{
		$i = 0;
		foreach($this->fields->toArray() as $f) {
			if($f['fields_caption'] == $fieldName) {
				return $this->fields[$i];
			}
			
			$i++;
		}
		
		return NULL;
	}
	
	/**
	 * Alias of getFieldValue($fieldName)
	 */
	function getFieldXquery($fieldName, $NS)
	{
		return $this->getFieldValue($fieldName, $NS);
	}
	
	/**
	 * Get Additional Fields
	 */
	function getFieldAdsXqueryAdd($fieldName)
	{
		$fields = array();
		foreach($this->fieldsAds->toArray() as $f) {
			if(strpos($f['fields_caption'], $fieldName.'_') === 0) {
				$fields[$f['fields_caption']] = $f['xquery'];
			}
		}

		return $fields;
	}
	
	/**
	 * 
	 * @param mixed $fieldName
	 * @param string $NS
	 */
	function getFieldValue($fieldName, $NS) {
		if($NS=='fieldsAdsPictures') {
			// $fieldName value is the index of the array $this->fieldsAdsPictures
			return $this->fieldsAdsPictures[$fieldName]['xquery'];
		}
		
		foreach($this->$NS->toArray() as $f) {
			if($f['fields_caption'] == $fieldName) {
				return $f['xquery'];
			}
		}
		
		return '';
	}
	
	/**
	 * 
	 * @param string $fieldName
	 * @param mixed $fieldValue
	 */
	function setFieldValue($fieldName , $fieldValue)
	{
		$i=0;
		foreach($this->fields->toArray() as $f) {
			if($f['fields_caption'] == $fieldName) {
				$this->fields[$i]->xquery = $fieldValue;
				return;
			}
			
			$i++;
		}
		
		$this->fields[]->fields_caption = $fieldName;
		$this->fields[]->xquery = $fieldValue;
	}
	
	/**
	 * Return Pager Urls
	 * 
	 * @return array
	 */
	function getPageUrls()
    {
    	$urls = array();
   
    	$xpath = $this->getFieldXquery('add_link', 'fields');
    	$xpath = trim($xpath);
    	if(empty($xpath))
    		return $urls;
    	
    	/**
    	 * @TODO Този метод трябва да се премести в класа на модела Model_ParserProject
    	 */
    	$html = $this->getUrlContent($this->url);
    	
    	$dom = new Zend_Dom_Query($html);
    	$res = $dom->query($xpath);
    	foreach($res as $r) {
    		$urls[] = $r->getAttribute('href');
    	}
    	
    	return $urls;
    }
    
    /**
     * Return array with ads urls
     * 
     * @param string $xpath
     * @return array
     */
    function getInternalUrls()
    {
    	$pUrls = array();
    	if(!is_object($this)) 
    		return $pUrls;
    	
    	
    	$xpath = $this->getFieldXquery('add_link', 'fields');
    	$xpath = trim($xpath);
    	if(empty($xpath))
    		return $pUrls;
    	
    	$pageLink = $this->getFieldXquery('page_link', 'fields');
    	$pageLink = trim($pageLink);
    	
    	/**
    	 * @TODO Този метод трябва да се премести в класа на модела Model_ParserProject
    	 */
    	$html = $this->getUrlContent($this->url);
    	
    	if(!empty($pageLink) && !empty($html)) {
    		$pPageUrls = $this->getPageUrls($pageLink, $html);
    	}
		
    	
    	$dom = new Zend_Dom_Query($html);
    	$res = $dom->query($xpath);
    	foreach($res as $r) {
    		$url = $r->getAttribute('href');
    		$pUrls[] = SvProject_Parser::AbsoluteUrl($this, $url);
    	}

    	/* 
    	 * All the pages
    	 
    	if(!empty($pPageUrls)) {
    	    foreach($pPageUrls as $pu) {
    	        $pageHtml = $this->_getUrlContent($pu);
    	        $pageUrls = $this->_getInternalUrls($addLink, $pageHtml);
    	        $pUrls = array_merge($pUrls, $pageUrls);
    	    }
    	}
    	*/
    		
    	if(empty($pUrls))
    		return array();
    	
    	
    	return $pUrls;
    }
    
	/**
     * Browse URL and return its html content
     * 
     * @param string $url
     * @return string
     */
    function getUrlContent($url)
    {
    	$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$html = curl_exec($ch);
		curl_close($ch);
		
		/*
		 * Reencode html text to UTF-8
		 */
		 $regexCharset = "/(?:<meta[^>]*http-equiv[^>])*charset=(.*?)\"/i";
		 if(preg_match($regexCharset, $html, $matches)) {
			 $charset = $matches[1];
			 if($charset && ($charset !='utf-8')) {
			    $html = iconv($charset, 'UTF-8', $html);
			    $html = preg_replace($regexCharset, 'charset=utf-8"', $html);
			 }
		 }

		if(!$html) {
			throw new Exception(sprintf("Cannot browse this url: '%s'.", $url));
		}
		
		return $html;
    }
    
};
