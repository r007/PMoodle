 function objectFactory(){

	this.createObject = createObject;
	function createObject(name){
		if(name){
			try{
				return eval("new " + name +"()");
			}
			catch(e){
				throw(e);
				return null;
			}
		}
		
		return null;
		
	}
}

 var factory = new objectFactory();
