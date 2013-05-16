<?php
	class default_products extends upsmart_template {
		static function getName() {return "products";}
		static function getTitle() {return "Products";}
		static function getUsedData() {return array('products');}
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