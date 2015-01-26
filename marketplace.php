<?php
/*
Plugin Name: Steel Marketplace
Plugin URI: //Not yet developed
Description: A plugin that is part of the Sparks Framework. Extends Steel and creates an easy ecommerce development framework.
Version: 0.1
Author: Star Verte LLC
Author URI: http://starverte.com/
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/

  Copyright 2014 Star Verte LLC (email : info@starverte.com)

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
global $marketplace_ver;
$marketplace_ver = 0.1;

include_once dirname( __FILE__ ) . '/options.php';
include_once dirname( __FILE__ ) . '/templates.php';
include_once dirname( __FILE__ ) . '/variations.php';

/**
 * Load scripts
 */
add_action( 'admin_enqueue_scripts', 'steel_marketplace_admin_scripts' );
function steel_marketplace_admin_scripts() {
  global $marketplace_ver;
  wp_enqueue_style( 'steel-marketplace-admin-style', plugins_url('steel-marketplace/css/admin.css') );
  wp_enqueue_style( 'dashicons'                                                                     );

  wp_enqueue_script( 'jquery'              );
  wp_enqueue_script( 'jquery-ui-core'      );
  wp_enqueue_script( 'jquery-ui-sortable'  );
  wp_enqueue_script( 'jquery-ui-position'  );
  wp_enqueue_script( 'jquery-effects-core' );
  wp_enqueue_script( 'jquery-effects-blind');

  wp_enqueue_script( 'marketplace', plugins_url('steel-marketplace/js/marketplace.js'  ), array('jquery'), $marketplace_ver, true );

  wp_enqueue_media();
}
add_action( 'wp_enqueue_scripts', 'steel_marketplace_scripts' );
function steel_marketplace_scripts() {
  global $marketplace_ver;
  wp_enqueue_style ( 'marketplace-style', plugins_url('steel-marketplace/css/marketplace.css'  ), array(), $marketplace_ver);
}

add_action( 'init', 'steel_marketplace_init', 0 );
function steel_marketplace_init() {
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

  add_image_size( 'steel-product'           , 580, 360, true);
  add_image_size( 'steel-product-thumb'     , 150,  95, true);
  add_image_size( 'steel-product-view-thumb', 250, 155, true);
}

/*
 * Create custom meta boxes
 */
