<?php
 require_once("setup.php");

GLOBAL $BlockBeanFactory, $BlockSynchManager, $Out;


$page = new object;
$page->action = synch_GetRequestItem("action");
$page->self = $_SERVER['PHP_SELF'];
$page->message = null;
$BlockSynchManager->setDebug(0);
$Out->disable();

switch($page->action){
	case 1;//Preview the records to synch
		$page->recordsToSynch = $BlockSynchManager->getRecordsToSynch();
		break;
		
	case 2; //Perform the import.
		if($BlockSynchManager->performImport()){
			$page->message = "The blocks were successully imported";
		}
		break;
	case 3; //Perform a simple import.
		if($BlockSynchManager->performSimpleImport()){
			$page->message = "The blocks were successully imported";
		}
	break;
	
}
?>
<html>
	<head>
		<title>Block Synch</title>
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
	  			<a href="<?php echo $page->self?>?action=2">Commence import of the blocks.</a>
	  		</li>
	  		<li>	
	  			<a href="<?php echo $page->self?>?action=3">Commence basic import of the blocks.</a>
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
