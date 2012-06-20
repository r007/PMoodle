			setTimeout('fix_column_widths()', 20);
			
			function openpopup(url,name,options,fullscreen) {
			  fullurl = "http://pclt1048.open.ac.uk/offline/development/client-1.9" + url;
			  windowobj = window.open(fullurl,name,options);
			  if (fullscreen) {
				 windowobj.moveTo(0,0);
				 windowobj.resizeTo(screen.availWidth,screen.availHeight);
			  }
			  windowobj.focus();
			  return false;
			}
			
			function uncheckall() {
			  void(d=document);
			  void(el=d.getElementsByTagName('INPUT'));
			  for(i=0;i<el.length;i++) {
				void(el[i].checked=0);
			  }
			}
			
			function checkall() {
			  void(d=document);
			  void(el=d.getElementsByTagName('INPUT'));
			  for(i=0;i<el.length;i++) {
				void(el[i].checked=1);
			  }
			}
			
			function inserttext(text) {
			  text = ' ' + text + ' ';
			  if ( opener.document.forms['theform'].message.createTextRange && opener.document.forms['theform'].message.caretPos) {
				var caretPos = opener.document.forms['theform'].message.caretPos;
				caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
			  } else {
				opener.document.forms['theform'].message.value  += text;
			  }
			  opener.document.forms['theform'].message.focus();
			}
			
			
			
			
		
		/*
		 * Begin synch specific script
		 * @return bool
		 */
		 
		 	
		function toggleDisplay(id, value){
			
			if(!value){
				value = 'block';
			}

			style = YAHOO.util.Dom.getStyle(id, 'display') != value? value:'none';
		//	YAHOO.log('toggleDisplay: style = '+style);
			YAHOO.util.Dom.setStyle(id, 'display', style);
			return style=='none';

		}

		function hide(id){
			YAHOO.util.Dom.setStyle(id, 'display', 'none');
		}
		
		/*
		 * Toggle whether a nav item is able to fold by adding/removing the script controlling folding
		 * @param string id element id of element
		 */
		function toggleNavFoldingScript(id){
			// Get the element, check it script content. Set accordingly
			try{
				childNodes = YAHOO.util.Dom.get(id).childNodes;
				toggleScript(childNodes[0], cc16);
				toggleScript(childNodes[2], cc16);
			}
			catch(e){
				// do nothing
			}
			
		}
		
		/*	Toggle whether a script is applied to an object attribute
		 *	@param object o object with attribute to toggle
		 *	@param object s function to toggle		
		 */
		function toggleScript(o, s){
			
			if(o.onclick){
				o.onclick = null;
			}
			else {
				o.onclick = s;
			}
			
		}
		
		/*
		 * Toggle whether nav item synch content is shown or not.
		 * @param string id element id of element
		 */
		function toggleSynchItemDisplay(id){
			
			toggleDisplay('synch_div_1');
			try{
				node = YAHOO.util.Dom.get(id);
				if(parseInt(node.getAttribute('expanded'))){
					toggleNavFolding(id);
					toggleNavFoldingScript(id);
				}
				else {
					
					toggleNavFoldingScript(id);
					toggleNavFolding(id);
				}
			}
			catch(e){
				// do nothing
			}
		}

		/*
		 * Toggle nav item folding by element id.
		 * @param string id element id of element
		 */
		function toggleNavFolding(id){
			
			try{
				node= YAHOO.util.Dom.get(id).childNodes[2];
				if(node.onclick){
					node.onclick();
				}
			
			}
			catch(e){
				// do nothing
			}
		}
		
		function logAttibutes(attributes, text){
			if(!attributes){
				YAHOO.log("logAttibutes: attributes empty.");
				return;
			}
			
			if(!text){
				text = '';
			}
			var stream = '';
			YAHOO.log("logAttibutes: "+text);
			for(var i=0;i<attributes.length;i++){
				//stream+="attributes["+i+"] = "+attributes[i]+"\n";
				myLogger.iterate(attributes[i], 'attributes['+i+'] = ', true);
			}
	
		}
		
		/*
		 * Run the demonstration programmatically to save time. 
		 * @return void
		 */
		function runDemonstration(){
			toggleNavFolding('synch_nav_12');
			toggleNavFolding('synch_nav_123');
			toggleSynchItemDisplay('synch_nav_1234');
		}
		
		/*
		 * When a conflict resolution option is chosen the list item must be removed. 
		 * If it is the last list item then the parent UL tag and the Grand parent LI 
		 * tag must also be removed.
		 * @param string id element id of element
		 * @return void
		 */
		function resolveConflict(id){

			// remove element from tree.
			node = YAHOO.util.Dom.get(id);
			parentNode = node.parentNode;
			removeNode(node)

			if(!hasConflicts(parentNode)){
				var grandParentNode =parentNode.parentNode;
				removeNode(parentNode);
				removeNode(grandParentNode);
			}
			
		}
		
		function hasConflicts(node){
			if(!node){
				return false;
			}	
			
			try{
				childNodes = node.childNodes;
				for(var i=0;i<childNodes.length;i++){
					if(hasConflict(childNodes[i])){
						return true;
					}
				}
			}
			catch(e){
				return false;
			}
			
			return false;
		}
		
		function hasConflict(node){
			if(!node){
				return false;
			}

			if(YAHOO.util.Dom.hasClass(node, 'hasConflict')){
				return true;
			}
			
			return false;
		}
		
		function removeNode(node){
			
			//YAHOO.print_r(node, 'removeNode: node = ');
			parentNode = node.parentNode;
			parentNode.removeChild(node);
		}
		
		function hasChildren(node, threshold){
			if(!node){
				return false;
			}
			
			if(!threshold){
				threshold = 0;
			}
			
			if(node.childNodes && (node.childNodes.length > threshold)){
				return true;
			}
			
			return false;
		}
		
		function addListItem(id){
			// remove element from tree.
			node = YAHOO.util.Dom.get(id);
			var listItem = document.createElement('li');
			//listItem.innerHTML('new list item');
			var textNode = document.createTextNode("some text");
			listItem.appendChild(textNode);
			node.appendChild(listItem);
			YAHOO.iterate(listItem, 'addListItem: listItem = ', true);
		}
		
		/*
		 * Stop key nav items from expanding by removing the script controlling folding. 
		 * @param string id element id of element
		 */
		function restrictNavFolding(){
			toggleNavFoldingScript('synch_nav_1234');
		}
		
		//once the DOM has loaded, we can go ahead and set up our tree:
		YAHOO.util.Event.onDOMReady(restrictNavFolding);
		
