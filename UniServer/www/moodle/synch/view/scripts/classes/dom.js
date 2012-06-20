
function dom(){

	this.toggleVisibility = toggleVisibility;
	function toggleVisibility(id, display){
		
		var element = document.getElementById(id);
		if(element 
				&& element.style){
			if(!element.style.display){
				element.style.display = "none";
			}
			else {
				if(!display){
					display = "inline";
				}
				element.style.display = element.style.display == "none" ? display:"none";
			}
		}
	}

	this.getDisplay = getDisplay;
	function getDisplay(id){
		
		var element = document.getElementById(id);
		if(element 
				&& element.style){
			return element.style.display;
		}
		return "";
	}

	this.setDisplay = setDisplay;
	function setDisplay(id, newDisplay){
		
		var element = document.getElementById(id);
		if(element 
				&& element.style){
			try{
				element.style.display = newDisplay;
			}
			catch(e){
				element.style.display = "inline";
			}
			
		}
	}

	this.registerEvents = registerEvents;
	function registerEvents(){

	}

}
