<?php
/*
$Id$

  osCmax e-Commerce
  http://www.oscmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
 <tr>
  <td width="49%" valign="top"><?php echo tep_draw_textarea_field('comments', '60', '6', $comments); ?></td>
  <td width="2%"></td>
  <td width="49%" valign="top"><div class="finalProducts"></div><br><div style="float:right" class="orderTotals"><?php echo (MODULE_ORDER_TOTAL_INSTALLED ? '<table cellpadding="2" cellspacing="0" border="0">' . $order_total_modules->output() . '</table>' : '');?></div></td>
 </tr>
</table>
