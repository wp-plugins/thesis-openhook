<?php

/**
 * Prevent direct access to this file
 */
if ( ! function_exists( 'add_action' ) ) {
	@header( 'HTTP/1.1 403 Forbidden' );
	@header( 'Status: 403 Forbidden' );
	@header( 'Connection: Close' );
	@exit;
}

/**
 * Register our settings with WordPress, enabling saving them to database w/o fuss
 *
 * @todo Data sanitization
 */
function openhook_initiate_options() {
	register_setting( 'openhook_settings_general', 'openhook_general' );
	register_setting( 'openhook_settings_wordpress', 'openhook_wordpress' );
	register_setting( 'openhook_settings_thesis', 'openhook_thesis' );
}
add_action( 'admin_init', 'openhook_initiate_options' );

/**
 * Add OpenHook to admin menus
 *
 * @global array $submenu An array of the current menu items
 * @todo Add shortcut to Genesis menu
 */
function openhook_add_admin_page_menu_links() {
	global $submenu;

	# Sneak shortcuts to various hook menus into appropriate places in the existing WordPress & theme menus
	$submenu[ 'themes.php' ][ 500 ] = array( __( 'WordPress Hooks', 'openhook' ), 'delete_users', 'options-general.php?page=openhook&tab=wordpress', __( 'WordPress Hooks', 'OH' ) );
	$submenu[ 'thesis-options' ][ 500 ] = array( __( 'Thesis Hooks', 'openhook' ), 'delete_users', 'options-general.php?page=openhook&tab=thesis', __( 'Thesis Hooks', 'OH' ) );

	# Add our primary options page; set it to a variable for use in targeting our admin style
	$page = add_options_page( __( 'The OpenHook Customizations Manager', 'openhook' ), __( 'OpenHook', 'openhook' ), 'delete_users', 'openhook', 'openhook_setup_admin_page');

	add_action( 'admin_print_styles-' . $page, 'openhook_do_admin_css' );
}
add_action( 'admin_menu', 'openhook_add_admin_page_menu_links', 99 );

/**
 * Set up the styling for the OpenHook Customizations Manager
 */
function openhook_do_admin_css() {
	# Registers our style with WordPress
	wp_register_style( 'openhook-admin-css', OPENHOOK_PLUGIN_URL . 'style-admin.css', array(), '1.0.0', 'all');

	# Queue our style for output
	wp_enqueue_style( 'openhook-admin-css' );
}

/**
 * Create and echo OpenHook panel navigation tabs
 */
function openhook_set_up_admin_tabs( ) {
	# The tabs we want to use
	$tabs = array(
		'general' => __( 'General', 'OH' ),
		'thesis' => __( 'Thesis Hooks', 'OH' ),
		'wordpress' => __( 'WordPress Hooks', 'OH' ),
		'phpinfo' => 'phpinfo()',
		'contact' => __( 'Contact', 'OH' ),
	);
	$links = array();

	# Determine which tab is currently being viewed...
	if ( isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET[ 'tab' ], $tabs ) )
		$current = $_GET[ 'tab' ];
	# ...else assume the general tab
	else
		$current = 'general';

	# Construct tab HTML
	foreach( $tabs as $tab => $name ) {
		if ( $current == $tab )
			$links[] = "<a class='nav-tab nav-tab-active' href='?page=openhook&tab=$tab'>$name</a>";
		else
			$links[] = "<a class='nav-tab' href='?page=openhook&tab=$tab'>$name</a>";
	}

	# Echo the tabs
	echo '<div id="nav"><h3>';
	echo implode( '', $links );
	echo '</h3></div>';
}

/**
 * Output the OpenHook Customization Manager
 */
