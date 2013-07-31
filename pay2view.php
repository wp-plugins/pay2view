<?php
/*
Plugin Name: Pay2View
Plugin URI: http://getbutterfly.com/
Description: Let your users pay to view content. Use it to hide download links, images, paragraphs or other shortcodes. This plugin allows the administrator to use a shortcode and hide certain content from guests until payment is completed. Uses PayPal.
Author: Ciprian Popescu
Author URI: http://getbutterfly.com/
Version: 0.13
License: GPLv3
*/

define('PAY2VIEW_PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)));
define('PAY2VIEW_PLUGIN_PATH', WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__)));
define('PAY2VIEW_VERSION', '0.13');

if(!class_exists('RooPay2View')) {
	class RooPay2View {
		var $adminOptionsName = 'RooPay2Viewadminoption';
		function RooPay2View() {}
		function init() {
			$this->getAdminOptions();
		}

		function getAdminOptions() {
			$ppAdminOption = array(
				'p2v_capability' 	=> 'read',
				'paypal_email' 		=> 'your@paypalemail.com',
				'd_amount' 			=> 10,
				'currency' 			=> 'USD',
				'message' 			=> 'You must pay to see the content'
			);
			$tmpOption = get_option($this->adminOptionsName);
			if(!empty($tmpOption)) {
				foreach($tmpOption as $key => $value) {
					$ppAdminOption[$key] = $value;
				}
			}
			update_option($this->adminOptionsName, $ppAdminOption);
			return $ppAdminOption;
		}

		function printAdminPanel() {
			$tmpOption = $this->getAdminOptions();
			if(isset($_POST['Submit'])) {
				$tmpOption['p2v_capability'] 	= $_POST['p2v_capability'];
				$tmpOption['paypal_email'] 		= $_POST['paypal_email'];
				$tmpOption['d_amount'] 			= $_POST['d_amount'];
				$tmpOption['currency'] 			= $_POST['currency'];
				$tmpOption['message'] 			= $_POST['message'];
				update_option($this->adminOptionsName, $tmpOption);
				?>
				<div class="updated"><p><?php _e('Settings updated.', 'pay2view'); ?></p></div>
				<?php
			}
			?>
			<div class="wrap">  
				<div id="icon-options-general" class="icon32"></div>
				<h2>Pay2View <small>&middot; <?php echo PAY2VIEW_VERSION; ?></small></h2>
				<div id="poststuff" class="ui-sortable meta-box-sortables">
					<div class="postbox">
						<h3><?php _e('General Settings', 'pay2view'); ?></h3>
						<div class="inside">
							<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>"/>  
								<table class="widefat">
									<tr>
										<td><?php _e('Capability Behaviour', 'pay2view'); ?></td>
										<td>
											<input type="text" name="p2v_capability" id="p2v_capability" value="<?php echo $tmpOption['p2v_capability']; ?>" class="regular-text">
											<br><small><?php _e('For example, &quot;read&quot; applies to all members, while &quot;manage_options&quot; applies to administrators only.', 'pay2view'); ?></small>
											<br><small><a href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table"><?php _e('Read more about WordPress capabilities here.', 'pay2view'); ?></a></small>
										</td>
									</tr>
									<tr>
										<td><?php _e('PayPal&trade; Email', 'pay2view'); ?></td>
										<td><input type="email" name="paypal_email" id="paypal_email" value="<?php echo $tmpOption['paypal_email']; ?>" class="regular-text"></td>
									</tr>
									<tr>
										<td><?php _e('Default Price', 'pay2view'); ?></td>
										<td><input type="number" min="0" max="99999" name="d_amount" value="<?php echo $tmpOption['d_amount']; ?>"></td>
									</tr>
									<tr>
										<td><?php _e('PayPal&trade; Currency', 'pay2view'); ?></td>
										<td><select name="currency">
											<?php
											$currency = array(
												'USD' => 'US Dollar',
												'EUR' => 'Euro',
												'AUD' => 'Australian Dollar',
												'NOK' => 'Norwegian Krone',
												'BRL' => 'Brazilian Real',
												'CAD' => 'Canadian Dollar',
												'NZD' => 'New Zealand Dollar',
												'CZK' => 'Czech Koruna',
												'DKK' => 'Danish Krone',
												'HKD' => 'Hong Kong Dollar',
												'HUF' => 'Hungarian Forint',
												'ILS' => 'Israeli New Sheqel',
												'JPY' => 'Yen',
												'CHF' => 'Swiss Franc',
												'MYR' => 'Malaysian Ringgit',
												'MXN' => 'Mexican Peso',
												'PHP' => 'Philippine Peso',
												'PLN' => 'Polish Zloty',
												'SGD' => 'Singapore Dollar',
												'SEK' => 'Swedish Krona',
												'TWD' => 'New Taiwan Dollar',
												'THB' => 'Thai Baht',
												'TRY' => 'New Turkish Lira',
												'GBP' => 'British Pound'
											);

											foreach($currency as $code => $currencies) {
												$selected = ($tmpOption['currency'] == $code) ? ' selected' : '';
												echo '<option value="' . $code.'"' . $selected . '>' . $currencies . '</option>';
											}
											?>
											</select>
											<br><small><a href="https://www.paypal.com/cgi-bin/webscr?cmd=p/sell/mc/mc_intro-outside" rel="external"><?php _e('Read more about PayPal accepted currencies here.', 'pay2view'); ?></a></small>
										</td>
									</tr>
									<tr>
										<td><?php _e('Message', 'pay2view'); ?></td>
										<td><input type="text" name="message" value="<?php echo $tmpOption['message']; ?>" class="regular-text"></td>
									</tr>
								</table>

								<div class="submit"><input type="submit" name="Submit" class="button button-primary" value="<?php _e('Save Changes', 'pay2view'); ?>"></div>
							</form>

							<h4><?php _e('Help and Support', 'pay2view'); ?></h4>
							<p><?php _e('To hide post/page content (text, image, shortcode) use the <code>[paypal]Your content here[/paypal]</code> shortcode. It will use the default price and currency.', 'pay2view'); ?></p>
							<p><?php _e('To specify a price for your hidden content and override the default one, use the <code>[paypal amount=11]Your content here[/paypal]</code> shortcode.', 'pay2view'); ?></p>
							<p><?php _e('Place another shortcode inside the <strong>Pay2View</strong> payment shortcode: <code>[paypal][another-shortcode][/paypal]</code>.', 'pay2view'); ?></p>
							<p><?php _e('Based on your capability behaviour selection, only guests and non-members see the payment button. Members always see the hidden content.', 'pay2view'); ?></p>
							<p><small><a href="http://getbutterfly.com/wordpress-plugins/pay2view/" rel="external">http://getbutterfly.com/wordpress-plugins/pay2view/</a></small></p>
						</div>
					</div>
				</div>
			</div>
			<?php 
		}

		function HideContent($atts, $content = null) {
			extract(shortcode_atts(array(
				'amount' => 16
			), $atts));

			$tmpOption 			= $this->getAdminOptions(); 
			$atts['paypal_url'] = 'https://www.paypal.com/';
			$atts['email'] 		= $tmpOption['paypal_email'];
			$atts['url'] 		= $atts['paypal_url'];

			if(null != $content && (current_user_can($tmpOption['p2v_capability']) || ($_GET['id'] + 600) > time()))
				return $content; 
			else {
				$message = $this->genButton($atts);
				return $message;
			}
		}

		function genButton($a) {
			$tmpOption = $this->getAdminOptions();
			if($a['amount'] == '' || !is_numeric($a['amount']))
				$a['amount'] = $tmpOption['d_amount'];

			$button = '
			<div>' . $tmpOption['message'] . '</div>
			<br>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="' . $tmpOption['paypal_url'] . '">
				<input type="hidden" name="currency_code" value="' . $tmpOption['currency'] . '">
				<input type="hidden" name="return" value="' . $_SERVER['REQUEST_URI'] . '&id=' . time() . '">
				<input type="hidden" name="amount" value="' . $a['amount'] . '">
				<input type="hidden" name="item_name" value="' . get_the_title() . '">
				<input type="hidden" name="item_number" value="' . get_the_ID() . '">
				<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
			<br><br>';

			return $button;
		}
	}
}

if(class_exists('RooPay2View')) {
	$p2v = new RooPay2View();
}
if(!function_exists('RooPay2View_ap')) {
	function RooPay2View_ap() {
		global $p2v;
		if(!isset($p2v))
			return;

		add_options_page('Pay2View', 'Pay2View', 'manage_options', basename(__FILE__), array(&$p2v, 'printAdminPanel'));
	}
}
if(isset($p2v)) {
	add_action('admin_menu', 'RooPay2View_ap');
	add_action('activate_pay2view/pay2view.php', array(&$p2v, 'init'));
	add_shortcode('paypal',array(&$p2v, 'HideContent'));

	add_filter('the_content', 'do_shortcode');
}
?>
