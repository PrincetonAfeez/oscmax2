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
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '4'); ?></td>
    </tr>
	<tr>
      <td class="productinfo_header">
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right">&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>
  	<tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>

<?php
  if (isset($_GET['email']) && ($_GET['email'] == 'nonexistent')) {
?>
	<tr>
      <td colspan="2" class="messageStackError"><?php echo TEXT_NO_EMAIL_ADDRESS_FOUND; ?></td>
    </tr>
<?php
  }
?>

    <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
    <tr>
      <td><?php echo tep_draw_form('password_forgotten', tep_href_link(FILENAME_AFFILIATE_PASSWORD_FORGOTTEN, 'action=process', $request_type)); ?><br>
        <table border="0" width="100%" cellspacing="0" cellpadding="3">
     	  <tr>
            <td align="right" class="main"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
            <td class="main"><?php echo tep_draw_input_field('email_address', '', 'maxlength="96"'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><br>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td valign="top"><?php echo '<a href="' . tep_href_link(FILENAME_AFFILIATE, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td>
                <td align="right" valign="top"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td>
              </tr>
            </table>
            </td>
          </tr>
        </table></form>
      </td>
    </tr>
</table>
