<?php
/*
 * If current theme is Flint or a child theme of Flint,
 * these action hooks are used to display meta created
 * by the Steel plugin and its modules.
 *
 * @package Steel/Marketplace
 */

add_action('flint_widget_area_right_steel_product','steel_product_widget_area_right');
function steel_product_widget_area_right() {
  $product_option_set_1 = marketplace_options('product_option_set_1');
  $product_option_set_2 = marketplace_options('product_option_set_2'); ?>
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title"><?php the_title(); ?></h3>
      <div style="float: right; margin-top: -1.25em;">$<?php echo steel_product_meta( 'price' ) ?></div>
    </div>
    <div class="panel-body">
      <form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
      <input type="hidden" name="business" value="<?php echo marketplace_options('paypal_merch_id'); ?>">
      <input type="hidden" name="cmd" value="_cart">
      <input type="hidden" name="add" value="1">
      <input type="hidden" name="item_name" value="<?php the_title(); ?>">
      <input type="hidden" name="amount" value="<?php echo steel_product_meta( 'price' ) ?>">
      <input type="hidden" name="currency_code" value="USD">
      <?php if (!empty($product_option_set_1)) { ?>
        <div class="form-group">
          <label for="os0"><?php echo marketplace_options('product_option_set_1_name'); ?></label>
          <input type="hidden" name="on0" value="<?php echo marketplace_options('product_option_set_1_name'); ?>">
          <select class="form-control" name="os0" required>
            <option value="None selected">Select <?php echo strtolower(marketplace_options('product_option_set_1_name')); ?></option>
          <?php
            $options1 = explode(',',$product_option_set_1);
            foreach($options1 as $option) {
              echo '<option value="'.$option.'">'.$option.'</option>';
            }
          ?>
          </select>
        </div>
      <?php } ?>
      <?php if (!empty($product_option_set_2)) { ?>
        <div class="form-group">
          <label for="os1"><?php echo marketplace_options('product_option_set_2_name'); ?></label>
          <input type="hidden" name="on1" value="<?php echo marketplace_options('product_option_set_2_name'); ?>">
          <select class="form-control" name="os1" required>
            <option value="None selected">Select <?php echo strtolower(marketplace_options('product_option_set_2_name')); ?></option>
          <?php
            $options2 = explode(',',$product_option_set_2);
            foreach($options2 as $option) {
              echo '<option value="'.$option.'">'.$option.'</option>';
            }
          ?>
          </select>
        </div>
      <?php } ?>
        <button type="submit" class="btn btn-primary btn-block">Add to Cart</button>
        <div id="paypal-logo"><table border="0" cellpadding="10" cellspacing="0" align="center"><tr><td align="center"></td></tr><tr><td align="center"><a href="https://www.paypal.com/webapps/mpp/paypal-popup" title="How PayPal Works" onclick="javascript:window.open('https://www.paypal.com/webapps/mpp/paypal-popup','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700'); return false;"><img src="https://www.paypalobjects.com/webstatic/mktg/logo/AM_SbyPP_mc_vs_dc_ae.jpg" border="0" alt="PayPal Acceptance Mark"></a></td></tr></table></div>
      </form>
    </div>
  </div>
<?php
}