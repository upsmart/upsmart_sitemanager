<?php
	class default_products extends template {
		private $data;
		
		//The __toString function is where it gets applied.
		function __toString() {
			$out = '<div id="products">';
			
			foreach($out['products'] as $p) {
				$out .= "<a href='BASE_URL'><img src='{$out['products']['photo']}' alt='{$out['products']['shortdesc']}'/><div class='label'>{$out['products']['name']}</div></a>\n";
			}
			$out .= "</div>";
			return $out;
			return <<<EOHTML
<img src='{$this->data['images']['media_primary']}'/>
<p>Welcome to {$this->data['business']['name']}. {$this->data['profile']['mission']}</p>
EOHTML;
		}
	}