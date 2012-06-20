

function FormController(){
	this.currentForm = null;
	
	this.getCurrentForm = getCurrentForm;
	function getCurrentForm(){
		return this.currentForm;
	}
	
	this.setCurrentForm = setCurrentForm;
	function setCurrentForm(newValue){
		this.currentForm = newValue;
	}

	this.setCurrentFormByName = setCurrentFormByName;
	function setCurrentFormByName(formName){
		this.setCurrentForm(this.getFormByFormName(formName));
	}
	
	this.getFormByFormName = getFormByFormName;
	function getFormByFormName(formName){
		return document_getObject(formName);
	}
	
	this.getFormElements = getFormElements;
	function getFormElements(){
	
	//	out.iterate(this.getCurrentForm().elements, "form.elements = ", true);
		try{
			return this.getCurrentForm().elements;
		}
		catch(e){
			return [];
		}
	}

	this.getGroupByName = getGroupByName;
	function getGroupByName(groupName){
		var e;
		var group = [];
		var elements = this.getFormElements();
	//	out.iterate(elements, "elements = ", true);
		for(var i = 0; i< elements.length; i++){
			e = elements[i];
			if (e.name == groupName){
				group[group.length] = e;
			}
		}
	
		return group;
	}
	
	this.checkSingleGroupElement = checkSingleGroupElement;
	function checkSingleGroupElement(groupName, value) {
		
		value = String(value);
	//	out.add("groupName = "+groupName);
		var group = this.getGroupByName(groupName);
		var e;
		var checked = false;
	//	out.iterate(group, "group = ", true);
	
		for (var i = 0; i < group.length; i++) {
			e = group[i];
			checked = false;
			if (e.value == value){
				checked = true;
			}
			e.checked = checked
		}
	}
	
	this.getElement = getElement;
	function getElement(name){
		return document_getObject(name);
	}
	
	this.getElementValue = getElementValue;
	function getElementValue(name, type){
	
		var sValue = "";
	//	if( TestElement(name) ){
			switch(type){
				case "DDL":
					sValue = GetDDLValue(name);
				break;
				
				case "Radio":
					sValue = GetRadioValue(name);
				break;
				
				default:
					element = this.getElement(name);
					//sValue =  eval( "GLOBAL_FORM."+sElement+".value" );
				break;
			}
	//	}

		try{
			return element.value;
		}
		catch(e){
			return '';
		}
	
	}
	
	this.setElementValue = setElementValue;
	function setElementValue(name, value){
		try{
		//	value = "tested";
		//	out.flush();
			this.getElement(name).value = value;

			return true;
		}
		catch(e){
			return false;
		}
	}
	
	this.getAttribute = getAttribute;
	function getAttribute(name, element){

		element = element == null ? this.getCurrentForm(): element;
		for(var i=0;i<element.attributes.length;i++){
			if(element.attributes[i].name==name){
				return element.attributes[i];
			}
		}

		return null;
	}
	
	this.outputAttributes = outputAttributes;
	function outputAttributes(element, full, flush){

		element = element == null ? this.getCurrentForm(): element;
		for(var i=0;i<element.attributes.length;i++){
				out.iterate(element.attributes[i], "element.attributes["+i+"] = ", full);
		}

		if(flush){
			out.flush();
		}
	}
	
	this.setAction = setAction;
	function setAction(newValue){
		
		try{
			action = this.getAttribute("action");
			action.value = newValue;
			return true;
		}
		catch(e){
			return false;
		}
	}
}