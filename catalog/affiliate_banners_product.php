<?php
/*
  $Id: affiliate_banner_product.php,v 2.00 2003/10/12

  OSC-Affiliate

  Contribution based on:

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 - 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('affiliate_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_AFFILIATE, '', 'SSL'));
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_AFFILIATE_BANNERS_PRODUCT);

  $location = ' &raquo; <a href="' . tep_href_link(FILENAME_AFFILIATE_BANNERS_PRODUCT, '', 'NONSSL') . '" class="headerNavigation">' . NAVBAR_TITLE . '</a>';

// $affiliate_values = tep_db_query("select * from " . TABLE_AFFILIATE . " where affiliate_id = '" . $affiliate_id . "'");

  $affiliate_banners_values = tep_db_query("select * from " . TABLE_AFFILIATE_BANNERS . " where affiliate_products_id >'0' order by affiliate_banners_title");
  $content = affiliate_banners_product; 
  include (bts_select('main', $content_template)); // BTSv1.5
  require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>