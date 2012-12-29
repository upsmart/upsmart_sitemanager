<?php
	class default_products extends upsmart_template {
		function getName() {return "products";}
		function getTitle() {return "Products";}
		//The __toString function is where it gets applied.
		function __toString() {
			$out = '<div id="products">';
			
			foreach($this->data['products'] as $p) {
				$out .= "<div class='product'><a href='BASE_URL'><img src='{$p['photo']}' alt='{$p['shortdesc']}'/><div class='label'>{$p['name']}</div></a></div>\n";
			}
			$out .= "</div>";
			return $out;
		}
	}