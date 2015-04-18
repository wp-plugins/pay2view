<?php
/*
Plugin Name: Pay2View
Plugin URI: http://getbutterfly.com/wordpress-plugins-free/
Description: Let your users pay to view content. Use it to hide download links, images, paragraphs or other shortcodes. This plugin allows the administrator to use a shortcode and hide certain content from guests until payment is completed. Uses PayPal.
Author: Ciprian Popescu
Author URI: http://getbutterfly.com/
Version: 0.3
License: GPLv3

Copyright 2013, 2014, 2015 Ciprian Popescu (email: getbutterfly@gmail.com)

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

define('PAY2VIEW_PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)));
define('PAY2VIEW_PLUGIN_PATH', WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__)));
define('PAY2VIEW_VERSION', '0.3');

if(!class_exists('RooPay2View')) {
	class RooPay2View {
		var $adminOptionsName = 'RooPay2Viewadminoption';
		function RooPay2View() {}
		function init() {
			$this->getAdminOptions();
		}

		function getAdminOptions() {
			$ppAdminOption = array(
				'p2v_capability'    => 'read',
				'paypal_email'      => 'your@paypalemail.com',
				'd_amount'          => 10,
				'currency'          => 'USD',
				'time'              => 600,
				'message'           => 'You must pay to see the content',
				'button'            => 'https://www.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif',
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
				$tmpOption['p2v_capability']    = $_POST['p2v_capability'];
				$tmpOption['paypal_email']      = $_POST['paypal_email'];
				$tmpOption['d_amount']          = $_POST['d_amount'];
				$tmpOption['currency']          = $_POST['currency'];
				$tmpOption['time']              = $_POST['time'];
				$tmpOption['message']           = $_POST['message'];
				$tmpOption['button']            = $_POST['button'];

                update_option($this->adminOptionsName, $tmpOption);
				?>
				<div class="updated"><p><?php _e('Settings updated.', 'pay2view'); ?></p></div>
				<?php
			}
			?>
			<div class="wrap">  
				<div id="icon-options-general" class="icon32"></div>
				<h2>Multiuser Pay2View (Raspberry Edition) <sup><small><?php echo PAY2VIEW_VERSION; ?></small></sup></h2>
				<div id="poststuff" class="ui-sortable meta-box-sortables">
					<div class="postbox">
						<h3><?php _e('General Settings', 'pay2view'); ?></h3>
						<div class="inside">
							<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>"/>  
								<table class="widefat">
									<tr>
										<td><label for="p2v_capability"><?php _e('Capability Behaviour', 'pay2view'); ?></label></td>
										<td>
											<input type="text" name="p2v_capability" id="p2v_capability" value="<?php echo $tmpOption['p2v_capability']; ?>" class="regular-text">
											<br><small><?php _e('For example, &quot;read&quot; applies to all members, while &quot;manage_options&quot; applies to administrators only.', 'pay2view'); ?></small>
											<br><small><a href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table"><?php _e('Read more about WordPress capabilities here.', 'pay2view'); ?></a></small>
										</td>
									</tr>
									<tr>
										<td><label for="paypal_email"><?php _e('PayPal&trade; Email', 'pay2view'); ?></label></td>
										<td><input type="email" name="paypal_email" id="paypal_email" value="<?php echo $tmpOption['paypal_email']; ?>" class="regular-text"></td>
									</tr>
									<tr>
										<td><label for="d_amount"><?php _e('Default Price', 'pay2view'); ?></label></td>
										<td><input type="number" min="0" max="99999" name="d_amount" id="d_amount" value="<?php echo $tmpOption['d_amount']; ?>" class="text"></td>
									</tr>
									<tr>
										<td><label for="time"><?php _e('Default Cookie Time', 'pay2view'); ?></label></td>
										<td>
                                            <input type="number" min="0" max="99999999" name="time" id="time" value="<?php echo $tmpOption['time']; ?>" class="text"> <label>seconds</label>
                                            <br><small>This is the default lifetime of the payment cookie.</small>
                                        </td>
									</tr>
									<tr>
										<td><label for="currency"><?php _e('PayPal&trade; Currency', 'pay2view'); ?></label></td>
										<td><select id="currency" name="currency" class="regular-text">>
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
											<br><small><a href="https://www.paypal.com/cgi-bin/webscr?cmd=p/sell/mc/mc_intro-outside" rel="external"><?php _e('Read more about PayPal&trade; accepted currencies here.', 'pay2view'); ?></a></small>
										</td>
									</tr>
									<tr>
										<td><label for="message"><?php _e('Message', 'pay2view'); ?></label></td>
										<td><input type="text" name="message" id="message" value="<?php echo $tmpOption['message']; ?>" class="regular-text"></td>
									</tr>
									<tr>
										<td><label for="button"><?php _e('PayPal&trade; Button URL', 'pay2view'); ?></label></td>
										<td><input type="url" name="button" id="button" value="<?php echo $tmpOption['button']; ?>" class="regular-text"></td>
									</tr>
									<tr>
										<td><label for="button"><?php _e('PayPal&trade; Button Preview', 'pay2view'); ?></label></td>
										<td>
                                            <p><img src="<?php echo $tmpOption['button']; ?>" alt=""></p>
                                        </td>
									</tr>
								</table>

								<div class="submit"><input type="submit" name="Submit" class="button button-primary" value="<?php _e('Save Changes', 'pay2view'); ?>"></div>
							</form>

							<h4><?php _e('Help and Support', 'pay2view'); ?></h4>
							<p><?php _e('To hide post/page content (text, image, shortcode) use the <code>[paypal email="myemail@domain.com"]Your content here[/paypal]</code> shortcode. It will use the default price and currency.', 'pay2view'); ?></p>
							<p><?php _e('To specify a price for your hidden content and override the default one, use the <code>[paypal email="myemail@domain.com" amount="12"]Your content here[/paypal]</code> shortcode.', 'pay2view'); ?></p>
							<p><?php _e('Place another shortcode inside the <strong>Multiuser Pay2View</strong> payment shortcode: <code>[paypal][another-shortcode][/paypal]</code>.', 'pay2view'); ?></p>
							<p><?php _e('Based on your capability behaviour selection, only guests and non-members see the payment button. Members always see the hidden content.', 'pay2view'); ?></p>
							<p><small><a href="http://getbutterfly.com/wordpress-plugins/multiuser-pay2view/" rel="external">http://getbutterfly.com/wordpress-plugins/multiuser-pay2view/</a></small></p>
						</div>
					</div>
				</div>
			</div>
			<?php 
		}

		function HideContent($atts, $content = null) {
			$tmpOption = $this->getAdminOptions();

            extract(shortcode_atts(array(
                'email' => $tmpOption['paypal_email'],
				'amount' => $tmpOption['d_amount'],
                'currency' => $tmpOption['currency'],
			), $atts));

			$atts['paypal_url'] = 'https://www.paypal.com/';
			$atts['url'] 		= $atts['paypal_url'];
            if($email != '')
                $atts['email'] = $email;

            if($amount != '')
                $atts['amount'] = $amount;

            if($currency != '')
                $atts['currency'] = $currency;

			if(null != $content && (current_user_can($tmpOption['p2v_capability']) || ($_GET['id'] + $tmpOption['p2v_time']) > time()))
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
				<input type="hidden" name="business" value="' . $tmpOption['paypal_email'] . '">
				<input type="hidden" name="currency_code" value="' . $tmpOption['currency'] . '">
				<input type="hidden" name="return" value="' . $_SERVER['REQUEST_URI'] . '&id=' . time() . '">
				<input type="hidden" name="amount" value="' . $a['amount'] . '">
				<input type="hidden" name="item_name" value="' . get_the_title() . '">
				<input type="hidden" name="item_number" value="' . get_the_ID() . '">
				<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
			<br><br>';

            $button .= 'Generating button for ' . $a['email'] . ' with a value of ' . $a['currency'] . $a['amount'] . '';

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

		add_options_page('Multiuser Pay2View', 'Multiuser Pay2View', 'manage_options', basename(__FILE__), array(&$p2v, 'printAdminPanel'));
	}
}
if(isset($p2v)) {
	add_action('admin_menu', 'RooPay2View_ap');
	add_action('activate_pay2view/pay2view.php', array(&$p2v, 'init'));
	add_shortcode('paypal', array(&$p2v, 'HideContent'));

	add_filter('the_content', 'do_shortcode');
}
?>
