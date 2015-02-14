<?php
/*
Plugin Name: Steel Marketplace
Plugin URI: https://github.com/starverte/steel-marketplace.git
Description: A plugin that is part of the Sparks Framework. Extends Steel and creates an easy ecommerce development framework.
Version: 0.2.0 (Steel 1.2.0)
Author: Star Verte LLC
Author URI: http://starverte.com/
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/

  Copyright 2014-2015 Star Verte LLC (email : info@starverte.com)

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

function steel_marketplace_activate() {
  $dir = plugin_dir_path( __FILE__ );
  if (is_plugin_active( 'steel/steel.php' )) {
    $steel_data = get_plugin_data($dir.'/../steel/steel.php');
    if ($steel_data['Version'] < 1.2) {
      deactivate_plugins( plugin_basename( __FILE__ ) );
      wp_die( 'Please update Steel to version 1.2.0 or later before activating Steel Marketplace.' );
    }
  }
  else {
    deactivate_plugins( plugin_basename( __FILE__ ) );
    wp_die( 'Steel Marketplace requires Steel version 1.2.0 or later.' );
  }
}
register_activation_hook( __FILE__, 'steel_marketplace_activate' );

include_once dirname( __FILE__ ) . '/options.php';
include_once dirname( __FILE__ ) . '/variations.php';

/**
 * Load scripts
 */
add_action( 'admin_enqueue_scripts', 'steel_marketplace_admin_scripts' );
function steel_marketplace_admin_scripts() {
  wp_enqueue_style( 'steel-marketplace-admin-style', plugins_url('steel-marketplace/css/admin.css') );
  wp_enqueue_style( 'dashicons'                                                                     );

  wp_enqueue_script( 'jquery'              );
  wp_enqueue_script( 'jquery-ui-core'      );
  wp_enqueue_script( 'jquery-ui-sortable'  );
  wp_enqueue_script( 'jquery-ui-position'  );
  wp_enqueue_script( 'jquery-effects-core' );
  wp_enqueue_script( 'jquery-effects-blind');

  wp_enqueue_script( 'marketplace', plugins_url('steel-marketplace/js/marketplace.js'  ), array('jquery'), '0.2.0', true );

  wp_enqueue_media();
}
add_action( 'wp_enqueue_scripts', 'steel_marketplace_scripts' );
function steel_marketplace_scripts() {
  wp_enqueue_style ( 'marketplace-style', plugins_url('steel-marketplace/css/marketplace.css'  ), array(), '0.2.0');
}

