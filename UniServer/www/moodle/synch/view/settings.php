<?php
//admin_externalpage_print_header();
print_simple_box_start("center", "");
    if(!empty($page->message)){
    	echo '<p>'.$page->message.'</p>';
    }

?>
<form method="post" action="settings.php">
<div>
<input type="hidden" name="sesskey" value="<?php echo $USER->sesskey ?>" />
<input type="hidden" name="action" value="<?php echo $page->action ?>" />
<table cellpadding="9" cellspacing="0" width="635">

<tr>
    <td align="right" valign="top">Server Id:</td>
    <td valign="top"><input type="text" name="serverId" size="3" value="<?php echo $page->serverId; ?>" /></td>
</tr>

<tr>
    <td></td>
    <td><input type="submit" value="<?php print_string("savechanges"); ?>" /></td>
</tr>
</table>
</div>
</form>
<?php
print_simple_box_end();
//admin_externalpage_print_footer();
?>
