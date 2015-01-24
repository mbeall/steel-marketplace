<?php
/**
 * Options pages for various modules
 *
 * @package Steel
 */

/*
 * Add options pages
 */
add_action('admin_menu', '_steel_marketplace_admin_menu', 20);
function _steel_marketplace_admin_menu() {
  if (is_plugin_active('steel/steel.php')) {
    add_submenu_page( 'steel', 'Marketplace Options', 'Marketplace', 'manage_options', '_steel_marketplace', '_steel_marketplace_submenu_page' );
  }
  else {
    add_submenu_page( 'edit.php?post_type=_steel_product', 'Marketplace Options', 'Options', 'manage_options', '_steel_marketplace', '_steel_marketplace_submenu_page' );
  }
}
function _steel_marketplace_submenu_page() {
  ?>
  <div class="wrap">
    <h2>Marketplace Options</h2>
    <form action="options.php" method="post">
      <?php
      settings_fields('steel_options');
      do_settings_sections('_steel_marketplace');
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
add_action('admin_init', '_steel_marketplace_admin_init');
function _steel_marketplace_admin_init(){
  $options = steel_get_options();

  add_settings_section('paypal', 'PayPal', '_steel_paypal_section', '_steel_marketplace');
    add_settings_field('paypal_merchant_id', 'Merchant ID', '_steel_paypal_merchant_id_field', '_steel_marketplace', 'paypal' );

  add_settings_section('_steel_product_details', 'Product Details', '_steel_product_details_section', '_steel_marketplace');
    add_settings_field('steel_product_id_type'       , 'Product ID'        , 'steel_product_id_type_field'       , '_steel_marketplace', '_steel_product_details' );
    add_settings_field('_steel_product_price'     , 'Product Price'           , '_steel_product_price_field'     , '_steel_marketplace', '_steel_product_details' );
    add_settings_field('_steel_product_dimensions', 'Dimensions'              , '_steel_product_dimensions_field', '_steel_marketplace', '_steel_product_details' );
}

/*
 * Callback settings for Marketplace Options page
 */
function _steel_paypal_section() { echo ''; }
function _steel_paypal_merchant_id_field() {
  $options = steel_get_options();

  $output  = '<input id="paypal_merchant_id" name="steel_options[paypal_merchant_id]" size="40" type="text" value="' . $options["paypal_merchant_id"] . '">';
  echo $output;
}
function _steel_product_details_section() { echo 'Select the details you would like to be able to define within the product administration screen'; }
function steel_product_id_type_field() {
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
function _steel_product_price_field() {
  $options = steel_get_options(); ?>

  <div class="radio-group">
    <label for="steel_options[edit_product_price]"><input name="steel_options[edit_product_price]" type="checkbox" value="true" <?php checked( $options['edit_product_price'], true  ) ?>>Base Price</label>
    <label for="steel_options[edit_product_shipping]"><input name="steel_options[edit_product_shipping]" type="checkbox" value="true" <?php checked( $options['edit_product_shipping'], true  ) ?>>Additional Shipping Cost</label>
  </div>
  <?php
}
function _steel_product_dimensions_field() {
  $options = steel_get_options(); ?>

  <div class="radio-group">
    <label for="steel_options[edit_product_width_height]"><input name="steel_options[edit_product_width_height]" type="checkbox" value="true" <?php checked( $options['edit_product_width_height'], true  ) ?>>Width x Height</label>
    <label for="steel_options[edit_product_depth]"><input name="steel_options[edit_product_depth]" type="checkbox" value="true" <?php checked( $options['edit_product_depth'], true  ) ?>>Depth</label>
  </div>
  <?php
}

/*
 * Validate settings for Marketplace Options page
 */
function steel_save_marketplace_options($raw, $valid) {

  $valid['paypal_merchant_id'] = trim($raw['paypal_merchant_id']);
  if(!preg_match('/^[a-z0-9]{13}$/i', $valid['paypal_merchant_id']) && !empty($valid['paypal_merchant_id'])) { add_settings_error( 'paypal_merchant_id', 'invalid', 'Invalid PayPal Merchant ID. <span style="font-weight:normal;display:block;">A PayPal Merchant ID consists of 13 alphanumeric characters.</span>' ); }
  $valid['paypal_merchant_id'] = trim($raw['paypal_merchant_id']);

  $valid['edit_product_id'] = isset($raw['edit_product_id']) ? true : false;

  $valid['edit_product_price'     ] = isset($raw['edit_product_price']) ? true : false;
  $valid['product_id_type'] = trim($raw['product_id_type']);

  $valid['edit_product_shipping'    ] = isset($raw['edit_product_shipping'    ]) ? true : false;
  $valid['edit_product_width_height'] = isset($raw['edit_product_width_height']) ? true : false;
  $valid['edit_product_depth'       ] = isset($raw['edit_product_depth'       ]) ? true : false;

  return $valid;
}
add_filter('steel_options_validate', 'steel_save_marketplace_options');

/*
 * Add function _steel_marketplace_options
 */
function _steel_marketplace_options( $key ) {
  $options = steel_get_options();
  if (empty($options[ $key ])) :
    return false;
  else :
    return $options[ $key ];
  endif;
}

function steel_marketplace_option_defaults( $steel_defaults ) {
  $marketplace_defaults = array(
    'paypal_merchant_id'        => '',
    'edit_product_id'   => true,
    'product_id_type'   => 'id',
    'edit_product_price'        => true,
    'edit_product_shipping'     => false,
    'edit_product_width_height' => false,
    'edit_product_depth'        => false,
  );

  //BEGIN - Backwards compatibility
  $options = get_option('marketplace_options');

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

  return array_merge( $steel_defaults, $marketplace_defaults );
}
add_filter('steel_option_defaults','steel_marketplace_option_defaults');
?>
