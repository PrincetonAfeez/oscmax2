<?php
/*
$Id$

  osCmax e-Commerce
  http://www.osCmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td height="14"><?php echo tep_image(bts_select('images', 'infobox/top_left.png')); ?></td>
    <td width="100%" height="14" class="infoBoxHeading"><?php echo HEADING_SEARCH_HELP; ?></td>
    <td height="14"><?php echo tep_image(bts_select('images', 'infobox/top_right.png')); ?></td>
  </tr>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="1" class="infoBox">
  <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="3" class="infoBoxContents">
  <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '1'); ?></td>
  </tr>
  <tr>
    <td class="boxText"><?php echo TEXT_SEARCH_HELP; ?></td>
  </tr>
  <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '1'); ?></td>
  </tr>
</table>
</td>
  </tr>
</table>
<p class="smallText" align="right"><a href="javascript:window.close()"><?php echo TEXT_CLOSE_WINDOW; ?></a></p>
