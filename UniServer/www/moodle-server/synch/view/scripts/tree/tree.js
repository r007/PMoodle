/**
 *
 * @copyright &copy; 2007 The Open University
 * @author c.chambers@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package tree
 */
/****************************************************************************/
/****************************************************************************/
/****************************************************************************/

/**
 * The mdl_Tree class defines a tree controller object.
 *
 * @class mdl_Tree
 * @constructor
 */
function mdl_Tree(){
	
	/*
	*	The tree in the dom. May be irrelevant if the data is kept up to date. 
	*/
	var tree = [];
	
	/*
	*	The underlying data for the tree
	*/
	this.data = [];
	
	/*
	 * Id of the tree. Used in dom
	 */
	this.id = null
	
	/*
	 * Id of the container that holds the tree. Used in dom
	 */
	this.containerId = null
	
	/*
	 * Root path to retrieve files from. Used by images
	 */
	this.wwwroot = null
	
	/*
	 * Standard separator used to create ids and styles. Used in dom
	 */
	this.separator = '-';
	
	/*
	 * Standard separator used to create ids and styles. Used in dom
	 */
	this.listPrefix = 'list';
	
	/*
	 * Standard separator used to create ids and styles. Used in dom
	 */
	this.listItemPrefix = 'listItem';
	
	this.childStatusPrefix = 'childStatus'
	
	this.synchronisePrefix = 'synchronise';
	
	this.iconCollapsePrefix = 'icon-collapse';
	
	/*
	 * Ids are generated according to item type. Thus we need a simple 
	 * and quick system to identify the type. Used in dom
	 */
	this.ELEMENT_TYPE_UL = 0;
	this.ELEMENT_TYPE_LI = 1;
	this.ELEMENT_TYPE_CHILD_STATUS = 2;
	this.ELEMENT_TYPE_SYNCHRONISE = 3;
	this.ELEMENT_TYPE_ICON_COLLAPSE = 4;
	
	this.IMAGE_PATH_CHECK_CHILDREN = 'images/icon_checkChildren.gif';
	this.IMAGE_PATH_HAS_CHILDREN = 'images/icon_hasChildren.gif';
	this.IMAGE_PATH_NO_CHILDREN = 'images/icon_noChildren.gif';
	
	this.IMAGE_PATH_PLUS = 'images/plus.gif';
	this.IMAGE_PATH_MINUS = 'images/minus.gif';
	

};
mdl_Tree.prototype = {	
	
	getId: function(){
		return this.id;
	},
	
	setId: function(value){
		this.id = value;
	},
	
	getContainerId: function(){
		return this.containerId;
	},
	
	setContainerId: function(value){
		this.containerId = value;
	},
	
	getWWWRoot: function(){
		return this.wwwroot;
	},
	
	setWWWRoot: function(value){
		this.wwwroot = value;
	},
	
	/*
 	 * Take a node from the tree. Get its children from the data source. Append the children to the 
 	 * underlying array and into the browser dom. 
	 */
	loadNodeData: function(record){
		//YAHOO.log("mdl_tree.loadNodeData: record.data.id = "+record.data.id);
		//YAHOO.iterate(record.data, "mdl_tree.loadNodeData: record.data = ", true);
		
		
		// Load the record into the dom
		try{
			var ids = parentToChild[record.data.id];
			//YAHOO.iterate(ids, "mdl_tree.loadNodeData: ids = ", true);
			if(ids && ids.length){
				record.data.childIds = ids;
				for(var i=0;i<ids.length;i++){
					// Load the record into the underlying array
					//this.appendData(treeData[ids[i]]);
					
					this.loadBranch(treeData[ids[i]]);
					
					//YAHOO.iterate(treeData[ids[i]], "mdl_tree.loadNodeData: treeData[ids["+i+"]] = ", true);
					//var newNode = new YAHOO.widget.TextNode(treeData[ids[i]], record, false);
				}
			}

		}
		catch(e){
			//do nothing for now
		}

		record.checkedChildren = true;
		
		//YAHOO.print_r(this.data, 'mdl_tree.toggleBranch: this.data = ');
		//this.updateNodeChildStatus(record);
	},
	
	
	/*
	 * Update the relevant dom node to show the latest status of the record children. Values
	 * check for children, has children, no children
	 * remove the synchronise link if there are no children
	 */
	
	updateNodeChildStatus: function(record){
		
		//YAHOO.print_r(record, 'mdl_tree.updateRecordChildStatus: record = ');
		var src = '';
		if(!record.checkedChildren){
			src = this.IMAGE_PATH_CHECK_CHILDREN;
		}
		else if(this.recordHasChildren(record)){
			src = this.IMAGE_PATH_HAS_CHILDREN;
		}
		else {
			src = this.IMAGE_PATH_NO_CHILDREN;
			// Remove the synchronise link
			node = this.getElementById(record.data.id, this.ELEMENT_TYPE_SYNCHRONISE);
			removeNode(node);
		}
		
		// Update the image src
		img = this.getElementById(record.data.id, this.ELEMENT_TYPE_CHILD_STATUS);
		img.src = this.getWWWRoot() + '/' + src;
		
	},
	
	/*
	 * Load a root record to begin a branch
	 */
	
	loadRootNode: function(record){
		
		this.loadBranch(record);
	},
	
	appendData: function(record){
		if(!record || !record.data.id){
			return false;
		}
		
		this.data[record.data.id] = record;
		return true;
	},
	
	/*
	 * Display the root nodes of the tree. Or just the nodes that are passed in.
	 */
	 
	show: function(){
	},
	
	/*
	 * Create the trunk (ul)
	 */
	init: function(){
		this.createTrunk();
	},
	
	loadBranch: function (record){
		
		// Load the record into the underlying array
		this.appendData(record);
		//this.createBranch(record);

	},
	
	createTrunk: function(){
		var container = YAHOO.util.Dom.get(this.getContainerId());
		
		var trunk = document.createElement('ul');
		trunk.setAttribute('id', this.getId()+this.separator+this.listPrefix);
		container.appendChild(trunk);
	},
	
	/*
	 * Convenience method to get the trunk element
	 */
	getTrunk: function(){
		return YAHOO.util.Dom.get(this.getId());
	},
	
	/*
	 * Tree ids have a prefix to them. Pass in a standard id, return the corresponding id
	 * in tree format
	 */
	generateId: function(id, type){
		
		var suffix ='';
		switch(type){
			case this.ELEMENT_TYPE_UL:
				suffix = this.separator+this.listPrefix;
				break;
			case this.ELEMENT_TYPE_LI:
				suffix = this.separator+this.listItemPrefix;
				break;
				
			case this.ELEMENT_TYPE_CHILD_STATUS:
				suffix = this.separator+this.childStatusPrefix;
				break;
				
			case this.ELEMENT_TYPE_SYNCHRONISE:
				suffix = this.separator+this.synchronisePrefix;
				break;
				
			case this.ELEMENT_TYPE_ICON_COLLAPSE:
				suffix = this.separator+this.iconCollapsePrefix;
				break;
		}
		return this.getId()+this.separator+id+suffix;
	},
	
	/*
	 * Tree ids have a prefix to them. Pass in a standard id, return the corresponding id
	 * in tree format
	 */
	retrieveRecordId: function(id, type){
		
		try{
			list = id.split('-');
			return list[1];
		}
		catch(e){
			throw(e);
			return null;
		}
	},
	
	/*
	 * Tree ids have a prefix to them. Pass in the standard id, get a node back 
	 * containing the corresponding tree id
	 */
	getElementById: function(id, type){
		return YAHOO.util.Dom.get(this.generateId(id, type));
	},
	
	/*
	 * Create a branch from the record that is passed in. Append it to its parent given its id
	 */
	createBranch: function(record){
		
		var parent;
		// Is it a root branch
		if(!record.data.parentId){
			parent = YAHOO.util.Dom.get(this.getContainerId());
		}
		else if(!parent && record.data.parentId){
			parent = this.getElementById(record.data.parentId, this.ELEMENT_TYPE_LI);
		}

		if(!parent){
			return false;
		}

		var list = this.getElementById(record.data.parentId, this.ELEMENT_TYPE_UL);
		if(!list || list.length<1){
			list = document.createElement('ul');
			list.setAttribute('id', this.generateId(record.data.parentId, this.ELEMENT_TYPE_UL));
		}
		
		parent.appendChild(list);
		var listItem = document.createElement('li');
		list.appendChild(listItem);
		
		
		var img = document.createElement('img');
		img.src = this.IMAGE_PATH_CHECK_CHILDREN;
		img.id = this.generateId(record.data.id, this.ELEMENT_TYPE_CHILD_STATUS);
		listItem.appendChild(img);
		
		var text = document.createTextNode(record.data.label+' ');
		
		listItem.appendChild(text);
		
		var link = document.createElement('a');
		link.appendChild(document.createTextNode('synchronise'));
		link.id = this.generateId(record.data.id, this.ELEMENT_TYPE_SYNCHRONISE);
		link.setAttribute('class', 'link ');
		link.className = 'link';
		listItem.appendChild(link);
		
		listItem.setAttribute("id", this.generateId(record.data.id, this.ELEMENT_TYPE_LI));
		listItem.setAttribute('class', record.data.moduleType);
		listItem.className = record.data.moduleType; // Set for ie. Won't recognise the attribute otherwise
		listItem.setAttribute('title', record.data.moduleType);

	},
	
	toggleBranch: function (id){

		YAHOO.log('mdl_tree.toggleBranch: id = '+id);
		// does the branch have children already loaded
			//yes: show/hide them
			//no: has Moodle been checked for children
				//yes: Indicate there are no children i.e. remove the expand icon. 
				//		May need option to check again in case something is added. this should be rare though
				//no: check Moodle for child branches
		
		//YAHOO.log('mdl_tree.toggleBranch: id = '+id);
		var record = this.getRecordById(id);	
		YAHOO.print_r(this.data, 'mdl_tree.toggleBranch: this.data = ');
		//YAHOO.iterate(record.data, 'mdl_tree.togglerecordanchData.data = ', true);	
		
		if(this.recordHasChildren(record)){
			// Display the children. 
			this.toggleBranchDisplay(record.data.id, this.ELEMENT_TYPE_UL);
			return;
		}
		
		// No children to display. should remove the icon
		// TODO: when the UI is added the expand icon should be removed by changing the class.
		return;
	

	},
	
	/*
	 * Convenience method to generate the correct id for a branch and toggle its display
	 */
	toggleBranchDisplay: function (id, type){
		var hidden = toggleDisplay(this.generateId(id, type));
		//YAHOO.log('mdl_tree.toggleBranchDisplay: hidden = '+hidden);
		var src = hidden ? this.IMAGE_PATH_PLUS:this.IMAGE_PATH_MINUS;
		//YAHOO.log('mdl_tree.toggleBranchDisplay: src = '+src);
	
		
		// Update the image src
		img = this.getElementById(id, this.ELEMENT_TYPE_ICON_COLLAPSE);
		//YAHOO.log('mdl_tree.toggleBranchDisplay: img.src = '+img.src);
		img.src = this.getWWWRoot() + '/' + src;
	},
	
	getRecordById: function (id){
		if(!id || !this.data || !this.data[id]){
			return null;
		}
		
		return this.data[id];
	},
	
	/*
	 * Does the record passed in have any children stored as child ids?
	 */
	recordHasChildren: function(record){
		return record.data.childIds.length?true:false;
	},
	
	/*
	 * Has the server checked whether the record passed in has any children?
	 */
	recordHasCheckedChildren: function(record){
		return record.checkedChildren;
	},
	
	/*
	 * Simulate a time out to reflect the web service delay?
	 */
	setDelay: function(milliseconds, callback){
		
		
		// TODO: add the method code.
	}
}

function mdl_TreeBranch(id, label, parentId){
	this.id = id;
	this.label = label;
	this.parentId =parentId;
}
