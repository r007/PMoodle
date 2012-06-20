/**** Load Tree data ****/

function TreeNode(id, label, parentId, moduleType){
	
	this.data = {};
	
	this.data.id = id;
	this.data.label = label;
	this.data.parentId =parentId;
	this.data.moduleType = moduleType;
	
	this.data.childIds = [];
	
	this.checkedChildren = false;
}

/*
var treeData = {
					1: new TreeNode(1, "B857 Current issues in public management and social enterprise (10 mb, 43 files) ", 17, "course"),

					2:new TreeNode(2, "Partnerships Collaboration and Leadership (2.6 mb, 34 files)", 1, "week"),
					3:new TreeNode(3, "Collaboration.. forum (760 kb, 2 files) ", 2, "forum"),
					4:new TreeNode(4, "Collaboration.. report Wiki (2.5 mb, 12 files) ", 2, "wiki"),
					5:new TreeNode(5, "Collaboration.. TMA (30 kb 1 file) ", 2, "assignment"),
					6:new TreeNode(6, "Governance ... (12.8 mb, 43 files) ", 1, "week"),
					7:new TreeNode(7, "Governance Forum  (300 kb 3 files) ", 6, "forum"),
					8:new TreeNode(8, "Students' Governance Report Wiki (12.5 mb, 40 files) ", 6, "wiki"),
					9:new TreeNode(9, "Governance TMA (60 kb 0 files) ", 6, "assignment"),
					10:new TreeNode(10, "Advantages of collaboration (60 kb 3 files) ", 3, "discussion"),
					11:new TreeNode(11, "creating a community (35 kb 1 files) ", 10, "post"),
					12:new TreeNode(12, "share workload (17 kb 1 files) ", 10, "post"),
					13:new TreeNode(13, "pool ideas(8 kb 1 files) ", 10, "post"),
					14:new TreeNode(14, "Disadvantages of collaboration (20 kb 2 files) ", 3, "discussion"),
					15:new TreeNode(15, "can create extra work (10 kb 1 file(s)) ", 14, "post"),
					16:new TreeNode(16, "design by committee (10 kb 1 file(s)) ", 14, "post"),
					17: new TreeNode(17, "Open.ac.uk", 0, "hub"),
					18: new TreeNode(18, "Miscellaneous", 0, "category")


				};

var parentToChild = {
						1:[2,6],
						2:[3,4,5],
						3:[10,14],
						6:[7,8,9],
						10:[11,12,13],
						14:[15,16],
						17:[18],
						18:[1]
					};
					
					
*/
					
var tree = new mdl_Tree();

function initialiseSynchTree(){
	
	tree.setId("courseTree");
	tree.setContainerId("synchNav");
	tree.init();
	tree.setWWWRoot(tree_wwwroot);
	
	//YAHOO.iterate(treeData, "initialiseSynchTree: treeData = ", true);
	//YAHOO.print_r(treeData, "initialiseSynchTree: treeData = ");
	
	// set up the basic tree to start. show just the root nodes.
	tree.loadRootNode(treeData["101-1"]);
	
	var record;
	for(record in treeData){
		tree.loadNodeData(treeData[record]);
	}
	
	// Display the tree nodes just loaded
	tree.show();
}

function tree_toggleBranch(id){

	tree.toggleBranch(id);
}

function tree_clickHandler(e) { 
    //get the resolved (non-text node) target: 
    var elTarget = YAHOO.util.Event.getTarget(e);    

    //walk up the DOM tree looking for an <li> 
    //in the target's ancestry; desist when you 
    //reach the container div 
    while (elTarget.id != "synchNav") { 
       //are you an li? 
       if(elTarget.nodeName.toUpperCase() == "LI") { 
       	id = tree.retrieveRecordId(elTarget.id);
            //yes, an li: so write out a message to the log 
            //YAHOO.log("The clicked li had an id of " + id, "info", "clickExample"); 
            tree.toggleBranch(id);
            //and then stop looking: 
            break; 
        } else { 
            //wasn't the container, but wasn't an li; so 
            //let's step up the DOM and keep looking: 
            elTarget = elTarget.parentNode; 
        } 
    } 
} 
//attach clickHandler as a listener for any click on 
//the container div: 
//YAHOO.util.Event.on("synchNav", "click", tree_clickHandler); 