function openhook_setup_admin_page() {
	# Protect against unauthorized users
	if ( ! current_user_can( 'delete_users' ) ) {
		wp_die( 'Sorry, but you do not have the appropriate permissions to view this page.', 'openhook' );
	}
	else {
	?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e( 'OpenHook Customizations Manager', 'openhook' ); ?></h2>
	<?php openhook_set_up_admin_tabs(); ?>
	<div class="main">
		<?php
			$tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';

			switch( $tab ) {
			case 'thesis':
				openhook_generate_hook_page( 'thesis' );

				break;
			case 'wordpress':
				openhook_generate_hook_page( 'wordpress' );

				break;
			case 'phpinfo':
				openhook_generate_phpinfo_page();

				break;
			case 'contact':
				openhook_generate_contact_page();

				break;
			case 'general':
			default:
				openhook_generate_general_page();

				break;
			}
		?>
	</div>
	<div class="sidebar">
		<?php openhook_do_admin_sidebar(); ?>
	</div>
	<p class="footer"><?php printf( __( '&copy; Copyright %1$s Rick Beckman. <a href="%2$s">Open source software</a>.', 'openhook' ), '2008–' . date( 'Y' ), 'http://www.gnu.org/licenses/gpl-2.0.html' ); ?></p>
</div>
	<?php
	}
}

/**
 * Output JavaScript for hook panels
 */
function openhook_do_hook_panel_js( $hooks ) {
?>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#hook_selector').change(function(){
		var fieldsetName = jQuery(this).val();
		jQuery('fieldset.hook').hide().filter('#' + fieldsetName).fadeIn();
		jQuery('.button-primary').fadeIn();
	});
	jQuery('fieldset.hook').hide();
	jQuery('.button-primary').hide();
});
</script>
<?php
}

/**
 * Output a hook selector to allow swapping between hook options
 *
 * @param array $hooks The list of hooks from which we're selecting
 */
function openhook_build_hook_select_selector( $hooks, $options ) {
	echo '<select id="hook_selector">';
	echo '	<option value="0">-- ' . __( 'Select a hook to customize', 'openhook' ) . ' --</option>';

	foreach ( $hooks as $hook ) {
		$haz_customization = '';

		if ( $options[ $hook[ 'name' ] ][ 'action' ] != '' || count( $options[ $hook[ 'name' ] ] ) > 1 )
			$haz_customization = '* ';

		echo '<option value="hook_' . $hook[ 'name' ] . '"' . $stylize . '>' . $haz_customization . $hook[ 'name' ] . '</option>';
	}

	echo '</select>';
}

/**
 * Output the OpenHook Customization Manager&rsquo;s sidebar
 */
function openhook_do_admin_sidebar() {
?>
	<h3><?php _e( 'Plugin', 'openhook' ); ?></h3>
	<ul>
		<li><a href="http://rickbeckman.org/"><?php _e( 'OpenHook Homepage', 'openhook' ); ?></a></li>
		<li><a href="http://wordpress.org/tags/thesis-openhook"><?php _e( 'Community Support', 'openhook' ); ?></a></li>
	</ul>
	<h3><?php _e( 'Donation', 'openhook' ); ?></h3>
	<p><a href="https://www.wepay.com/donations/openhook" target="_blank"><img src="https://www.wepay.com/img/widgets/donate_with_wepay.png" alt="Donate with WePay" /></a></p>
	<p><?php _e( 'Developing OpenHook is a labor of love. Your donation, of any size, is greatly appreciated.', 'openhook' ); ?></p>
	<h3><?php _e( 'Miscellanea', 'openhook' ); ?></h3>
	<ul>
		<li><a href="http://rickbeckman.org/">Rick Beckman</a></li>
		<li><a href="http://twitter.com/BrazenlyGeek"><?php _e( 'Follow me on Twitter', 'openhook' ); ?></a></li>
		<li><a href="http://zerply.com/rick-beckman"><?php _e( 'Endorse me on Zerply', 'openhook' ); ?></a></li>
	</ul>
	<h3><?php _e( 'Credits', 'openhook' ); ?></h3>
	<ul>
		<li><a href="http://get-thesis.com/">DIYthemes</a>, <?php _e( 'for the reason for this plugin to exist', 'openhook' ); ?>
		<li><a href="http://wordpress.org/extend/plugins/k2-hook-up/">K2 Hook Up</a>, <?php _e( 'for inspiration &amp; initial code base', 'openhook' ); ?></li>
		<li><a href="http://wordpress-plugins.feifei.us/hashcash/">WordPress Hashcash</a>, <?php _e( 'for this great sidebar code', 'openhook' ); ?></li>
	</ul>
	<h3><?php _e( 'Dedication', 'openhook' ); ?></h3>
	<p><?php _e( 'OpenHook is proudly dedicated to the fantastic WordPress and Thesis communities, and to my wonderful family (Jessica, Sophia, and Anya)!', 'openhook' ); ?></p>
<?php
}

