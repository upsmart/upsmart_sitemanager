<?php
	class default_home extends upsmart_template {
		static function getName() {return "home";}
		static function getTitle() {return "Welcome";}
		static function getUsedData() {return array('business','profile');}
		
		//The __toString function is where it gets applied.
		function __toString() {
			return <<<EOHTML
<img src='{$this->data['profile']['media1']}'/>
<p>Welcome to {$this->data['business']['name']}. {$this->data['profile']['mission']}</p>
EOHTML;
		}
	}