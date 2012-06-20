<?php
/*
 * Created on 5 Mar 2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 	GLOBAL $page, $Out;

 	if($page->recordsToSynch && count($page->recordsToSynch)){
 		$table = new object;	
		echo "The following records are to be synchronised.<br />";
 		$records = $page->recordsToSynch;
 		echo "<table>\n";
 		$fields = array_keys((array)$records[0]->getData());
 		echo "\t<tr>\n";
 		foreach($fields as $field){
 			echo "\t<th>$field</th>\n";	
 		}
 		echo "\t</tr>\n";
 		
 		$record;
 		for($i=0;$i<count($records);$i++){
 			echo "\t<tr>\n";	
 		
 			$record = $records[$i]->getData();	
 		 	foreach($record as $field => $value){
 				echo "\t\t<td>$value</td>\n";	
 		 			
 			}
 			echo "\t</tr>\n";		
 		}
 		echo "</table>\n";
 		echo "<a href=\"synch-forum.php?action=2\">Synchronise forum</a> ";
 		echo "<a href=\"synch-forum.php?action=3\">Perform basic forum synch</a>";
 	}
 	else {
 		echo "There are no forum records to synchronise";
 	}
?>