add_action( 'init', 'steel_marketplace_init', 0 );
function steel_marketplace_init() {
  $options = steel_get_options();

  $labels = array(
    'name'                => _x( 'Products', 'Post Type General Name', 'steel' ),
    'singular_name'       => _x( 'Product', 'Post Type Singular Name', 'steel' ),
    'menu_name'           => __( 'Marketplace', 'steel' ),
    'all_items'           => __( 'All Products', 'steel' ),
    'view_item'           => __( 'View Product', 'steel' ),
    'add_new_item'        => __( 'Add New Product', 'steel' ),
    'add_new'             => __( 'New Product', 'steel' ),
    'edit_item'           => __( 'Edit Product', 'steel' ),
    'update_item'         => __( 'Update Product', 'steel' ),
    'search_items'        => __( 'Search products', 'steel' ),
    'not_found'           => __( 'No products found', 'steel' ),
    'not_found_in_trash'  => __( 'No products found in trash. Did you check recycling?', 'steel' ),
  );

  $rewrite = array(
    'slug'                => 'products',
    'with_front'          => true,
    'pages'               => false,
    'feeds'               => false,
  );

  $args = array(
    'label'               => __( 'steel_product', 'steel' ),
    'description'         => __( 'Products in a Marketplace', 'steel' ),
    'labels'              => $labels,
    'supports'            => array( 'title', 'editor', ),
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 5,
    'menu_icon'           => 'dashicons-cart',
    'can_export'          => true,
    'has_archive'         => true,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'rewrite'             => $rewrite,
    'capability_type'     => 'page',
  );

  register_post_type( 'steel_product', $args );

  $labels2 = array(
    'name'                       => _x( 'Product Categories', 'Taxonomy General Name', 'steel' ),
    'singular_name'              => _x( 'Product Category', 'Taxonomy Singular Name', 'steel' ),
    'menu_name'                  => __( 'Product Categories', 'steel' ),
    'all_items'                  => __( 'All Product Categories', 'steel' ),
    'parent_item'                => __( '', 'steel' ),
    'parent_item_colon'          => __( '', 'steel' ),
    'new_item_name'              => __( 'New Product Category Name', 'steel' ),
    'add_new_item'               => __( 'Add New Product Category', 'steel' ),
    'edit_item'                  => __( 'Edit Product Category', 'steel' ),
    'update_item'                => __( 'Update Product Category', 'steel' ),
    'separate_items_with_commas' => __( 'Separate categories with commas', 'steel' ),
    'search_items'               => __( 'Search categories', 'steel' ),
    'add_or_remove_items'        => __( 'Add or remove categories', 'steel' ),
    'choose_from_most_used'      => __( 'Choose from the most used categories', 'steel' ),
  );

  $rewrite2 = array(
    'slug'                       => 'browse',
    'with_front'                 => true,
    'hierarchical'               => true,
  );

  $args2 = array(
    'labels'                     => $labels2,
    'hierarchical'               => true,
    'public'                     => true,
    'show_ui'                    => true,
    'show_admin_column'          => true,
    'show_in_nav_menus'          => true,
    'show_tagcloud'              => false,
    'rewrite'                    => $rewrite2,
  );

  register_taxonomy( 'steel_product_category', 'steel_product', $args2 );
  register_taxonomy_for_object_type( 'post_tag', 'steel_product' );

  register_taxonomy(
    'steel_product_manufacturer',
    array( 'steel_product' ),
    array(
      'labels'            => array(
        'name'                => _x( 'Manufacturers', 'Taxonomy General Name', 'steel' ),
        'singular_name'       => _x( 'Manufacturer', 'Taxonomy Singular Name', 'steel' ),
        'menu_name'           => __( 'Manufacturers', 'steel' ),
        'all_items'           => __( 'All Manufacturers', 'steel' ),
        'parent_item'         => __( 'Parent Manufacturer', 'steel' ),
        'parent_item_colon'   => __( 'Parent Manufacturer:', 'steel' ),
        'new_item_name'       => __( 'New Manufacturer Name', 'steel' ),
        'add_new_item'        => __( 'Add New Manufacturer', 'steel' ),
        'edit_item'           => __( 'Edit Manufacturer', 'steel' ),
        'update_item'         => __( 'Update Manufacturer', 'steel' ),
        'search_items'        => __( 'Search manufacturers', 'steel' ),
        'add_or_remove_items' => __( 'Add or remove manufacturers', 'steel' ),
        'not_found'           => __( 'No manufacturers found', 'steel' ),
      ),
      'public'            => $options['edit_product_manufacturer'],
      'show_in_nav_menus' => false,
      'show_tagcloud'     => false,
      'meta_box_cb'       => false,
      'hierarchical'      => true,
    )
  );

  add_image_size( 'steel-product'           , 580, 360, true );
  add_image_size( 'steel-product-thumb'     , 150,  95, true );
  add_image_size( 'steel-product-view-thumb', 250, 155, true );
}

/*
 * Create custom meta boxes
 */
