<?php
/**
 * Product Variations
 *
 * @package Steel
 */

if ( ! function_exists( 'steel_marketplace_variations' ) ) {
  // Register Custom Taxonomies
  function steel_marketplace_variations() {
    $options = get_option('marketplace_options');
    $steel_options = steel_get_options();

    register_taxonomy(
      'steel_product_color',
      array( 'steel_product' ),
      array(
        'labels'            => array(
          'name'                => _x( 'Colors', 'Taxonomy General Name', 'steel' ),
          'singular_name'       => _x( 'Color', 'Taxonomy Singular Name', 'steel' ),
          'menu_name'           => __( 'Color', 'steel' ),
          'all_items'           => __( 'All Colors', 'steel' ),
          'parent_item'         => __( 'Color Family', 'steel' ),
          'parent_item_colon'   => __( 'Color Family:', 'steel' ),
          'new_item_name'       => __( 'New Color Name', 'steel' ),
          'add_new_item'        => __( 'Add New Color', 'steel' ),
          'edit_item'           => __( 'Edit Color', 'steel' ),
          'update_item'         => __( 'Update Color', 'steel' ),
          'search_items'        => __( 'Search colors', 'steel' ),
          'add_or_remove_items' => __( 'Add or remove colors', 'steel' ),
          'not_found'           => __( 'No colors found', 'steel' ),
        ),
        'public'            => $steel_options['edit_product_colors'],
        'show_in_nav_menus' => false,
        'show_tagcloud'     => false,
        'meta_box_cb'       => false,
        'hierarchical'      => true,
      )
    );

    register_taxonomy(
      'steel_product_size',
      array( 'steel_product' ),
      array(
        'labels'            => array(
          'name'                => _x( 'Sizes', 'Taxonomy General Name', 'steel' ),
          'singular_name'       => _x( 'Size', 'Taxonomy Singular Name', 'steel' ),
          'menu_name'           => __( 'Size', 'steel' ),
          'all_items'           => __( 'All Sizes', 'steel' ),
          'parent_item'         => __( 'Size Family', 'steel' ),
          'parent_item_colon'   => __( 'Size Family:', 'steel' ),
          'new_item_name'       => __( 'New Size Name', 'steel' ),
          'add_new_item'        => __( 'Add New Size', 'steel' ),
          'edit_item'           => __( 'Edit Size', 'steel' ),
          'update_item'         => __( 'Update Size', 'steel' ),
          'search_items'        => __( 'Search sizes', 'steel' ),
          'add_or_remove_items' => __( 'Add or remove sizes', 'steel' ),
          'not_found'           => __( 'No sizes found', 'steel' ),
        ),
        'public'            => $steel_options['edit_product_sizes'],
        'show_in_nav_menus' => false,
        'show_tagcloud'     => false,
        'meta_box_cb'       => false,
        'hierarchical'      => true,
      )
    );

    if (!empty($options['product_option_set_1'])) {
      if (stristr($options['product_option_set_1_name'], 'color')) {
        $product_options = explode(',', $options['product_option_set_1']);

        if (!empty(str_ireplace(
          array(
            'color',
            'colors',
            ' color',
            'color ',
            ' colors',
            'colors ',
          ),
          '',
          $options['product_option_set_1_name']
        ))) {

          wp_insert_term($options['product_option_set_1_name'], 'steel_product_color');

          $parent = term_exists($options['product_option_set_1_name'], 'steel_product_color');
          $parent_id = $parent['term_id'];

          foreach ($product_options as $option)
            wp_insert_term( $option, 'steel_product_color', array( 'parent' => $parent_id ) );

        }
        else {
          foreach ($product_options as $option)
            wp_insert_term( $option, 'steel_product_color' );
        }
      }
      elseif (stristr($options['product_option_set_1_name'], 'size')) {
        $product_options = explode(',', $options['product_option_set_1']);

        if (!empty(str_ireplace(
          array(
            'size',
            'sizes',
            ' size',
            'size ',
            ' sizes',
            'sizes ',
          ),
          '',
          $options['product_option_set_1_name']
        ))) {

          wp_insert_term($options['product_option_set_1_name'], 'steel_product_size');

          $parent = term_exists($options['product_option_set_1_name'], 'steel_product_size');
          $parent_id = $parent['term_id'];

          foreach ($product_options as $option)
            wp_insert_term( $option, 'steel_product_size', array( 'parent' => $parent_id ) );

        }
        else {
          foreach ($product_options as $option)
            wp_insert_term( $option, 'steel_product_size' );
        }
      }
      else {
        $taxonomy = strtolower($options['product_option_set_1_name']);
        $taxonomy = sanitize_title($taxonomy);
        $taxonomy = str_replace('-', '_', $taxonomy);

        $product_options = explode(',', $options['product_option_set_1']);

        register_taxonomy( $taxonomy, 'steel_product', array( 'label' => __( $options['product_option_set_1_name'] ) ) );

        foreach ($product_options as $option)
          wp_insert_term( $option, $taxonomy );

      }
      $options['product_option_set_1_name'] = null;
      $options['product_option_set_1'] = null;

      update_option('marketplace_options', $options);
    }
    if (!empty($options['product_option_set_2'])) {
      if (stristr($options['product_option_set_2_name'], 'color')) {
        $product_options = explode(',', $options['product_option_set_2']);

        if (!empty(str_ireplace(
          array(
            'color',
            'colors',
            ' color',
            'color ',
            ' colors',
            'colors ',
          ),
          '',
          $options['product_option_set_2_name']
        ))) {

          wp_insert_term($options['product_option_set_2_name'], 'steel_product_color');

          $parent = term_exists($options['product_option_set_2_name'], 'steel_product_color');
          $parent_id = $parent['term_id'];

          foreach ($product_options as $option)
            wp_insert_term( $option, 'steel_product_color', array( 'parent' => $parent_id ) );

        }
        else {
          foreach ($product_options as $option)
            wp_insert_term( $option, 'steel_product_color' );
        }
      }
      elseif (stristr($options['product_option_set_2_name'], 'size')) {
        $product_options = explode(',', $options['product_option_set_2']);

        if (!empty(str_ireplace(
          array(
            'size',
            'sizes',
            ' size',
            'size ',
            ' sizes',
            'sizes ',
          ),
          '',
          $options['product_option_set_2_name']
        ))) {

          wp_insert_term($options['product_option_set_2_name'], 'steel_product_size');

          $parent = term_exists($options['product_option_set_2_name'], 'steel_product_size');
          $parent_id = $parent['term_id'];

          foreach ($product_options as $option)
            wp_insert_term( $option, 'steel_product_size', array( 'parent' => $parent_id ) );

        }
        else {
          foreach ($product_options as $option)
            wp_insert_term( $option, 'steel_product_size' );
        }
      }
      else {
        $taxonomy = strtolower($options['product_option_set_2_name']);
        $taxonomy = sanitize_title($taxonomy);
        $taxonomy = str_replace('-', '_', $taxonomy);

        $product_options = explode(',', $options['product_option_set_2']);

        register_taxonomy( $taxonomy, 'steel_product', array( 'label' => __( $options['product_option_set_2_name'] ) ) );

        foreach ($product_options as $option)
          wp_insert_term( $option, $taxonomy );

      }
      $options['product_option_set_2_name'] = null;
      $options['product_option_set_2'] = null;

      update_option('marketplace_options', $options);
    }
  }
}
add_action( 'init', 'steel_marketplace_variations', 0 );
