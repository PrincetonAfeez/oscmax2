<?php
/* 
$Id$

  osCmax e-Commerce
  http://www.oscmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/

/*
  Open Featured Sets manufacturers listing module
*/
if (isset($featured_manufacturers_array)) {
if (sizeof($featured_manufacturers_array) <> '0') {  

  if ((FEATURED_MANUFACTURERS_SET_STYLE == '5') || (FEATURED_MANUFACTURERS_SET_STYLE == '6')) {
    $info_box_heading = array();
    $info_box_heading[] = array('text' => OPEN_FEATURED_BOX_MANUFACTURERS_HEADING);

    new infoBoxHeading($info_box_heading,true,true);
  }
  
  $info_box_contents = array();
  $info_box_contents[] = array('text' => '<table border="0" width="100%" cellspacing="0" cellpadding="0"><tr>'."\n" );
  
  $num_columns = (sizeof($featured_manufacturers_array)>(int)FEATURED_MANUFACTURERS_COLUMNS?FEATURED_MANUFACTURERS_COLUMNS:sizeof($featured_manufacturers_array));
  
  for($i=0, $col=1; $i<sizeof($featured_manufacturers_array); $i++, $col++) {  
    $info_box_contents[0]['text'] .= '<td valign="top" align="center" style="height: '.FEATURED_MANUFACTURERS_VLINE_IMAGE_HEIGHT.'px;">'."\n";
    $info_box_contents[0]['text'] .= '<table border="0" cellspacing="0" cellpadding="4" width="100%"><tr>'."\n";
    $info_box_contents[0]['text'] .= '<td><table border="0" cellspacing="0" cellpadding="'.FEATURED_MANUFACTURERS_CELLPADDING.'" width="100%"><tr><td align="center" valign="top" class="featuredManufacturers"><a href="' . tep_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $featured_manufacturers_array[$i]['id'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . MANUFACTURERS_IMAGES_DIR . $featured_manufacturers_array[$i]['image'], $featured_manufacturers_array[$i]['name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br><a href="' . tep_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $featured_manufacturers_array[$i]['id'], 'NONSSL') . '">' . $featured_manufacturers_array[$i]['name'] . '</a></td></tr></table></td>'."\n";
	if ( FEATURED_MANUFACTURERS_SET_STYLE == '3' && (($i+1) != sizeof($featured_manufacturers_array)) && (($col / $num_columns) != floor($col / $num_columns))) { $info_box_contents[0]['text'] .= '<td align="center" style="height: '.FEATURED_MANUFACTURERS_VLINE_IMAGE_HEIGHT.'px;"><div style="background-color: #'.FEATURED_MANUFACTURERS_LINE_COLOR.'; width: '.FEATURED_MANUFACTURERS_LINE_THICKNESS.'px; height: '.FEATURED_MANUFACTURERS_VLINE_IMAGE_HEIGHT.'px;">' . tep_draw_separator('pixel_trans.gif', FEATURED_MANUFACTURERS_LINE_THICKNESS, FEATURED_MANUFACTURERS_VLINE_IMAGE_HEIGHT) . '</div></td>'; }
	elseif (FEATURED_MANUFACTURERS_SET_STYLE == '3') { $info_box_contents[0]['text'] .= '<td align="center" style="height: '.FEATURED_MANUFACTURERS_VLINE_IMAGE_HEIGHT.'px;">' . tep_draw_separator('pixel_trans.gif', FEATURED_MANUFACTURERS_LINE_THICKNESS, '1') . '</td>'; }
    $info_box_contents[0]['text'] .= '</tr></table>'."\n".'</td>'."\n";
    if ((($col / $num_columns) == floor($col / $num_columns)) && (($i+1) != sizeof($featured_manufacturers_array))) {
      $info_box_contents[0]['text'] .= '</tr><tr><td colspan="' . ($num_columns+($num_columns-1)) . '" align="center">'.(FEATURED_MANUFACTURERS_SET_STYLE == '3'?'<div style="background-color: #'.FEATURED_MANUFACTURERS_LINE_COLOR.'; width: 100%; height: '.FEATURED_MANUFACTURERS_LINE_THICKNESS.'px;">' . tep_draw_separator('pixel_trans.gif', '1', FEATURED_MANUFACTURERS_LINE_THICKNESS) . '</div>':tep_draw_separator('pixel_trans.gif', '1', '1')).'</td></tr><tr>'; 
	  $col = 0; // column reset
    }
  } // closed 'for' loop
  
  while (($i/$num_columns) != floor($i/$num_columns)) {
    $info_box_contents[0]['text'] .= '<td style="height: 100%;">&nbsp;</td>'."\n";
	$i++;
  }
  
  $info_box_contents[0]['text'] .= '</tr></table>'."\n"; 
  
  if ((FEATURED_MANUFACTURERS_SET_STYLE == '1') || (FEATURED_MANUFACTURERS_SET_STYLE == '3')) {
    new contentBox($info_box_contents, true);
  } else {
    new contentBox($info_box_contents);
  }

  if ((FEATURED_MANUFACTURERS_SET_STYLE == '4') || (FEATURED_MANUFACTURERS_SET_STYLE == '6')) {
    //echo '<img src="images/info_box_' . FEATURED_SET_STYLE_SHADOW . '_shadow.gif" width="100%" height="13" alt="">';
  }

  echo tep_draw_separator('pixel_trans.gif', '100%', '10');

}
} // end if isset
?>