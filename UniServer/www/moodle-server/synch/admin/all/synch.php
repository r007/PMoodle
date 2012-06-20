<?php
 require_once("setup.php");

GLOBAL $Out;
$page = new object;
$page->action = synch_GetRequestItem("action");
$page->self = $_SERVER['PHP_SELF'];
$page->message = null;
$page->debug = false;
$Out->disable();
$managers = array(
				"Role",
				"User",
				"Group",
				"Course",
				"Forum"
			);

switch($page->action){
	case 1;//Preview the records to synch
		$page->recordsToSynch = $RoleSynchManager->getRecordsToSynch();
		break;
		
	case 2; //Perform the import.
		for($i=0;$i<count($managers);$i++){
			$manager = 	"$".$managers[$i]."SynchManager";
			eval("GLOBAL $manager;");
			eval($manager."->setDebug(\$page->debug);");
			$synched = false;
			eval("\$synched = ".$manager."->performImport();");
			if($synched){
				$page->message .="The ".$managers[$i]." section was successfully synchronised<br />\n";
			}
		}
		break;
		
	case 3; //Perform a simple import.
		$page->message = '';
		for($i=0;$i<count($managers);$i++){
			$manager = 	"$".$managers[$i]."SynchManager";
			eval("GLOBAL $manager;");
			eval($manager."->setDebug(\$page->debug);");
			$synched = false;
			eval("\$synched = ".$manager."->performSimpleImport();");
			if($synched){
				$page->message .="The ".$managers[$i]." section was successfully synchronised<br />\n";
			}
		}
		//$RoleSynchManager->performSimpleImport();
	break;
	
}
?>
<html>
	<head>
		<title>All Synch</title>
	</head>
	<body>
		<?php include("navigation.php");

			if(isset($page->message)){
				echo $page->message;
			}
		?>
		
		<ul>
			<li>	
	  			<a href="<?php echo $page->self?>?action=1">View records to be imported.</a>
	  		</li>
	  		<li>	
	  			<a href="<?php echo $page->self?>?action=2">Commence import of all data.</a>
	  		</li>
	  		<li>	
	  			<a href="<?php echo $page->self?>?action=3">Commence basic import of all data.</a>
	  		</li>
	  	</ul> 
	  	
	</body>
</html>
<?php require_once("../synch-teardown.php");?>
