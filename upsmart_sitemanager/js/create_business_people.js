/**
  * @package UpSmart_SiteManager
  */

(function($) {
	if(typeof upsmart === "undefined") { upsmart = {} }
	upsmart.people = {
		people: [],
		pcounter: 0,
		acceptFileTypes: ["image/png","image/jpeg","image/jpg","image/gif"],
 
		init: function() {
			$("#new").click(upsmart.people.showAddDialog);
			$("#team .person").live("click",upsmart.people.showEditDialog);
			$("#form").submit(upsmart.people.prepareSubmit);
		},
 
		prepareSubmit: function() {
			$("#form input[name=json]").attr("value",JSON.stringify(upsmart.people.people));
		},
 
		createForm: function(n) {
			item = $("<table> \
				<tr><th>Name</th><td class='twoinput'><input name='pfname' placeholder='Jane'/><input name='plname' placeholder='Smith'/></tr> \
				<tr><th>Title</th><td><input name='ptitle' placeholder='Chief Executive Officer'/></tr> \
				<tr><th>Short Bio</th><td><textarea name='pbio'/></textarea></td></tr> \
				<tr><th>Photo</th><td><input id='photo_upload' name='photo'/> <input type='button' id='photo_button' value='Open Media Library'/></tr> \
				<tr><td colspan='2'>(Optional) Upload a photo of <acronym title='Replace this with their first name?'>this person</acronym>. The bigger the better&mdash;don't worry, we'll scale this down for you.</td></tr> \
			</table>");
			if(n < upsmart.people.people.length) {
				p = upsmart.people.people[n];
				item.find("input[name=pfname]").attr("value",p.fname);
				item.find("input[name=plname]").attr("value",p.lname);
				item.find("input[name=ptitle]").attr("value",p.title);
				item.find("textarea[name=pbio]").attr("value",p.bio);
				item.find("input[name=photo]").attr("value",p.photo);
			}
			return item;
		},
		
		showAddDialog: function() {
			$("#dialog").data("person",upsmart.people.pcounter);
			$("#dialog").html(upsmart.people.createForm(upsmart.people.pcounter));
			$("#photo_button").click(open_media_library);
			upsmart.people.pcounter++;
			$("#dialog").dialog({
				width: 600,
				modal: true,
				buttons: {
					"Add": upsmart.people.addPerson,
					"Cancel": function() {
						$(this).dialog("close");
					}
				}
			});
		},
		showEditDialog: function() {
			pid = $(this).data("person");
			$("#dialog").data("person",pid);
			$("#dialog").html(upsmart.people.createForm(pid));
			$("#photo_button").click(open_media_library);
			$("#dialog").dialog({
				width: 600,
				modal: true,
				buttons: {
					"Save": upsmart.people.addPerson,
					"Cancel": function() {
						$(this).dialog("close");
					}
				}
			});
		},
		
		addPerson: function() {
			//Create a json object for this person.
			
			
			var person = {
				id: $("#dialog").data("person"),
				fname: $("#dialog input[name=pfname]").attr("value"),
				lname: $("#dialog input[name=plname]").attr("value"),
				title: $("#dialog input[name=ptitle]").attr("value"),
				bio:   $("#dialog textarea[name=pbio]").attr("value"),
				photo: $("#dialog input[name=photo]").attr("value"),
			}
			
			$(this).dialog("close");
			
			upsmart.people.finishAddPerson(person);
		},
		finishAddPerson: function(person) {
			upsmart.people.people[person.id] = person;
			if($("#person"+person.id).length == 0) {
				box = $("<div class='person'></div>").attr("id","person"+person.id);
				box.data("person",person.id);
				box.insertBefore($("#new"));
			} else {
				box = $("#person"+person.id);
				box.html("");
			}
			
			box.append($("<img/>").attr("src",person.photo));
			box.append($("<div/>").attr("class","label").html(person.fname+" "+person.lname));
		}
	}
	
	$(document).ready(upsmart.people.init);
}(jQuery));