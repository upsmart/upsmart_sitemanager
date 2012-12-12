<?php
	/**
	 * @package UpSmart_SiteManager
	 */
	 
	//Class for dealing with potential differences between themes.
	interface theme {
		abstract function changeLogo($url);
		abstract function changePrimaryColor($c);
	}
	class template {
		protected $data = null;
		
		function __construct($n) {
			$this->data = $n;
		}
	}