<?php
	class default_about extends upsmart_template {
		static function getName() {return "about-us";}
		static function getTitle() {return "About Us";}
		static function getUsedData() {return array('profile');}
		
		//The __toString function is where it gets applied.
		function __toString() {
			$out = '';
			if(!empty($this->data['profile']['about'])) {
				$out .= "<h1>What We're About</h1>{$this->data['profile']['about']}\n\n";
			}
			if(!empty($this->data['profile']['history'])) {
				$out .= "<h1>Where We Come From</h1>{$this->data['profile']['history']}\n\n";
			}
			
			return $out;
		}
	}