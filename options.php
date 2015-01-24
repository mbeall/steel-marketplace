<?php
/**
 * Options pages for various modules
 *
 * @package Steel
 */

/*
 * Add options pages
 */
add_action('admin_menu', 'steel_marketplace_admin_menu', 20);
function steel_marketplace_admin_menu() {
  if (is_plugin_active('steel/steel.php')) {
    add_submenu_page( 'steel', 'Marketplace Options', 'Marketplace', 'manage_options', 'steel_marketplace', 'marketplace_submenu_page' );
  }
  else {
    add_submenu_page( 'edit.php?post_type=steel_product', 'Marketplace Options', 'Options', 'manage_options', 'steel_marketplace', 'marketplace_submenu_page' );
  }
}
function marketplace_submenu_page() {
  ?>
  <div class="wrap">
    <h2>Marketplace Options</h2>
    <form action="options.php" method="post">
      <?php
      settings_fields('marketplace_options');
      do_settings_sections('steel_marketplace');
      settings_errors();
      ?>
      <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
    </form>
  </div>
  <?php
}

/*
 * Register settings for options page
 */
add_action('admin_init', 'steel_marketplace_admin_init');
function steel_marketplace_admin_init(){
  $options = get_option('marketplace_options');
  $option_set1 = !empty($options['product_option_set_1_name']) ? $options['product_option_set_1_name'] : 'Option Set 1';
  $option_set2 = !empty($options['product_option_set_2_name']) ? $options['product_option_set_2_name'] : 'Option Set 2';

  //Register Marketplace Options
  register_setting('marketplace_options', 'marketplace_options', 'marketplace_options_validate' );

  add_settings_section('paypal', 'PayPal', 'paypal_section', 'steel_marketplace');
    add_settings_field('paypal_merch_id', 'Merchant ID', 'paypal_merch_id_field', 'steel_marketplace', 'paypal' );

  add_settings_section('product_details', 'Product Details', 'product_details_section', 'steel_marketplace');
    add_settings_field('product_ref'       , 'Reference Number'        , 'product_ref_field'       , 'steel_marketplace', 'product_details' );
    add_settings_field('product_price'     , 'Product Price'           , 'product_price_field'     , 'steel_marketplace', 'product_details' );
    add_settings_field('product_shipping'  , 'Additional shipping cost', 'product_shipping_field'  , 'steel_marketplace', 'product_details' );
    add_settings_field('product_dimensions', 'Dimensions'              , 'product_dimensions_field', 'steel_marketplace', 'product_details' );

  add_settings_section('product_options', 'Product Options', 'product_options_section', 'steel_marketplace');
    add_settings_field('product_option_set_1_name', 'Option Set 1 Name'      , 'product_option_set_1_name_field', 'steel_marketplace', 'product_options' );
    add_settings_field('product_option_set_1'     , $option_set1 . ' Options', 'product_option_set_1_field'     , 'steel_marketplace', 'product_options' );
    add_settings_field('product_option_set_2_name', 'Option Set 2 Name'      , 'product_option_set_2_name_field', 'steel_marketplace', 'product_options' );
    add_settings_field('product_option_set_2'     , $option_set2 . ' Options', 'product_option_set_2_field'     , 'steel_marketplace', 'product_options' );
}

/*
 * Callback settings for Marketplace Options page
 */
