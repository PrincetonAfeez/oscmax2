<?php
/*
$Id$

  osCmax e-Commerce
  http://www.osCmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/
// WTL - this goes in admin
require('includes/application_top.php');
// Include current language file, if not exists, fall use English (Why is this not a standard procedure in OsCommerce?)
if(file_exists(DIR_WS_LANGUAGES . $language . '/' . "information.php")) {
	include(DIR_WS_LANGUAGES . $language . '/' . "information.php");
}
else {
	include(DIR_WS_LANGUAGES . 'english/' . "information.php");
}
$languages = tep_get_languages();
// Group information
$gID = (isset($_GET['gID'])) ? $_GET['gID'] : ((isset($_POST['gID'])) ? $_POST['gID'] : 1);
$info_group_query = tep_db_query("select information_group_title, locked from " . TABLE_INFORMATION_GROUP . " where information_group_id = '" . (int)$gID . "'");
$info_group = tep_db_fetch_array($info_group_query);
//$info_group['locked'] = ''; // DEBUG, to ignore locked fields

// WTL - Removed for testing 11/19/07
// Create additional information records for unknown languages
// NOTE: This is a function with some overhead, but is needed as this is an add-on
//       and the new languages are not automatically generated by languages.php
function tep_update_information_languages($language_id = 0) {
	global $languages, $languages_id, $gID;
	if ($language_id == 0) $language_id = $languages_id;

	// Count all items
	$information_query = tep_db_query("select count(*) as information_count from " . TABLE_INFORMATION. " where information_group_id = '" . (int)$gID . "'");
	$information = tep_db_fetch_array($information_query);
	$information_count_all = $information['information_count'];

	// Count items for main language
	$information_query = tep_db_query("select count(*) as information_count from " . TABLE_INFORMATION . " where information_group_id = '" . (int)$gID . "' and language_id = '" . (int)$language_id . "'");
	$information = tep_db_fetch_array($information_query);
	$information_count_single = $information['information_count'];

	if($information_count_all !=  $information_count_single * sizeof($languages))
	{
		// Create array of language id's in information table
		$information_query = tep_db_query("select language_id from " . TABLE_INFORMATION . " where information_group_id = '" . (int)$gID . "' group by language_id");
		while ($information_language = tep_db_fetch_array($information_query)) {
			$information_languages[] = $information_language['language_id'];
		}

		// Create array of language id's in languages
		foreach( $languages as $language) {
			$languages_ids[] = $language['id'];
		}

		// Remove entries with languages no longer being used
		foreach( $information_languages as $_language_id) {
			if(!in_array($_language_id, $languages_ids)) {
				tep_db_query("delete from " . TABLE_INFORMATION . " where information_group_id = '" . (int)$gID . "' and language_id = '" . (int)$_language_id . "'");
			}
		}

		$information_query = tep_db_query("select * from " . TABLE_INFORMATION . " where information_group_id = '" . (int)$gID . "' and language_id = '" . (int)$language_id . "'");
		while ($information = tep_db_fetch_array($information_query)) {
			foreach( $languages_ids as $_language_id) {
				if(!in_array($_language_id, $information_languages)) {
					$sql_data_array = array(
						'language_id' 				=> $_language_id,
						'visible' 					=> tep_db_prepare_input($information['visible']),
						'sort_order' 				=> tep_db_prepare_input($information['sort_order']),
						'information_id'			=> tep_db_prepare_input($information['information_id']),
						'information_group_id' 		=> tep_db_prepare_input($information['information_group_id']),
						'information_title' 		=> tep_db_prepare_input($information['information_title']),
						'information_description' 	=> tep_db_prepare_input($information['information_description']),
						'information_url' 		    => tep_db_prepare_input($information['information_url']),
						'information_target'    	=> tep_db_prepare_input($information['information_target'])
					);
					tep_db_perform(TABLE_INFORMATION, $sql_data_array);
				}
			}
		}
	}
}
function tep_get_information_list ($language_id = 0) {
	global $languages_id, $gID;
	if ($language_id == 0) $language_id = $languages_id;

    $information_query = tep_db_query("select * from " . TABLE_INFORMATION . " where language_id = '" . (int)$language_id . "' and information_group_id = '" . (int)$gID . "' order by sort_order");

	$c=0;
	while ($buffer = tep_db_fetch_array($information_query)) {
		$result[$c] = $buffer;
		$c++;
	}
	return $result;
}
// WT
function tep_get_information_entry ($information_id, $language_id = 0, $column = '') {
	global $languages_id;
	if ($language_id == 0) $language_id = $languages_id;

	$information_query = tep_db_query("select * from " . TABLE_INFORMATION . " where information_id = '" . (int)$information_id . "' and language_id = '" . (int)$language_id . "'");
	$information = tep_db_fetch_array($information_query);

	if(!empty($column)) return $information[$column];
	else return $information;
}
$warning = tep_image(DIR_WS_ICONS . 'warning.gif', WARNING_INFORMATION);
function error_message($error) {
	global $warning;
	switch ($error) {
		case "20":return "<tr class=messageStackError><td>$warning ." . ERROR_20_INFORMATION . "</td></tr>";break;
		case "80":return "<tr class=messageStackError><td>$warning " . ERROR_80_INFORMATION . "</td></tr>";break;
		default:return $error;
	}
}

switch($_REQUEST['information_action']) {

	case "AddSure":
	$language_id = $languages[0]['id']; // First insert the 1st known language
	$sql_data_array = array(
		'language_id' => $language_id,
		'information_group_id' => tep_db_prepare_input($gID)
	);
	if(isset($_POST['visible']))					$sql_data_array['visible'] = tep_db_prepare_input($_POST['visible']);
	if(isset($_POST['sort_order']))					$sql_data_array['sort_order'] = tep_db_prepare_input($_POST['sort_order']);
	if(isset($_POST['parent_id']))					$sql_data_array['parent_id'] = tep_db_prepare_input($_POST['parent_id']);
	if(isset($_POST['information_title']))			$sql_data_array['information_title'] = tep_db_prepare_input($_POST['information_title'][$language_id]);
	if(isset($_POST['information_description']))	$sql_data_array['information_description'] = tep_db_prepare_input($_POST['information_description'][$language_id]);
	if(isset($_POST['information_url']))			$sql_data_array['information_url'] = tep_db_prepare_input($_POST['information_url']);
	if(isset($_POST['information_target']))	        $sql_data_array['information_target'] = tep_db_prepare_input($_POST['information_target']);

	tep_db_perform(TABLE_INFORMATION, $sql_data_array);

	$information_id = tep_db_insert_id();

	for ($i=1, $n=sizeof($languages); $i<$n; $i++) {
		$language_id = $languages[$i]['id'];

		$sql_data_array = array(
			'information_id' => $information_id,
			'language_id' => $languages[$i]['id'],
			'information_group_id' => tep_db_prepare_input($gID)
		);
		if(isset($_POST['visible']))					$sql_data_array['visible'] = tep_db_prepare_input($_POST['visible']);
		if(isset($_POST['sort_order']))					$sql_data_array['sort_order'] = tep_db_prepare_input($_POST['sort_order']);
		if(isset($_POST['parent_id']))					$sql_data_array['parent_id'] = tep_db_prepare_input($_POST['parent_id']);
		if(isset($_POST['information_title']))			$sql_data_array['information_title'] = tep_db_prepare_input($_POST['information_title'][$language_id]);
		if(isset($_POST['information_description']))	$sql_data_array['information_description'] = tep_db_prepare_input($_POST['information_description'][$language_id]);
		if(isset($_POST['information_url']))			$sql_data_array['information_url'] = tep_db_prepare_input($_POST['information_url']);
	    if(isset($_POST['information_target']))	        $sql_data_array['information_target'] = tep_db_prepare_input($_POST['information_target']);

		if(count($sql_data_array) > 1) {
			if(tep_db_perform(TABLE_INFORMATION, $sql_data_array)){
              $messageStack->add_session(SUCCED_INFORMATION . ADD_QUEUE_INFORMATION, 'success');
            }else{
              $messageStack->add_session(ERROR_ADDING, 'error');
		    }
		}
	}
    tep_redirect(tep_href_link(FILENAME_INFORMATION_MANAGER, 'gID=' . $gID));
	break;

	case "Update":
	if ((int)$_POST['information_id']) {
        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
			$language_id = $languages[$i]['id'];
			$sql_data_array = array();
			if(isset($_POST['visible']))					$sql_data_array['visible'] = tep_db_prepare_input($_POST['visible']);
			if(isset($_POST['sort_order']))					$sql_data_array['sort_order'] = tep_db_prepare_input($_POST['sort_order']);
			if(isset($_POST['parent_id']))					$sql_data_array['parent_id'] = tep_db_prepare_input($_POST['parent_id']);
			if(isset($_POST['information_title']))			$sql_data_array['information_title'] = tep_db_prepare_input($_POST['information_title'][$language_id]);
			if(isset($_POST['information_description']))	$sql_data_array['information_description'] = tep_db_prepare_input($_POST['information_description'][$language_id]);
            if(isset($_POST['information_url']))			$sql_data_array['information_url'] = tep_db_prepare_input($_POST['information_url']);
	        if(isset($_POST['information_target']))	        $sql_data_array['information_target'] = tep_db_prepare_input($_POST['information_target']);
	
			if(count($sql_data_array) > 0) {
				$sql_data_array['information_group_id'] = tep_db_prepare_input($gID);
				if (tep_db_perform(TABLE_INFORMATION, $sql_data_array, 'update', "information_id = '" . (int)$_POST['information_id'] . "' and language_id = '" . (int)$language_id . "'")){
                  $messageStack->add_session('Updated Successfully', 'success');
                }
			}
		}
      tep_redirect(tep_href_link(FILENAME_INFORMATION_MANAGER, 'gID=' . $gID));
	}else{
      $error="80";
    }
	break;
}
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/javascript/jquery-ui-1.8.2.custom.css">
<script type="text/javascript" src="includes/general.js"></script>
<!-- CKeditor -->
<script type="text/javascript" src="<?php echo DIR_WS_INCLUDES . 'javascript/ckeditor/ckeditor.js'?>"></script>
<!-- CKeditor End -->
</head>
<body onLoad="HTMLArea.replaceAll(config);">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top">
      <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
      <!-- left_navigation //-->
      <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
      <!-- left_navigation_eof //-->
      </table>
    </td>
    <!-- body_text //-->
    <td valign="top">
      <table width="100%">
        <tr>
          <td>
<?php
switch($_REQUEST['information_action']) {

case "Added":
	$data = tep_get_information_list();
	$no = 1;
	if (sizeof($data) > 0) {
		while (list($key, $val)=each($data)) {$no++; }
	}
	$title = ADD_QUEUE_INFORMATION . " #$no";
	echo tep_draw_form('added',FILENAME_INFORMATION_MANAGER, 'information_action=AddSure');
	echo tep_draw_hidden_field('gID', $gID);
	include('information_form.php');
	break;

  case "Edit":
	if ($information_id = $_REQUEST['information_id']) {
		$edit = tep_get_information_entry($information_id);
		$data = tep_get_information_list();
		$button = array("Update");
		$title = EDIT_ID_INFORMATION . " $information_id";
		echo tep_draw_form('edit', FILENAME_INFORMATION_MANAGER, 'information_action=Update');
		echo tep_draw_hidden_field('information_id', $information_id);
		echo tep_draw_hidden_field('gID', $_GET['gID']);
		include('information_form.php');
	}
	else {$error="80";}
	break;
  case 'Visible':
	if ($_GET['visible'] == '0') {
		$vivod=DEACTIVATION_ID_INFORMATION;
		tep_db_query("update " . TABLE_INFORMATION . " set visible = '0' where information_id = '" . (int)$_GET['information_id'] . "'");
	}
	else {
		$vivod=ACTIVATION_ID_INFORMATION;
		tep_db_query("update " . TABLE_INFORMATION . " set visible = '1' where information_id = '" . (int)$_GET['information_id'] . "'");
	}
	$data=tep_get_information_list();
	$title="$confirm $vivod $information_id " . SUCCED_INFORMATION . "";
	include('information_list.php');
	break;

  case "Delete":
	if ($information_id = $_GET['information_id']) {
		$delete = tep_get_information_entry($information_id);
		$data = tep_get_information_list();
		$title = DELETE_CONFIRMATION_ID_INFORMATION . " $information_id";
		echo '<tr class=pageHeading><td>' . $title . '</td></tr>';
		echo '<tr class=dataTableHeadingRow><td align=left class=dataTableHeadingContent>' . ENTRY_TITLE . '</td></tr>';
		echo '<tr><td class="dataTableContent" bgcolor="#DEE4E8" style="line-height: 18px;">' . $delete['information_title'] . '</td></tr>';
		echo '<tr><td></td></tr><tr><td align=right>';
		echo tep_draw_form('delete',FILENAME_INFORMATION_MANAGER, "information_action=DelSure&amp;information_id=" . $delete['information_id']);
		echo tep_draw_hidden_field('information_id', $information_id);
		echo tep_draw_hidden_field('gID', $gID);
		echo tep_image_submit('button_delete.gif', IMAGE_DELETE);
		echo '&nbsp;<a href="' . tep_href_link(FILENAME_INFORMATION_MANAGER, "gID=$gID", 'NONSSL') . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>';
		echo "</form></td></tr>";
	}
	else {$error="80";}
	break;


  case "DelSure":
	if ($information_id = $_GET['information_id']) {
		tep_db_query("delete from " . TABLE_INFORMATION . " where information_id = '" . (int)$information_id . "'");
		$data = tep_get_information_list();
		$title = "$confirm " . DELETED_ID_INFORMATION . " $information_id " . SUCCED_INFORMATION . "";
		include('information_list.php');
	}
	else {$error="80";}
	break;

  default:
	$data = tep_get_information_list();
	$title = MANAGER_INFORMATION . " <FONT SIZE='-1'>/ " . $info_group['information_group_title'] . "</FONT>";
	include('information_list.php');
	flush();
	tep_update_information_languages();
} // END SWITCH


if ($error) {
	$content=error_message($error);
	echo $content;
	$data = tep_get_information_list();
	$no = 1;
	if (sizeof($data) > 0) {
		while (list($key, $val)=each($data)) {$no++; }
	}
	$title = ADD_QUEUE_INFORMATION . " $no";
	echo tep_draw_form('addsure',FILENAME_INFORMATION_MANAGER, 'information_action=AddSure');
	echo tep_draw_hidden_field('gID', $gID);
	include('information_form.php');
}
?>

<?php if ($action = '') { ?> 
          </td>
        </tr>
<?php } ?>        

      </table>
    </td>
    <!-- body_text_eof //-->
  </tr>
</table>
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
</body>
</html>