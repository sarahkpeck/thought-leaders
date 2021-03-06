<?php
/**
* optimizeMember's PayPal IPN handler ( inner processing routine ).
*
* Copyright: © 2009-2011
* {@link http://www.optimizepress.com/ optimizePress, Inc.}
* ( coded in the USA )
*
* Released under the terms of the GNU General Public License.
* You should have received a copy of the GNU General Public License,
* along with this software. In the main directory, see: /licensing/
* If not, see: {@link http://www.gnu.org/licenses/}.
*
* @package optimizeMember\PayPal
* @since 110720
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_paypal_notify_in_subscr_or_wa_w_level"))
	{
		/**
		* optimizeMember's PayPal IPN handler ( inner processing routine ).
		*
		* @package optimizeMember\PayPal
		* @since 110720
		*/
		class c_ws_plugin__optimizemember_paypal_notify_in_subscr_or_wa_w_level
			{
				/**
				* optimizeMember's PayPal IPN handler ( inner processing routine ).
				*
				* @package optimizeMember\PayPal
				* @since 110720
				*
				* @param array $vars Required. An array of defined variables passed by {@link optimizeMember\PayPal\c_ws_plugin__optimizemember_paypal_notify_in::paypal_notify()}.
				* @return array|bool The original ``$paypal`` array passed in ( extracted ) from ``$vars``, or false when conditions do NOT apply.
				*
				* @todo Optimize with ``empty()`` and ``isset()``.
				*/
				public static function cp ($vars = array ()) /* Conditional phase for ``c_ws_plugin__optimizemember_paypal_notify_in::paypal_notify()``. */
					{
						extract ($vars); /* Extract all vars passed in from: ``c_ws_plugin__optimizemember_paypal_notify_in::paypal_notify()``. */
						/**/
						if (/**/(!empty ($paypal["txn_type"]) && preg_match ("/^(web_accept|subscr_signup)$/i", $paypal["txn_type"]))/**/
						&& (!empty ($paypal["item_number"]) && preg_match ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["membership_item_number_w_level_regex"], $paypal["item_number"]))/**/
						&& (!empty ($paypal["subscr_id"]) || (!empty ($paypal["txn_id"]) && ($paypal["subscr_id"] = $paypal["txn_id"])))/**/
						&& (empty ($paypal["payment_status"]) || empty ($payment_status_issues) || !preg_match ($payment_status_issues, $paypal["payment_status"]))/**/
						&& (!empty ($paypal["payer_email"]))/**/)
							{
								eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
								do_action ("ws_plugin__optimizemember_during_paypal_notify_before_subscr_signup", get_defined_vars ());
								unset ($__refs, $__v); /* Unset defined __refs, __v. */
								/**/
								if (!get_transient ($transient_ipn = "s2m_ipn_" . md5 ("optimizemember_transient_" . $_paypal_s)) && set_transient ($transient_ipn, time (), 31556926 * 10))
									{
										$paypal["optimizemember_log"][] = "optimizeMember `txn_type` identified as ( `web_accept|subscr_signup` ).";
										/**/
										list ($paypal["level"], $paypal["ccaps"], $paypal["eotper"]) = preg_split ("/\:/", $paypal["item_number"], 3);
										/**/
										$paypal["ip"] = (preg_match ("/ip address/i", $paypal["option_name2"]) && $paypal["option_selection2"]) ? $paypal["option_selection2"] : "";
										$paypal["ip"] = (!$paypal["ip"] && preg_match ("/^[a-z0-9]+~[0-9\.]+$/i", $paypal["invoice"])) ? preg_replace ("/^[a-z0-9]+~/i", "", $paypal["invoice"]) : $paypal["ip"];
										/**/
										$paypal["period1"] = (preg_match ("/^[1-9]/", $paypal["period1"])) ? $paypal["period1"] : "0 D"; /* Defaults to "0 D" ( zero days ). */
										$paypal["mc_amount1"] = (strlen ($paypal["mc_amount1"]) && $paypal["mc_amount1"] > 0) ? $paypal["mc_amount1"] : "0.00"; /* "0.00". */
										/**/
										if (preg_match ("/^web_accept$/i", $paypal["txn_type"])) /* Conversions for Lifetime & Fixed-Term sales. */
											{
												$paypal["period3"] = ($paypal["eotper"]) ? $paypal["eotper"] : "1 L"; /* 1 Lifetime. */
												$paypal["mc_amount3"] = $paypal["mc_gross"]; /* The "Buy Now" amount is the full gross. */
											}
										/**/
										$paypal["initial_term"] = (preg_match ("/^[1-9]/", $paypal["period1"])) ? $paypal["period1"] : "0 D"; /* Defaults to "0 D" ( zero days ). */
										$paypal["initial"] = (strlen ($paypal["mc_amount1"]) && preg_match ("/^[1-9]/", $paypal["period1"])) ? $paypal["mc_amount1"] : $paypal["mc_amount3"];
										$paypal["regular"] = $paypal["mc_amount3"]; /* This is the Regular Payment Amount that is charged to the Customer. Always required by PayPal. */
										$paypal["regular_term"] = $paypal["period3"]; /* This is just set to keep a standard; this way both initial_term & regular_term are available. */
										$paypal["recurring"] = ($paypal["recurring"]) ? $paypal["mc_amount3"] : "0"; /* If non-recurring, this should be zero, otherwise Regular. */
										/**/
										eval ('$ipn_signup_vars = $paypal; unset($ipn_signup_vars["optimizemember_log"]);'); /* Create array of IPN signup vars w/o optimizemember_log. */
										/*
										New Subscription with advanced update vars ( option_name1, option_selection1 )? These variables are used in Subscr. Modifications.
										*/
										if (preg_match ("/(referenc|associat|updat|upgrad)/i", $paypal["option_name1"]) && $paypal["option_selection1"]) /* Advanced way to handle Subscription mods. */
											/* This advanced method is required whenever a Subscription that is already completed, or was never setup to recur in the first place needs to be modified.
											PayPal will not allow the `modify=1|2` parameter to be used in those scenarios, because technically there is no billing to update; only the account. */
											{
												eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
												do_action ("ws_plugin__optimizemember_during_paypal_notify_before_subscr_signup_w_update_vars", get_defined_vars ());
												unset ($__refs, $__v); /* Unset defined __refs, __v. */
												/**/
												$paypal["optimizemember_log"][] = "optimizeMember `txn_type` identified as ( `web_accept|subscr_signup` ) w/ update vars.";
												/**/
												/* Check for both the old & new subscr_id's, just in case the Return routine already changed it. */
												if (($user_id = c_ws_plugin__optimizemember_utils_users::get_user_id_with ($paypal["subscr_id"], $paypal["option_selection1"])) && is_object ($user = new WP_User ($user_id)) && $user->ID)
													{
														if (!$user->has_cap ("administrator")) /* Do NOT process this routine on Administrators. */
															{
																$processing = $modifying = $during = true; /* Yes, we ARE processing this. */
																/**/
																eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
																do_action ("ws_plugin__optimizemember_during_paypal_notify_during_before_subscr_signup_w_update_vars", get_defined_vars ());
																do_action ("ws_plugin__optimizemember_during_collective_mods", $user_id, get_defined_vars (), "ipn-upgrade-downgrade", "modification", "optimizemember_level" . $paypal["level"]);
																unset ($__refs, $__v); /* Unset defined __refs, __v. */
																/**/
																$fields = get_user_option ("optimizemember_custom_fields", $user_id); /* These will be needed in the routines below. */
																$user_reg_ip = get_user_option ("optimizemember_registration_ip", $user_id); /* Original IP during Registration. */
																$user_reg_ip = $paypal["ip"] = ($user_reg_ip) ? $user_reg_ip : $paypal["ip"]; /* Now merge conditionally. */
																/**/
																if (is_multisite () && !is_user_member_of_blog ($user_id)) /* Must have a Role on this Blog. */
																	{
																		add_existing_user_to_blog (array ("user_id" => $user_id, "role" => "optimizemember_level" . $paypal["level"]));
																		$user = new WP_User ($user_id);
																	}
																/**/
																$current_role = c_ws_plugin__optimizemember_user_access::user_access_role ($user);
																/**/
																if ($current_role !== "optimizemember_level" . $paypal["level"]) /* Only if we need to. */
																	$user->set_role ("optimizemember_level" . $paypal["level"]); /* (upgrade/downgrade) */
																/**/
																if ($paypal["ccaps"] && preg_match ("/^-all/", str_replace ("+", "", $paypal["ccaps"])))
																	foreach ($user->allcaps as $cap => $cap_enabled)
																		if (preg_match ("/^access_optimizemember_ccap_/", $cap))
																			$user->remove_cap ($ccap = $cap);
																/**/
																if ($paypal["ccaps"] && preg_replace ("/^-all[\r\n\t\s;,]*/", "", str_replace ("+", "", $paypal["ccaps"])))
																	foreach (preg_split ("/[\r\n\t\s;,]+/", preg_replace ("/^-all[\r\n\t\s;,]*/", "", str_replace ("+", "", $paypal["ccaps"]))) as $ccap)
																		if (strlen ($ccap = trim (strtolower (preg_replace ("/[^a-z_0-9]/i", "", $ccap)))))
																			$user->add_cap ("access_optimizemember_ccap_" . $ccap);
																/**/
																update_user_option ($user_id, "optimizemember_subscr_gateway", $paypal["subscr_gateway"]);
																update_user_option ($user_id, "optimizemember_subscr_id", $paypal["subscr_id"]);
																update_user_option ($user_id, "optimizemember_custom", $paypal["custom"]);
																/**/
																if (!get_user_option ("optimizemember_registration_ip", $user_id))
																	update_user_option ($user_id, "optimizemember_registration_ip", $paypal["ip"]);
																/**/
																update_user_option ($user_id, "optimizemember_ipn_signup_vars", $ipn_signup_vars);
																/**/
																delete_user_option ($user_id, "optimizemember_file_download_access_log");
																/**/
																if (preg_match ("/^web_accept$/i", $paypal["txn_type"]) && $paypal["eotper"])
																	{
																		update_user_option ($user_id, "optimizemember_auto_eot_time", /* Set exclusively by the IPN handler; to avoid duplicate extensions. */
																		($eot_time = c_ws_plugin__optimizemember_utils_time::auto_eot_time ("", "", "", $paypal["eotper"], "", get_user_option ("optimizemember_auto_eot_time", $user_id))));
																		$paypal["optimizemember_log"][] = "Automatic EOT ( End Of Term ) Time set to: " . date ("D M j, Y g:i:s a T", $eot_time) . ".";
																	}
																else /* Otherwise, we need to clear the Auto-EOT Time. */
																	delete_user_option ($user_id, "optimizemember_auto_eot_time");
																/**/
																$pr_times = get_user_option ("optimizemember_paid_registration_times", $user_id);
																$pr_times["level"] = (!$pr_times["level"]) ? time () : $pr_times["level"]; /* Preserves existing. */
																$pr_times["level" . $paypal["level"]] = (!$pr_times["level" . $paypal["level"]]) ? time () : $pr_times["level" . $paypal["level"]];
																update_user_option ($user_id, "optimizemember_paid_registration_times", $pr_times); /* Update now. */
																/**/
																c_ws_plugin__optimizemember_user_notes::clear_user_note_lines ($user_id, "/^Demoted by optimizeMember\:/");
																/**/
																$paypal["optimizemember_log"][] = "optimizeMember Level/Capabilities updated w/ advanced update routines.";
																/**/
																c_ws_plugin__optimizemember_email_configs::email_config () . wp_mail ($paypal["payer_email"], apply_filters ("ws_plugin__optimizemember_modification_email_sbj", _x ("Thank you! Your account has been updated.", "s2member-front", "s2member"), get_defined_vars ()), apply_filters ("ws_plugin__optimizemember_modification_email_msg", _x ("Thank you! You've been updated to:", "s2member-front", "s2member") . "\n" . $paypal["item_name"] . "\n\n" . _x ("Please log back in now.", "s2member-front", "s2member") . "\n" . wp_login_url (), get_defined_vars ()), "From: \"" . preg_replace ('/"/', "'", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["reg_email_from_name"]) . "\" <" . $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["reg_email_from_email"] . ">\r\nContent-Type: text/plain; charset=utf-8") . c_ws_plugin__optimizemember_email_configs::email_config_release ();
																/**/
																$paypal["optimizemember_log"][] = "Modification Confirmation Email sent to Customer, with a URL that provides them with a way to log back in.";
																/**/
																if ($processing && $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["modification_notification_urls"] && is_array ($cv = preg_split ("/\|/", $paypal["custom"])))
																	{
																		foreach (preg_split ("/[\r\n\t]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["modification_notification_urls"]) as $url)
																			/**/
																			if (($url = preg_replace ("/%%cv([0-9]+)%%/ei", 'urlencode(trim($cv[$1]))', $url)) && ($url = preg_replace ("/%%subscr_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["subscr_id"])), $url)))
																				if (($url = preg_replace ("/%%initial%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["initial"])), $url)) && ($url = preg_replace ("/%%regular%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["regular"])), $url)) && ($url = preg_replace ("/%%recurring%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["recurring"])), $url)))
																					if (($url = preg_replace ("/%%initial_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["initial_term"])), $url)) && ($url = preg_replace ("/%%regular_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["regular_term"])), $url)))
																						if (($url = preg_replace ("/%%item_number%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["item_number"])), $url)) && ($url = preg_replace ("/%%item_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["item_name"])), $url)))
																							if (($url = preg_replace ("/%%first_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["first_name"])), $url)) && ($url = preg_replace ("/%%last_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["last_name"])), $url)))
																								if (($url = preg_replace ("/%%full_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode (trim ($paypal["first_name"] . " " . $paypal["last_name"]))), $url)))
																									if (($url = preg_replace ("/%%payer_email%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["payer_email"])), $url)))
																										/**/
																										if (($url = preg_replace ("/%%user_first_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($user->first_name)), $url)) && ($url = preg_replace ("/%%user_last_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($user->last_name)), $url)))
																											if (($url = preg_replace ("/%%user_full_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode (trim ($user->first_name . " " . $user->last_name))), $url)))
																												if (($url = preg_replace ("/%%user_email%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($user->user_email)), $url)))
																													if (($url = preg_replace ("/%%user_login%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($user->user_login)), $url)))
																														if (($url = preg_replace ("/%%user_ip%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($user_reg_ip)), $url)))
																															if (($url = preg_replace ("/%%user_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($user_id)), $url)))
																																{
																																	if (is_array ($fields) && !empty ($fields))
																																		foreach ($fields as $var => $val) /* Custom Registration/Profile Fields. */
																																			if (!($url = preg_replace ("/%%" . preg_quote ($var, "/") . "%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode (maybe_serialize ($val))), $url)))
																																				break;
																																	/**/
																																	if (($url = trim (preg_replace ("/%%(.+?)%%/i", "", $url))))
																																		c_ws_plugin__optimizemember_utils_urls::remote ($url);
																																}
																		/**/
																		$paypal["optimizemember_log"][] = "Modification Notification URLs have been processed.";
																	}
																/**/
																if ($processing && $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["modification_notification_recipients"] && is_array ($cv = preg_split ("/\|/", $paypal["custom"])))
																	{
																		$msg = $sbj = "( optimizeMember / API Notification Email ) - Modification";
																		$msg .= "\n\n"; /* Spacing in the message body. */
																		/**/
																		$msg .= "subscr_id: %%subscr_id%%\n";
																		$msg .= "initial: %%initial%%\n";
																		$msg .= "regular: %%regular%%\n";
																		$msg .= "recurring: %%recurring%%\n";
																		$msg .= "initial_term: %%initial_term%%\n";
																		$msg .= "regular_term: %%regular_term%%\n";
																		$msg .= "item_number: %%item_number%%\n";
																		$msg .= "item_name: %%item_name%%\n";
																		$msg .= "first_name: %%first_name%%\n";
																		$msg .= "last_name: %%last_name%%\n";
																		$msg .= "full_name: %%full_name%%\n";
																		$msg .= "payer_email: %%payer_email%%\n";
																		/**/
																		$msg .= "user_first_name: %%user_first_name%%\n";
																		$msg .= "user_last_name: %%user_last_name%%\n";
																		$msg .= "user_full_name: %%user_full_name%%\n";
																		$msg .= "user_email: %%user_email%%\n";
																		$msg .= "user_login: %%user_login%%\n";
																		$msg .= "user_ip: %%user_ip%%\n";
																		$msg .= "user_id: %%user_id%%\n";
																		/**/
																		if (is_array ($fields) && !empty ($fields))
																			foreach ($fields as $var => $val)
																				$msg .= $var . ": %%" . $var . "%%\n";
																		/**/
																		$msg .= "cv0: %%cv0%%\n";
																		$msg .= "cv1: %%cv1%%\n";
																		$msg .= "cv2: %%cv2%%\n";
																		$msg .= "cv3: %%cv3%%\n";
																		$msg .= "cv4: %%cv4%%\n";
																		$msg .= "cv5: %%cv5%%\n";
																		$msg .= "cv6: %%cv6%%\n";
																		$msg .= "cv7: %%cv7%%\n";
																		$msg .= "cv8: %%cv8%%\n";
																		$msg .= "cv9: %%cv9%%";
																		/**/
																		if (($msg = preg_replace ("/%%cv([0-9]+)%%/ei", 'trim($cv[$1])', $msg)) && ($msg = preg_replace ("/%%subscr_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["subscr_id"]), $msg)))
																			if (($msg = preg_replace ("/%%initial%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["initial"]), $msg)) && ($msg = preg_replace ("/%%regular%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["regular"]), $msg)) && ($msg = preg_replace ("/%%recurring%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["recurring"]), $msg)))
																				if (($msg = preg_replace ("/%%initial_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["initial_term"]), $msg)) && ($msg = preg_replace ("/%%regular_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["regular_term"]), $msg)))
																					if (($msg = preg_replace ("/%%item_number%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["item_number"]), $msg)) && ($msg = preg_replace ("/%%item_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["item_name"]), $msg)))
																						if (($msg = preg_replace ("/%%first_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["first_name"]), $msg)) && ($msg = preg_replace ("/%%last_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["last_name"]), $msg)))
																							if (($msg = preg_replace ("/%%full_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(trim ($paypal["first_name"] . " " . $paypal["last_name"])), $msg)))
																								if (($msg = preg_replace ("/%%payer_email%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["payer_email"]), $msg)))
																									/**/
																									if (($msg = preg_replace ("/%%user_first_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($user->first_name), $msg)) && ($msg = preg_replace ("/%%user_last_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($user->last_name), $msg)))
																										if (($msg = preg_replace ("/%%user_full_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(trim ($user->first_name . " " . $user->last_name)), $msg)))
																											if (($msg = preg_replace ("/%%user_email%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($user->user_email), $msg)))
																												if (($msg = preg_replace ("/%%user_login%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($user->user_login), $msg)))
																													if (($msg = preg_replace ("/%%user_ip%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($user_reg_ip), $msg)))
																														if (($msg = preg_replace ("/%%user_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($user_id), $msg)))
																															{
																																if (is_array ($fields) && !empty ($fields))
																																	foreach ($fields as $var => $val) /* Custom Registration/Profile Fields. */
																																		if (!($msg = preg_replace ("/%%" . preg_quote ($var, "/") . "%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(maybe_serialize ($val)), $msg)))
																																			break;
																																/**/
																																if ($sbj && ($msg = trim (preg_replace ("/%%(.+?)%%/i", "", $msg)))) /* Still have a ``$sbj`` and a ``$msg``? */
																																	/**/
																																	foreach (c_ws_plugin__optimizemember_utils_strings::parse_emails ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["modification_notification_recipients"]) as $recipient)
																																		wp_mail ($recipient, apply_filters ("ws_plugin__optimizemember_modification_notification_email_sbj", $sbj, get_defined_vars ()), apply_filters ("ws_plugin__optimizemember_modification_notification_email_msg", $msg, get_defined_vars ()), "Content-Type: text/plain; charset=utf-8");
																															}
																		/**/
																		$paypal["optimizemember_log"][] = "Modification Notification Emails have been processed.";
																	}
																/**/
																if ($processing && ($code = $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["modification_tracking_codes"]) && is_array ($cv = preg_split ("/\|/", $paypal["custom"])))
																	{
																		if (($code = preg_replace ("/%%cv([0-9]+)%%/ei", 'trim($cv[$1])', $code)) && ($code = preg_replace ("/%%subscr_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["subscr_id"]), $code)))
																			if (($code = preg_replace ("/%%initial%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["initial"]), $code)) && ($code = preg_replace ("/%%regular%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["regular"]), $code)) && ($code = preg_replace ("/%%recurring%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["recurring"]), $code)))
																				if (($code = preg_replace ("/%%initial_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["initial_term"]), $code)) && ($code = preg_replace ("/%%regular_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["regular_term"]), $code)))
																					if (($code = preg_replace ("/%%item_number%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["item_number"]), $code)) && ($code = preg_replace ("/%%item_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["item_name"]), $code)))
																						if (($code = preg_replace ("/%%first_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["first_name"]), $code)) && ($code = preg_replace ("/%%last_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["last_name"]), $code)))
																							if (($code = preg_replace ("/%%full_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(trim ($paypal["first_name"] . " " . $paypal["last_name"])), $code)))
																								if (($code = preg_replace ("/%%payer_email%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["payer_email"]), $code)))
																									{
																										if (($code = preg_replace ("/%%user_first_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($user->first_name), $code)) && ($code = preg_replace ("/%%user_last_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($user->last_name), $code)))
																											if (($code = preg_replace ("/%%user_full_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(trim ($user->first_name . " " . $user->last_name)), $code)))
																												if (($code = preg_replace ("/%%user_email%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($user->user_email), $code)))
																													if (($code = preg_replace ("/%%user_login%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($user->user_login), $code)))
																														if (($code = preg_replace ("/%%user_ip%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($user_reg_ip), $code)))
																															if (($code = preg_replace ("/%%user_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($user_id), $code)))
																																{
																																	if (is_array ($fields) && !empty ($fields))
																																		foreach ($fields as $var => $val) /* Custom Registration/Profile Fields. */
																																			if (!($code = preg_replace ("/%%" . preg_quote ($var, "/") . "%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(maybe_serialize ($val)), $code)))
																																				break;
																																	/**/
																																	if (($code = trim (preg_replace ("/%%(.+?)%%/i", "", $code)))) /* This gets stored into a Transient Queue. */
																																		{
																																			$paypal["optimizemember_log"][] = "Storing Modification Tracking Codes into a Transient Queue. These will be processed on-site.";
																																			set_transient ("s2m_" . md5 ("optimizemember_transient_modification_tracking_codes_" . $paypal["subscr_id"]), $code, 43200);
																																		}
																																}
																									}
																	}
																/**/
																eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
																do_action ("ws_plugin__optimizemember_during_paypal_notify_during_subscr_signup_w_update_vars", get_defined_vars ());
																unset ($__refs, $__v); /* Unset defined __refs, __v. */
															}
														else
															$paypal["optimizemember_log"][] = "Unable to modify Subscription. The existing User ID is associated with an Administrator. Stopping here. Otherwise, an Administrator could lose access.";
													}
												else
													$paypal["optimizemember_log"][] = "Unable to modify Subscription. Could not get the existing User ID from the DB. Please check the `on0` and `os0` variables in your Button Code.";
												/**/
												eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
												do_action ("ws_plugin__optimizemember_during_paypal_notify_after_subscr_signup_w_update_vars", get_defined_vars ());
												unset ($__refs, $__v); /* Unset defined __refs, __v. */
											}
										/*
										New Subscription. Normal Subscription signup, we are not updating anything for a past Subscription.
										*/
										else /* Else this is a normal Subscription signup, we are not updating anything. */
											{
												eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
												do_action ("ws_plugin__optimizemember_during_paypal_notify_before_subscr_signup_wo_update_vars", get_defined_vars ());
												unset ($__refs, $__v); /* Unset defined __refs, __v. */
												/**/
												$paypal["optimizemember_log"][] = "optimizeMember `txn_type` identified as ( `web_accept|subscr_signup` ) w/o update vars.";
												/**/
												if (($registration_url = c_ws_plugin__optimizemember_register_access::register_link_gen ($paypal["subscr_gateway"], $paypal["subscr_id"], $paypal["custom"], $paypal["item_number"])) && is_array ($cv = preg_split ("/\|/", $paypal["custom"])))
													{
                                                        $paypal["optimizemember_log"][] = "DEBUG: Registration link generated: " . $registration_url . ".";
														$processing = $during = true; /* Yes, we ARE processing this. */
														/**/
														$sbj = preg_replace ("/%%registration_url%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($registration_url), $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"][(($_GET["optimizemember_paypal_proxy"] && preg_match ("/pro-emails/", $_GET["optimizemember_paypal_proxy_use"])) ? "pro_" : "") . "signup_email_subject"]);
														$msg = preg_replace ("/%%registration_url%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($registration_url), $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"][(($_GET["optimizemember_paypal_proxy"] && preg_match ("/pro-emails/", $_GET["optimizemember_paypal_proxy_use"])) ? "pro_" : "") . "signup_email_message"]);
														$rec = preg_replace ("/%%registration_url%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($registration_url), $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"][(($_GET["optimizemember_paypal_proxy"] && preg_match ("/pro-emails/", $_GET["optimizemember_paypal_proxy_use"])) ? "pro_" : "") . "signup_email_recipients"]);
                                                        $paypal["optimizemember_log"][] = "DEBUG: Signup Email Recipients: " . $rec . ".";
														/**/
														if (($rec = preg_replace ("/%%cv([0-9]+)%%/ei", 'trim($cv[$1])', $rec)) && ($rec = preg_replace ("/%%subscr_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["subscr_id"]), $rec)))
															if (($rec = preg_replace ("/%%initial%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["initial"]), $rec)) && ($rec = preg_replace ("/%%regular%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["regular"]), $rec)))
																if (($rec = preg_replace ("/%%initial_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["initial_term"]), $rec)) && ($rec = preg_replace ("/%%regular_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["regular_term"]), $rec)))
																	if (($rec = preg_replace ("/%%initial_cycle%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(c_ws_plugin__optimizemember_utils_time::period_term ($paypal["initial_term"])), $rec)) && ($rec = preg_replace ("/%%regular_cycle%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(c_ws_plugin__optimizemember_utils_time::period_term ($paypal["regular_term"], $paypal["recurring"])), $rec)))
																		if (($rec = preg_replace ("/%%recurring%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["recurring"]), $rec)) && ($rec = preg_replace ("/%%recurring\/regular_cycle%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs((($paypal["recurring"]) ? $paypal["recurring"] . " / " . c_ws_plugin__optimizemember_utils_time::period_term ($paypal["regular_term"], true) : "0 / non-recurring")), $rec)))
																			if (($rec = preg_replace ("/%%item_number%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["item_number"]), $rec)) && ($rec = preg_replace ("/%%item_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["item_name"]), $rec)))
																				if (($rec = preg_replace ("/%%first_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_dq (c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["first_name"])), $rec)) && ($rec = preg_replace ("/%%last_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_dq (c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["last_name"])), $rec)))
																					if (($rec = preg_replace ("/%%full_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_dq (c_ws_plugin__optimizemember_utils_strings::esc_refs(trim ($paypal["first_name"] . " " . $paypal["last_name"]))), $rec))) /* **NOTE** c_ws_plugin__optimizemember_utils_strings::esc_dq() is applied here. ( ex. "N\"ame" <email> ). */
																						if (($rec = preg_replace ("/%%payer_email%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["payer_email"]), $rec)))
																							if (($rec = preg_replace ("/%%user_ip%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["ip"]), $rec)))
																								/**/
																								if (($sbj = preg_replace ("/%%cv([0-9]+)%%/ei", 'trim($cv[$1])', $sbj)) && ($sbj = preg_replace ("/%%subscr_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["subscr_id"]), $sbj)))
																									if (($sbj = preg_replace ("/%%initial%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["initial"]), $sbj)) && ($sbj = preg_replace ("/%%regular%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["regular"]), $sbj)))
																										if (($sbj = preg_replace ("/%%initial_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["initial_term"]), $sbj)) && ($sbj = preg_replace ("/%%regular_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["regular_term"]), $sbj)))
																											if (($sbj = preg_replace ("/%%initial_cycle%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(c_ws_plugin__optimizemember_utils_time::period_term ($paypal["initial_term"])), $sbj)) && ($sbj = preg_replace ("/%%regular_cycle%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(c_ws_plugin__optimizemember_utils_time::period_term ($paypal["regular_term"], $paypal["recurring"])), $sbj)))
																												if (($sbj = preg_replace ("/%%recurring%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["recurring"]), $sbj)) && ($sbj = preg_replace ("/%%recurring\/regular_cycle%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs((($paypal["recurring"]) ? $paypal["recurring"] . " / " . c_ws_plugin__optimizemember_utils_time::period_term ($paypal["regular_term"], true) : "0 / non-recurring")), $sbj)))
																													if (($sbj = preg_replace ("/%%item_number%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["item_number"]), $sbj)) && ($sbj = preg_replace ("/%%item_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["item_name"]), $sbj)))
																														if (($sbj = preg_replace ("/%%first_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["first_name"]), $sbj)) && ($sbj = preg_replace ("/%%last_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["last_name"]), $sbj)))
																															if (($sbj = preg_replace ("/%%full_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(trim ($paypal["first_name"] . " " . $paypal["last_name"])), $sbj)))
																																if (($sbj = preg_replace ("/%%payer_email%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["payer_email"]), $sbj)))
																																	if (($sbj = preg_replace ("/%%user_ip%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["ip"]), $sbj)))
																																		/**/
																																		if (($msg = preg_replace ("/%%cv([0-9]+)%%/ei", 'trim($cv[$1])', $msg)) && ($msg = preg_replace ("/%%subscr_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["subscr_id"]), $msg)))
																																			if (($msg = preg_replace ("/%%initial%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["initial"]), $msg)) && ($msg = preg_replace ("/%%regular%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["regular"]), $msg)))
																																				if (($msg = preg_replace ("/%%initial_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["initial_term"]), $msg)) && ($msg = preg_replace ("/%%regular_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["regular_term"]), $msg)))
																																					if (($msg = preg_replace ("/%%initial_cycle%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(c_ws_plugin__optimizemember_utils_time::period_term ($paypal["initial_term"])), $msg)) && ($msg = preg_replace ("/%%regular_cycle%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(c_ws_plugin__optimizemember_utils_time::period_term ($paypal["regular_term"], $paypal["recurring"])), $msg)))
																																						if (($msg = preg_replace ("/%%recurring%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["recurring"]), $msg)) && ($msg = preg_replace ("/%%recurring\/regular_cycle%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs((($paypal["recurring"]) ? $paypal["recurring"] . " / " . c_ws_plugin__optimizemember_utils_time::period_term ($paypal["regular_term"], true) : "0 / non-recurring")), $msg)))
																																							if (($msg = preg_replace ("/%%item_number%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["item_number"]), $msg)) && ($msg = preg_replace ("/%%item_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["item_name"]), $msg)))
																																								if (($msg = preg_replace ("/%%first_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["first_name"]), $msg)) && ($msg = preg_replace ("/%%last_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["last_name"]), $msg)))
																																									if (($msg = preg_replace ("/%%full_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(trim ($paypal["first_name"] . " " . $paypal["last_name"])), $msg)))
																																										if (($msg = preg_replace ("/%%payer_email%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["payer_email"]), $msg)))
																																											if (($msg = preg_replace ("/%%user_ip%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["ip"]), $msg)))
																																												/**/
                                                                                                                                                                                $paypal["optimizemember_log"][] = "DEBUG: Rec before final if: " . $rec . ".";
																																												if (($rec = trim (preg_replace ("/%%(.+?)%%/i", "", $rec))) && ($sbj = trim (preg_replace ("/%%(.+?)%%/i", "", $sbj))) && ($msg = trim (preg_replace ("/%%(.+?)%%/i", "", $msg))))
																																													{
                                                                                                                                                                                        $paypal["optimizemember_log"][] = "DEBUG: Before foreach and sending emails: " . $rec . ".";
																																														foreach (c_ws_plugin__optimizemember_utils_strings::parse_emails ($rec) as $recipient) /* Go through a possible list of recipients. */
                                                                                                                                                                                            $paypal["optimizemember_log"][] = "DEBUG: SENDING email to: " . $recipient . ".";
																																															c_ws_plugin__optimizemember_email_configs::email_config () . wp_mail ($recipient, apply_filters ("ws_plugin__optimizemember_signup_email_sbj", $sbj, get_defined_vars ()), apply_filters ("ws_plugin__optimizemember_signup_email_msg", $msg, get_defined_vars ()), "From: \"" . preg_replace ('/"/', "'", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["reg_email_from_name"]) . "\" <" . $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["reg_email_from_email"] . ">\r\nContent-Type: text/plain; charset=utf-8") . c_ws_plugin__optimizemember_email_configs::email_config_release ();
																																														/**/
																																														$paypal["optimizemember_log"][] = "Signup Confirmation Email sent to: " . $rec . ".";
																																													}
														/**/
														if ($processing && $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["signup_notification_urls"] && is_array ($cv = preg_split ("/\|/", $paypal["custom"])))
															{
																foreach (preg_split ("/[\r\n\t]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["signup_notification_urls"]) as $url)
																	/**/
																	if (($url = preg_replace ("/%%cv([0-9]+)%%/ei", 'urlencode(trim($cv[$1]))', $url)) && ($url = preg_replace ("/%%subscr_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["subscr_id"])), $url)))
																		if (($url = preg_replace ("/%%initial%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["initial"])), $url)) && ($url = preg_replace ("/%%regular%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["regular"])), $url)) && ($url = preg_replace ("/%%recurring%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["recurring"])), $url)))
																			if (($url = preg_replace ("/%%initial_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["initial_term"])), $url)) && ($url = preg_replace ("/%%regular_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["regular_term"])), $url)))
																				if (($url = preg_replace ("/%%item_number%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["item_number"])), $url)) && ($url = preg_replace ("/%%item_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["item_name"])), $url)))
																					if (($url = preg_replace ("/%%first_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["first_name"])), $url)) && ($url = preg_replace ("/%%last_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["last_name"])), $url)))
																						if (($url = preg_replace ("/%%full_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode (trim ($paypal["first_name"] . " " . $paypal["last_name"]))), $url)))
																							if (($url = preg_replace ("/%%payer_email%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["payer_email"])), $url)))
																								if (($url = preg_replace ("/%%user_ip%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["ip"])), $url)))
																									/**/
																									if (($url = trim (preg_replace ("/%%(.+?)%%/i", "", $url))))
																										c_ws_plugin__optimizemember_utils_urls::remote ($url);
																/**/
																$paypal["optimizemember_log"][] = "Signup Notification URLs have been processed.";
															}
														/**/
														if ($processing && $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["signup_notification_recipients"] && is_array ($cv = preg_split ("/\|/", $paypal["custom"])))
															{
																$msg = $sbj = "( optimizeMember / API Notification Email ) - Signup";
																$msg .= "\n\n"; /* Spacing in the message body. */
																/**/
																$msg .= "subscr_id: %%subscr_id%%\n";
																$msg .= "initial: %%initial%%\n";
																$msg .= "regular: %%regular%%\n";
																$msg .= "recurring: %%recurring%%\n";
																$msg .= "initial_term: %%initial_term%%\n";
																$msg .= "regular_term: %%regular_term%%\n";
																$msg .= "item_number: %%item_number%%\n";
																$msg .= "item_name: %%item_name%%\n";
																$msg .= "first_name: %%first_name%%\n";
																$msg .= "last_name: %%last_name%%\n";
																$msg .= "full_name: %%full_name%%\n";
																$msg .= "payer_email: %%payer_email%%\n";
																$msg .= "user_ip: %%user_ip%%\n";
																/**/
																$msg .= "cv0: %%cv0%%\n";
																$msg .= "cv1: %%cv1%%\n";
																$msg .= "cv2: %%cv2%%\n";
																$msg .= "cv3: %%cv3%%\n";
																$msg .= "cv4: %%cv4%%\n";
																$msg .= "cv5: %%cv5%%\n";
																$msg .= "cv6: %%cv6%%\n";
																$msg .= "cv7: %%cv7%%\n";
																$msg .= "cv8: %%cv8%%\n";
																$msg .= "cv9: %%cv9%%";
																/**/
																if (($msg = preg_replace ("/%%cv([0-9]+)%%/ei", 'trim($cv[$1])', $msg)) && ($msg = preg_replace ("/%%subscr_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["subscr_id"]), $msg)))
																	if (($msg = preg_replace ("/%%initial%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["initial"]), $msg)) && ($msg = preg_replace ("/%%regular%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["regular"]), $msg)) && ($msg = preg_replace ("/%%recurring%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["recurring"]), $msg)))
																		if (($msg = preg_replace ("/%%initial_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["initial_term"]), $msg)) && ($msg = preg_replace ("/%%regular_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["regular_term"]), $msg)))
																			if (($msg = preg_replace ("/%%item_number%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["item_number"]), $msg)) && ($msg = preg_replace ("/%%item_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["item_name"]), $msg)))
																				if (($msg = preg_replace ("/%%first_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["first_name"]), $msg)) && ($msg = preg_replace ("/%%last_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["last_name"]), $msg)))
																					if (($msg = preg_replace ("/%%full_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(trim ($paypal["first_name"] . " " . $paypal["last_name"])), $msg)))
																						if (($msg = preg_replace ("/%%payer_email%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["payer_email"]), $msg)))
																							if (($msg = preg_replace ("/%%user_ip%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["ip"]), $msg)))
																								/**/
																								if ($sbj && ($msg = trim (preg_replace ("/%%(.+?)%%/i", "", $msg)))) /* Still have a ``$sbj`` and a ``$msg``? */
																									/**/
																									foreach (c_ws_plugin__optimizemember_utils_strings::parse_emails ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["signup_notification_recipients"]) as $recipient)
																										wp_mail ($recipient, apply_filters ("ws_plugin__optimizemember_signup_notification_email_sbj", $sbj, get_defined_vars ()), apply_filters ("ws_plugin__optimizemember_signup_notification_email_msg", $msg, get_defined_vars ()), "Content-Type: text/plain; charset=utf-8");
																/**/
																$paypal["optimizemember_log"][] = "Signup Notification Emails have been processed.";
															}
														/**/
														if ($processing && ($code = $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["signup_tracking_codes"]) && is_array ($cv = preg_split ("/\|/", $paypal["custom"])))
															{
																if (($code = preg_replace ("/%%cv([0-9]+)%%/ei", 'trim($cv[$1])', $code)) && ($code = preg_replace ("/%%subscr_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["subscr_id"]), $code)))
																	if (($code = preg_replace ("/%%initial%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["initial"]), $code)) && ($code = preg_replace ("/%%regular%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["regular"]), $code)) && ($code = preg_replace ("/%%recurring%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["recurring"]), $code)))
																		if (($code = preg_replace ("/%%initial_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["initial_term"]), $code)) && ($code = preg_replace ("/%%regular_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["regular_term"]), $code)))
																			if (($code = preg_replace ("/%%item_number%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["item_number"]), $code)) && ($code = preg_replace ("/%%item_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["item_name"]), $code)))
																				if (($code = preg_replace ("/%%first_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["first_name"]), $code)) && ($code = preg_replace ("/%%last_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["last_name"]), $code)))
																					if (($code = preg_replace ("/%%full_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(trim ($paypal["first_name"] . " " . $paypal["last_name"])), $code)))
																						if (($code = preg_replace ("/%%payer_email%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["payer_email"]), $code)))
																							if (($code = preg_replace ("/%%user_ip%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["ip"]), $code)))
																								/**/
																								if (($code = trim (preg_replace ("/%%(.+?)%%/i", "", $code)))) /* This gets stored into a Transient Queue. */
																									{
																										$paypal["optimizemember_log"][] = "Storing Signup Tracking Codes into a Transient Queue. These will be processed on-site.";
																										set_transient ("s2m_" . md5 ("optimizemember_transient_signup_tracking_codes_" . $paypal["subscr_id"]), $code, 43200);
																									}
															}
														/**/
														eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
														do_action ("ws_plugin__optimizemember_during_paypal_notify_during_subscr_signup_wo_update_vars", get_defined_vars ());
														unset ($__refs, $__v); /* Unset defined __refs, __v. */
													}
												else
													$paypal["optimizemember_log"][] = "Unable to generate Registration URL for Membership Access. Possible data corruption within the IPN response.";
												/**/
												eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
												do_action ("ws_plugin__optimizemember_during_paypal_notify_after_subscr_signup_wo_update_vars", get_defined_vars ());
												unset ($__refs, $__v); /* Unset defined __refs, __v. */
											}
										/**/
										if ($processing && $_GET["optimizemember_paypal_proxy"] && ($url = $_GET["optimizemember_paypal_proxy_return_url"]) && is_array ($cv = preg_split ("/\|/", $paypal["custom"]))) /* A Proxy is requesting a Return URL? */
											{
												if (($user_id && is_object ($user) && $user->ID) || (($user_id = c_ws_plugin__optimizemember_utils_users::get_user_id_with ($paypal["subscr_id"], $paypal["option_selection1"])) && is_object ($user = new WP_User ($user_id)) && $user->ID))
													{
														$fields = get_user_option ("optimizemember_custom_fields", $user_id); /* These will be needed in the routines below. */
														$user_reg_ip = get_user_option ("optimizemember_registration_ip", $user_id); /* Original IP during Registration. */
														/**/
														if (($url = preg_replace ("/%%cv([0-9]+)%%/ei", 'urlencode(trim($cv[$1]))', $url)) && ($url = preg_replace ("/%%subscr_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["subscr_id"])), $url)))
															if (($url = preg_replace ("/%%initial%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["initial"])), $url)) && ($url = preg_replace ("/%%regular%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["regular"])), $url)) && ($url = preg_replace ("/%%recurring%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["recurring"])), $url)))
																if (($url = preg_replace ("/%%initial_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["initial_term"])), $url)) && ($url = preg_replace ("/%%regular_term%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["regular_term"])), $url)))
																	if (($url = preg_replace ("/%%item_number%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["item_number"])), $url)) && ($url = preg_replace ("/%%item_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["item_name"])), $url)))
																		if (($url = preg_replace ("/%%first_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["first_name"])), $url)) && ($url = preg_replace ("/%%last_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["last_name"])), $url)))
																			if (($url = preg_replace ("/%%full_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode (trim ($paypal["first_name"] . " " . $paypal["last_name"]))), $url)))
																				if (($url = preg_replace ("/%%payer_email%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["payer_email"])), $url)))
																					if (($url = preg_replace ("/%%modification%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ((int)$modifying)), $url)))
																						{
																							if (($url = preg_replace ("/%%user_first_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($user->first_name)), $url)) && ($url = preg_replace ("/%%user_last_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($user->last_name)), $url)))
																								if (($url = preg_replace ("/%%user_full_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode (trim ($user->first_name . " " . $user->last_name))), $url)))
																									if (($url = preg_replace ("/%%user_email%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($user->user_email)), $url)))
																										if (($url = preg_replace ("/%%user_login%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($user->user_login)), $url)))
																											if (($url = preg_replace ("/%%user_ip%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($user_reg_ip)), $url)))
																												if (($url = preg_replace ("/%%user_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($user_id)), $url)))
																													{
																														if (is_array ($fields) && !empty ($fields))
																															foreach ($fields as $var => $val) /* Custom Registration/Profile Fields. */
																																if (!($url = preg_replace ("/%%" . preg_quote ($var, "/") . "%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode (maybe_serialize ($val))), $url)))
																																	break;
																														/**/
																														if (($url = trim ($url))) /* Preserve remaining replacements. */
																															/* Because the parent routine may perform replacements too. */
																															$paypal["optimizemember_paypal_proxy_return_url"] = $url;
																													}
																						}
													}
												/**/
												$paypal["optimizemember_log"][] = "Subscr. Return ( `modification=" . (int)$modifying . "` ), a Proxy Return URL is ready.";
											}
										/**/
										if ($processing /* Process a payment now? Special cases for web_accept and/or Proxy requests with `subscr-signup-as-subscr-payment`. */
										&& (preg_match ("/^web_accept$/i", $paypal["txn_type"]) || ($_GET["optimizemember_paypal_proxy"] && preg_match ("/subscr-signup-as-subscr-payment/", $_GET["optimizemember_paypal_proxy_use"]) && $paypal["txn_id"] && $paypal["mc_gross"] > 0))/**/
										&& (($user_id && is_object ($user) && $user->ID) || (($user_id = c_ws_plugin__optimizemember_utils_users::get_user_id_with ($paypal["subscr_id"], $paypal["option_selection1"])) && is_object ($user = new WP_User ($user_id)) && $user->ID)))
											{
												$paypal["optimizemember_log"][] = "User exists. Handling `payment` for Subscription via ( `" . ((preg_match ("/^web_accept$/i", $paypal["txn_type"])) ? "web_accept" : "subscr-signup-as-subscr-payment") . "` ).";
												/**/
												$pr_times = get_user_option ("optimizemember_paid_registration_times", $user_id);
												$pr_times["level"] = (!$pr_times["level"]) ? time () : $pr_times["level"]; /* Preserves existing. */
												$pr_times["level" . $paypal["level"]] = (!$pr_times["level" . $paypal["level"]]) ? time () : $pr_times["level" . $paypal["level"]];
												update_user_option ($user_id, "optimizemember_paid_registration_times", $pr_times); /* Update now. */
												/**/
												if (!get_user_option ("optimizemember_first_payment_txn_id", $user_id)) /* 1st payment? */
													update_user_option ($user_id, "optimizemember_first_payment_txn_id", $paypal["txn_id"]);
												/**/
												update_user_option ($user_id, "optimizemember_last_payment_time", time ()); /* Update the last payment time. */
												/**/
												$fields = get_user_option ("optimizemember_custom_fields", $user_id); /* These will be needed in the routines below. */
												$user_reg_ip = get_user_option ("optimizemember_registration_ip", $user_id); /* Original IP during Registration. */
												/**/
												if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["payment_notification_urls"] && is_array ($cv = preg_split ("/\|/", $paypal["custom"])))
													{
														foreach (preg_split ("/[\r\n\t]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["payment_notification_urls"]) as $url)
															/**/
															if (($url = preg_replace ("/%%cv([0-9]+)%%/ei", 'urlencode(trim($cv[$1]))', $url)) && ($url = preg_replace ("/%%subscr_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["subscr_id"])), $url)))
																if (($url = preg_replace ("/%%amount%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["mc_gross"])), $url)) && ($url = preg_replace ("/%%txn_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["txn_id"])), $url)))
																	if (($url = preg_replace ("/%%item_number%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["item_number"])), $url)) && ($url = preg_replace ("/%%item_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["item_name"])), $url)))
																		if (($url = preg_replace ("/%%first_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["first_name"])), $url)) && ($url = preg_replace ("/%%last_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["last_name"])), $url)))
																			if (($url = preg_replace ("/%%full_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode (trim ($paypal["first_name"] . " " . $paypal["last_name"]))), $url)))
																				if (($url = preg_replace ("/%%payer_email%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($paypal["payer_email"])), $url)))
																					{
																						if (($url = preg_replace ("/%%user_first_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($user->first_name)), $url)) && ($url = preg_replace ("/%%user_last_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($user->last_name)), $url)))
																							if (($url = preg_replace ("/%%user_full_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode (trim ($user->first_name . " " . $user->last_name))), $url)))
																								if (($url = preg_replace ("/%%user_email%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($user->user_email)), $url)))
																									if (($url = preg_replace ("/%%user_login%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($user->user_login)), $url)))
																										if (($url = preg_replace ("/%%user_ip%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($user_reg_ip)), $url)))
																											if (($url = preg_replace ("/%%user_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode ($user_id)), $url)))
																												{
																													if (is_array ($fields) && !empty ($fields))
																														foreach ($fields as $var => $val) /* Custom Registration/Profile Fields. */
																															if (!($url = preg_replace ("/%%" . preg_quote ($var, "/") . "%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(urlencode (maybe_serialize ($val))), $url)))
																																break;
																													/**/
																													if (($url = trim (preg_replace ("/%%(.+?)%%/i", "", $url))))
																														c_ws_plugin__optimizemember_utils_urls::remote ($url);
																												}
																					}
														/**/
														$paypal["optimizemember_log"][] = "Payment Notification URLs have been processed.";
													}
												/**/
												if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["payment_notification_recipients"] && is_array ($cv = preg_split ("/\|/", $paypal["custom"])))
													{
														$msg = $sbj = "( optimizeMember / API Notification Email ) - Payment";
														$msg .= "\n\n"; /* Spacing in the message body. */
														/**/
														$msg .= "subscr_id: %%subscr_id%%\n";
														$msg .= "amount: %%amount%%\n";
														$msg .= "txn_id: %%txn_id%%\n";
														$msg .= "item_number: %%item_number%%\n";
														$msg .= "item_name: %%item_name%%\n";
														$msg .= "first_name: %%first_name%%\n";
														$msg .= "last_name: %%last_name%%\n";
														$msg .= "full_name: %%full_name%%\n";
														$msg .= "payer_email: %%payer_email%%\n";
														/**/
														$msg .= "user_first_name: %%user_first_name%%\n";
														$msg .= "user_last_name: %%user_last_name%%\n";
														$msg .= "user_full_name: %%user_full_name%%\n";
														$msg .= "user_email: %%user_email%%\n";
														$msg .= "user_login: %%user_login%%\n";
														$msg .= "user_ip: %%user_ip%%\n";
														$msg .= "user_id: %%user_id%%\n";
														/**/
														if (is_array ($fields) && !empty ($fields))
															foreach ($fields as $var => $val)
																$msg .= $var . ": %%" . $var . "%%\n";
														/**/
														$msg .= "cv0: %%cv0%%\n";
														$msg .= "cv1: %%cv1%%\n";
														$msg .= "cv2: %%cv2%%\n";
														$msg .= "cv3: %%cv3%%\n";
														$msg .= "cv4: %%cv4%%\n";
														$msg .= "cv5: %%cv5%%\n";
														$msg .= "cv6: %%cv6%%\n";
														$msg .= "cv7: %%cv7%%\n";
														$msg .= "cv8: %%cv8%%\n";
														$msg .= "cv9: %%cv9%%";
														/**/
														if (($msg = preg_replace ("/%%cv([0-9]+)%%/ei", 'trim($cv[$1])', $msg)) && ($msg = preg_replace ("/%%subscr_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["subscr_id"]), $msg)))
															if (($msg = preg_replace ("/%%amount%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["mc_gross"]), $msg)) && ($msg = preg_replace ("/%%txn_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["txn_id"]), $msg)))
																if (($msg = preg_replace ("/%%item_number%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["item_number"]), $msg)) && ($msg = preg_replace ("/%%item_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["item_name"]), $msg)))
																	if (($msg = preg_replace ("/%%first_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["first_name"]), $msg)) && ($msg = preg_replace ("/%%last_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["last_name"]), $msg)))
																		if (($msg = preg_replace ("/%%full_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(trim ($paypal["first_name"] . " " . $paypal["last_name"])), $msg)))
																			if (($msg = preg_replace ("/%%payer_email%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($paypal["payer_email"]), $msg)))
																				{
																					if (($msg = preg_replace ("/%%user_first_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($user->first_name), $msg)) && ($msg = preg_replace ("/%%user_last_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($user->last_name), $msg)))
																						if (($msg = preg_replace ("/%%user_full_name%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(trim ($user->first_name . " " . $user->last_name)), $msg)))
																							if (($msg = preg_replace ("/%%user_email%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($user->user_email), $msg)))
																								if (($msg = preg_replace ("/%%user_login%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($user->user_login), $msg)))
																									if (($msg = preg_replace ("/%%user_ip%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($user_reg_ip), $msg)))
																										if (($msg = preg_replace ("/%%user_id%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs($user_id), $msg)))
																											{
																												if (is_array ($fields) && !empty ($fields))
																													foreach ($fields as $var => $val) /* Custom Registration/Profile Fields. */
																														if (!($msg = preg_replace ("/%%" . preg_quote ($var, "/") . "%%/i", c_ws_plugin__optimizemember_utils_strings::esc_refs(maybe_serialize ($val)), $msg)))
																															break;
																												/**/
																												if ($sbj && ($msg = trim (preg_replace ("/%%(.+?)%%/i", "", $msg)))) /* Still have a ``$sbj`` and a ``$msg``? */
																													/**/
																													foreach (c_ws_plugin__optimizemember_utils_strings::parse_emails ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["payment_notification_recipients"]) as $recipient)
																														wp_mail ($recipient, apply_filters ("ws_plugin__optimizemember_payment_notification_email_sbj", $sbj, get_defined_vars ()), apply_filters ("ws_plugin__optimizemember_payment_notification_email_msg", $msg, get_defined_vars ()), "Content-Type: text/plain; charset=utf-8");
																											}
																				}
														/**/
														$paypal["optimizemember_log"][] = "Payment Notification Emails have been processed.";
													}
											}
										else if ($processing /* Process a payment now? Special cases for web_accept and/or Proxy requests with `subscr-signup-as-subscr-payment`. */
										&& (preg_match ("/^web_accept$/i", $paypal["txn_type"]) || ($_GET["optimizemember_paypal_proxy"] && preg_match ("/subscr-signup-as-subscr-payment/", $_GET["optimizemember_paypal_proxy_use"]) && $paypal["txn_id"] && $paypal["mc_gross"] > 0)))
											{
												$paypal["optimizemember_log"][] = "Storing `payment` for Subscription via ( `" . ((preg_match ("/^web_accept$/i", $paypal["txn_type"])) ? "web_accept" : "subscr-signup-as-subscr-payment") . "` ).";
												/**/
												$ipn = array ("txn_type" => "subscr_payment"); /* Create a simulated IPN response for txn_type=subscr_payment. */
												/**/
												foreach ($paypal as $var => $val)
													if (in_array ($var, array ("subscr_gateway", "subscr_id", "txn_id", "custom", "invoice", "mc_gross", "mc_currency", "tax", "payer_email", "first_name", "last_name", "item_name", "item_number", "option_name1", "option_selection1", "option_name2", "option_selection2")))
														$ipn[$var] = $val;
												/**/
												$paypal["optimizemember_log"][] = "Creating an IPN response for `subscr_payment`. This will go into a Transient Queue; and be processed during registration.";
												/**/
												set_transient ("s2m_" . md5 ("optimizemember_transient_ipn_subscr_payment_" . $paypal["subscr_id"]), $ipn, 43200);
											}
										/**/
										if ($processing /* Store signup vars now? If the User already exists in the database, we can go ahead and store these right now. */
										&& (($user_id && is_object ($user) && $user->ID) || (($user_id = c_ws_plugin__optimizemember_utils_users::get_user_id_with ($paypal["subscr_id"], $paypal["option_selection1"])) && is_object ($user = new WP_User ($user_id)) && $user->ID)))
											{
												$paypal["optimizemember_log"][] = "Storing IPN signup vars now. These are associated with a User's account record; for future reference.";
												/**/
												update_user_option ($user_id, "optimizemember_ipn_signup_vars", $ipn_signup_vars);
											}
										else if ($processing) /* Otherwise, we can store these into a Transient Queue for registration processing. */
											{
												$paypal["optimizemember_log"][] = "Storing IPN signup vars into a Transient Queue. These will be processed on registration.";
												/**/
												set_transient ("s2m_" . md5 ("optimizemember_transient_ipn_signup_vars_" . $paypal["subscr_id"]), $ipn_signup_vars, 43200);
											}
									}
								else /* Else, this is a duplicate IPN. Must stop here. */
									{
										$paypal["optimizemember_log"][] = "Not processing. Duplicate IPN.";
										$paypal["optimizemember_log"][] = "optimizeMember `txn_type` identified as ( `web_accept|subscr_signup` ).";
										$paypal["optimizemember_log"][] = "Duplicate IPN. Already processed. This IPN will be ignored.";
									}
								/**/
								eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
								do_action ("ws_plugin__optimizemember_during_paypal_notify_after_subscr_signup", get_defined_vars ());
								unset ($__refs, $__v); /* Unset defined __refs, __v. */
								/**/
								return apply_filters ("c_ws_plugin__optimizemember_paypal_notify_in_subscr_or_wa_w_level", $paypal, get_defined_vars ());
							}
						else
							return apply_filters ("c_ws_plugin__optimizemember_paypal_notify_in_subscr_or_wa_w_level", false, get_defined_vars ());
					}
			}
	}
?>