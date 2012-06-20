<?php
 require_once("setup.php");

GLOBAL $RoleBeanFactory, $RoleSynchManager;


$page = new object;
$page->action = synch_GetRequestItem("action");
$page->self = $_SERVER['PHP_SELF'];

//$RoleSynchManager->setDebug(1);

switch($page->action){
	case 1;//Preview the records to synch
		$page->recordsToSynch = $RoleSynchManager->getRecordsToSynch();
		break;
		
	case 2; //Perform the import.
		$RoleSynchManager->performImport();
		
	case 3; //Perform a simple import.
		if($RoleSynchManager->performSimpleImport()){
			$page->message = "The roles were successully imported";
		}
	break;
	
}
?>
<html>
	<head>
		<title>Role Synch</title>
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
	  			<a href="<?php echo $page->self?>?action=2">Commence import of the roles.</a>
	  		</li>
	  		<li>	
	  			<a href="<?php echo $page->self?>?action=3">Commence basic import of the roles.</a>
	  		</li>
	  	</ul> 
	  	
	</body>
</html>
<?php require_once("../synch-teardown.php");?>
