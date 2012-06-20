		<link rel="stylesheet" type="text/css" href="<?php echo$CFG->synch->wwwroot?>/view/stylesheets/synch.css" />
		<link type="text/css" rel="stylesheet" href="<?php echo$CFG->synch->wwwroot?>/view/yui/logger/assets/logger.css" />
		
		<style type="text/css">
		
			#debugLogReader{height: 1000px;}
			.hidden {display:none}
			.hasConflict {background-color:#DDD;}
			.containsConflict {background-color:#DEE8EE;}
			.link {cursor: pointer; color:#0066aa;}
            
            ul.key, .synch-session ul{
                list-style:none;
            }
            
            /*
            #synchNav ul {
                display:none;
            }
            */
            
            
            #synchNav ul.root {
                display:block;
            }
		</style>
		
			<!-- Dependency source files -->  
            <!-- There are lots of dependency files. These will be reduced in time. For now many of them are necessary simply to load the 
                 debugger. This isn't strictly necessary but since this is a development release we thought you might want to switch on the 
                 debugger and take a look at what is happening behind the scenes.-->
		<script type="text/javascript" src="<?php echo$CFG->synch->wwwroot?>/view/yui/yahoo/yahoo-min.js"></script>
		<script type="text/javascript" src="<?php echo$CFG->synch->wwwroot?>/view/yui/dom/dom-min.js"></script>
		
		<script type="text/javascript" src="<?php echo$CFG->synch->wwwroot?>/view/yui/event/event-min.js"></script>
		<script type="text/javascript" src="<?php echo$CFG->synch->wwwroot?>/view/yui/dragdrop/dragdrop-min.js"></script>
		<script type="text/javascript" src="<?php echo$CFG->synch->wwwroot?>/view/yui/logger/logger-min.js"></script>
		
        <!-- this is an extra file providing extra features to the logger class --> 
		<script type="text/javascript" defer="defer" src="<?php echo$CFG->synch->wwwroot?>/view/scripts/classes/advancedLogger.js"></script>

		<script type="text/javascript" src="<?php echo$CFG->synch->wwwroot?>/view/scripts/tree/tree.js"></script>
		<script type="text/javascript" defer="defer" src="<?php echo$CFG->synch->wwwroot?>/view/scripts/synch_select_items.js"></script>
		<script type="text/javascript" src="<?php echo$CFG->synch->wwwroot?>/view/scripts/synch_tree.js"></script>

							<h2 class="headingblock header">Find Content</h2>
                            <p>Use this page to find courses from a Moodle Server, load them onto this Offline Moodle and synchronise changes that are made. To begin, you must first find the item you wish to synchronise.</p>
                        
                       <p>Below you will find a list of servers providing content you can download. Explore this content by clicking the <img src="view/images/plus.gif" alt="expand" /> icon to load new content under the selected item. Click the <img src="view/images/minus.gif" alt="collapse" /> icon to hide content. When you have found the item you wish to synchronise, click the 'Synchronise' link next to it to begin the synchronisation process.</p>

						 <a href="<?php echo$CFG->synch->wwwroot?>/server_edit.php?hubId=0">Add a server</a>
						<div id="synchNav">
						<!-- Start Dynamic Synch Nav -->
						<?php
							$SynchViewController->writeNavigationHierarchy($SynchContentHierarchy, $remoteServerId);
						?>
						
						<!-- End Dynamic Synch Nav -->
						</div>
                        <a href="<?php echo$CFG->synch->wwwroot?>/find_content.php?clearCache=true">Clear Cache</a>
						 <h4>Key</h4>
                            <p>These icons indicate the type of item displayed in the content tree above.</p>
                            <ul class="key">
                                <li><img src="<?php echo$CFG->synch->wwwroot?>/view/images/icon_hub.gif" alt="Server" />: Moodle Server. This server allows synchronisation</li>
                                <li><img src="<?php echo$CFG->synch->wwwroot?>/view/images/icon_category.gif" alt="Category"/>: Category. Moodle Category</li>
                                <li><img src="<?php echo$CFG->synch->wwwroot?>/view/images/icon_course.gif" alt="Course"/>: Course. Moodle Course</li>
                                <li><img src="<?php echo$CFG->synch->wwwroot?>/view/images/icon_section.gif" alt="Section"/>: Section. Part of a course</li>
                            </ul>
                        
							
							<script type="text/javascript" defer="defer">
		//instantiate log reader
		 
		 var myConfigs = { 
	   		width: "1000px", 
	   		height: "100em",
	   		top: "500px",
	  		newestOnTop:false
		}; 
		 var myLogger = new YAHOO.widget.LogReader("myLogger", myConfigs);
		 myLogger.setTitle("Newest on top");
		// myLogger.width = 1000px;			
		//myLogger.show();
		
		myLogger.hide();
		//YAHOO.iterate(myLogger, 'myLogger = ');
		
		//initialiseSynchTree()
		var tree_wwwroot = '<?echo $CFG->synch->wwwroot.'/view'?>';

		</script>