add_action( 'add_meta_boxes', 'steel_product_meta_boxes' );
function steel_product_meta_boxes() {
  add_meta_box('steel_product_details', 'Product Details', 'steel_product_details', 'steel_product', 'side'        );
  add_meta_box('steel_product_view_meta'  , 'Product Views'  , 'steel_product_view_meta'  , 'steel_product', 'side', 'high');
}
function steel_product_details() {
  $options = steel_get_options();

  if ($options['edit_product_id']) {?>
    <p class="product_ref"><span class="form-addon-left"><?php echo strtoupper($options['product_id_type']); ?></span><input type="text" size="18" name="product_ref" placeholder="Product ID" value="<?php echo steel_product_meta('ref'); ?>" /></p>
  <?php
  }
  if ($options['edit_product_price']) {?>
    <p class="product_price">
      <label>Base price</label><br />
      <span class="form-addon-left">$</span><input type="text" size="21" name="product_price" placeholder="Price" value="<?php echo steel_product_meta ('price'); ?>" />
    </p>
  <?php
  }
  if ($options['edit_product_shipping']) {?>
    <p class="product_shipping">
      <label>Additional shipping cost</label><br />
      <span class="form-addon-left">$</span><input type="text" size="21" name="product_shipping" value="<?php echo steel_product_meta('shipping'); ?>" />
    </p>
  <?php
  }
  if ($options['edit_product_width_height']) {?>
    <p class="product_dimensions">
      <label>Dimensions</label><br />
      <input type="text" size="5" name="product_width" placeholder="Width" value="<?php echo steel_product_meta('width'); ?>" /> x
      <input type="text" size="5" name="product_height" placeholder="Height" value="<?php echo steel_product_meta('height'); ?>" />
      <?php if ($options['edit_product_depth']) { ?>x
        <input type="text" size="5" name="product_depth" placeholder="Depth" value="<?php echo steel_product_meta('depth'); ?>" />
      <?php } ?>
    </p>
  <?php
  }
}
function steel_product_view_meta() {
  global $post;
  $product_view_order = steel_product_meta( 'view_order' );

  //Backwards compatibility for Sparks Store
  if (has_post_thumbnail()) {
    $thumb_id = get_post_thumbnail_id();
    $product_view_order .= ','.$thumb_id;
    update_post_meta($post->ID, 'product_view_order'   , $product_view_order);
    delete_post_meta($post->ID, '_thumbnail_id');
  }

  $product_views = explode(',', $product_view_order);

  $output = '';
  $output .= '<a href="#" class="button add_product_view_media" id="btn_above" title="Add product_view to product_viewhow"><span class="steel-icon-cover-photo"></span> New product view</a>';
  $output .= '<div id="product_view">';
  foreach ($product_views as $product_view) {
    if (!empty($product_view)) {
      $image = wp_get_attachment_image_src( $product_view, 'steel-product-view-thumb' );
      $output .= '<div class="product-view" id="';
      $output .= $product_view;
      $output .= '">';
      $output .= '<div class="product-view-controls"><span id="controls_'.$product_view.'">'.steel_product_meta( 'view_title_'.$product_view ).'</span><a class="del-product-view" href="#" onclick="deleteView(\''.$product_view.'\')" title="Delete product view"><span class="steel-icon-dismiss" style="float:right"></span></a></div>';
      $output .= '<img id="product_view_img_'.$product_view.'" src="'.$image[0].'" width="'.$image[1].'" height="'.$image[2].'">';
      $output .= '<span class="steel-icon-cover-photo" style="float:left;padding:5px;"></span><input class="product-view-title" type="text" size="23" name="product_view_title_';
      $output .= $product_view;
      $output .= '" id="product_view_title_'.$product_view.'" value="'.steel_product_meta( 'view_title_'.$product_view ).'" placeholder="Title (i.e. Front)" style="margin:0;" />';
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
add_action('save_post', 'save_steel_product');
function save_steel_product() {
  global $post;
  if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE && (isset($post_id))) { return $post_id; }
  if(defined('DOING_AJAX') && DOING_AJAX && (isset($post_id))) { return $post_id; } //Prevents the metaboxes from being overwritten while quick editing.
  if(preg_match('/\edit\.php/', $_SERVER['REQUEST_URI']) && (isset($post_id))) { return $post_id; } //Detects if the save action is coming from a quick edit/batch edit.
  if (isset($_POST['product_ref'     ])) { update_post_meta($post->ID, 'product_ref'     , $_POST['product_ref'     ]); }
  if (isset($_POST['product_price'   ])) { update_post_meta($post->ID, 'product_price'   , $_POST['product_price'   ]); }
  if (isset($_POST['product_shipping'])) { update_post_meta($post->ID, 'product_shipping', $_POST['product_shipping']); }
  if (isset($_POST['product_width'   ])) { update_post_meta($post->ID, 'product_width'   , $_POST['product_width'   ]); }
  if (isset($_POST['product_height'  ])) { update_post_meta($post->ID, 'product_height'  , $_POST['product_height'  ]); }
  if (isset($_POST['product_depth'   ])) { update_post_meta($post->ID, 'product_depth'   , $_POST['product_depth'   ]); }

  if (isset($_POST['product_view_order']   )) {
    update_post_meta($post->ID, 'product_view_order'   , $_POST['product_view_order']);
    $product_views = explode(',', get_post_meta($post->ID, 'product_view_order', true));
    foreach ($product_views as $product_view) {
      if (isset($_POST['product_view_title_'   . $product_view])) { update_post_meta($post->ID, 'product_view_title_'  . $product_view, $_POST['product_view_title_'   . $product_view]); }
    }
  }
}

/*
 * Display Product metadata
 */
function steel_product_meta( $key, $post_id = NULL ) {
  global $post;
  $custom = $post_id == NULL ? get_post_custom($post->ID) : get_post_custom($post_id);
  $meta = !empty($custom['product_'.$key][0]) ? $custom['product_'.$key][0] : '';
  return $meta;
}

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
