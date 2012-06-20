<?php
 require_once("setup.php");

GLOBAL $UserBeanFactory, $UserSynchManager;


$page = new object;
$page->action = synch_GetRequestItem("action");
$page->self = $_SERVER['PHP_SELF'];
$page->message = null;
$UserSynchManager->setDebug(1);

switch($page->action){
	case 1;//Preview the records to synch
		$page->recordsToSynch = $UserSynchManager->getRecordsToSynch();
		break;
		
	case 2; //Perform the import.
		$UserSynchManager->performImport();
		
	case 3; //Perform a simple import.
	//$UserSynchManager->performSimpleImport();
	//$UserSynchManager->initialise();
		if($UserSynchManager->performSimpleImport()){
			$page->message = "The users were successully imported";
		}
		
	break;
	
}
?>
<html>
	<head>
		<title>User Synch</title>
	</head>
	<body>
		<?php include("navigation.php");

			if(isset($page->message)){
				echo $page->message;
			}
		?>
		
		<ul>
			<li>	
	  			<a href="<?php echo $page->self?>?action=1">View user records to be imported.</a>
	  		</li>
	  		<li>	
	  			<a href="<?php echo $page->self?>?action=2">Commence import of users.</a>
	  		</li>
	  		<li>	
	  			<a href="<?php echo $page->self?>?action=3">Commence basic import of users.</a>
	  		</li>
	  		<li>	
	  			<a href="<?php echo $page->self?>?action=4">Rebuild the course cache.</a>
	  		</li>
	  	</ul> 
	  	
	  	<?php
	  		if(isset($page->recordsToSynch) && count($page->recordsToSynch)){
	  			
	  //			include("body.synch.preview.php");
	  		
	  		}
	  	?>

	</body>
</html>
<?php require_once("../synch-teardown.php");?>
