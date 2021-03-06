<?php
/*
$Id$

  osCmax e-Commerce
  http://www.osCmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/
  
      echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_PRODUCT_INFO, tep_get_all_get_params(array('action')) . 'action=add_product')); ?>
<?php echo tep_draw_hidden_field('products_id', (isset($_GET['products_id']) ? $_GET['products_id'] : '0')) . tep_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART, ' style="position: absolute; left: -1000px"'); // Bug fix: 378 ?>
<?php
  if ($product_check['total'] < 1) {
    // tell browsers that the page is a 404
	header( "HTTP/1.0 404 Not Found" ); 
    //  adapted for Separate Pricing Per Customer v4.2 2007/06/23, Hide products and categories from groups 2008/08/05
// BOF Separate Pricing Per Customer, Hide products and categories from groups
      $hide_product = true; // needed for column_right
// EOF Separate Pricing Per Customer, Hide products and categories from groups
   // BOF Separate Price per Customer
     if(!tep_session_is_registered('sppc_customer_group_id')) {
     $customer_group_id = '0';
     } else {
      $customer_group_id = $sppc_customer_group_id;
     }
   // EOF Separate Price per Customer
?>
	<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><?php new infoBox(array(array('text' => TEXT_PRODUCT_NOT_FOUND))); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
            <tr class="infoBoxContents">
              <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
<?php
  } else {
// BOF: Product Extra Fields	  
//    $product_info_query = tep_db_query("select p.products_id, pd.products_name, pd.products_description, pd.tab1, pd.tab2, pd.tab3, pd.tab4, pd.tab5, pd.tab6, p.products_model, p.products_quantity, p.products_image, pd.products_url, p.products_price, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$_GET['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "'");

	  $query = "select p.products_id, pd.products_name, pd.products_description, pd.tab1, pd.tab2, pd.tab3, pd.tab4, pd.tab5, pd.tab6, p.products_model, p.products_quantity, p.products_image, pd.products_url, p.products_msrp, p.products_price, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id, p.products_status";
	foreach ($epf as $e) {
      $query .= ", pd." . $e['field'];
    }  
	  $query .= " from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status > '0' and p.products_id = '" . (int)$_GET['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "'";

	$product_info_query = tep_db_query($query);
// EOF: Product Extra Fields	
	$product_info = tep_db_fetch_array($product_info_query);

    tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_viewed = products_viewed+1 where products_id = '" . (int)$_GET['products_id'] . "' and language_id = '" . (int)$languages_id . "'");

// BOF QPBPP for SPPC
    $pf->loadProduct((int)$_GET['products_id'], (int)$languages_id);
    $products_price = $pf->getPriceString();
	$hi_product_price = $pf->getHiPrice();
	$lo_product_price = $pf->getLowPrice();
// EOF QPBPP for SPPC

    //check if on special offer
	$new_price = tep_get_products_special_price($product_info['products_id']);

// BOF: MSRP
  if ($product_info['products_msrp'] != '' && $product_info['products_msrp'] != 0) { // If MSRP blank then hide everything
    if ($product_info['products_msrp'] > $product_info['products_price']) { // If MSRP > Price then show MSRP price 
	  //Draw MSRP table
	  $msrp_products_price = '<table class="productinfo_msrp" align="right" border="0" width="100%" cellspacing="0" cellpadding="0">';
      
      //Add MSRP
      $msrp_products_price .= '<tr class="PriceListBIG"><td align="right">' . TEXT_PRODUCTS_MSRP  . $currencies->display_price($product_info['products_msrp'], tep_get_tax_rate($product_info['products_tax_class_id'])) . '</td></tr>';
	
	  if ($new_price == '') { //Product not on special
	    $msrp_products_price .= '<tr class="pricenowBIG"><td align="right">' . TEXT_PRODUCTS_OUR_PRICE . $currencies->display_price($hi_product_price, tep_get_tax_rate($product_info['products_tax_class_id'])) . '</td></tr>';
		$msrp_products_price .= '<tr class="savingBIG"><td align="right" >' . TEXT_PRODUCTS_SAVINGS_RRP .  $currencies->display_price(($product_info['products_msrp'] -  $hi_product_price), tep_get_tax_rate($product_info['products_tax_class_id'])) . '&nbsp;('. number_format(100 - (($hi_product_price / $product_info['products_msrp']) * 100)) . '%)</td></tr>';
	  } else { //Product is on special
	    $msrp_products_price .= '<tr class="usualpriceBIG"><td align="right">' . TEXT_PRODUCTS_USUALPRICE .   $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . '</td></tr>';
	    $msrp_products_price .= '<tr class="pricenowBIG"><td align="right">' . TEXT_PRODUCTS_PRICENOW .  $currencies->display_price($new_price, tep_get_tax_rate($product_info['products_tax_class_id'])) . '</td></tr>';
	    $msrp_products_price .= '<tr class="savingBIG"><td align="right" >' . TEXT_PRODUCTS_SAVINGS_RRP .  $currencies->display_price(($product_info['products_msrp'] -  $new_price), tep_get_tax_rate($product_info['products_tax_class_id'])) . '&nbsp;('. number_format(100 - (($new_price / $product_info['products_msrp']) * 100)) . '%)</td></tr>';
	  }
	//Close table and add spacer
    $msrp_products_price .= '</table><table><tr><td>' . tep_draw_separator('pixel_trans.gif', '100%', '10') . '</td></tr></table>';
	} // end if MSRP if less than price
  } // end if MSRP is blank
// EOF: MSRP

// BOF QPBPP for SPPC
  $min_order_text = '';
  $min_order_qty = $pf->getMinOrderQty();
  $qtyBlocks = $pf->getQtyBlocks();
  // If vars are not set then set them to 1 to allow plus/minus to work.
  if ($min_order_qty == 0) { $min_order_qty = 1; }
  if ($qtyBlocks == 0) { $qtyBlocks = 1; }
  if ( ($min_order_qty > 1) || ($qtyBlocks > 1) ) {
    $min_order_text .= '<br>';
    if ($min_order_qty > 1) {
      $min_order_text .= '<span class="smallText">' . MINIMUM_ORDER_TEXT . $min_order_qty . '</span>&nbsp;';
    }  
    if ($qtyBlocks > 1) {
      $min_order_text .= '<span class="smallText">' . MINIMUM_QTY_TEXT . $qtyBlocks . '</span>';
    }
  }
// EOF QPBPP for SPPC
?> 
   <table width="100%" border="0" cellspacing="0" cellpadding="0">
     <tr>
        <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '4'); ?></td>
     </tr>
	<?php // BOF: Mod - Wishlist
	      //DISPLAY PRODUCT WAS ADDED TO WISHLIST IF WISHLIST REDIRECT IS ENABLED
      if(tep_session_is_registered('wishlist_id')) { ?>
      <tr>
        <td colspan="3" class="messageStackSuccess"><?php echo PRODUCT_ADDED_TO_WISHLIST; ?></td>
      </tr>
      <tr>
        <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
	<?php
      tep_session_unregister('wishlist_id');
      }
	      // EOF: Mod - Wishlist
		  
	  // Add message to request that product options are selected
	  if (isset($_GET['opts']) && $_GET['opts'] == 1) { ?>
	  <tr>
        <td colspan="3" class="messageStackAlert"><?php echo PRODUCT_HAS_OPTIONS_MESSAGE; ?></td>
      </tr>
      <tr>
        <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
	  <?php
      }
	  ?>
      
	  <tr>
        <td class="productinfo_header" colspan="3">
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
              <td class="pageHeading" valign="top" width="70%"><?php echo $product_info['products_name'] . $min_order_text; ?></td>
              <td class="pageHeading" align="right" valign="top">
              <?php 
              if ( ($hi_product_price == CALL_FOR_PRICE_VALUE) || ($lo_product_price == CALL_FOR_PRICE_VALUE) ){
                echo '<a href="' . tep_href_link(FILENAME_CONTACT_US, 'enquiry=' . TEXT_QUESTION_PRICE_ENQUIRY . '%0D%0A%0D%0A' . TEXT_QUESTION_MODEL . '%20' . str_replace(' ', '%20', $product_info['products_model']) . '%0D%0A' .  TEXT_QUESTION_PRODUCT_NAME . '%20' . str_replace(' ', '%20', $product_info['products_name']) . '%0D%0A' . TEXT_QUESTION_PRODUCT_ID . '%20' .  $product_info['products_id'] .'%0D%0A%0D%0A') . '">' . $products_price . '</a>';
              } elseif ($product_info['products_status'] == '2') {
                echo TEXT_PRODUCT_DISCONTINUED;
              } else { echo $products_price; }?>
              </td>  
            </tr>
          </table>
        </td>
	  </tr>
      <tr>
        <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>

      <tr>
        <td valign="top">
        <!-- Left Column Starts -->
        	<table width="<?php echo PRODUCT_IMAGE_WIDTH; ?>" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <!-- Image Big Starts -->
              	<td class="productinfo_imagebig" width="<?php echo PRODUCT_IMAGE_WIDTH; ?>">
                <?php			
				$image_lg = mopics_get_imagebase($product_info['products_image'], DIR_WS_IMAGES . DYNAMIC_MOPICS_BIGIMAGES_DIR);	
	            if ($lg_image_ext = mopics_file_exists($image_lg, DYNAMIC_MOPICS_BIG_IMAGE_TYPES)) {
        	        $image_size = @getimagesize($image_lg . '.' . $lg_image_ext);

            		//BOF SLIMBOX
            		$lightlarge = $image_lg . "." . $lg_image_ext;
					// Build Cloud Zoom rel to pass admin settings into a href
					$cloud_zoom_params = 'zoomWidth: \'' . CLOUDZOOM_WIDTH .  '\', zoomHeight: \'' . CLOUDZOOM_HEIGHT . '\', position: \'' . CLOUDZOOM_POSITION . '\', adjustX: ' . CLOUDZOOM_ADJUSTX . ', adjustY: ' . CLOUDZOOM_ADJUSTY . ', tint: ' . CLOUDZOOM_TINT . ', tintOpacity: ' . CLOUDZOOM_TINTOPACITY . ', lensOpacity: ' . CLOUDZOOM_LENSOPACITY . ', softFocus: ' . CLOUDZOOM_SOFTFOCUS . ', smoothMove: ' . CLOUDZOOM_SMOOTHMOVE . ', showTitle: ' . CLOUDZOOM_SHOWTITLE . ', titleOpacity: ' . CLOUDZOOM_TITLEOPACITY;
					?>         
                    <a href='<?php echo DIR_WS_IMAGES . DYNAMIC_MOPICS_BIGIMAGES_DIR . $product_info['products_image']; ?>' class = 'cloud-zoom' id='zoom1' rel="<?php echo $cloud_zoom_params; ?>">
                    <img src="<?php echo DIR_WS_IMAGES . DYNAMIC_MOPICS_PRODUCTS_DIR . $product_info['products_image']; ?>" alt='' title="<?php echo addslashes($product_info['products_name']); ?>" />
                    </a>
                    <?php
            	} else {
          			if (mopics_file_exists(DIR_WS_IMAGES . DYNAMIC_MOPICS_PRODUCTS_DIR . $product_info['products_image'])) {
					  echo tep_image(DIR_WS_IMAGES . DYNAMIC_MOPICS_PRODUCTS_DIR . $product_info['products_image'], stripslashes($product_info['products_name']), PRODUCT_IMAGE_WIDTH, PRODUCT_IMAGE_HEIGHT);
					} else {
					  echo tep_image(DIR_WS_ICONS . 'default_lg.png', TEXT_MISSING_IMAGE, PRODUCT_IMAGE_WIDTH, PRODUCT_IMAGE_HEIGHT);
					}
            	}
				?>
                </td>
                <!-- Image Big Ends -->
              </tr>
              <tr>
        		<td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      		  </tr>
              <tr>
                <!-- Thumbnails Starts -->
              	<td width="<?php echo PRODUCT_IMAGE_WIDTH; ?>" align="center">
					<?php
        			//// BEGIN:  Added for Dynamic MoPics v3.000
 					if (is_file(DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $product_info['products_image']) && DIR_WS_IMAGES . DYNAMIC_MOPICS_THUMBS_DIR . $product_info['products_image'] != "pixel_trans.gif") {
                     	include(DIR_WS_MODULES . 'dynamic_mopics.php');
       				}
        			//// END:  Added for Dynamic MoPics v3.000
					?>
                </td>
                <!-- Thumbnails Ends -->
              </tr>
            
            <!-- Conditional AddThis Starts -->
            <?php if (ADD_THIS_ENABLED == 'true') { ?>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
              <tr>
                <td class="productinfo_boxes"><?php echo stripslashes(ADD_THIS_CODE); ?></td>      
              </tr>
            <?php } ?>
            <!-- Conditional AddThis Starts -->
            
              
            <!-- Conditional URL Starts -->
            <?php if (tep_not_null($product_info['products_url'])) { ?>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
              <tr>
                <td class="productinfo_boxes"><?php echo sprintf(TEXT_MORE_INFORMATION, tep_href_link(FILENAME_REDIRECT, 'action=url&goto=' . urlencode($product_info['products_url']), 'NONSSL', true, false)); ?></td>
              </tr>
            <?php } ?>
            <!-- Conditional URL Starts -->
            
            </table>
        <!-- Left Column Ends -->
        </td>
                
        <!-- Central Spacer Start -->
        <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '10'); ?></td>
        <!-- Central Spacer End -->
        
        <td valign="top">
        
        <!-- Right Column Starts -->
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">

              <?php if (isset($msrp_products_price)) { ?>
			  <!-- MSRP Starts -->
              <tr>
                <td valign="top">
                  <?php echo $msrp_products_price; ?>
                </td>
              </tr>
              <!-- MSRP Ends -->  
              <?php } ?>
              <tr>
                <!-- Description Starts -->
              	<td valign="top">
                <?php
                    // BOF: Tabs by PGM
                    if(USE_PRODUCT_DESCRIPTION_TABS != 'True') {
                      echo stripslashes($product_info['products_description']);
                    } else {
                      include(DIR_WS_MODULES . 'product_tabs.php'); }
                    // EOF: Tabs by PGM
					
					// start indvship
					$extra_shipping_query = tep_db_query("select products_ship_price, products_ship_price_two from " . TABLE_PRODUCTS_SHIPPING . " where products_id = '" . (int)$_GET['products_id'] . "'");
					if (tep_db_num_rows($extra_shipping_query)) {
					  $extra_shipping = tep_db_fetch_array($extra_shipping_query);
					  if($extra_shipping['products_ship_price'] == '0.00'){
						echo TEXT_FREE_SHIPPING;
					  } else {
						echo TEXT_ADDITIONAL_SHIPPING . $extra_shipping['products_ship_price'];
						if (($extra_shipping['products_ship_price_two']) > 0) {
						  echo TEXT_ADDITIONAL_SHIPPING_2 . $extra_shipping['products_ship_price_two'] . TEXT_ADDITIONAL_SHIPPING_3;
						} else {
						  echo TEXT_ADDITIONAL_SHIPPING_4;
						}
					  }
					}
					// end indvship		    
                ?>
                </td>
                <!-- Description Ends -->
              </tr>
              <!-- Product Extra Fields Starts -->
			  <?php
                // begin Extra Product Fields
                    foreach ($epf as $e) {
                      $mt = ($e['uses_list'] && !$e['multi_select'] ? ($product_info[$e['field']] == 0) : !tep_not_null($product_info[$e['field']]));
					  if (!$mt) { // only display if information is set for product
					    echo '<tr>';
						echo '  <td colspan="3">' . tep_draw_separator('pixel_trans.gif', '100%', '10') . '</td>';
						echo '</tr>';
						echo '<tr>';
              		    echo '  <td class="productinfo_epf">';
                        echo '<b>' . $e['label'] . ': </b>';
                        if ($e['uses_list']) {
                          if ($e['multi_select']) {
                            $values = explode('|', trim($product_info[$e['field']], '|'));
                            $listing = array();
                            foreach ($values as $val) {
                              $listing[] = tep_get_extra_field_list_value($val, $e['show_chain'], $e['display_type']);
                            }
                            echo implode(', ', $listing);
                          } else {
                            echo tep_get_extra_field_list_value($product_info[$e['field']], $e['show_chain'], $e['display_type']);
                          }
                        } else {
                          echo $product_info[$e['field']];
                        }
                        echo '<br>';
						echo '  </td>';
						echo '</tr>';
                      }
                    } 
			    // end Extra Product Field
              ?>
              <!-- Product Extra Fields Ends -->
            
			<!-- Conditional Atrributes Starts -->              
            <?php
		$products_attributes_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$_GET['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int)$languages_id . "'");
    		$products_attributes = tep_db_fetch_array($products_attributes_query);
    
			if ($products_attributes['total'] > 0) { ?>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
              <tr>
                <td class="productinfo_boxes">
                <?php
				//++++ QT Pro: Begin Changed code
				$products_id = (preg_match("/^\d{1,10}(\{\d{1,10}\}\d{1,10})*$/",$_GET['products_id']) ? $_GET['products_id'] : (int)$_GET['products_id']);
				require(DIR_WS_CLASSES . 'pad_' . PRODINFO_ATTRIBUTE_PLUGIN . '.php');
				$class = 'pad_' . PRODINFO_ATTRIBUTE_PLUGIN;
				$pad = new $class($products_id);
				echo $pad->draw(); ?>
                </td>
              </tr>
            <?php } // end if ?>
				
			<!-- Conditional Stock Table Starts -->
            <?php
			if (PRODINFO_ATTRIBUTE_DISPLAY_STOCK_LIST == 'true') { ?>
              <?php include(DIR_WS_MODULES . "qtpro_stock_table.php"); ?>
			<?php } ?>
			<!-- Conditional Stock Table Ends -->
            	
            <!-- Conditional Ask a Question Starts -->
			<?php
            if (SHOW_ASK_A_QUESTION == 'True' && $product_info['products_status'] != '2') { ?>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
              <tr>
                <td class="productinfo_boxes" align="center"><?php echo '<a href="' . tep_href_link(FILENAME_CONTACT_US, 'enquiry=' . TEXT_QUESTION_ABOUT . str_replace(' ', '%20', $product_info['products_name']) . '%0D%0A' . TEXT_QUESTION_MODEL . '%20' . str_replace(' ', '%20', $product_info['products_model']) . '%0D%0A' . TEXT_QUESTION_PRODUCT_ID . '%20' . $product_info['products_id'] .'%0D%0A%0D%0A' . TEXT_QUESTION_TYPE . '%0D%0A%0D%0A') . '">' . tep_image_button('button_aaq.gif', IMAGE_BUTTON_AAQ) . '</a>'; ?></td>
              </tr>
            <?php } //end if ?>            
            <!-- Conditional Ask a Question Ends -->
            
          </table>        
        <?php echo tep_draw_separator('pixel_trans.gif', '325', '1'); ?>
        <!-- Right Column Ends -->
        </td>
      </tr>

      <tr>
        <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
    
      <!-- Buttons Starts -->
      <tr>
        <td colspan="3" class="productinfo_buttons">
          <table border="0" width="100%" cellspacing="1" cellpadding="2">
            <tr>
              <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
					<?php
                    if (PRODUCT_REVIEWS_ENABLE == 'True') {
                      $reviews_query = tep_db_query("select count(*) as count from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd where r.approved = '1' and r.products_id = '" . (int)$_GET['products_id'] . "' and rd.languages_id = '" . (int)$languages_id . "' and r.reviews_id = rd.reviews_id");
                      $reviews = tep_db_fetch_array($reviews_query);
					  if ($reviews['count'] > 0) {
					    echo '<td class="main" align="left"><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params()) . '">' . tep_image_button('button_reviews.gif', IMAGE_BUTTON_REVIEWS) . '</a></td>'; 
					  } else {
						echo '<td class="main" align="left"><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, tep_get_all_get_params()) . '">' . tep_image_button('button_write_review.gif', IMAGE_BUTTON_WRITE_REVIEW) . '</a></td>';
					  }
					} ?>
                	<!-- Wish List 3.5 Start -->
                	<td align="left"><?php if ($product_info['products_status'] != '2') echo tep_image_submit('button_wishlist.gif', IMAGE_BUTTON_WISHLIST, 'name="wishlist" value="wishlist"'); ?></td>
 	                <!-- Wish List 3.5 End   -->
       	            <!-- ADDED PLUS AND MINUS BUTTONS PGM -->
                    <td class="main" align="right" width="250">
                      <table width="200" border="0" cellpadding="0">
                        <tr>
                        <?php // START EASY CALL FOR PRICE v1.4
                            if ( ($hi_product_price == CALL_FOR_PRICE_VALUE) || ($lo_product_price == CALL_FOR_PRICE_VALUE) ){ ?>
						    <td class="main" align="right" rowspan="2"><?php echo '<a href="' . tep_href_link(FILENAME_CONTACT_US, 'enquiry=' . TEXT_QUESTION_PRICE_ENQUIRY . '%0D%0A%0D%0A' . TEXT_QUESTION_MODEL . '%20' . str_replace(' ', '%20', $product_info['products_model']) . '%0D%0A' .  TEXT_QUESTION_PRODUCT_NAME . '%20' . str_replace(' ', '%20', $product_info['products_name']) . '%0D%0A' . TEXT_QUESTION_PRODUCT_ID . '%20' .  $product_info['products_id'] .'%0D%0A%0D%0A') . '">' . tep_image_button('button_cfp.gif', IMAGE_BUTTON_CFP); ?></a></td>
                      <?php } elseif ($product_info['products_status'] == '2') {
                            echo '<a href="' . tep_href_link(FILENAME_CONTACT_US, 'enquiry=' . TEXT_QUESTION_ABOUT . str_replace(' ', '%20', $product_info['products_name']) . '%0D%0A' . TEXT_QUESTION_MODEL . '%20' . str_replace(' ', '%20', $product_info['products_model']) . '%0D%0A' . TEXT_QUESTION_PRODUCT_ID . '%20' . $product_info['products_id'] .'%0D%0A%0D%0A' . TEXT_QUESTION_TYPE . '%0D%0A%0D%0A') . '">' . tep_image_button('button_aaq.gif', IMAGE_BUTTON_AAQ) . '</a>';
                            } else { ?>  
                       	  	<td align="center"><img src="<?php echo DIR_WS_ICONS . 'plus.png' ?>" onclick="TextBox_AddToIntValue('product-quantity-product.id', +<?php echo $qtyBlocks; ?>, <?php echo $min_order_qty; ?>)" alt="<?php echo $qtyBlocks; ?>" class="plusminus"></td>
                          	<td rowspan="2" align="center"><?php echo tep_draw_input_field('cart_quantity', max($qtyBlocks, $min_order_qty, 1), 'size="2" id="product-quantity-product.id" '); ?></td>
                            <?php // START: PGM Edit to switch Add to Cart image if stock = 0
							  if ($product_info['products_quantity'] == 0 && STOCK_IMAGE_SWITCH == 'true') { ?>
							    <td class="main" align="right" rowspan="2"><?php echo tep_draw_hidden_field('products_id', $product_info['products_id']) . tep_image_submit('button_out_of_stock.gif', IMAGE_OUT_OF_STOCK); ?></td>
						<?php } else { 
							      if (BASKET_CART == 'cart') { ?>
                                    <td class="main" align="right" rowspan="2"><?php echo tep_draw_hidden_field('products_id', $product_info['products_id']) . tep_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART); ?></td>
                            <?php } else { ?>
								    <td class="main" align="right" rowspan="2"><?php echo tep_draw_hidden_field('products_id', $product_info['products_id']) . tep_image_submit('button_in_basket.gif', IMAGE_BUTTON_IN_BASKET); ?></td>
							<?php } 
							  } // END: PGM Edit to switch Add to Cart image if stock = 0 ?>
                        </tr>
                        <tr>
                          <td align="center"><img src="<?php echo DIR_WS_ICONS . 'minus.png' ?>" onclick="TextBox_AddToIntValue('product-quantity-product.id',-<?php echo $qtyBlocks; ?>, <?php echo $min_order_qty; ?>)" alt="<?php echo $qtyBlocks; ?>" class="plusminus"></td>
                        <?php } // END: EASY CALL FOR PRICE v1.4 ?>
                        </tr>
                      </table>
                    </td>
                    <!-- ADDED PLUS AND MINUS BUTTONS PGM -->
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <!-- Buttons Ends -->
      
      <!-- Review & Date Added Starts -->
      <tr>
        <td colspan="3">
          <table width="100%">
            <tr>
              <td class="smallText" align="left" width="20%">
              <?php
			  if (PRODUCT_REVIEWS_ENABLE == 'True') {
                  if ($reviews['count'] > 0) {
                    echo TEXT_CURRENT_REVIEWS . ' ' . $reviews['count'];
                  }
			  }
			  ?>	  
              </td>
              <td align="center" class="smallText" width="60%">
              <?php 
			  if (PRODUCT_LIST_DATE_ADDED == 'true') {
			    if ($product_info['products_date_available'] > date('Y-m-d H:i:s')) {
                  echo sprintf(TEXT_DATE_AVAILABLE, tep_date_short($product_info['products_date_available']));
                } else {
                  echo sprintf(TEXT_DATE_ADDED, tep_date_short($product_info['products_date_added']));
                } 
              } ?>
              </td>
              <td align="right" width="20%"></td>
            </tr>
          </table>
        </td>
      </tr>
      <!-- Review & Date Added Ends -->
      
      <tr>
        <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      
      <!-- Page Module Controller Starts -->
      <tr>
        <td colspan="3" align="center">
      	  <?php include (DIR_WS_MODULES . FILENAME_PI_PAGE_MODULES); ?>
        </td>
      </tr>
      <!-- Page Module Controller Ends -->
  </table>
  
<?php
  } // end if wrapping entire code
?>
</form>