add_action( 'add_meta_boxes', 'steel_product_meta_boxes' );
function steel_product_meta_boxes() {
  add_meta_box('steel_product_details_meta', 'Product Details', 'steel_product_details_meta', 'steel_product', 'side');
  add_meta_box('steel_product_view_meta', 'Product Views', 'steel_product_view_meta', 'steel_product', 'side', 'high');
}
function steel_product_details_meta() {
  $options = steel_get_options();
  $details = steel_get_product_meta();

  if ($options['edit_product_id']) {?>
    <p class="product_id"><span class="form-addon-left"><?php echo strtoupper($options['product_id_type']); ?></span><input type="text" size="18" name="product_id" placeholder="Product ID" value="<?php echo $details['product_id'][0]; ?>" /></p>
  <?php
  }
  if ($options['edit_product_id_alt']) {?>
    <p class="product_id_alt"><span class="form-addon-left"><?php echo strtoupper($options['product_id_alt_type']); ?></span><input type="text" size="16" name="product_id_alt" placeholder="Alternate Product ID" value="<?php echo $details['product_id_alt'][0]; ?>" /></p>
  <?php
  }
  if ($options['edit_product_price']) {?>
    <p class="product_price">
      <label>Base price</label><br />
      <span class="form-addon-left">$</span><input type="text" size="21" name="product_price" placeholder="Price" value="<?php echo $details['product_price'][0]; ?>" />
    </p>
  <?php
  }
  if ($options['edit_product_shipping']) {?>
    <p class="product_shipping">
      <label>Additional shipping cost</label><br />
      <span class="form-addon-left">$</span><input type="text" size="21" name="product_shipping" placeholder="Shipping" value="<?php echo $details['product_shipping'][0]; ?>" />
    </p>
  <?php
  }
  if ($options['edit_product_width_height']) {?>
    <p class="product_dimensions">
      <label>Dimensions (<?php echo $options['product_dimensions_units']; ?>)</label><br />
      <input type="text" size="5" name="product_width" placeholder="Width" value="<?php echo $details['product_width'][0]; ?>" /> x
      <input type="text" size="5" name="product_height" placeholder="Height" value="<?php echo $details['product_height'][0]; ?>" />
      <?php if ($options['edit_product_depth']) { ?>x
        <input type="text" size="5" name="product_depth" placeholder="Depth" value="<?php echo $details['product_depth'][0]; ?>" />
      <?php } ?>
    </p>
  <?php
  }
  if ($options['edit_product_weight']) {?>
    <p class="product_weight">
      <label>Weight (<?php echo $options['product_weight_units']; ?>)</label><br />
      <input type="text" size="21" name="product_weight" placeholder="Weight" value="<?php echo $details['product_weight'][0]; ?>" />
    </p>
  <?php
  }
  if ($options['edit_product_color']) {?>
    <p class="product_color">
      <label>Color</label><br />
      <input type="text" size="21" name="product_color" placeholder="i.e. Green" value="<?php echo $details['product_color'][0]; ?>" />
    </p>
  <?php
  }
  if ($options['edit_product_manufacturer']) {?>
    <p class="product_manufacturer">
      <label>Manufacturer</label><br />
      <select name="product_manufacturer">
        <option>Select</option>
        <?php
          $choices = get_terms('steel_product_manufacturer',array('hide_empty'=>false,'fields'=>'id=>name'));

          foreach ( $choices as $value => $label ) {
            echo '<option value="'.$value.'" '.selected(has_term($value, 'steel_product_manufacturer'),true).'>'.$label.'</option>';
          }
        ?>
      </select>
    </p>
  <?php
  }
  if ($options['edit_product_warranty']) {?>
    <p class="product_warranty">
      <label>Warranty</label><br />
      <input type="text" size="5" name="product_warranty_num" placeholder="#" value="<?php echo $details['product_warranty_num'][0]; ?>" />
      <select name="product_warranty_period">
        <?php
          $choices = array(
            'd' => 'day(s)',
            'W' => 'week(s)',
            'm' => 'month(s)',
            'y' => 'year(s)',
          );

          foreach ( $choices as $value => $label ) {
            echo '<option value="'.$value.'" '.selected($details['product_warranty_period'][0],$value).'>'.$label.'</option>';
          }
        ?>
      </select>
    </p>
  <?php
  }
  do_action('steel_product_details');
}
function steel_product_view_meta() {
  global $post;
  $details = steel_get_product_meta();
  $product_view_order = $details['product_view_order'][0];

  //Backwards compatibility for Sparks Store
  if (has_post_thumbnail()) {
    $thumb_id = get_post_thumbnail_id();
    $product_view_order .= ','.$thumb_id;
    update_post_meta($post->ID, 'product_view_order'   , $product_view_order);
    delete_post_meta($post->ID, '_thumbnail_id');
  }

  $product_views = explode(',', $product_view_order);

  $output = '';
  $output .= '<a href="#" class="button add_product_view_media" id="btn_above" title="Add product_view to product_viewhow"><span class="dashicons dashicons-format-image"></span> New product view</a>';
  $output .= '<div id="product_view">';
  foreach ($product_views as $product_view) {
    if (!empty($product_view)) {
      $image = wp_get_attachment_image_src( $product_view, 'steel-product-view-thumb' );
      $title = !empty($details['product_view_title_'.$product_view][0]) ? $details['product_view_title_'.$product_view][0] : '&nbsp;' ;
      $output .= '<div class="product-view" id="';
      $output .= $product_view;
      $output .= '">';
      $output .= '<div class="product-view-controls"><span id="controls_'.$product_view.'">'.$title.'</span><a class="del-product-view" href="#" onclick="deleteView(\''.$product_view.'\')" title="Delete product view"><span class="dashicons dashicons-dismiss" style="float:right"></span></a></div>';
      $output .= '<img id="product_view_img_'.$product_view.'" src="'.$image[0].'" width="'.$image[1].'" height="'.$image[2].'">';
      $output .= '<span class="dashicons dashicons-format-image" style="float:left;padding:5px;"></span><input class="product-view-title" type="text" size="23" name="product_view_title_';
      $output .= $product_view;
      $output .= '" id="product_view_title_'.$product_view.'" value="'.$title.'" placeholder="Title (i.e. Front)" />';
      $output .= '</div>';
    }
  }
  $output .= '</div>';

  echo $output; ?>

  <input type="hidden" name="product_view_order" id="product_view_order" value="<?php echo $product_view_order; ?>">
  <div style="float:none; clear:both;"></div><?php
}