/**
 * Generate general options
 */
function openhook_generate_general_page() {
	if ( isset( $_GET[ 'action' ] ) ) {
		define( 'OPENHOOK_SAFEGUARD', true );

		if ( $_GET[ 'action' ] == 'cleanup_openhook' )
			openhook_delete_options( 'legacy' );
		if ( $_GET[ 'action' ] == 'upgrade_openhook' )
			openhook_import_old_options();
		if ( $_GET[ 'action' ] == 'uninstall_openhook' )
			openhook_delete_options();
	}

	$options = get_option( 'openhook_general' );

	$thesis_active = ( isset( $options[ 'active_actions' ][ 'openhook_thesis' ] ) && $options[ 'active_actions' ][ 'openhook_thesis' ] ) ? 1 : 0;
	$wordpress_active = ( isset( $options[ 'active_actions' ][ 'openhook_wordpress' ] ) && $options[ 'active_actions' ][ 'openhook_wordpress' ] ) ? 1 : 0;
?>
<script>
function confirmUpgrade()
{
    return confirm("<?php echo esc_js( __( 'Are you sure you want to migrate your OpenHook 2 settings to OpenHook 3? This will overwrite all current OpenHook 3 options.', 'openhook' ) ); ?>");
}
function confirmCleanUp()
{
	return confirm("<?php echo esc_js( __( 'Are you sure you want to delete all old OpenHook 2 settings? This action is irreversible!', 'openhook' ) ); ?>");
}
function confirmUninstall()
{
	return confirm("<?php echo esc_js( __( 'Are you sure you want to delete all OpenHook 3 settings? This action is irreversible!', 'openhook' ) ); ?>");
}
</script>
<p><?php _e( 'Here are just a few settings to tailor your OpenHook experience. Note that no custom actions will be processed unless you enable them below.', 'openhook' ); ?></p>
<form method="post" action="options.php">
	<?php settings_fields( OPENHOOK_SETTINGS_GENERAL ); ?>
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e( 'Active action groups', 'openhook' ); ?></th>
			<td>
				<label><input type="checkbox" name="openhook_general[active_actions][openhook_thesis]" value="1"<?php checked( 1, $thesis_active ); ?> /> Thesis</label> <?php printf( __('(Don&rsquo;t have Thesis? <a href="%s">Get it today</a>!)', 'openhook' ), 'http://get_thesis.com/' ); ?><br />
				<span class="description"><small><?php _e( 'Currently supporting all action hooks present in Thesis 1.8.4.', 'openhook' ); ?></small></span><br />
				<label><input type="checkbox" name="openhook_general[active_actions][openhook_wordpress]" value="1"<?php checked( 1, $wordpress_active ); ?> /> WordPress</label><br />
				<span class="description"><?php _e( 'OpenHook allows customizing hooks in multiple contexts. To save on processing power, it will only process the hooks you want. Disabling the hook contexts does not delete your saved customizations.', 'openhook' ); ?></span>
			</td>
		</tr>
	</table>
	<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'openhook' ); ?>" /></p>
