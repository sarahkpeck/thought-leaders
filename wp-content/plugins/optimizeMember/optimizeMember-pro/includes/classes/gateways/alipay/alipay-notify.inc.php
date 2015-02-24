<?php
/**
* AliPay IPN Handler.
*
* Copyright: © 2009-2011
* {@link http://www.optimizepress.com/ optimizePress, Inc.}
* ( coded in the USA )
*
* This WordPress plugin ( optimizeMember Pro ) is comprised of two parts:
*
* o (1) Its PHP code is licensed under the GPL license, as is WordPress.
* 	You should have received a copy of the GNU General Public License,
* 	along with this software. In the main directory, see: /licensing/
* 	If not, see: {@link http://www.gnu.org/licenses/}.
*
* o (2) All other parts of ( optimizeMember Pro ); including, but not limited to:
* 	the CSS code, some JavaScript code, images, and design;
* 	are licensed according to the license purchased.
* 	See: {@link http://www.optimizepress.com/prices/}
*
* Unless you have our prior written consent, you must NOT directly or indirectly license,
* sub-license, sell, resell, or provide for free; part (2) of the optimizeMember Pro Module;
* or make an offer to do any of these things. All of these things are strictly
* prohibited with part (2) of the optimizeMember Pro Module.
*
* Your purchase of optimizeMember Pro includes free lifetime upgrades via optimizeMember.com
* ( i.e. new features, bug fixes, updates, improvements ); along with full access
* to our video tutorial library: {@link http://www.optimizepress.com/videos/}
*
* @package optimizeMember\AliPay
* @since 1.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_pro_alipay_notify"))
	{
		/**
		* AliPay IPN Handler.
		*
		* @package optimizeMember\AliPay
		* @since 1.5
		*/
		class c_ws_plugin__optimizemember_pro_alipay_notify
			{
				/**
				* Handles AliPay IPN URL processing.
				*
				* @package optimizeMember\AliPay
				* @since 1.5
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null|inner Return-value of inner routine.
				*/
				public static function alipay_notify ()
					{
						if (!empty ($_POST["notify_type"]) && preg_match ("/^trade_status_sync$/i", $_POST["notify_type"]))
							{
								return c_ws_plugin__optimizemember_pro_alipay_notify_in::alipay_notify ();
							}
					}
			}
	}
?>