		<?php global $CFG;?>
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
            
            table .synch-session{
                width:100%;
                margin: auto;
            }

            table.synch-session thead th {
              vertical-align: top;
            }
            
            table.synch-session tbody td,
            table.synch-session thead th
            {
                padding: 5px;
            }
            
            .synch-session {
                background-color:#FFFFFF;
                border-color:#DDDDDD;
            }

            .synch-session {
                font-size:0.8em;
            }
             
            .synch-session {
                margin-left:auto;
                margin-right:auto;
                width:90%;
            }
             
            .synch-session {
                width:100%;
            }
             
            .synch-session, .forumpost {
                border-collapse:separate;
                border-style:solid;
                border-width:1px;
            }
            
            .synch-session th {
                background-color:#FFDA9D;
                border-color:#FFB63B;
            }
            
            .synch-session th  {
                border-left:1px solid;
                border-right:1px solid;
            }
            
            th {
                font-weight:bold;
            }
		</style>
		
<?php $item = $page->synchItem;?>

							<h2 class="headingblock header">Finish Session</h2>
                        <p>You have successfully sychronised the contents of the following item. View the course <a href="<?php echo $CFG->wwwroot?>/course/view.php?id=<?php echo $item->id?>"> <?php echo $item->title?></a>.</p>

                        <table class="synch-session">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Summary</th>
                                    <th>Last Synchronised</th>    
                                </tr>
                            </thead>
                            <tbody>
                                
                                <tr>
                                    <td width="150"><a href="<?php echo $CFG->wwwroot?>/course/view.php?id=<?php echo $item->id?>"> <?php echo $item->title?></a></td>
                                    <td width="80"><?php echo $SynchViewController->getImageForContentItemType(synch_view_controller::$TYPE_COURSE);
                                    echo ' '.$item->type?></td> 
                                    <td width="500"><?php echo $item->summary?></td>
                                    <td><?php echo $item->lastSynchronised?></td>
                                </tr>
                            </tbody>
                            
                        </table>