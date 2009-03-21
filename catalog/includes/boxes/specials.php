<?php
/*
$Id: specials.php 3 2006-05-27 04:59:07Z user $

  osCMax Power E-Commerce
  http://oscdox.com

  Copyright 2006 osCMax

  Released under the GNU General Public License
*/

// Most of this file is changed or moved to BTS - Basic Template System - format.

  if (isset($HTTP_GET_VARS['products_id'])){
  	
// BOF Separate Price per Customer
/*  if ($random_product = tep_random_select("select p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, p.products_image, s.specials_new_products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_SPECIALS . " s where p.products_status = '1' and p.products_id = s.products_id and pd.products_id = s.products_id and pd.language_id = '" . (int)$languages_id . "' and s.status = '1' order by s.specials_date_added desc limit " . MAX_RANDOM_SELECT_SPECIALS)) { */

//  global variable (session): $sppc_customers_group_id -> local variable $customer_group_id

  if(!tep_session_is_registered('sppc_customer_group_id')) { 
  $customer_group_id = '0';
  } else {
   $customer_group_id = $sppc_customer_group_id;
  }

  if ($customer_group_id == '0')  {
      $random_product = tep_random_select("select p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, p.products_image, s.specials_new_products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_SPECIALS . " s where p.products_status = '1' and p.products_id = s.products_id and pd.products_id = s.products_id and pd.language_id = '" . (int)$languages_id . "' and s.status = '1' and s.customers_group_id = '0' order by s.specials_date_added desc limit " . MAX_RANDOM_SELECT_SPECIALS);
  } else { // $sppc_customer_group_id is in the session variables, so must be set
      $random_product = tep_random_select("select p.products_id, pd.products_name, IF(pg.customers_group_price IS NOT NULL,pg.customers_group_price, p.products_price) as products_price, p.products_tax_class_id, p.products_image, s.specials_new_products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_SPECIALS . " s LEFT JOIN " . TABLE_PRODUCTS_GROUPS . " pg using (products_id, customers_group_id) where p.products_status = '1' and p.products_id = s.products_id and pd.products_id = s.products_id and pd.language_id = '" . (int)$languages_id . "' and s.status = '1' and s.customers_group_id= '".$customer_group_id."' order by s.specials_date_added desc limit " . MAX_RANDOM_SELECT_SPECIALS);
    }

  if (tep_not_null($random_product)) {
// EOF Separate Price per Customer	  
?>
<!-- specials //-->
<?php
  $boxHeading = BOX_HEADING_SPECIALS;
  $corner_left = 'square';
  $corner_right = 'square';
  $boxContent_attributes = ' align="center"';
  $boxLink = '<a href="' . tep_href_link(FILENAME_SPECIALS) . '"><img src="images/infobox/arrow_right.gif" border="0" alt="more" title=" more " width="12" height="10"></a>';
  $box_base_name = 'specials'; // for easy unique box template setup (added BTSv1.2)

  $box_id = $box_base_name . 'Box';  // for CSS styling paulm (editted BTSv1.2)
  $boxContent = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $random_product["products_id"]) . '">' . tep_image(DIR_WS_IMAGES . $random_product['products_image'], $random_product['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $random_product['products_id']) . '">' . $random_product['products_name'] . '</a><br><span style="text-decoration:line-through">' . $currencies->display_price($random_product['products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])) . '</span><br><span class="productSpecialPrice">' . $currencies->display_price($random_product['specials_new_products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])) . '</span>';





include (bts_select('boxes', $box_base_name)); // BTS 1.5
  $boxLink = '';
  $boxContent_attributes = '';
?>
<!-- specials_eof //-->
<?php
  }
 } 
?>
