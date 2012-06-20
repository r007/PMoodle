
	var page = {};
	page.forms = [];
	page.forms[0] = {};
	page.collapsableFieldsets = [];
	page.collapsableMenus = [];


	var task_id = '';
	function addSelectedItems(form, fieldAllName, fieldSelectedName) {
		var fieldAll = eval("form."+fieldAllName);
		var fieldSelected = eval("form."+fieldSelectedName);

		var at = fieldAll.length -1;
		var td = fieldSelected.length -1;
		var tasks = "x";

		//Check to see if None is currently in the dependencies list, and if so, remove it.

		if(td>=0 && fieldSelected.options[0].value==task_id){
			fieldSelected.options[0] = null;
			td = fieldSelected.length -1;
		}

		//build array of task dependencies
		for (td; td > -1; td--) {
			tasks = tasks + "," + fieldSelected.options[td].value + ","
		}

		//Pull selected resources and add them to list
		for (at; at > -1; at--) {
			if (fieldAll.options[at].selected && tasks.indexOf( "," + fieldAll.options[at].value + "," ) == -1) {
				t = fieldSelected.length
				opt = new Option( fieldAll.options[at].text, fieldAll.options[at].value );
				fieldSelected.options[t] = opt
			}
		}

	}

function removeSelectedItems(form, fieldAllName, fieldSelectedName) {
	var fieldSelected = eval("form."+fieldSelectedName);
	var td = fieldSelected.length -1;

	for (td; td > -1; td--) {
		if (fieldSelected.options[td].selected) {
			fieldSelected.options[td] = null;
		}
	}

}

function updateSelectedItems(action, form, fieldAllName, fieldSelectedName){

	if(form && fieldAllName && fieldSelectedName){
		switch(action){
			case "add":
				addSelectedItems(form, fieldAllName, fieldSelectedName);
				break;

			case "remove":
				removeSelectedItems(form, fieldAllName, fieldSelectedName);
				break;
		}
	}

}


function form_highlightInput(field, focus){
	if(!field){return false;}
	if(focus){
		if(field.defaultValue==field.value){
			field.value='';
		}
		field.className+=' user-input';
	}
	else {
		field.className=field.className.remove('user-input');
	}

}

String.prototype.remove = string_remove;
function string_remove(text){
	if(!text){ return null;}
	if(this.indexOf(text) != -1){
		return this.substr(0, this.indexOf(text)) + this.substr( ( this.indexOf(text)+text.length ), this.length);
	}
}

function form_checkGroup( formName, sGroup ) {

	var oForm;
	if(formName){
		oForm = document_getObject(formName)
	}
	if( !oForm ){
		oForm = document.DBAdminForm;
	}

	var e;
	for ( var i = 0; i < oForm.elements.length; i++ ) {

		e = oForm.elements[ i ];
		if ( e.name == sGroup ){
			e.checked = oForm[ sGroup + "All" ].checked;
		}
	}
}

function form_unCheckGroup(formName, sGroup) {

	var oForm;
	if(formName){
		oForm = document_getObject(formName)
	}

	if( !oForm ){
		oForm = document.DBAdminForm;
	}

	for ( var i = 0; i < oForm.elements.length; i++ ) {
		var e = oForm.elements[ i ];
		if ( e.name == sGroup ){
			e.checked = false;
		}
	}
	oForm[ sGroup + "All" ].checked = false;
}

function form_unCheckField(formName, sValue, sGroup){

	var oForm;
	if(formName){
		oForm = document_getObject(formName)
	}

	if( !oForm ){
		oForm = document.DBAdminForm;
	}
	for ( var i = 0; i < oForm.elements.length; i++ ) {
		var e = oForm.elements[ i ];
		if ( e.name == sGroup && e.value == sValue ){
			e.checked = false;
		}
	}
	oForm[ sGroup + "All" ].checked = false;
}

function form_checkField(formName, group, value){

	var form;
	if(formName){
		form = document_getObject(formName)
	}

	if(!form){
		form = document.DBAdminForm;
	}
	for(var i=0;i<form.elements.length; i++){
		var e = form.elements[ i ];
		if(e.name == group && e.value == value){
			e.checked = true;
		}
	}
}

function form_isRadioChecked(formName, radio){

	var form;
	if(formName){
		form = document_getObject(formName)
	}

	if(!form){
		return false;
	}
	for(var i=0;i<form.elements.length; i++){
		var e = form.elements[ i ];
		if(e.name == radio && e.checked == true){
			return true;
		}
	}
}

