
function output(){

	this.METHOD_WRITE = 1;
	this.METHOD_ALERT = 2;
	this.METHOD_NEWWINDOW = 3;
	this.METHOD_FRAME = 4;

	var stream="";
	var method= this.METHOD_WRITE
	var outputWindow = null;
	var frame;

	this.setMethod = setMethod;
	function setMethod(newMethod){
		method = newMethod;
	}

	this.setFrame = setFrame;
	function setFrame(newFrame){
		frame = newFrame;
	}

	this.add = add;
	function add(text){
		stream += text+"\n<br/>";
	}

	this.append = append;
	function append(text){
		stream += text+"\n<br/>";
	}

	this.clear = clear;
	function clear(){
		stream="";
	}

	this.flush = flush;
	function flush(){
		switch(method){
			case this.METHOD_WRITE:
				document.write(stream);
				break;

			case this.METHOD_ALERT:
				alert(stream);
				break;

			case this.METHOD_NEWWINDOW:
				if(!outputWindow){
					outputWindow = window.open();
				}
				outputWindow.document.write(stream);
				//alert(stream);
				break;

			case this.METHOD_FRAME:
				window.frames[frame].document.write(stream);
				break;


		}
		clear();
	}

	this.iterate = iterate;
	function iterate(o, text, full){ // object o, String text, boolean full
		if(o){
			this.add("Properties in " + text);
			var property = "";
			for(var name in o){
				property = name;
				if(full){
					try{
						property+=": "+o[name];
					}
					catch(e){
						//do nothing for now.
					}
				}
				this.add(property);
			}
		}
		else {
			this.add(text+"empty");
		}
	}

}
