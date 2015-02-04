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
    add_submenu_page( 'steel', 'Marketplace Options', 'Marketplace', 'manage_options', 'steel_marketplace', 'steel_marketplace_submenu_page' );
  }
  else {
    add_submenu_page( 'edit.php?post_type=steel_product', 'Marketplace Options', 'Options', 'manage_options', 'steel_marketplace', 'steel_marketplace_submenu_page' );
  }
}
function steel_marketplace_submenu_page() {
  ?>
  <div class="wrap">
    <h2>Marketplace Options</h2>
    <form action="options.php" method="post">
      <?php
      settings_fields('steel_options');
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
  add_settings_section('paypal', 'PayPal', 'steel_marketplace_paypal_section', 'steel_marketplace');
    add_settings_field('paypal_merchant_id', 'Merchant ID', 'steel_marketplace_paypal_merchant_id_field', 'steel_marketplace', 'paypal' );

  add_settings_section('product_details', 'Product Details', 'steel_marketplace_product_details_section', 'steel_marketplace');
    add_settings_field('product_id'     , 'Product ID'     , 'steel_marketplace_product_id_field'   , 'steel_marketplace', 'product_details' );
    add_settings_field('product_price'  , 'Product Price'  , 'steel_marketplace_product_price_field', 'steel_marketplace', 'product_details' );
    add_settings_field('dimensions'     , 'Dimensions'     , 'steel_marketplace_dimensions_field'   , 'steel_marketplace', 'product_details' );

  add_settings_section('product_options', 'Product Options', 'steel_marketplace_product_options_section', 'steel_marketplace');
    add_settings_field('product_options', 'Product Options', 'steel_marketplace_product_options_field', 'steel_marketplace', 'product_options' );
}

/*
 * Callback settings for Marketplace Options page
 */
function steel_marketplace_paypal_section() { echo ''; }
function steel_marketplace_paypal_merchant_id_field() {
  $options = steel_get_options();

  $output  = '<input id="paypal_merchant_id" name="steel_options[paypal_merchant_id]" size="40" type="text" value="' . $options["paypal_merchant_id"] . '">';
  echo $output;
}
function steel_marketplace_product_details_section() { echo 'Select the details you would like to be able to define within the product administration screen'; }
function steel_marketplace_product_id_field() {
  $options = steel_get_options(); ?>

  <div class="radio-group">
    <label for="steel_options[edit_product_id]"><input name="steel_options[edit_product_id]" type="checkbox" value="true"  <?php checked( $options['edit_product_id'], true  ) ?>>Show</label>
    <select name="steel_options[product_id_type]">
    <?php
    $choices = array(
      'id'     => 'Product ID',
      'gtin13' => 'GTIN-13',
      'gtin14' => 'GTIN-14',
      'gtin8'  => 'GTIN-8',
      'mpn'    => 'MPN',
      'sku'    => 'SKU',
    );

    foreach ( $choices as $value => $label ) {
      echo '<option value="'.$value.'" '.selected($options['product_id_type'],$value).'>'.$label.'</option>';
    }
    ?>
    </select>
  </div>
  <?php
}
function steel_marketplace_product_price_field() {
  $options = steel_get_options(); ?>

  <div class="radio-group">
    <label for="steel_options[edit_product_price]"><input name="steel_options[edit_product_price]" type="checkbox" value="true" <?php checked( $options['edit_product_price'], true  ) ?>>Base Price</label>
    <label for="steel_options[edit_product_shipping]"><input name="steel_options[edit_product_shipping]" type="checkbox" value="true" <?php checked( $options['edit_product_shipping'], true  ) ?>>Additional Shipping Cost</label>
  </div>
  <?php
}
function steel_marketplace_dimensions_field() {
  $options = steel_get_options(); ?>

  <div class="radio-group">
    <label for="steel_options[edit_product_width_height]"><input name="steel_options[edit_product_width_height]" type="checkbox" value="true" <?php checked( $options['edit_product_width_height'], true  ) ?>>Width x Height</label>
    <label for="steel_options[edit_product_depth]"><input name="steel_options[edit_product_depth]" type="checkbox" value="true" <?php checked( $options['edit_product_depth'], true  ) ?>>Depth</label>
    <select name="steel_options[product_dimensions_units]">
    <?php
    $choices = array(
      'in' => 'inches',
      'ft' => 'feet',
      'mm' => 'millimeters',
      'cm' => 'centimeters',
      'm'  => 'meters',
    );

    foreach ( $choices as $value => $label ) {
      echo '<option value="'.$value.'" '.selected($options['product_dimensions_units'],$value).'>'.$label.'</option>';
    }
    ?>
    </select>
  </div>
  <?php
}