</form>
<h3><?php _e( 'Manage Options', 'openhook' ); ?></h3>
<p><?php _e( 'Have you used previous versions of OpenHook? You have the option of importing your customizations into the OpenHook 3 structure; this will not remove your old options, allowing you to downgrade if desired. Note that upgrading from OpenHook 2 will overwrite your WordPress &amp; Thesis hook customizations made with OpenHook 3!', 'openhook' ); ?></p>
<p><a onclick="return confirmUpgrade();" href="<?php echo admin_url( 'options-general.php?page=openhook&tab=general&action=upgrade_openhook' ); ?>" class="button-secondary"><?php _e( 'Upgrade from OpenHook 2', 'openhook' ); ?></a></p>
<p><?php _e( 'If you&rsquo;re satisfied with OpenHook 3, you may remove any legacy OpenHook options found in the database as well. Note that this is irreversible, and you should consider making a backup of your database (at the very least, a backup of your options table) prior to removing the legacy options.', 'openhook' ); ?></p>
<p><a onclick="return confirmCleanUp();" href="<?php echo admin_url( 'options-general.php?page=openhook&tab=general&action=cleanup_openhook' ); ?>" class="button-secondary"><?php _e( 'Remove legacy options', 'openhook' ); ?></a></p>
<p><?php _e( 'Want to start fresh with your customizations or wish to uninstall OpenHook? You can easily remove all of OpenHook 3&rsquo;s options with the following button. You are strongly encouraged to have a database backup available as this action is not reversible!', 'openhook' ); ?></p>
<p><a onclick="return confirmUninstall();" href="<?php echo admin_url( 'options-general.php?page=openhook&tab=general&action=uninstall_openhook' ); ?>" class="button-secondary"><?php _e( 'Remove OpenHook 3 options', 'openhook' ); ?></a></p>
<?php
}

/**
 * Generate phpinfo() display page
 */
function openhook_generate_phpinfo_page() {
	echo '<h3>' . __( 'Under the Hood: Your Server Details', 'openhook' ) . '</h3>';
	echo '<p>' . sprintf( __( 'When deciding what customizations to make to your site, it can be useful to know what your server supports. The below information describes your server environment. If your server is missing a feature you require, upgrade to a more customizable host, such as <a href="%1$s">DreamHost</a> or (for you &uuml;ber geeks wanting total customizability) <a href="%2$s">Linode</a>! Otherwise, consider contacting your own host to see if they can enable the feature(s) you want.', 'openhook' ), 'http://bit.ly/GetDreamHost', 'http://bit.ly/GetLinode' ) . '</p>';

	$matches = array();

	# Begin content buffering
	ob_start();

	phpinfo();

	# Capture the styling information & the body of phpinfo()
	preg_match( '%<style type="text/css">(.*?)</style>.*?(<body>.*</body>)%s', ob_get_clean(), $matches );

	echo '<div class="phpinfo_display"><style type="text/css">.phpinfo_display { max-width: 600px; overflow: auto; } ' . join( "\n", array_map( create_function( '$i', 'return ".phpinfo_display " . preg_replace( "/,/", ",.phpinfodisplay ", $i );' ), preg_split( '/\n/', $matches[ 1 ] ) ) ) . '</style>', $matches[2], '</div>';
}

/**
 * Generate contact page
 */
function openhook_generate_contact_page() {
?>
	<p><?php _e( 'Feature request? Support request? Bug report? You&rsquo;re invited to get in touch with me!', 'openhook' ); ?></p>
	<ul>
		<li>Email: <a href="mailto:rick.beckman@gmail.com">rick.beckman@gmail.com</a></li>
		<li>Twitter: <a href="http://twitter.com/BrazenlyGeek">@BrazenlyGeek</a></li>
	</ul>
	<script>
		id = 126606;
	</script>
	<script src="http://kontactr.com/wp.js"></script>
<?php
}

/**
 * Generate fieldset for our hook options
 *
 * @todo Move JavaScript to header or an external file
 */
function openhook_generate_hook_page( $context ) {
	# Get the hooks we&rsquo;ll be dealing with
	$hook = array();
	if ( $context == 'thesis' )
		$hooks = openhook_thesis_hooks();
	if ( $context == 'wordpress' )
		$hooks = openhook_wordpress_hooks();

	# Output necessary JavaScript
	openhook_do_hook_panel_js( $hooks );
?>
<form method="post" action="options.php">
	<?php settings_fields( "openhook_settings_$context" ); ?>
	<?php $options = get_option( "openhook_$context" ); ?>
	<p><?php _e( 'You can freely swap between hooks without losing your customizations. When you are finished customizing, the save button will save all hook customizations at once.', 'openhook' ); ?></p>
	<p>
		<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'OH' ); ?>" style="float: left;" />
		<label style="float: right; text-align: right;"><?php _e( 'Hook selection:', 'openhook' ); ?> <?php openhook_build_hook_select_selector( $hooks ); ?></label>
	</p>
	<div class="clear"></div>
<?php
	foreach ( $hooks as $hook ) {
		openhook_build_hook_forms( $hook, $options, $context );
	}
