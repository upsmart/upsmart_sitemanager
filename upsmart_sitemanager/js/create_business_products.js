/**
  * @package UpSmart_SiteManager
  */

(function($) {
	if(typeof upsmart === "undefined") { upsmart = {} }
	upsmart.products = {
		products: [],
		pcounter: 0,
		acceptFileTypes: ["image/png","image/jpeg","image/jpg","image/gif"],
 
		init: function() {
			$("#new").click(upsmart.products.showAddDialog);
			$("#team .product").live("click",upsmart.products.showEditDialog);
			$("#form").submit(upsmart.products.prepareSubmit);
		},
 
		prepareSubmit: function() {
			$("#form input[name=json]").attr("value",JSON.stringify(upsmart.products.products));
		},
 
		createForm: function(n) {
			item = $("<table> \
				<tr><th>Product Name</th><td><input name='name' placeholder='Widgetizer 2000'/></tr> \
				<tr><th colspan='2'>Short Description</th></tr> \
				<tr><td colspan='2'>Write about your product in 140 characters or less.<br/><textarea name='shortdesc'></textarea></td></tr> \
				<tr><th colspan='2'>Full Description</th></tr> \
				<tr><td colspan='2'>Can be as long as you'd like. Think about: <ul><li>What does it do?</li><li>How does it do it?</li><li>Why is it special?</li></ul><br/><textarea name='fulldesc'></textarea></td></tr> \
				<tr><th>Photo</th><td><input type='file' name='photo'/></tr> \
				<tr><td colspan='2'>Upload a photo of the product. The bigger the better&mdash;don't worry, we'll scale this down for you.</td></tr> \
			</table>");
			if(n < upsmart.products.products.length) {
				p = upsmart.products.products[n];
				item.find("input[name=name]").attr("value",p.name);
				item.find("textarea[name=shortdesc]").attr("value",p.shortdesc);
				item.find("textarea[name=fulldesc]").attr("value",p.fulldesc);
			}
			return item;
		},
		
		showAddDialog: function() {
			$("#dialog").data("product",upsmart.products.pcounter);
			$("#dialog").html(upsmart.products.createForm(upsmart.products.pcounter));
			upsmart.products.pcounter++;
			$("#dialog").dialog({
				width: 600,
				modal: true,
				buttons: {
					"Add": upsmart.products.addProduct,
					"Cancel": function() {
						$(this).dialog("close");
					}
				}
			});
		},
		showEditDialog: function() {
			pid = $(this).data("product");
			$("#dialog").data("product",pid);
			$("#dialog").html(upsmart.products.createForm(pid));
			$("#dialog").dialog({
				width: 600,
				modal: true,
				buttons: {
					"Save": upsmart.products.addProduct,
					"Cancel": function() {
						$(this).dialog("close");
					}
				}
			});
		},
		
		addProduct: function() {
			//Create a json object for this product.
			
			
			var product = {
				id: $("#dialog").data("product"),
				name: $("#dialog input[name=name]").attr("value"),
				shortdesc:   $("#dialog textarea[name=shortdesc]").attr("value"),
				longdesc:   $("#dialog textarea[name=longdesc]").attr("value"),
				photo: null,
			}
			
			$(this).dialog("close");
			
			var file = $("#dialog input[name=photo]")[0].files[0];
			if(typeof file === 'undefined' || upsmart.products.acceptFileTypes.indexOf(file.type) == -1) {
				upsmart.products.finishAddProduct(product);
				return
			}
			var reader = new FileReader();
			//Use a closure to keep access to the product.
			reader.onload = (function(product) {
				return function(e) {
					//Save the photo to the object.
					product.photo = e.target.result;
					upsmart.products.finishAddProduct(product);
				}
				
			}(product));
			reader.readAsDataURL(file);
		},
		finishAddProduct: function(product) {
			upsmart.products.products[product.id] = product;
			if($("#product"+product.id).length == 0) {
				box = $("<div class='product'></div>").attr("id","product"+product.id);
				box.data("product",product.id);
				box.insertBefore($("#new"));
			} else {
				box = $("#product"+product.id);
				box.html("");
			}
			
			box.append($("<img/>").attr("src",product.photo));
			box.append($("<div/>").attr("class","label").html(product.name));
		}
	}
	
	$(document).ready(upsmart.products.init);
}(jQuery));