function form_setRadioByValue(formName, radio, value){

	var form;
	if(formName){
		form = document_getObject(formName)
	}

	if(!form){
		return;
	}
	for(var i=0;i<form.elements.length; i++){
		var e = form.elements[ i ];
		if(e.name == radio && e.value == value){
			e.checked = true;
		}
	}

}

function form_getRadioValue(formName, radio){

	var form;
	if(formName){
		form = document_getObject(formName)
	}

	if(!form){
		return false;
	}
	for(var i=0;i<form.elements.length; i++){
		var e = form.elements[ i ];
		if(e.name == radio && e.checked == true){
			return e.value;
		}
	}

	return null;
}

function document_getObject(name) {

  if (document.getElementById && document.getElementById(name)) {
  	return document.getElementById(name);
  }
  else if (document.all && document.all[name]) {
	return document.all[name];
  }
  else if (document.layers && document.layers[name]) {
   	return document.layers[name];
  }

}

/*
 * Accepts: String name, String option1, string option2
 * Returns: Boolean value indicating state of element
 */

function document_toggleDisplay(name, option1, option2){
	var element = document_getObject(name)
	if(!element){
		return null;
	}

	if(element.style.display == option1){
		element.style.display = option2
		return 1;
	}
	else {
		element.style.display = option1;
		return 0;
	}

}

function document_setDisplay(name, value){
	var element = document_getObject(name);
	if(!element){
		return null;
	}

	element.style.display = value
}

function page_setCollapsableFieldsets(fieldsets){
	if(!page || !fieldsets){
		return null;
	}

	page.collapsableFieldsets = fieldsets;
}

function page_setCollapsableMenus(menus){
	if(!page || !menus){
		return null;
	}

	page.collapsableMenus = menus;
}




function form_confirm(msg){
	return confirm(msg);
}

function checkGroup(formName, sGroupToggle, sGroupUnCheck){
	form_checkGroup(formName, sGroupToggle);
}

function toggleDisplay(name, type){
	return document_toggleDisplay(name, "block", "none");
}

function toggleFieldDisplay(name, type){
	toggleFieldDisplayIcon(
				name,
				document_toggleDisplay(name+"Fields", "block", "none"),
				"collapse-control-collapse.gif",
				"collapse-control-expand.gif"
			);
}

function toggleFieldDisplayIcon(name, state, src1, src2){
	if(!name || !src1 || !src2){
		return null;
	}
	name+="CollapseIcon";
	if(!state){
		document_swapImage(name, src1);
	}
	else {
		document_swapImage(name, src2);
	}
}

function document_swapImage(name, newSrc){
	var element = document_getObject(name);
	if(!element || !element.src || !newSrc){
		return null;
	}

	element.src = element.src.substring(0, element.src.lastIndexOf("/")+1)+newSrc;

}


function expandAllFields(bitwise){

	if(!bitwise){
		bitwise = 1;
	}
	if(bitwise && 1){
		for(var x=0;x<page.collapsableFieldsets.length;x++){
			document_setDisplay(page.collapsableFieldsets[x]+"Fields", "block");
			document_swapImage(page.collapsableFieldsets[x]+"CollapseIcon", "collapse-control-collapse.gif");
		}
	}

	if(bitwise && 2){
		for(var x=0;x<page.collapsableMenus.length;x++){
			document_setDisplay(page.collapsableMenus[x]+"Fields", "block");
			document_swapImage(page.collapsableMenus[x]+"CollapseIcon", "collapse-control-collapse.gif");
		}
	}

}

function collapseAllFields(){
	for(var x=0;x<page.collapsableFieldsets.length;x++){
		document_setDisplay(page.collapsableFieldsets[x]+"Fields", "none");
		document_swapImage(page.collapsableFieldsets[x]+"CollapseIcon", "collapse-control-expand.gif");

	}
}

function expandFieldset(name, bitwise){

	if(!bitwise){
		bitwise = 1;
	}
	if(bitwise && 1){
		document_setDisplay(name+"Fields", "block");
		document_swapImage(name+"CollapseIcon", "collapse-control-collapse.gif");
	}

	if(bitwise && 2){
		for(var x=0;x<page.collapsableMenus.length;x++){
			document_setDisplay(page.collapsableMenus[x]+"Fields", "block");
			document_swapImage(page.collapsableMenus[x]+"CollapseIcon", "collapse-control-collapse.gif");
		}
	}

}

function initialise(){
	expandAllFields();
}