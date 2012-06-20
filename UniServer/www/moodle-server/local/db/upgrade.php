<?php
function xmldb_local_upgrade($oldversion) {
    global $CFG, $THEME, $db;
    
    $result = true;
    
    if ($result && $oldversion < 2008032500) {

    /// Define table backup_guids to be created
        $table = new XMLDBTable('backup_guids');

    /// Adding fields to table backup_guids
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('guid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('src_table', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('src_field', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('src_value', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table backup_guids
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
   //     $table->addKeyInfo('backup_guids_uk', XMLDB_KEY_UNIQUE, array('src_table', 'src_field', 'src_value'));

    /// Adding indexes to table backup_guids
        $table->addIndexInfo('guid_idx', XMLDB_INDEX_UNIQUE, array('guid'));

    /// Launch create table for backup_guids
        $result = $result && create_table($table);

    /// Define table restore_guids to be created
        $table = new XMLDBTable('restore_guids');

    /// Adding fields to table restore_guids
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('guid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('src_table', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('src_field', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('src_value', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table restore_guids
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        // uncommenting this key - it doesn't work for mysql users.
 //       $table->addKeyInfo('backup_guids_uk', XMLDB_KEY_UNIQUE, array('src_table', 'src_field', 'src_value'));

    /// Adding indexes to table restore_guids
        $table->addIndexInfo('guid_idx', XMLDB_INDEX_UNIQUE, array('guid'));

    /// Launch create table for restore_guids
        $result = $result && create_table($table);
    }
    
    if ($result && $oldversion < 2008032501) {

    /// Define field src_value to be dropped from backup_guids
        $table = new XMLDBTable('backup_guids');
        $field = new XMLDBField('courseid');

    /// Launch drop field src_value
        $result = $result && drop_field($table, $field);
    }
    
    if ($result && $oldversion < 2008032602) {

    /// Define key backup_guids_uk (unique) to be dropped form restore_guids
        $table = new XMLDBTable('restore_guids');
        $key = new XMLDBKey('backup_guids_uk');
        $key->setAttributes(XMLDB_KEY_UNIQUE, array('src_table', 'src_field', 'src_value', 'courseid'));

    /// Launch drop key backup_guids_uk
        $result = $result && drop_key($table, $key);
        

    /// Define key backup_guids_uk (unique) to be added to restore_guids
  //      $table = new XMLDBTable('restore_guids');
 //       $key = new XMLDBKey('backup_guids_uk');
 //       $key->setAttributes(XMLDB_KEY_UNIQUE, array('src_table', 'src_field', 'src_value', 'courseid'));

    /// Launch add key backup_guids_uk
//        $result = $result && add_key($table, $key);


    /// Define index guid_idx (not unique) to be dropped form restore_guids
        $table = new XMLDBTable('restore_guids');
        $index = new XMLDBIndex('guid_idx');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('guid'));

    /// Launch drop index guid_idx
        $result = $result && drop_index($table, $index);
    
    /// Define index guid_idx (not unique) to be added to restore_guids
        $table = new XMLDBTable('restore_guids');
        $index = new XMLDBIndex('guid_idx');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('guid'));

    /// Launch add index guid_idx
        $result = $result && add_index($table, $index);        
    }
    
    //new incremental_courses table.
    if ($result && $oldversion < 2008041101) { // new incremental table.
   
      /// Define table to be created
        $table = new XMLDBTable('incremental_courses');

    /// Adding fields to table
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('laststarttime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('lastendtime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('laststatus', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('nextstarttime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

    /// Adding keys to table
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
    /// Launch create table
        $result = $result && create_table($table);

    }
       
    if ($result && $oldversion < 2008041102) { //new incremental_instance table.
   
      /// Define table
        $table = new XMLDBTable('incremental_instance');

    /// Adding fields to table
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('filename', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('hash', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
    /// Launch create table
        $result = $result && create_table($table);

   }
    
    return($result);   
}
    
?>
