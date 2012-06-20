<?php
          $server = $page->server;
          $details = $server->toObject();

//admin_externalpage_print_header();
print_simple_box_start("center", "");
    if(!empty($page->message)){
    	echo '<p>'.$page->message.'</p>';
    }

?>
<form method="post" action="server_edit.php">
<div>
<input type="hidden" name="sesskey" value="<?php echo $USER->sesskey ?>" />
<input type="hidden" name="action" value="<?php echo $page->action ?>" />
<input type="hidden" name="id" value="<?php echo isset($details->id)?  $details->id : '0' ; ?>" />
<input type="hidden" name="hubId" value="<?php echo isset($details->serverId)?  SynchContentHierarchy::generateDataItemId($details->serverId, synch_view_controller::$TYPE_ID_HUB) : '0' ; ?>" />
<table cellpadding="9" cellspacing="0" width="635">

<tr>
    <td align="right" valign="top">Server:</td>
    <td valign="top">
        <select name="mnetHostId">
        <?php foreach($hosts as $host){
            $selected = $details->mnetHostId == $host->id?'selected="selected" ':'';
            echo '<option value="'.$host->id.'" '.$selected.'>'.$host->name.'</option>';
        }?>
        </select>
        </td>
</tr>
<tr>
    <td align="right" valign="top">Description:</td>
    <td valign="top"><input type="text" name="description" size="60" value="<?php echo synch_print_value(@$details->description); ?>" /></td>
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
