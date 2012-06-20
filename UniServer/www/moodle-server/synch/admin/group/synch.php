<?php
 require_once dirname(__FILE__). "/setup.php";

GLOBAL $GroupBeanFactory, $GroupSynchManager, $Out;


$page = new object;
$page->action = synch_GetRequestItem("action");
$page->self = $_SERVER['PHP_SELF'];
$page->message = null;
$GroupSynchManager->setDebug(0);
$Out->disable();

switch($page->action){
	case 1;//Preview the records to synch
		$page->recordsToSynch = $GroupSynchManager->getRecordsToSynch();
		break;
		
	case 2; //Perform the import.
		$GroupSynchManager->performImport();
		
	case 3; //Perform a simple import.
		if($GroupSynchManager->performSimpleImport()){
			$page->message = "The groups were successully imported";
		}
	break;
	
}
?>
<html>
	<head>
		<title>Group Synch</title>
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
	  			<a href="<?php echo $page->self?>?action=2">Commence import of the groups.</a>
	  		</li>
	  		<li>	
	  			<a href="<?php echo $page->self?>?action=3">Commence basic import of the groups.</a>
	  		</li>
	  	</ul> 
	  	
	</body>
</html>
<?php require_once dirname(__FILE__). "/../synch-teardown.php";?>