function paypal_section() { echo ''; }
function paypal_merch_id_field() {
  $options = get_option('marketplace_options');

  $output  = '<input id="paypal_merch_id" name="marketplace_options[paypal_merch_id]" size="40" type="text" value="';
  $output .= !empty($options["paypal_merch_id"]) ? $options["paypal_merch_id"] : '';
  $output .= '">';
  echo $output;
}
function product_details_section() { echo 'Select the details you would like to be able to define within the product administration screen'; }
function product_ref_field() {
  $options = get_option('marketplace_options');

  $details = !empty($options['product_ref']) ? $options['product_ref'] : 'true'; ?>

  <div class="radio-group">
    <label for="marketplace_options[product_ref]"><input name="marketplace_options[product_ref]" type="radio" value="true"  <?php checked( $details, 'true'  ) ?>>Show</label>
    <label for="marketplace_options[product_ref]"><input name="marketplace_options[product_ref]" type="radio" value="false" <?php checked( $details, 'false' ) ?>>Hide</label>
  </div>
  <?php
}
function product_price_field() {
  $options = get_option('marketplace_options');

  $details = !empty($options['product_price']) ? $options['product_price'] : 'true'; ?>

  <div class="radio-group">
    <label for="marketplace_options[product_price]"><input name="marketplace_options[product_price]" type="radio" value="true"  <?php checked( $details, 'true'  ) ?>>Show</label>
    <label for="marketplace_options[product_price]"><input name="marketplace_options[product_price]" type="radio" value="false" <?php checked( $details, 'false' ) ?>>Hide</label>
  </div>
  <?php
}
function product_shipping_field() {
  $options = get_option('marketplace_options');

  $details = !empty($options['product_shipping']) ? $options['product_shipping'] : 'true'; ?>

  <div class="radio-group">
    <label for="marketplace_options[product_shipping]"><input name="marketplace_options[product_shipping]" type="radio" value="true"  <?php checked( $details, 'true'  ) ?>>Show</label>
    <label for="marketplace_options[product_shipping]"><input name="marketplace_options[product_shipping]" type="radio" value="false" <?php checked( $details, 'false' ) ?>>Hide</label>
  </div>
  <?php
}
function product_dimensions_field() {
  $options = get_option('marketplace_options');

  $details = !empty($options['product_dimensions']) ? $options['product_dimensions'] : 'false'; ?>

  <div class="radio-group">
    <label for="marketplace_options[product_dimensions]"><input name="marketplace_options[product_dimensions]" type="radio" value="true"  <?php checked( $details, 'true'  ) ?>>Show</label>
    <label for="marketplace_options[product_dimensions]"><input name="marketplace_options[product_dimensions]" type="radio" value="false" <?php checked( $details, 'false' ) ?>>Hide</label>
  </div>
  <?php
}
function product_options_section() { echo 'Define global options for all products that have no cost variance'; }
function product_option_set_1_name_field() {
  $options = get_option('marketplace_options');

  $output  = '<label for="marketplace_options[product_option_set_1_name]">';
  $output .= '<input id="product_option_set_1_name" name="marketplace_options[product_option_set_1_name]" size="40" type="text" value="';
  $output .= !empty($options["product_option_set_1_name"]) ? $options["product_option_set_1_name"] : '';
  $output .= '">';
  $output .= ' i.e. Colors</label>';
  echo $output;
}
function product_option_set_1_field() {
  $options = get_option('marketplace_options');

  $output  = '<label for="marketplace_options[product_option_set_1]">';
  $output .= '<input id="product_option_set_1" name="marketplace_options[product_option_set_1]" size="40" type="text" value="';
  $output .= !empty($options["product_option_set_1"]) ? $options["product_option_set_1"] : '';
  $output .= '">';
  $output .= ' Seperate with commas</label>';
  echo $output;
}
function product_option_set_2_name_field() {
  $options = get_option('marketplace_options');

  $output  = '<label for="marketplace_options[product_option_set_2_name]">';
  $output .= '<input id="product_option_set_2_name" name="marketplace_options[product_option_set_2_name]" size="40" type="text" value="';
  $output .= !empty($options["product_option_set_2_name"]) ? $options["product_option_set_2_name"] : '';
  $output .= '">';
  $output .= ' i.e. Sizes</label>';
  echo $output;
}
function product_option_set_2_field() {
  $options = get_option('marketplace_options');

  $output  = '<label for="marketplace_options[product_option_set_2]">';
  $output .= '<input id="product_option_set_2" name="marketplace_options[product_option_set_2]" size="40" type="text" value="';
  $output .= !empty($options["product_option_set_2"]) ? $options["product_option_set_2"] : '';
  $output .= '">';
  $output .= ' Seperate with commas</label>';
  echo $output;
}

/*
 * Validate settings for Marketplace Options page
 */
function marketplace_options_validate($input) {
  global $newinput;

    $newinput['paypal_merch_id'] = trim($input['paypal_merch_id']);
    if(!preg_match('/^[a-z0-9]{13}$/i', $newinput['paypal_merch_id']) & !empty($newinput['paypal_merch_id'])) { add_settings_error( 'paypal_merch_id', 'invalid', 'Invalid PayPal Merchant ID. <span style="font-weight:normal;display:block;">A PayPal Merchant ID consists of 13 alphanumeric characters.</span>' ); }
    $newinput['paypal_merch_id'] = trim($input['paypal_merch_id']);

    $newinput['product_ref'          ] = trim($input['product_ref'          ]);
    $newinput['product_price'        ] = trim($input['product_price'        ]);
    $newinput['product_shipping'     ] = trim($input['product_shipping'     ]);
    $newinput['product_dimensions'   ] = trim($input['product_dimensions'   ]);
    $newinput['product_option_set_1_name'] = trim($input['product_option_set_1_name']);
    $newinput['product_option_set_1'     ] = trim($input['product_option_set_1'     ]);
    $newinput['product_option_set_2_name'] = trim($input['product_option_set_2_name']);
    $newinput['product_option_set_2'     ] = trim($input['product_option_set_2'     ]);

  return $newinput;
}

/*
 * Add function marketplace_options
 */
function marketplace_options( $key ) {
  $options = get_option('marketplace_options');
  if (empty($options[ $key ])) :
    return false;
  else :
    return $options[ $key ];
  endif;
}
?>
