<?php
	/**
	 * @package UpSmart_SiteManager
	 */
	 
	//Class for dealing with potential differences between themes.
	/*interface theme {
		abstract function changeLogo($url);
		abstract function changePrimaryColor($c);
	}*/
	abstract class upsmart_template {
		protected $data = null;
		
		function __construct($n) {
			$this->data = $n;
		}
		
		abstract function getName();
		abstract function getTitle();
	}