function steel_marketplace_product_options_section() { echo 'Select which options are available to the buyer. This will also create administration screens to add and edit available options on a global and individual product level.'; }
function steel_marketplace_product_options_field() {
  $options = steel_get_options(); ?>

  <div class="radio-group">
    <label for="steel_options[edit_product_colors]"><input name="steel_options[edit_product_colors]" type="checkbox" value="true" <?php checked( $options['edit_product_colors'], true  ) ?>>Color</label>
    <label for="steel_options[edit_product_sizes]"><input name="steel_options[edit_product_sizes]" type="checkbox" value="true" <?php checked( $options['edit_product_sizes'], true  ) ?>>Size</label>
  </div>
  <?php
}

/*
 * Validate settings for Marketplace Options page
 */
function steel_marketplace_save_options($valid, $raw) {
  if (isset($raw['product_id_type'])) {
    $valid = array();
    $valid = steel_get_options();

    $valid['paypal_merchant_id'] = trim($raw['paypal_merchant_id']);
    if(!preg_match('/^[a-z0-9]{13}$/i', $valid['paypal_merchant_id']) && !empty($valid['paypal_merchant_id'])) { add_settings_error( 'paypal_merchant_id', 'invalid', 'Invalid PayPal Merchant ID. <span style="font-weight:normal;display:block;">A PayPal Merchant ID consists of 13 alphanumeric characters.</span>' ); }
    $valid['paypal_merchant_id'] = trim($raw['paypal_merchant_id']);

    $valid['edit_product_id'] = $raw['edit_product_id'] == 'true' ? true : false;
    $valid['product_id_type'] = trim($raw['product_id_type']);

    $valid['edit_product_price'       ] = $raw['edit_product_price'       ] == 'true' ? true : false;
    $valid['edit_product_shipping'    ] = $raw['edit_product_shipping'    ] == 'true' ? true : false;
    $valid['edit_product_width_height'] = $raw['edit_product_width_height'] == 'true' ? true : false;
    $valid['edit_product_depth'       ] = $raw['edit_product_depth'       ] == 'true' ? true : false;
    $valid['product_dimensions_units' ] = trim($raw['product_dimensions_units']);

    $valid['edit_product_colors'] = $raw['edit_product_colors'] == 'true' ? true : false;
    $valid['edit_product_sizes' ] = $raw['edit_product_sizes' ] == 'true' ? true : false;
  }

  return $valid;
}
add_filter('steel_save_options', 'steel_marketplace_save_options', 10, 2);

function steel_marketplace_option_defaults( $steel_defaults ) {
  $defaults = array(
    'paypal_merchant_id'        => '',
    'edit_product_id'           => true,
    'product_id_type'           => 'id',
    'edit_product_price'        => true,
    'edit_product_shipping'     => false,
    'edit_product_width_height' => false,
    'edit_product_depth'        => false,
    'edit_product_colors'       => false,
    'edit_product_sizes'        => false,
  );

  //BEGIN - Backwards compatibility
  $options = get_option('marketplace_options');

  $defaults['paypal_merchant_id'] = $options['paypal_merch_id'];

  if (!empty($options['product_ref'])) {
    if ($options['product_ref'] == 'true')
      $defaults['edit_product_id'] = true;
    elseif ($options['product_ref'] == 'false')
      $defaults['edit_product_id'] = false;
  }

  if (!empty($options['product_price'])) {
    if ($options['product_price'] == 'true')
      $defaults['edit_product_price'] = true;
    elseif ($options['product_price'] == 'false')
      $defaults['edit_product_price'] = false;
  }

  if (!empty($options['product_shipping'])) {
    if ($options['product_shipping'] == 'true')
      $defaults['edit_product_shipping'] = true;
    elseif ($options['product_shipping'] == 'false')
      $defaults['edit_product_shipping'] = false;
  }

  if (!empty($options['product_dimensions'])) {
    if ($options['product_dimensions'] == 'true')
      $defaults['edit_product_width_height'] = true;
    elseif ($options['product_dimensions'] == 'false')
      $defaults['edit_product_width_height'] = false;
  }
  //END - Backwards compatibility

  return wp_parse_args( $steel_defaults, $defaults );
}
add_filter('steel_option_defaults','steel_marketplace_option_defaults');

/*
 * Add function marketplace_options
 * Deprecated. Use steel_get_options() instead.
 */
function marketplace_options( $key ) {
  $options = steel_get_options();
  return $options[$key];
}