/*
 * Save data from meta boxes
 */
add_action('save_post_steel_product', 'steel_save_steel_product');
function steel_save_steel_product() {
  global $post;
  if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE && (isset($post_id))) { return $post_id; }
  if(defined('DOING_AJAX') && DOING_AJAX && (isset($post_id))) { return $post_id; } //Prevents the metaboxes from being overwritten while quick editing.
  if(preg_match('/\edit\.php/', $_SERVER['REQUEST_URI']) && (isset($post_id))) { return $post_id; } //Detects if the save action is coming from a quick edit/batch edit.

  do_action('steel_save_steel_product_meta');
}

add_action('steel_save_steel_product_meta', 'steel_save_steel_product_details', 10);
function steel_save_steel_product_details() {
  global $post;

  if (isset($_POST['product_id']))
    update_post_meta($post->ID, 'product_id', sanitize_text_field($_POST['product_id']));
  if (isset($_POST['product_id_alt']))
    update_post_meta($post->ID, 'product_id_alt', sanitize_text_field($_POST['product_id_alt']));
  if (isset($_POST['product_color']))
    update_post_meta($post->ID, 'product_color', sanitize_text_field($_POST['product_color']));

  if (isset($_POST['product_price'])) {
    if (!empty($_POST['product_price']))
      update_post_meta($post->ID, 'product_price', round(floatval($_POST['product_price']),2));
    else
      delete_post_meta($post->ID, 'product_price');
  }

  if (isset($_POST['product_shipping'])) {
    if (!empty($_POST['product_shipping']))
      update_post_meta($post->ID, 'product_shipping', round(floatval($_POST['product_shipping']),2));
    else
      delete_post_meta($post->ID, 'product_shipping');
  }

  if (isset($_POST['product_width'])) {
    if (!empty($_POST['product_width']))
      update_post_meta($post->ID, 'product_width', floatval($_POST['product_width']));
    else
      delete_post_meta($post->ID, 'product_width');
  }

  if (isset($_POST['product_height'])) {
    if (!empty($_POST['product_height']))
      update_post_meta($post->ID, 'product_height', floatval($_POST['product_height']));
    else
      delete_post_meta($post->ID, 'product_height');
  }

  if (isset($_POST['product_depth'])) {
    if (!empty($_POST['product_depth']))
      update_post_meta($post->ID, 'product_depth', floatval($_POST['product_depth']));
    else
      delete_post_meta($post->ID, 'product_depth');
  }

  if (isset($_POST['product_weight'])) {
    if (!empty($_POST['product_weight']))
      update_post_meta($post->ID, 'product_weight', floatval($_POST['product_weight']));
    else
      delete_post_meta($post->ID, 'product_weight');
  }

  if (isset($_POST['product_manufacturer'])) {
    if (!empty($_POST['product_manufacturer']))
      wp_set_object_terms( $post->ID, absint($_POST['product_manufacturer']), 'steel_product_manufacturer' );
    else
      wp_set_object_terms( $post->ID, null, 'steel_product_manufacturer' );
  }

  if (isset($_POST['product_warranty_num'])) {
    if (!empty($_POST['product_warranty_num']))
      update_post_meta($post->ID, 'product_warranty_num', absint($_POST['product_warranty_num']));
    else
      delete_post_meta($post->ID, 'product_warranty_num');
  }

  if (isset($_POST['product_warranty_period'])) {
    if (in_array($_POST['product_warranty_period'], array( 'd', 'W', 'm', 'y' )) )
      update_post_meta($post->ID, 'product_warranty_period', $_POST['product_warranty_period']);
    else
      update_post_meta($post->ID, 'product_warranty_period', 'm');
  }
}

