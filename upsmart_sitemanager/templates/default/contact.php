<?php
	class default_contact extends upsmart_template {
		static function getName() {return "contact";}
		static function getTitle() {return "Contact";}
		static function getUsedData() {return array();}
		
		//The __toString function is where it gets applied.
		function __toString() {
			return "[CONTACT-US-FORM]";
		}
	}