/*
 * Extends YAHOO.widget.LogReader
 */

 
	YAHOO.widget.LogReader.prototype.iterate = function (o, text, full){ // object o, String text, boolean full
		var stream = "";
		if(o){
			
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
				stream+=property+"\n";
				
			}
			//YAHOO.log();
			YAHOO.log("Properties in " + text+"\n"+stream);
			
		}
		else {
			YAHOO.log(text+"empty");
		}
	}
	
	YAHOO.iterate = function (o, text, full){ // object o, String text, boolean full
		var stream = "";
		if(o){
			
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
				stream+=property+"\n";
				
			}
			//YAHOO.log();
			YAHOO.log("Properties in " + text+"\n"+stream);
			
		}
		else {
			YAHOO.log(text+"empty");
		}
	}

	YAHOO.print_r = function(o, text, full){
	
		//YAHOO.log(text+print_r(o, '', full));
		YAHOO.log(text+print_r(o));
	}
	
	
	function print_r_old (o, indent, full){
	
		var stream = '';
		indent+='\t';

		YAHOO.log("print_r: start of method = ");
		YAHOO.log("print_r: o.constructor = "+o.constructor);
		if(o.constructor == Array ||
		     o.constructor == Object 
		     || o.constructor == HTMLLIElement){

		    for(var p in o){
		    	YAHOO.log("print_r: p = "+p);
		      if(o[p]
		      	&& (o[p].constructor == Array||
			         o[p].constructor == Object)
		         	){
					stream+=indent+"["+p+"] => "+typeof(o)+"\n";
		        	stream+=print_r(o[p]);
		      } else {
		      
					stream+=indent+"["+p+"] => "+o[p]+"";
		      }
		    }
	  	}
	  	return stream;
	}
	
	
	function print_r(arr,level){
		var dumped_text = "";
		if(!level){
			level = 0;
		}
		
		//YAHOO.log('print_r: level = '+level);
		//The padding given at the beginning of the line.
		var level_padding = "";
		for(var j=0;j<level+1;j++){
			level_padding += "    ";
		}
		
		if(typeof(arr) == 'object') { //Array/Hashes/Objects
		 for(var item in arr) {
		  var value = arr[item];
		 
		  if(typeof(value) == 'object') { //If it is an array,
		   dumped_text += level_padding + "'" + item + "' ...\n";
		   dumped_text += print_r(value,level+1);
		  } else {
		   dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
		  }
		 }
		} else { //Stings/Chars/Numbers etc.
		 dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
		}
		return dumped_text;
	} 

 