add_action('steel_save_steel_product_meta', 'steel_save_steel_product_views', 20);
function steel_save_steel_product_views() {
  global $post;

  if (isset($_POST['product_view_order']   )) {
    update_post_meta($post->ID, 'product_view_order', $_POST['product_view_order']);
    $product_views = explode(',', get_post_meta($post->ID, 'product_view_order', true));
    foreach ($product_views as $product_view) {
      if (isset($_POST['product_view_title_' . $product_view])) { update_post_meta($post->ID, 'product_view_title_' . $product_view, $_POST['product_view_title_' . $product_view]); }
    }
  }
}

/*
 * Display Product metadata
 *
 * @deprecated Use steel_get_product_meta() instead
 */
function steel_product_meta( $key, $post_id = NULL ) {
  global $post;
  $custom = $post_id == NULL ? get_post_custom($post->ID) : get_post_custom($post_id);
  $meta = !empty($custom['product_'.$key][0]) ? $custom['product_'.$key][0] : '';
  return $meta;
}

function steel_get_product_meta( $post_id = NULL ) {
  global $post;
  $post_id = $post_id == NULL ? $post->ID : $post_id;

  $defaults = array(
    'product_id'              => array(''),
    'product_id_alt'          => array(''),
    'product_price'           => array(''),
    'product_shipping'        => array(''),
    'product_width'           => array(''),
    'product_height'          => array(''),
    'product_depth'           => array(''),
    'product_color'           => array(''),
    'product_warranty_num'    => array(''),
    'product_warranty_period' => array('m'),
    'product_weight'          => array(''),
    'product_view_order'      => array(''),
  );
  $details = apply_filters('steel_product_meta', $defaults);
  $meta = get_post_custom( $post_id );

  return wp_parse_args($meta, $details);
}

/*
 * Display product dimensions
 *
 * @deprecated
 * @todo Replace with steel_get_product_dimensions
 */
