<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
//
// CodeX: Breaking Down the Barriers to Source Code Sharing inside Xerox
// Copyright (c) Xerox Corporation, CodeX / CodeX Team, 2001-2002. All Rights Reserved
// http://codex.xerox.com
//
// $Id$
//
//	Originally written by Quentin Cregan, SourceForge 06/2000
//	Modified by Laurent Julliard 2001-2004, CodeX Team, Xerox

require('../doc_utils.php');
require('pre.php');

if (!($group_id)) {
	exit_no_group();
}

if (!(user_ismember($group_id,"D1"))) {
	exit_permission_denied();
}

function main_page($group_id) {
    docman_header_admin(array('title'=>'Document Manager Admin Page',
			      'help'=>'DocumentAdministration.html'));
    echo '<p><b>Pending Submissions:</b>  <p>';
    display_docs('3',$group_id);
    // doc_group 3 == pending
    echo '<p>';
    echo '<b>Active Submissions:</b>  <p>';
    display_docs('1',$group_id);
    //doc_group 1 == active
    docman_footer($params);
    
}

//begin to seek out what this page has been called to do.

if (strstr($mode,"docedit")) {
    $query = "select * from doc_data,doc_groups "
	."where docid='$docid' "
	."and doc_groups.doc_group = doc_data.doc_group "
	."and doc_groups.group_id = '$group_id'";
    $result = db_query($query);
    $row = db_fetch_array($result);
    
    docman_header_admin(array('title'=>'Edit Document',
			'help'=>'DocumentAdministration.html#DocumentPublication'));
    
    echo '
	
	<form name="editdata" action="index.php?mode=docdoedit&group_id='.$group_id.'" method="POST"  enctype="multipart/form-data">
      <INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="'.$sys_max_size_upload.'">

	  <table border="0" width="75%">

	  <tr>
	    <th>Document Title:</th>
	    <td><input type="text" name="title" size="60" maxlength="255" value="'.$row['title'].'"></td>
	    <!-- td class="example">(e.g. How to use the download server)</td -->

	  </tr>
	  <tr>
	  </tr>
	  <tr>
	  <th>Description:</th>
			       
	  <td><textarea cols="60" rows="4"  wrap="virtual" name="description">'.$row['description'].'</textarea></td>
	  <!-- td class="example">(e.g. Instructions on how to download files for newbies)</td -->

	</tr>

	<tr>
	<th> <input type="checkbox" name="upload_instead" value="1"> <B>Upload New File:</B></th>
	<td> <input type="file" name="uploaded_data" size="50">
      <br><span class="smaller"><i>(The maximum upload file size is ';
    echo formatByteToMb($sys_max_size_upload);
    echo ' Mb)</i></span>
	</td>
	<!-- td> (HTML file) </td -->

	</tr>';
	// Display the content only for HTML and text documents that were not uploaded but copy/pasted
	if ( ( ($row['filetype'] == 'text/html')||($row['filetype'] == 'text/plain') ) && ($row['filesize']==0) ){
            echo '<tr>
	<th valign="top"><br><br>or Edit Document in place:</th>
	<td><textarea cols="60" rows="20" wrap="virtual" name="data">';
	    echo $row['data'];
            echo '</textarea></td>
	</tr>';
        }

	echo '<tr>
	<th>Group doc belongs in:</th>
        	<td>';

    display_groups_option($group_id,$row['doc_group']);
    
    echo '</td>
	    </tr>

	    <tr>
	    <th>State:</th>
	    <td>';

    $res_states=db_query("select * from doc_states;");
    echo html_build_select_box ($res_states, 'stateid', $row['stateid']);
    
    echo '</td>
	    </tr>

	    <tr>
	    <th>Access to registered user only:</th>
		<td><input type="checkbox" name="restricted_access" value="1"';
		if ( $row['restricted_access'] == 1 ) {
		    print " checked";
		}
    echo '></td>
	    </tr>

                      </table>

	   <input type="hidden" name="docid" value="'.$row['docid'].'">
	   <input type="submit" value="Submit Edit">

	   </form>';

    docman_footer($params);

} elseif (strstr($mode,"groupdelete")) {
    $query = "select docid "
	."from doc_data "
	."where doc_group = ".$doc_group."";
    $result = db_query($query);
    if (db_numrows($result) < 1) {
	$query = "delete from doc_groups "
	    ."where doc_group = '$doc_group' "
	    ."and group_id = $group_id";
	db_query($query);
	docman_header_admin(array('title'=>'Document Group Delete',
			'help'=>'DocumentGroupManagement.html'));
	print "<p><b>Group deleted. (GroupID : ".$doc_group.")</b>";	
	docman_footer($params);	
	
    } else {
	
	db_query($query);
	docman_header_admin(array('title'=>'Document Group Delete - Failed',
			'help'=>'DocumentGroupManagement.html'));
	print "Group was not deleted.  Cannot delete groups that still have documents grouped under them."; 
	docman_footer($params);
    }
    
} elseif (strstr($mode,"groupedit")) {
    docman_header_admin(array('title'=>'Document Group Edit',
			      'help'=>'DocumentAdministration.html#DocumentGroupManagement'));
    $query = "select * "
	."from doc_groups "
	."where doc_group = '$doc_group' "
	."and group_id=$group_id";
    $result = db_query($query);
    $row = db_fetch_array($result);
    echo '
			<h2>Edit a Group</h2>

			<form name="editgroup" action="index.php?mode=groupdoedit&group_id='.$group_id.'" method="POST">
			<table>
			<tr><th>Group Name:</th>  <td><input type="text" name="groupname" value="'.$row['groupname'].'"></td></tr>
			<input type="hidden" name="doc_group" value="'.$row['doc_group'].'">
			<tr><td> <input type="submit"></td></tr></table>	
			</form>	
			';
    docman_footer($params);
    
} elseif (strstr($mode,"groupdoedit")) {
    $query = "update doc_groups "
	."set groupname='".htmlspecialchars($groupname)."' "
	."where doc_group='$doc_group' "
	."and group_id = '$group_id'";
    db_query($query);
    $feedback .= "Document Group Edited.";
    main_page($group_id);
    
} elseif (strstr($mode,"docdoedit")) {
    
    // Check a number of things: group_id is correct, title and
    // description are non empty
    if (!$doc_group || $doc_group ==100) {
    	//cannot add a doc unless an appropriate group is provided
    	exit_error('Error','No Valid Document Group Was Selected');
    }
    
    if (!$title || !$description) { 
    	exit_missing_param();
    }
    
    //Page security - checks someone isnt updating a doc
    //that isnt theirs.

    $query = "select dd.docid "
	."from doc_data dd, doc_groups dg "
	."where dd.doc_group = dg.doc_group "
	."and dg.group_id = ".$group_id." "
	."and dd.docid = '".$docid."'"; 
		
    $result = db_query($query);
	
    if (db_numrows($result) == 1) {

	// Upload the document if needed
	if ($upload_instead) {
	    $data = addslashes(fread( fopen($uploaded_data, 'r'), filesize($uploaded_data)));
	    if ((strlen($data) > 0) && (strlen($data) < $sys_max_size_upload)) {
		//size is fine
		$feedback .= ' Document Uploaded ';
	    } else {
		//too big or small
		$feedback .= ' ERROR - document must be non null and < '.$sys_max_size_upload.' chars in length ';
		exit_error('Missing Info',$feedback.' - Please click back and fix the error.');
	    }
	}

	if ( !isset($restricted_access) ) {
	    $restricted_access = 0;
	}

	if ($upload_instead) {
	    // Upload file
            $query = "update doc_data "
                ."set title = '".htmlspecialchars($title)."', "
                ."data = '".$data."', "
                ."updatedate = '".time()."', "
                ."doc_group = '".$doc_group."', "
                ."stateid = '".$stateid."', " 
                ."description = '".htmlspecialchars($description)."', "
                ."restricted_access = '".$restricted_access."', "
                ."filename = '".$uploaded_data_name."', "
                ."filesize = '".$uploaded_data_size."', "
                ."filetype = '".$uploaded_data_type."' "
                ."where docid = '".$docid."'"; 
	} else {
	    if ( strlen($data) > 0 ) {
	        // Copy/paste data
        	$query = "update doc_data "
        	    ."set title = '".htmlspecialchars($title)."', "
        	    ."data = '".htmlspecialchars($data)."', "
        	    ."updatedate = '".time()."', "
        	    ."doc_group = '".$doc_group."', "
        	    ."stateid = '".$stateid."', " 
        	    ."description = '".htmlspecialchars($description)."', "
        	    ."restricted_access = '".$restricted_access."', "
        	    ."filename = '', "
        	    ."filesize = '0', "
        	    ."filetype = 'text/html' "
        	    ."where docid = '".$docid."'"; 
            } else {
                // No new document - Just update the associated data
        	$query = "update doc_data "
        	    ."set title = '".htmlspecialchars($title)."', "
        	    ."updatedate = '".time()."', "
        	    ."doc_group = '".$doc_group."', "
        	    ."stateid = '".$stateid."', " 
        	    ."description = '".htmlspecialchars($description)."', "
        	    ."restricted_access = '".$restricted_access."' "
        	    ."where docid = '".$docid."'"; 
            }
        }
    		
	$res_insert = db_query($query);
    if (db_affected_rows($res_insert) < 1) {
    	$feedback .= ' ERROR - SQL Error= '. db_error();
    	exit_error('DB Error',$feedback);
    }
    
	$feedback .= "Document \" ".htmlspecialchars($title)." \" updated";
	main_page($group_id);

    } else {

	    exit_error("Error","Unable to update - Document does not exist, or document's group not the same as that to which your account belongs.");

    }

} elseif (strstr($mode,"groupadd")) {
    $query = "insert into doc_groups(groupname,group_id) " 
	."values ('"
	."".htmlspecialchars($groupname)."',"
	."'$group_id')";
		
    db_query($query);
    $feedback .= "Group ".htmlspecialchars($groupname)." added.";
    main_page($group_id);
	
} elseif (strstr($mode,"editgroups")) {
    docman_header_admin(array('title'=>'Edit Document Group List',
			      'help'=>'DocumentAdministration.html#DocumentGroupManagement'));
    echo '<h2>Edit Document Groups</h2>
	    <h3>Add a Group</h3>
	    <form name="addgroup" action="index.php?mode=groupadd&group_id='.$group_id.'" method="POST">
	    <table>
	        <tr><th>New Group Name:</th>  <td><input type="text" name="groupname"></td><td><input type="submit" value="Add"></td></tr></table>	
	    </form>	
	<h3>Group List</h3>	';
    display_groups($group_id);

} elseif (strstr($mode,"editdocs")) {

    docman_header_admin(array('title'=>'Edit Document List',
			      'help'=>'DocumentAdministration.html'));
    echo '<h2>Edit Documents</h2>';
    print "<p><b>Active Documents:</b><p>";	
    display_docs('1',$group_id);
    print "<p><b>Pending Documents:</b><p>";	
    display_docs('3',$group_id);
    print "<p><b>Hidden Documents:</b><p>";	
    display_docs('4',$group_id);
    print "<p><b>Deleted Documents:</b><p>";	
    display_docs('2',$group_id);
    print "<p><b>Private Documents:</b><p>";	
    display_docs('5',$group_id);
    docman_footer($params);	

} else {
    main_page($group_id);
} //end else

?>