?>
	<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'OH' ); ?>" /></p>
</form>	
<?php
}

/**
 * Outputs the form for hook customization
 *
 * @param $hook array Information about the hook to be customized
 * @param $options array Current customizations for display in the form
 */
function openhook_build_hook_forms( $hook, $options, $context ) {
	$action = ( isset( $options[ $hook[ 'name' ] ][ 'action' ] ) && $options[ $hook[ 'name' ] ][ 'action' ] != '' ) ? $options[ $hook[ 'name' ] ][ 'action' ] : '';
	$php = ( isset( $options[ $hook[ 'name' ] ][ 'php' ] ) && $options[ $hook[ 'name' ] ][ 'php' ] ) ? 1 : 0;
	$shortcodes = ( isset( $options[ $hook[ 'name' ] ][ 'shortcodes' ] ) && $options[ $hook[ 'name' ] ][ 'shortcodes' ] ) ? 1 : 0;
	$disable = ( isset( $options[ $hook[ 'name' ] ][ 'disable' ] ) && $options[ $hook[ 'name' ] ][ 'disable' ] ) ? 1 : 0;
	$unhook = 0;
?>
	<fieldset class="hook" id="hook_<?php echo $hook[ 'name' ]; ?>">
		<h3><?php echo $hook[ 'name' ]; ?></h3>
		<?php if ( isset( $hook[ 'desc' ] ) ) : echo '<p class="description">' . $hook[ 'desc' ] . '</p>'; endif; ?>
		<h4><?php _e( 'Lights, Camera, ACTION!', 'openhook' ); ?></h4>
		<p class="description"><?php _e( 'Add whatever customizations you can dream up to this hook!', 'openhook' ); ?></p>
		<textarea rows="10" cols="50" name="openhook_<?php echo $context; ?>[<?php echo $hook[ 'name' ]; ?>][action]"><?php echo esc_textarea( $action ); ?></textarea>
		<p>
			<label><input type="checkbox" name="openhook_<?php echo $context; ?>[<?php echo $hook[ 'name' ]; ?>][php]" value="1"<?php checked( '1', $php ); ?> /> <?php _e( 'Process <abbr title="PHP: Hypertext Preprocessor">PHP</abbr> on this hook?', 'openhook'); ?></label><br />
			<label><input type="checkbox" name="openhook_<?php echo $context; ?>[<?php echo $hook[ 'name' ]; ?>][shortcodes]" value="1"<?php checked( '1', $shortcodes ); ?> /> <?php _e( 'Process shortcodes on this hook?', 'openhook'); ?></label><br />
			<label><input type="checkbox" name="openhook_<?php echo $context; ?>[<?php echo $hook[ 'name' ]; ?>][disable]" value="1"<?php checked( '1', $disable ); ?> /> <?php _e( 'Disable this action? This will keep your action saved, but will prevent it from being processed.', 'openhook' ); ?></label>
		</p>
	<?php if ( isset( $hook[ 'unhook' ] ) ) : ?>
		<h4><?php _e( 'Manage Default Actions', 'openhook' ); ?></h4>
		<p class="description"><?php _e( 'The following actions may be attached to this hook; you may disable them if you would like. (They may have already been disabled via other means, such as by a plugin. The following settings apply even if you have disabled the custom action using the above setting.', 'openhook' ); ?></p>
<?php
			foreach ( $hook[ 'unhook' ] as $action ) {
				$unhook = ( isset( $options[ $hook[ 'name' ] ][ 'unhook' ][ $action[ 'name' ] ] ) && $options[ $hook[ 'name' ] ][ 'unhook' ][ $action[ 'name' ] ] ) ? 1 : 0;
			?>
			<p>
				<label><input type="checkbox" name="openhook_<?php echo $context; ?>[<?php echo $hook[ 'name' ]; ?>][unhook][<?php echo $action[ 'name' ]; ?>]" value="1"<?php checked( '1', $unhook ); ?> /> <?php echo $action[ 'name' ]; ?>()</label><br />
				<span class="description"><?php echo $action[ 'desc' ]; ?></span>
			</p>
<?php
			}
	endif;
	?>
	</fieldset>
<?php
}