function steel_product_dimensions( $args = array(), $sep = ' x ' ) {
  $defaults = array (
    'sep1' => $sep,
    'sep2' => $sep,
    'dimensions' => 3,
    'unit' => ' in',
  );
  $args = wp_parse_args($args, $defaults);
  $args = (object) $args;
  $width  = steel_product_meta('width' );
  $height = steel_product_meta('height');
  $depth  = steel_product_meta('depth' );
  if ( $dimensions = 3 && !empty($width) && !empty($height) && !empty($depth)) { printf( $width . $args->unit . $args->sep1 . $height . $args->unit . $args->sep2 . $depth . $args->unit ); }
    elseif ( !empty($width) && !empty($height) ) { printf( $width . $args->unit . $args->sep1 . $height . $args->unit ); }
}

/*
 * Display product_viewshow by id
 *
 * @deprecated
 * @todo Replace with steel_get_product_views
 */
function steel_product_thumbnail( $size = 'full' ) {
  global $post;
  $post_id = $post->ID;
  $product_view_order = steel_product_meta( 'view_order' );
  $product_views = explode(',', $product_view_order);
  if (has_post_thumbnail()) { the_post_thumbnail($size); }
  elseif ($product_view_order && is_singular()) {
    $output     = '';
    $indicators = '';
    $items      = '';
    $controls   = '';
    $thumbs     = '';
    $count      = -1;
    $i          = -1;
    $t          = -1;
    $indicators .= '<ol class="carousel-indicators">';
    foreach ($product_views as $product_view) {
      if (!empty($product_view)) {
        $count += 1;
        $indicators .= $count >= 1 ? '<li data-target="#product_views" data-slide-to="'.$count.'"></li>' : '<li data-target="#product_views" data-slide-to="'.$count.'" class="active"></li>';
      }
    }
    $indicators .= '</ol>';
    //Wrapper for product_views
    foreach ($product_views as $product_view) {
      if (!empty($product_view)) {
        $image   = wp_get_attachment_image_src( $product_view, 'steel-product' );
        $title   = steel_product_meta( 'view_title_'  .$product_view, $post_id );
        $i += 1;
        $items .= $i >= 1 ? '<div class="item">' : '<div class="item active">';
        $items .= !empty($link) ? '<a href="'.$link.'">' : '';
        $items .= '<img id="product_view_img_'.$product_view.'" src="'.$image[0].'" alt="'.$title.'">';
        $items .= !empty($link) ? '</a>' : '';
        if (!empty($title) || !empty($content)) {
          $items .= '<div class="carousel-caption">';
            if (!empty($title)) { $items .= '<p id="product_views_title_'.$product_view.'">' .$title.'</p>'; }
          $items .= '</div>';//.carousel-caption
        }
        $items .= '</div>';//.item
      }
    }
    //Wrapper for product_views
    foreach ($product_views as $product_view) {
      if (!empty($product_view) & $i >= 1) {
        $image   = wp_get_attachment_image_src( $product_view, 'steel-product-thumb' );
        $title   = steel_product_meta( 'view_title_'  .$product_view, $post_id );
        $t += 1;
        $thumbs .= $t >= 1 ? '<a href="#product_views" data-slide-to="'.$t.'">' : '<a href="#product_views" data-slide-to="'.$t.'" class="active">';
        $thumbs .= '<img id="product_view_img_'.$product_view.'" src="'.$image[0].'" alt="'.$title.'">';
        $thumbs .= '</a>';//.thumb
      }
    }
    //Output
    $output .= '<div id="product_views" class="carousel product_views" data-ride="carousel" data-interval="false">';
    $output .= '<div class="carousel-inner">';
    $output .= $items;
    $output .= '</div>';
    $output .= $thumbs;
    $output .= '</div>';
    echo $output;
  }
  elseif ($product_view_order) {
    $output     = '';
    $i          = -1;
    //Wrapper for product_views
    foreach ($product_views as $product_view) {
      if (!empty($product_view) && $i<0) {
        $image   = wp_get_attachment_image_src( $product_view, 'steel-product' );
        $title   = steel_product_meta( 'view_title_'  .$product_view, $post_id );
        $i += 1;
        $output .= '<img id="product_view_img_'.$product_view.'" src="'.$image[0].'" alt="'.$title.'">';
      }
    }
    echo $output;
  }
}
?>
