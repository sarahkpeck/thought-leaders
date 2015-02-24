<?php
/**
* Menu page for the optimizeMember plugin ( Integrations page ).
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
* @package optimizeMember\Menu_Pages
* @since 3.0
*/
if(realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/**/
if(!class_exists("c_ws_plugin__optimizemember_menu_page_integrations"))
	{
		/**
		* Menu page for the optimizeMember plugin ( Integrations page ).
		*
		* @package optimizeMember\Menu_Pages
		* @since 110531
		*/
		class c_ws_plugin__optimizemember_menu_page_integrations
			{
				public function __construct()
					{
						echo '<div class="wrap ws-menu-page op-bsw-wizard op-bsw-content">'."\n";
						/**/
						echo '<div class="op-bsw-header">';
							echo '<div class="op-logo"><img src="' . $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["dir_url"]."/images/" . 'logo-optimizepress.png" alt="OptimizePress" height="50" class="animated flipInY"></div>';
						echo '</div>';
						echo '<div class="op-bsw-main-content">';
						echo '<h2>Other optimizeMember Integrations</h2>'."\n";
						/**/
						echo '<table class="ws-menu-page-table">'."\n";
						echo '<tbody class="ws-menu-page-table-tbody">'."\n";
						echo '<tr class="ws-menu-page-table-tr">'."\n";
						echo '<td class="ws-menu-page-table-l">'."\n";
						/**/
						do_action("ws_plugin__optimizemember_during_integrations_page_before_left_sections", get_defined_vars());
						/**/
						if(apply_filters("ws_plugin__optimizemember_during_integrations_page_during_left_sections_display_bbpress", true, get_defined_vars()))
							{
								do_action("ws_plugin__optimizemember_during_integrations_page_during_left_sections_before_bbpress", get_defined_vars());
								/**/
								echo '<div class="ws-menu-page-group" title="bbPress Plugin Integration ( 2.0+ plugin version )" default-state="open">'."\n";
								/**/
								echo '<div class="ws-menu-page-section ws-plugin--optimizemember-bbpress-section">'."\n";
								echo '<h3>bbPress Plugin Integration ( easy peasy )</h3>'."\n";
								echo '<input type="button" value="Update Roles/Capabilities" class="ws-menu-page-right ws-plugin--optimizemember-update-roles-button button" style="min-width:175px;" />'."\n";
								echo '<p>The plugin version of <a href="http://www.optimizepress.com/bbpress-plugin" target="_blank" rel="external">bbPress 2.0+</a> integrates seamlessly with WordPress. If bbPress was already installed when you activated optimizeMember, your optimizeMember Roles/Capabilities are already configured to work in harmony with bbPress. If you didn\'t, you can simply click the "Update Roles/Capabilities" button here. That\'s all it takes. Once your Roles/Capbilities are updated, optimizeMember and bbPress are fully integrated with each other.</p>'."\n";
								/**/
								echo '<div class="ws-menu-page-hr"></div>'."\n";
								/**/
								echo '<h3>bbPress Forums and optimizeMember Roles/Capabilities</h3>'."\n";
								echo '<p>optimizeMember configures your Membership Roles (by default, these include: <em>optimizeMember Level 1</em>, <em>optimizeMember Level 2</em>, <em>optimizeMember Level 3</em>, <em>optimizeMember Level 4</em>), with a default set of bbPress permissions that allow all Members to both spectate and particpate in your forums, just as if they were a WordPress Subscriber Role (or a bbPress Participant Role).</p>'."\n";
								echo '<p>bbPress also adds some new Roles (dynamic Roles in bbPress 2.2+) to your WordPress installation. These include but are not limited to: <em>Keymaster</em> and <em>Moderator</em>. optimizeMember allows Forum Keymasters &amp; Moderators full access to the highest Membership Level you offer; just like it does with <em>Administrators</em>, <em>Editors</em>, <em>Authors</em>, and <em>Contributors</em>.</p>'."\n";
								echo '<p><strong>Membership Levels provide incremental access:</strong></p>'."\n";
								echo '<p>* A Member with Level 4 access, will also be able to access Levels 0, 1, 2 &amp; 3 <em>(plus spectate/participate in bbPress Forums)</em>.<br />* A Member with Level 3 access, will also be able to access Levels 0, 1 &amp; 2 <em>(plus spectate/participate in bbPress Forums)</em>.<br />* A Member with Level 2 access, will also be able to access Levels 0 &amp; 1 <em>(plus spectate/participate in bbPress Forums)</em>.<br />* A Member with Level 1 access, will also be able to access Level 0 <em>(plus spectate/participate in bbPress Forums)</em>.<br />* A Subscriber with Level 0 access, will ONLY be able to access Level 0 <em>(plus spectate/participate in bbPress Forums)</em>.<br />* A public Visitor will have NO access to protected content <em>(and no special access to bbPress Forums)</em>.</p>'."\n";
								echo '<p><em>* WordPress Subscribers <strong class="ws-menu-page-hilite">and bbPress Spectators/Participants</strong> are at Membership Level 0. If you\'re allowing Open Registration via optimizeMember, Subscribers will be at Level 0 (a Free Subscriber).</em></p>'."\n";
								echo '<p><em>* WordPress Administrators, Editors, Authors, Contributors, <strong class="ws-menu-page-hilite">and bbPress Keymasters/Moderators</strong> have Level 4 access, with respect to optimizeMember. All of their other Roles/Capabilities are left untouched.</em></p>'."\n";
								/**/
								echo '<div class="ws-menu-page-hr"></div>'."\n";
								/**/
								echo '<h3>Protecting Content Introduced by bbPress</h3>'."\n";
								echo '<p>You can protect individual Forum Topics/Posts/Replies at different Levels with optimizeMember, or even with Custom Capabilities. Forum Topics/Posts/Replies are integrated by bbPress internally as "Custom Post Types", which can be protected by optimizeMember either through Post Level Access Restrictions, or through URI Level Access Restrictions (recommended). If you choose to use Post Level Access Restrictions, please remember that optimizeMember will provide you with drop-down menus whenever you add or edit Forum Topics/Posts/Replies to make things easier for you.</p>'."\n";
								echo '<p>Regarding optimizeMember\'s Post Level Access Restrictions with bbPress. Before you decide to use Post Level Access Restrictions, please take a look at: <code>optimizeMember -> Restriction Options -> URI Access Restrictions</code> and consider the following limitations in the current release of optimizeMember. It is currently NOT possible to protect a Forum, and have all Topics inside that Forum protected automatically. In order to accomplish that, you\'ll need to use optimizeMember\'s URI Access Restrictions. Also, optimizeMember is currently NOT capable of protecting Topic Tags; but you can use URI Restrictions for these also.</p>'."\n";
								do_action("ws_plugin__optimizemember_during_integrations_page_during_left_sections_during_api_easy_way", get_defined_vars());
								echo '</div>'."\n";
								/**/
								echo '</div>'."\n";
								/**/
								do_action("ws_plugin__optimizemember_during_integrations_page_during_left_sections_after_bbpress", get_defined_vars());
							}
						/**/
						do_action("ws_plugin__optimizemember_during_integrations_page_after_left_sections", get_defined_vars());
						/**/
						echo '</td>'."\n";
						/**/
						echo '<td class="ws-menu-page-table-r">'."\n";
						c_ws_plugin__optimizemember_menu_pages_rs::display();
						echo '</td>'."\n";
						/**/
						echo '</tr>'."\n";
						echo '</tbody>'."\n";
						echo '</table>'."\n";
						/**/
						echo '</div>'."\n";
						echo '</div>'."\n";
					}
			}
	}
/**/
new c_ws_plugin__optimizemember_menu_page_integrations();
?>