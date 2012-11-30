<?php
/*
 * Plugin Name: OpenHook
 * Plugin URI: http://www.openhook.net/
 * Description: Easy access to the assorted hooks available in WordPress & Thesis.
 * Version: 3.4
 * Author: Rick Beckman
 * Author URI: http://rickbeckman.org/
 * License: GNU General Public License v3.0 (or later)
 * License URI: http://www.opensource.org/licenses/gpl-license.php
*/

/**
 * Prevent direct access to this file
 */
if ( ! defined( 'DB_NAME' ) ) {
	@header( 'HTTP/1.1 403 Forbidden' );
	@header( 'Status: 403 Forbidden' );
	@header( 'Connection: Close' );
	@exit;
}

/**
 * Protect nuclear-level functions
 */
if ( defined( 'OPENHOOK_SAFEGUARD' ) )
	wp_die( sprintf( __( 'The constant %s is defined which creates a security risk.', 'openhook' ), '<code>OPENHOOK_SAFEGUARD</code>' ) );

/**
 * Define OpenHook constants
 */
define( 'OPENHOOK_VERSION', '7' ); # Dev version (used for internal feature tracking)
define( 'OPENHOOK_SETTINGS_GENERAL', 'openhook_settings_general' );
define( 'OPENHOOK_SETTINGS_THESIS', 'openhook_settings_thesis' );
define( 'OPENHOOK_SETTINGS_WORDPRESS', 'openhook_settings_wordpress' );
define( 'OPENHOOK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'OPENHOOK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'OPENHOOK_LANG_DIR', OPENHOOK_PLUGIN_DIR . 'languages/' );

class OpenHook {
	/**
	 * Initializes the OpenHook class
	 *
	 * Runs through several actions, such as activating our i18n text domain, required to get us started
	 *
	 * @since 4.0
	 */
	function __construct() {
		register_activation_hook( __FILE__, array( $this, 'activate' ) );

		add_action( 'admin_init', array( $this, 'openhook_initiate_options' ) );
		add_action( 'admin_menu', array( $this, 'openhook_add_admin_page_menu_links' ), 99 );
		add_action( 'init', array( $this, 'upgrade' ) ); # Upgrade, if needed
		add_action( 'init', array( $this, 'i18n' ) ); # Internationalization
		add_action( 'init', array( $this, 'openhook_execute_hooks' ) ); # Attach custom actions to hooks

		# The meat & potatoes of OpenHook processes on the frontend only
		if ( ! is_admin() ) {
			# Handle hook visualization
			$options = get_option( 'openhook_general' );
			if ( isset( $options[ 'visualize_hooks' ] ) && $options[ 'visualize_hooks' ] )
				add_action( 'init', array( $this, 'openhook_setup_hook_visualization' ) );
		}

	}

	public function activate( $network_wide ) {
		# Install OpenBox
		$this->install_openbox();

		# Set version in database
		update_option( 'openhook_version', OPENHOOK_VERSION );
	}

	/**
	 * Handle anything that needs upgraded aside from standard plugin files
	 *
	 * @since 4.0
	 */
	public function upgrade() {
		# Ensure OpenBox stays upgraded
		if ( version_compare( get_option( 'openhook_version' ), OPENHOOK_VERSION ) )
			$this->install_openbox();
	}

	/**
	* Loads the plugin text domain for translation
	*/
	public function i18n() {
		load_plugin_textdomain( 'openhook', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}


	/**
 * Setup hook visualizations
 *
 * @since 3.3
 */
function openhook_setup_hook_visualization() {
	if ( current_user_can( 'delete_users' ) ) {
		$wordpress_hooks = $this->openhook_wordpress_hooks();
		$thesis_hooks = $this->openhook_thesis_hooks();
		$all_hooks = array_merge( (array) $wordpress_hooks, (array) $thesis_hooks );

		wp_register_style( 'openhook-visualization', OPENHOOK_PLUGIN_URL . 'style-visualization.css', array(), '1.0.0', 'screen' );

		wp_enqueue_style( 'openhook-visualization' );

		foreach ( $all_hooks as $hook ) {
			add_action( $hook[ 'name' ], array( $this, 'openhook_do_hook_visualization' ), 1 );
		}
	}
}

/**
 * Output hook name/visualization
 *
 * @since 3.3
 */
function openhook_do_hook_visualization() {
	$current_action = current_filter();

	if ( $current_action != 'wp_head' )
		echo '<span class="openhook">' . $current_action . '</span>';
}

/**
 * Determine which actions to take for each of our hooks
 */
function openhook_execute_hooks() {
	$wordpress_hooks = $this->openhook_wordpress_hooks();
	$thesis_hooks = $this->openhook_thesis_hooks();
	$all_hooks = array_merge( (array) $wordpress_hooks, (array) $thesis_hooks );
	$all_options = $this->openhook_get_relevant_options();

	# Go through each of our hooks, doing stuff if needed
	foreach ( $all_hooks as $hook ) {
		# Add actions to all required hooks
		if ( isset( $all_options[ $hook[ 'name' ] ][ 'action' ] ) && ! isset ( $all_options[ $hook[ 'name' ] ][ 'disable' ] ) )
			add_action( $hook[ 'name' ], array( $this, 'openhook_execute_action' ) );

		# Unhook actions as needed
		if ( isset( $hook[ 'unhook' ] ) ) {
			foreach ( $hook[ 'unhook' ] as $action ) {
				if ( isset( $all_options[ $hook[ 'name' ] ][ 'unhook' ][ $action[ 'name' ] ] ) ) {
					$priority = isset( $action[ 'priority' ] ) ? $action[ 'priority' ] : false;

					if ( $priority )
						remove_action( $hook[ 'name' ], $action[ 'name' ], $priority );
					else
						remove_action( $hook[ 'name' ], $action[ 'name' ] );
				}
			}
		}
	}
}

/**
 * Process an action
 */
function openhook_execute_action() {
	# Determine the current hook/filter we're acting upon & get our options to act
	$hook = current_filter();
	$options = $this->openhook_get_relevant_options();

	# Bail out if we have neither a hook or options to work with
	if( ! $hook || ! $options )
		return;

	# Nice names for our options
	$action = $options[ $hook ][ 'action' ];
	$php = isset( $options[ $hook ][ 'php' ] ) ? 1 : 0;
	$shortcodes = isset( $options[ $hook ][ 'shortcodes' ] ) ? 1 : 0;

	# Process shortcodes if needed
	$value = $shortcodes ? do_shortcode( $action ) : $action;

	# Output our action, with or w/o PHP as needed
	if ( $php )
		eval( "?>$value<?php " );
	else
		echo $value;
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


/**
 * Add OpenHook to admin menus
 *
 * @global array $submenu An array of the current menu items
 * @todo Add shortcut to Genesis menu
 */
function openhook_add_admin_page_menu_links() {
	global $submenu;

	# Sneak shortcuts to various hook menus into appropriate places in the existing WordPress & theme menus
	$submenu[ 'themes.php' ][ 500 ] = array( __( 'WordPress Hooks', 'openhook' ), 'delete_users', 'options-general.php?page=openhook&tab=wordpress', __( 'WordPress Hooks', 'openhook' ) );
	$submenu[ 'thesis-options' ][ 500 ] = array( __( 'Thesis Hooks', 'openhook' ), 'delete_users', 'options-general.php?page=openhook&tab=thesis', __( 'Thesis Hooks', 'openhook' ) );
	$submenu[ 'tools.php' ][ 500 ] = array( 'phpinfo()', 'delete_users', 'options-general.php?page=openhook&tab=phpinfo', 'phpinfo()' );

	# Add our primary options page; set it to a variable for use in targeting our admin style
	$page = add_options_page( __( 'The OpenHook Customizations Manager', 'openhook' ), __( 'OpenHook', 'openhook' ), 'delete_users', 'openhook', array( $this, 'openhook_setup_admin_page' ) );

	add_action( 'admin_print_styles-' . $page, array( $this, 'openhook_do_admin_css' ) );
}


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
	# IDs & names of tabs
	$tabs = array(
		'general' => __( 'General', 'openhook' ),
		'thesis' => __( 'Thesis 1.8.5', 'openhook' ),
		'wordpress' => __( 'WordPress', 'openhook' ),
		'phpinfo' => 'phpinfo()',
		'contact' => __( 'Contact', 'openhook' ),
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
	<h2><?php _e( 'OpenHook', 'openhook' ); ?></h2>
	<?php $this->openhook_set_up_admin_tabs(); ?>
	<div class="main">
		<?php
			$tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';

			switch( $tab ) {
			case 'thesis':
				$this->openhook_generate_hook_page( 'thesis' );

				break;
			case 'wordpress':
				$this->openhook_generate_hook_page( 'wordpress' );

				break;
			case 'phpinfo':
				$this->openhook_generate_phpinfo_page();

				break;
			case 'contact':
				$this->openhook_generate_contact_page();

				break;
			case 'general':
			default:
				$this->openhook_generate_general_page();

				break;
			}
		?>
	</div>
	<div class="sidebar">
		<?php $this->openhook_do_admin_sidebar(); ?>
	</div>
	<p class="footer"><?php printf( __( '&copy; Copyright %1$s Rick Beckman. <a href="%2$s">Open source software</a>.', 'openhook' ), '2008–' . date( 'Y' ), 'http://www.gnu.org/licenses/gpl.html' ); ?></p>
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
		<li><a href="http://openhook.net/"><?php _e( 'OpenHook Homepage', 'openhook' ); ?></a></li>
		<li><a href="http://wordpress.org/tags/thesis-openhook"><?php _e( 'Community Support', 'openhook' ); ?></a></li>
	</ul>
	<h3><?php _e( 'Support Development', 'openhook' ); ?></h3>
	<p><a href="https://www.wepay.com/donations/openhook" target="_blank"><img src="https://www.wepay.com/img/widgets/donate_with_wepay.png" alt="Donate with WePay" /></a></p>
	<p><?php _e( 'Developing OpenHook is a labor of love. Your donation, of any size, is greatly appreciated.', 'openhook' ); ?></p>
	<h3><?php _e( 'Credits', 'openhook' ); ?></h3>
	<ul>
		<li><a href="http://get-thesis.com/">DIYthemes</a>, <?php _e( 'for the reason for this plugin to exist', 'openhook' ); ?>
		<li><a href="http://wordpress.org/extend/plugins/k2-hook-up/">K2 Hook Up</a>, <?php _e( 'for inspiration &amp; initial code base', 'openhook' ); ?></li>
		<li><a href="http://wordpress-plugins.feifei.us/hashcash/">WordPress Hashcash</a>, <?php _e( 'for this great sidebar code', 'openhook' ); ?></li>
	</ul>
<?php
}

/**
 * Generate general options
 */
function openhook_generate_general_page() {
	if ( isset( $_POST ) ) {
		define( 'OPENHOOK_SAFEGUARD', true );

		if ( ! empty( $_REQUEST[ 'cleanup_openhook' ] ) )
			$this->openhook_delete_options( 'legacy' );
		if ( ! empty( $_REQUEST[ 'upgrade_openhook' ] ) )
			$this->openhook_import_old_options();
		if ( ! empty( $_REQUEST[ 'uninstall_openhook' ] ) )
			$this->openhook_delete_options();
	}

	$options = get_option( 'openhook_general' );

	$thesis_active = ( isset( $options[ 'active_actions' ][ 'openhook_thesis' ] ) && $options[ 'active_actions' ][ 'openhook_thesis' ] ) ? 1 : 0;
	$wordpress_active = ( isset( $options[ 'active_actions' ][ 'openhook_wordpress' ] ) && $options[ 'active_actions' ][ 'openhook_wordpress' ] ) ? 1 : 0;
	$visualize_hooks = ( isset( $options[ 'visualize_hooks' ] ) && $options [ 'visualize_hooks' ] ) ? 1 : 0;
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
<form method="post" action="options.php">
	<p><?php _e( 'Here are just a few settings to tailor your OpenHook experience. Note that no custom actions will be processed unless you enable them below.', 'openhook' ); ?></p>
	<?php settings_fields( OPENHOOK_SETTINGS_GENERAL ); ?>
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e( 'Active hook groups', 'openhook' ); ?></th>
			<td>
				<p><label><input type="checkbox" name="openhook_general[active_actions][openhook_thesis]" value="1"<?php checked( 1, $thesis_active ); ?> /> <a href="http://get-thesis.com/">Thesis 1.8.5</a></label><br />
				<label><input type="checkbox" name="openhook_general[active_actions][openhook_wordpress]" value="1"<?php checked( 1, $wordpress_active ); ?> /> WordPress</label></p>
				<p class="description"><?php _e( 'OpenHook allows customizing several different themes&apos; hooks. To save on processing power, it will only process the hooks you specify here by enabling them on the themes of your choice. Disabling the hook groups does not delete your saved customizations.', 'openhook' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e( 'Visualize hooks', 'openhook' ); ?></th>
			<td>
				<p><label><input type="checkbox" name="openhook_general[visualize_hooks]" value="1"<?php checked( 1, $visualize_hooks ); ?> /> <?php _e( 'Visualize hooks', 'openhook' ); ?></label></p>
				<p class="description"><?php _e( 'Turning on hook visualization will output the name of each hook in use on your site in a colored box, allowing you to get a better idea of where a hook will execute at. Hook visualizations will only appear to logged in users who have the ability to access OpenHook settings.', 'openhook' ); ?></p>
			</td>
		</tr>
	</table>
	<p><?php printf( __( 'What about <a href="%1$s"><strong>Thesis 2</strong></a>? Hooks in Thesis 2 are dynamically defined, making providing a definitive list of hooks to customize difficult. However, you don&apos;t have to wait for developers to create &amp; charge you for the &ldquo;boxes&rdquo; you need to customize your site! OpenHook has your back with <strong>OpenBox</strong>, a box without limits. It is <a href="%2$s">installed for you already</a> and is ready for use in the Thesis 2 skin editor.', 'openhook' ), 'http://get-thesis.com/', admin_url( 'admin.php?page=thesis&canvas=boxes' ) ); ?></p>
	<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'openhook' ); ?>" /></p>
</form>
<h3><?php _e( 'Manage Options', 'openhook' ); ?></h3>
<form method="post" action="options-general.php?page=openhook&tab=general">
<p><?php _e( 'Have you used previous versions of OpenHook? You have the option of importing your customizations into the OpenHook 3 structure; this will not remove your old options, allowing you to downgrade if desired. Note that upgrading from OpenHook 2 will overwrite your WordPress &amp; Thesis hook customizations made with OpenHook 3!', 'openhook' ); ?></p>
<p><input class="button-secondary" onclick="return confirmUpgrade();" type="submit" name="upgrade_openhook" value="<?php esc_attr_e( 'Upgrade from OpenHook 2', 'openhook' ); ?>" /></p>
<p><?php _e( 'If you&rsquo;re satisfied with OpenHook 3, you may remove any legacy OpenHook options found in the database as well. Note that this is irreversible, and you should consider making a backup of your database (at the very least, a backup of your options table) prior to removing the legacy options.', 'openhook' ); ?></p>
<p><input class="button-secondary" onclick="return confirmCleanUp();" type="submit" name="cleanup_openhook" value="<?php esc_attr_e( 'Remove legacy options', 'openhook' ); ?>" /></p>
<p><?php _e( 'Want to start fresh with your customizations or wish to uninstall OpenHook? You can easily remove all of OpenHook&rsquo;s options with the following button. You are strongly encouraged to have a database backup available as this action is not reversible!', 'openhook' ); ?></p>
<p><input class="button-secondary" onclick="return confirmUninstall();" type="submit" name="uninstall_openhook" value="<?php esc_attr_e( 'Remove all OpenHook options', 'openhook' ); ?>" /></p>
<?php
}

/**
 * Generate phpinfo() display page
 */
function openhook_generate_phpinfo_page() {
	echo '<h3>' . __( 'Your Server Details', 'openhook' ) . '</h3>';
	echo '<p>' . sprintf( __( 'When deciding what customizations to make to your site, it can be useful to know what your server supports. The below information describes your server environment. If your server is missing a feature you require, upgrade to a more powerful host, such as <a href="%1$s">DreamHost</a>! Otherwise, consider contacting your own host to see if they can enable the feature(s) you want.', 'openhook' ), 'http://bit.ly/GetDreamHost' ) . '</p>';

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
<p><?php printf( __( 'Prior to submitting a bug report, remember the immortal advice: Don&rsquo;t panic! Help me help you by submitting <a href="%s">the best possible bug reports</a> you can.', 'openhook' ), 'http://www.chiark.greenend.org.uk/~sgtatham/bugs.html' ); ?>
<ul>
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
		$hooks = $this->openhook_thesis_hooks();
	if ( $context == 'wordpress' )
		$hooks = $this->openhook_wordpress_hooks();

	# Output necessary JavaScript
	$this->openhook_do_hook_panel_js( $hooks );

	# Get general options
	$general_options = get_option( 'openhook_general' );
	$actions_active = ( isset( $general_options[ 'active_actions' ][ "openhook_$context" ] ) && $general_options[ 'active_actions' ][ "openhook_$context" ] ) ? 1 : 0;

	if ( ! $actions_active ) { ?>
	<div id='openhook-warning' class='updated fade'><p><?php printf( __( '%1$s actions are currently <strong>disabled</strong>. When you are ready for your customizations to appear on your site, please enable the %1$s action groups in the <a href="%2$s">general OpenHook settings</a>.', 'openhook' ), str_replace( 'Wordpress', 'WordPress', ucfirst( $context ) ), admin_url( 'options-general.php?page=openhook' ) ); ?></p></div>
	<?php
	}
?>
<form method="post" action="options.php">
	<?php settings_fields( "openhook_settings_$context" ); ?>
	<?php $options = get_option( "openhook_$context" ); ?>
	<p><?php _e( 'You can freely swap between hooks without losing your customizations. When you are finished customizing, the save button will save all hook customizations at once.', 'openhook' ); ?></p>
	<p>
		<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'openhook' ); ?>" style="float: left;" />
		<label style="float: right; text-align: right;"><?php _e( 'Hook selection:', 'openhook' ); ?> <?php $this->openhook_build_hook_select_selector( $hooks, $options ); ?></label>
	</p>
	<div class="clear"></div>
<?php
	foreach ( $hooks as $hook ) {
		$this->openhook_build_hook_forms( $hook, $options, $context );
	}
?>
	<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'openhook' ); ?>" /></p>
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
			<label><input type="checkbox" name="openhook_<?php echo $context; ?>[<?php echo $hook[ 'name' ]; ?>][php]" value="1"<?php checked( '1', $php ); ?> /> <?php _e( 'Process <abbr title="PHP: Hypertext Preprocessor">PHP</abbr> on this hook? (Your code must be wrapped in <code>&lt;?php ?&gt;</code> tags)', 'openhook'); ?></label><br />
			<label><input type="checkbox" name="openhook_<?php echo $context; ?>[<?php echo $hook[ 'name' ]; ?>][shortcodes]" value="1"<?php checked( '1', $shortcodes ); ?> /> <?php _e( 'Process shortcodes on this hook?', 'openhook'); ?></label><br />
			<label><input type="checkbox" name="openhook_<?php echo $context; ?>[<?php echo $hook[ 'name' ]; ?>][disable]" value="1"<?php checked( '1', $disable ); ?> /> <?php _e( 'Disable this action? This will keep your action saved, but will prevent it from being processed.', 'openhook' ); ?></label>
		</p>
	<?php if ( isset( $hook[ 'unhook' ] ) ) : ?>
		<h4><?php _e( 'Remove Default Actions', 'openhook' ); ?></h4>
		<p class="description"><?php _e( 'The following actions may be attached to this hook; selecting them below will remove them from this hook so that you may use them elsewhere, if you so choose. They may have already been disabled via other means, such as by a plugin. The following settings apply even if you have disabled the custom action using the above setting.', 'openhook' ); ?></p>
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

	/**
	 * Install OpenBox to allow custom code in Thesis 2
	 *
	 * @since 4.0
	 */
	public function install_openbox() {
		include_once( ABSPATH . '/wp-admin/includes/file.php' );

		if ( get_filesystem_method() === 'direct' ) {
			# Use the WordPress file management system to manage file manipulations
			WP_Filesystem();

			$f = $GLOBALS[ 'wp_filesystem' ];

			# Users may opt to install the OpenBox even without Thesis 2 installed;
			# in that situation, we'll need to ensure the whole file structure is created.
			if ( ! is_dir( WP_CONTENT_DIR . '/thesis/boxes/openbox' ) ) {
				$directories = array( 'thesis/', 'thesis/boxes/', 'thesis/boxes/openbox/' );

				# Loop the file structure, creating directories which do not exist
				foreach ( $directories as $dir )
					$f->mkdir( $f->wp_content_dir() . $dir );
			}

			# This installs the OpenBox file itself
			$f->put_contents( $f->wp_content_dir() . 'thesis/boxes/openbox/box.php', file_get_contents( OPENHOOK_PLUGIN_DIR . 'openbox.inc' ) );
		}
	}

	/**
	 * Remove OpenBox
	 *
	 * @since 4.0
	 */
	public function uninstall_openbox() {
		include_once( ABSPATH . '/wp-admin/includes/file.php' );

		if ( get_filesystem_method() === 'direct' ) {
			# Use the WordPress file management system to manage file manipulations
			WP_Filesystem();

			$f = $GLOBALS[ 'wp_filesystem' ];

			$f->rmdir( $f->wp_content_dir() . 'thesis/boxes/openbox/' );
		}
	}

/**
 * Return all hook options to be processed
 *
 * @return array Concatenated list of all relevant options
 * @todo Add theme selecting logic
 */
function openhook_get_relevant_options() {
	$option = get_option( 'openhook_general' );
	$relevant_options = isset( $option[ 'active_actions' ] ) ? $option[ 'active_actions' ] : '';
	$thesis = get_option( 'openhook_thesis' );
	$wordpress = get_option( 'openhook_wordpress' );
	$return = array();

	if ( isset( $relevant_options[ 'openhook_wordpress' ] ) && $relevant_options[ 'openhook_wordpress' ] ) {
		if ( is_array( $wordpress ) )
			$return = array_merge( $return, $wordpress );
	}
	if ( isset( $relevant_options[ 'openhook_thesis' ] ) && $relevant_options[ 'openhook_thesis' ] ) {
		if ( is_array( $thesis ) )
			$return = array_merge( $return, $thesis );
	}

	return $return;
}

/**
 * Import Thesis 2 options into Thesis 3
 */
function openhook_import_old_options() {
	if ( current_user_can( 'delete_users' ) && defined( 'OPENHOOK_SAFEGUARD' ) ) {
		$wordpress = array();
		$thesis = array();

		$wordpress[ 'wp_head' ][ 'action' ] = stripslashes( get_option( 'openhook_wp_head' ) );
		$wordpress[ 'wp_head' ][ 'php' ] = get_option( 'openhook_wp_head_php' );
		$wordpress[ 'wp_footer' ][ 'action' ] = stripslashes( get_option( 'openhook_wp_footer' ) );
		$wordpress[ 'wp_footer' ][ 'php' ] = get_option( 'openhook_wp_footer_php' );

		update_option( 'openhook_wordpress', $wordpress );

		$thesis[ 'thesis_hook_before_html' ][ 'action' ] = stripslashes( get_option( 'openhook_before_html' ) );
		$thesis[ 'thesis_hook_before_html' ][ 'php' ] = get_option( 'openhook_before_html_php' );
		$thesis[ 'thesis_hook_after_html' ][ 'action' ] = stripslashes( get_option( 'openhook_after_html' ) );
		$thesis[ 'thesis_hook_before_html' ][ 'php' ] = get_option( 'openhook_after_html_php' );
		$thesis[ 'thesis_hook_before_header' ][ 'action' ] = stripslashes( get_option( 'openhook_before_header' ) );
		$thesis[ 'thesis_hook_before_header' ][ 'php' ] = get_option( 'openhook_before_header_php' );
		$thesis[ 'thesis_hook_before_header' ][ 'unhook' ][ 'thesis_nav_menu' ] = get_option( 'openhook_before_header_nav_menu' );
		$thesis[ 'thesis_hook_after_header' ][ 'action' ] = stripslashes( get_option( 'openhook_after_header' ) );
		$thesis[ 'thesis_hook_after_header' ][ 'php' ] = get_option( 'openhook_after_header_php' );
		$thesis[ 'thesis_hook_header' ][ 'action' ] = stripslashes( get_option( 'openhook_header' ) );
		$thesis[ 'thesis_hook_header' ][ 'php' ] = get_option( 'openhook_header_php' );
		$thesis[ 'thesis_hook_header' ][ 'unhook' ][ 'thesis_default_header' ] = get_option( 'openhook_header_default_header' );
		$thesis[ 'thesis_hook_before_title' ][ 'action' ] = stripslashes( get_option( 'openhook_before_title' ) );
		$thesis[ 'thesis_hook_before_title' ][ 'php' ] = get_option( 'openhook_before_title_php' );
		$thesis[ 'thesis_hook_after_title' ][ 'action' ] = stripslashes( get_option( 'openhook_after_title' ) );
		$thesis[ 'thesis_hook_after_title' ][ 'php' ] = get_option( 'openhook_after_title_php' );
		$thesis[ 'thesis_hook_before_content_box' ][ 'action' ] = stripslashes( get_option( 'openhook_before_content_box' ) );
		$thesis[ 'thesis_hook_before_content_box' ][ 'php' ] = get_option( 'openhook_before_content_box_php' );
		$thesis[ 'thesis_hook_after_content_box' ][ 'action' ] = stripslashes( get_option( 'openhook_after_content_box' ) );
		$thesis[ 'thesis_hook_after_content_box' ][ 'php' ] = get_option( 'openhook_after_content_box_php' );
		$thesis[ 'thesis_hook_before_content' ][ 'action' ] = stripslashes( get_option( 'openhook_before_content' ) );
		$thesis[ 'thesis_hook_before_content' ][ 'php' ] = get_option( 'openhook_before_content_php' );
		$thesis[ 'thesis_hook_after_content' ][ 'action' ] = stripslashes( get_option( 'openhook_after_content' ) );
		$thesis[ 'thesis_hook_after_content' ][ 'php' ] = get_option( 'openhook_after_content_php' );
		$thesis[ 'thesis_hook_before_content_area' ][ 'action' ] = stripslashes( get_option( 'openhook_before_content_area' ) );
		$thesis[ 'thesis_hook_before_content_area' ][ 'php' ] = get_option( 'openhook_before_content_area_php' );
		$thesis[ 'thesis_hook_after_content_area' ][ 'action' ] = stripslashes( get_option( 'openhook_after_content_area' ) );
		$thesis[ 'thesis_hook_after_content_area' ][ 'php' ] = get_option( 'openhook_after_content_area_php' );
		$thesis[ 'thesis_hook_after_content_area' ][ 'unhook' ][ 'thesis_post_navigation' ] = get_option( 'openhook_after_content_post_navigation' );
		$thesis[ 'thesis_hook_after_content_area' ][ 'unhook' ][ 'thesis_prev_next_posts' ] = get_option( 'openhook_after_content_prev_next_posts' );
		$thesis[ 'thesis_hook_post_box_top' ][ 'action' ] = stripslashes( get_option( 'openhook_post_box_top' ) );
		$thesis[ 'thesis_hook_post_box_top' ][ 'php' ] = get_option( 'openhook_post_box_top_php' );
		$thesis[ 'thesis_hook_post_box_bottom' ][ 'action' ] = stripslashes( get_option( 'openhook_post_box_bottom' ) );
		$thesis[ 'thesis_hook_post_box_bottom' ][ 'php' ] = get_option( 'openhook_post_box_bottom_php' );
		$thesis[ 'thesis_hook_content_box_top' ][ 'action' ] = stripslashes( get_option( 'openhook_content_box_top' ) );
		$thesis[ 'thesis_hook_content_box_top' ][ 'php' ] = get_option( 'openhook_content_box_top_php' );
		$thesis[ 'thesis_hook_content_box_bottom' ][ 'action' ] = stripslashes( get_option( 'openhook_content_box_bottom' ) );
		$thesis[ 'thesis_hook_content_box_bottom' ][ 'php' ] = get_option( 'openhook_content_box_bottom_php' );
		$thesis[ 'thesis_hook_feature_box' ][ 'action' ] = stripslashes( get_option( 'openhook_feature_box' ) );
		$thesis[ 'thesis_hook_feature_box' ][ 'php' ] = get_option( 'openhook_feature_box_php' );
		$thesis[ 'thesis_hook_before_post_box' ][ 'action' ] = stripslashes( get_option( 'openhook_before_post_box' ) );
		$thesis[ 'thesis_hook_before_post_box' ][ 'php' ] = get_option( 'openhook_before_post_box_php' );
		$thesis[ 'thesis_hook_after_post_box' ][ 'action'] = get_option( 'openhook_after_post_box' );
		$thesis[ 'thesis_hook_after_post_box' ][ 'php' ] = get_option( 'openhook_after_post_box_php' );
		$thesis[ 'thesis_hook_before_teasers_box' ][ 'action' ] = stripslashes( get_option( 'openhook_before_teasers_box' ) );
		$thesis[ 'thesis_hook_before_teasers_box' ][ 'php' ] = get_option( 'openhook_before_teasers_box_php' );
		$thesis[ 'thesis_hook_after_teasers_box' ][ 'action' ] = stripslashes( get_option( 'openhook_after_teasers_box' ) );
		$thesis[ 'thesis_hook_after_teasers_box' ][ 'php' ] = get_option( 'openhook_after_teasers_box_php' );
		$thesis[ 'thesis_hook_before_post' ][ 'action' ] = stripslashes( get_option( 'openhook_before_post' ) );
		$thesis[ 'thesis_hook_before_post' ][ 'php'] = get_option( 'openhook_before_post_php' );
		$thesis[ 'thesis_hook_after_post' ][ 'action' ] = stripslashes( get_option( 'openhook_after_post' ) );
		$thesis[ 'thesis_hook_after_post' ][ 'php' ] = get_option( 'openhook_after_post_php' );
		$thesis[ 'thesis_hook_after_post' ][ 'unhook' ][ 'thesis_post_tags' ] = get_option( 'openhook_after_post_post_tags' );
		$thesis[ 'thesis_hook_after_post' ][ 'unhook' ][ 'thesis_comments_link' ] = get_option( 'openhook_after_post_comments_link' );
		$thesis[ 'thesis_hook_before_teaser_box' ][ 'action' ] = stripslashes( get_option( 'openhook_before_teaser_box' ) );
		$thesis[ 'thesis_hook_before_teaser_box' ][ 'php' ] = get_option( 'openhook_before_teaser_box_php' );
		$thesis[ 'thesis_hook_after_teaser_box' ][ 'action' ] = stripslashes( get_option( 'openhook_after_teaser_box' ) );
		$thesis[ 'thesis_hook_after_teaser_box' ][ 'php' ] = get_option( 'openhook_after_teaser_box_php' );
		$thesis[ 'thesis_hook_before_teaser' ][ 'action' ] = stripslashes( get_option( 'openhook_before_teaser' ) );
		$thesis[ 'thesis_hook_before_teaser' ][ 'php' ] = get_option( 'openhook_before_teaser_php' );
		$thesis[ 'thesis_hook_after_teaser' ][ 'action' ] = stripslashes( get_option( 'openhook_after_teaser' ) );
		$thesis[ 'thesis_hook_after_teaser' ][ 'php' ] = get_option( 'openhook_after_teaser_php' );
		$thesis[ 'thesis_hook_before_headline' ][ 'action' ] = stripslashes( get_option( 'openhook_before_headline' ) );
		$thesis[ 'thesis_hook_before_headline' ][ 'php' ] = get_option( 'openhook_before_headline_php' );
		$thesis[ 'thesis_hook_after_headline' ][ 'action' ] = stripslashes( get_option( 'openhook_after_headline' ) );
		$thesis[ 'thesis_hook_after_headline' ][ 'php' ] = get_option( 'openhook_after_headline_php' );
		$thesis[ 'thesis_hook_before_teaser_headline' ][ 'action' ] = stripslashes( get_option( 'openhook_before_teaser_headline' ) );
		$thesis[ 'thesis_hook_before_teaser_headline' ][ 'php' ] = get_option( 'openhook_before_teaser_headline_php' );
		$thesis[ 'thesis_hook_after_teaser_headline' ][ 'action' ] = stripslashes( get_option( 'openhook_after_teaser_headline' ) );
		$thesis[ 'thesis_hook_after_teaser_headline' ][ 'php' ] = get_option( 'openhook_after_teaser_headline_php' );
		$thesis[ 'thesis_hook_byline_item' ][ 'action' ] = stripslashes( get_option( 'openhook_byline_item' ) );
		$thesis[ 'thesis_hook_byline_item' ][ 'php' ] = get_option( 'openhook_byline_item_php' );
		$thesis[ 'thesis_hook_before_comment_meta' ][ 'action' ] = stripslashes( get_option( 'openhook_before_comment_meta' ) );
		$thesis[ 'thesis_hook_before_comment_meta' ][ 'php' ] = get_option( 'openhook_before_comment_meta_php' );
		$thesis[ 'thesis_hook_after_comment_meta' ][ 'action' ] = stripslashes( get_option( 'openhook_after_comment_meta' ) );
		$thesis[ 'thesis_hook_after_comment_meta' ][ 'php' ] = get_option( 'openhook_after_comment_meta_php' );
		$thesis[ 'thesis_hook_comment_field' ][ 'action' ] = stripslashes( get_option( 'openhook_comment_field' ) );
		$thesis[ 'thesis_hook_comment_field' ][ 'php' ] = get_option( 'openhook_comment_field_php' );
		$thesis[ 'thesis_hook_comment_form' ][ 'action' ] = stripslashes( get_option( 'openhook_comment_form' ) );
		$thesis[ 'thesis_hook_comment_form' ][ 'php' ] = get_option( 'openhook_comment_form_php' );
		$thesis[ 'thesis_hook_comment_form' ][ 'unhook' ][ 'show_subscription_checkbox' ] = get_option( 'openhook_comment_form_show_subscription_checkbox' );
		$thesis[ 'thesis_hook_archives_template' ][ 'action' ] = stripslashes( get_option( 'openhook_archives_template' ) );
		$thesis[ 'thesis_hook_archives_template' ][ 'php' ] = get_option( 'openhook_archives_template_php' );
		$thesis[ 'thesis_hook_archives_template' ][ 'unhook' ][ 'thesis_archives_template' ] = get_option( 'openhook_archives_template_archives_template' );
		$thesis[ 'thesis_hook_custom_template' ][ 'action' ] = stripslashes( get_option( 'openhook_custom_template' ) );
		$thesis[ 'thesis_hook_custom_template' ][ 'php' ] = get_option( 'openhook_custom_template_php' );
		$thesis[ 'thesis_hook_custom_template' ][ 'unhook' ][ 'thesis_custom_template_sample' ] = get_option( 'openhook_custom_template_custom_template_sample' );
		$thesis[ 'thesis_hook_faux_admin' ][ 'action' ] = stripslashes( get_option( 'openhook_faux_admin' ) );
		$thesis[ 'thesis_hook_faux_admin' ][ 'php' ] = get_option( 'openhook_faux_admin_php' );
		$thesis[ 'thesis_hook_404_title' ][ 'action' ] = stripslashes( get_option( 'openhook_404_title' ) );
		$thesis[ 'thesis_hook_404_title' ][ 'php' ] = get_option( 'openhook_404_title_php' );
		$thesis[ 'thesis_hook_404_title' ][ 'unhook' ][ 'thesis_404_title' ] = get_option( 'openhook_404_title_404_title' );
		$thesis[ 'thesis_hook_404_content' ][ 'action' ] = stripslashes( get_option( 'openhook_404_content' ) );
		$thesis[ 'thesis_hook_404_content' ][ 'php' ] = get_option( 'openhook_404_content_php' );
		$thesis[ 'thesis_hook_404_content' ][ 'unhook' ][ 'thesis_404_content' ] = get_option( 'openhook_404_content_404_content' );
		$thesis[ 'thesis_hook_before_sidebars' ][ 'action' ] = stripslashes( get_option( 'openhook_before_sidebars' ) );
		$thesis[ 'thesis_hook_before_sidebars' ][ 'php' ] = get_option( 'openhook_before_sidebars_php' );
		$thesis[ 'thesis_hook_after_sidebars' ][ 'action' ] = stripslashes( get_option( 'openhook_after_sidebars' ) );
		$thesis[ 'thesis_hook_after_sidebars' ][ 'php' ] = get_option( 'openhook_after_sidebars_php' );
		$thesis[ 'thesis_hook_after_multimedia_box' ][ 'action' ] = stripslashes( get_option( 'openhook_after_multimedia_box' ) );
		$thesis[ 'thesis_hook_after_multimedia_box' ][ 'php' ] = get_option( 'openhook_after_multimedia_box_php' );
		$thesis[ 'thesis_hook_multimedia_box' ][ 'action' ] = stripslashes( get_option( 'openhook_multimedia_box' ) );
		$thesis[ 'thesis_hook_multimedia_box' ][ 'php' ] = get_option( 'openhook_multimedia_box_php' );
		$thesis[ 'thesis_hook_before_sidebar_1' ][ 'action' ] = stripslashes( get_option( 'openhook_before_sidebar_1' ) );
		$thesis[ 'thesis_hook_before_sidebar_1' ][ 'php' ] = get_option( 'openhook_before_sidebar_1_php' );
		$thesis[ 'thesis_hook_after_sidebar_1' ][ 'action' ] = stripslashes( get_option( 'openhook_after_sidebar_1' ) );
		$thesis[ 'thesis_hook_after_sidebar_1' ][ 'php' ] = get_option( 'openhook_after_sidebar_1_php' );
		$thesis[ 'thesis_hook_before_sidebar_2' ][ 'action' ] = stripslashes( get_option( 'openhook_before_sidebar_2' ) );
		$thesis[ 'thesis_hook_before_sidebar_2' ][ 'php' ] = get_option( 'openhook_before_sidebar_2_php' );
		$thesis[ 'thesis_hook_after_sidebar_2' ][ 'action' ] = stripslashes( get_option( 'openhook_after_sidebar_2' ) );
		$thesis[ 'thesis_hook_after_sidebar_2' ][ 'php' ] = get_option( 'openhook_after_sidebar_2_php' );
		$thesis[ 'thesis_hook_before_footer' ][ 'action' ] = stripslashes( get_option( 'openhook_before_footer' ) );
		$thesis[ 'thesis_hook_before_footer' ][ 'php' ] = get_option( 'openhook_before_footer_php' );
		$thesis[ 'thesis_hook_after_footer' ][ 'action' ] = stripslashes( get_option( 'openhook_after_footer' ) );
		$thesis[ 'thesis_hook_after_footer' ][ 'php' ] = get_option( 'openhook_after_footer_php' );
		$thesis[ 'thesis_hook_footer' ][ 'action' ] = stripslashes( get_option( 'openhook_footer' ) );
		$thesis[ 'thesis_hook_footer' ][ 'php' ] = get_option( 'openhook_footer_php' );
		$thesis[ 'thesis_hook_footer' ][ 'unhook' ][ 'thesis_attribution' ] = get_option( 'openhook_footer_thesis_attribution' );

		update_option( 'openhook_thesis', $thesis );

		echo '<div id="setting-error-settings_updated" class="updated settings-error"> 
<p><strong>' . __( 'You have successfully upgraded to OpenHook 3.', 'openhook' ) . '</strong></p></div>';
	}
	else {
		wp_die( __( 'You do not have permission to do this.', 'openhook' ) );
	}
}

/**
 * Delete legacy options
 */
function openhook_delete_options( $legacy = false ) {
	if ( current_user_can( 'delete_users' ) && defined( 'OPENHOOK_SAFEGUARD' ) ) {
		if ( $legacy == 'legacy' ) {
			delete_option( 'openhook_save_button' );
			delete_option( 'openhook_wp_head' );
			delete_option( 'openhook_wp_head_php' );
			delete_option( 'openhook_before_html' );
			delete_option( 'openhook_before_html_php' );
			delete_option( 'openhook_after_html' );
			delete_option( 'openhook_after_html_php' );
			delete_option( 'openhook_after_html_footer_scripts' );
			delete_option( 'openhook_after_html_ie_clear' );
			delete_option( 'openhook_before_header' );
			delete_option( 'openhook_before_header_php' );
			delete_option( 'openhook_before_header_nav_menu' );
			delete_option( 'openhook_after_header' );
			delete_option( 'openhook_after_header_php' );
			delete_option( 'openhook_header' );
			delete_option( 'openhook_header_php' );
			delete_option( 'openhook_header_default_header' );
			delete_option( 'openhook_before_title' );
			delete_option( 'openhook_before_title_php' );
			delete_option( 'openhook_after_title' );
			delete_option( 'openhook_after_title_php' );
			delete_option( 'openhook_before_content_box' );
			delete_option( 'openhook_before_content_box_php' );
			delete_option( 'openhook_after_content_box' );
			delete_option( 'openhook_after_content_box_php' );
			delete_option( 'openhook_before_content' );
			delete_option( 'openhook_before_content_php' );
			delete_option( 'openhook_after_content' );
			delete_option( 'openhook_after_content_php' );
			delete_option( 'openhook_before_content_area' );
			delete_option( 'openhook_before_content_area_php' );
			delete_option( 'openhook_after_content_area' );
			delete_option( 'openhook_after_content_area_php' );
			delete_option( 'openhook_after_content_post_navigation' );
			delete_option( 'openhook_after_content_prev_next_posts' );
			delete_option( 'openhook_post_box_top' );
			delete_option( 'openhook_post_box_top_php' );
			delete_option( 'openhook_post_box_bottom' );
			delete_option( 'openhook_post_box_bottom_php' );
			delete_option( 'openhook_content_box_top' );
			delete_option( 'openhook_content_box_top_php' );
			delete_option( 'openhook_content_box_bottom' );
			delete_option( 'openhook_content_box_bottom_php' );
			delete_option( 'openhook_feature_box' );
			delete_option( 'openhook_feature_box_php' );
			delete_option( 'openhook_before_post_box' );
			delete_option( 'openhook_before_post_box_php' );
			delete_option( 'openhook_before_post_box_add_post_image' );
			delete_option( 'openhook_after_post_box' );
			delete_option( 'openhook_after_post_box_php' );
			delete_option( 'openhook_before_teasers_box' );
			delete_option( 'openhook_before_teasers_box_php' );
			delete_option( 'openhook_after_teasers_box' );
			delete_option( 'openhook_after_teasers_box_php' );
			delete_option( 'openhook_before_post' );
			delete_option( 'openhook_before_post_php' );
			delete_option( 'openhook_after_post' );
			delete_option( 'openhook_after_post_php' );
			delete_option( 'openhook_after_post_trackback_rdf' );
			delete_option( 'openhook_after_post_post_tags' );
			delete_option( 'openhook_after_post_comments_link' );
			delete_option( 'openhook_before_teaser_box' );
			delete_option( 'openhook_before_teaser_box_php' );
			delete_option( 'openhook_before_teaser_box_add_thumb' );
			delete_option( 'openhook_after_teaser_box' );
			delete_option( 'openhook_after_teaser_box_php' );
			delete_option( 'openhook_before_teaser' );
			delete_option( 'openhook_before_teaser_php' );
			delete_option( 'openhook_after_teaser' );
			delete_option( 'openhook_after_teaser_php' );
			delete_option( 'openhook_before_headline' );
			delete_option( 'openhook_before_headline_php' );
			delete_option( 'openhook_after_headline' );
			delete_option( 'openhook_after_headline_php' );
			delete_option( 'openhook_before_teaser_headline' );
			delete_option( 'openhook_before_teaser_headline_php' );
			delete_option( 'openhook_after_teaser_headline' );
			delete_option( 'openhook_after_teaser_headline_php' );
			delete_option( 'openhook_byline_item' );
			delete_option( 'openhook_byline_item_php' );
			delete_option( 'openhook_before_comment_meta' );
			delete_option( 'openhook_before_comment_meta_php' );
			delete_option( 'openhook_after_comment_meta' );
			delete_option( 'openhook_after_comment_meta_php' );
			delete_option( 'openhook_comment_field' );
			delete_option( 'openhook_comment_field_php' );
			delete_option( 'openhook_comment_form' );
			delete_option( 'openhook_comment_form_php' );
			delete_option( 'openhook_comment_form_show_subscription_checkbox' );
			delete_option( 'openhook_archives_template' );
			delete_option( 'openhook_archives_template_php' );
			delete_option( 'openhook_archives_template_archives_template' );
			delete_option( 'openhook_archive_info_default_archive_info' );
			delete_option( 'openhook_custom_template' );
			delete_option( 'openhook_custom_template_php' );
			delete_option( 'openhook_custom_template_custom_template_sample' );
			delete_option( 'openhook_faux_admin' );
			delete_option( 'openhook_faux_admin_php' );
			delete_option( 'openhook_archive_info' );
			delete_option( 'openhook_archive_info_php' );
			delete_option( 'openhook_404_title' );
			delete_option( 'openhook_404_title_php' );
			delete_option( 'openhook_404_title_404_title' );
			delete_option( 'openhook_404_content' );
			delete_option( 'openhook_404_content_php' );
			delete_option( 'openhook_404_content_404_content' );
			delete_option( 'openhook_before_sidebars' );
			delete_option( 'openhook_before_sidebars_php' );
			delete_option( 'openhook_after_sidebars' );
			delete_option( 'openhook_after_sidebars_php' );
			delete_option( 'openhook_after_multimedia_box' );
			delete_option( 'openhook_after_multimedia_box_php' );
			delete_option( 'openhook_multimedia_box' );
			delete_option( 'openhook_multimedia_box_php' );
			delete_option( 'openhook_before_sidebar_1' );
			delete_option( 'openhook_before_sidebar_1_php' );
			delete_option( 'openhook_after_sidebar_1' );
			delete_option( 'openhook_after_sidebar_1_php' );
			delete_option( 'openhook_before_sidebar_2' );
			delete_option( 'openhook_before_sidebar_2_php' );
			delete_option( 'openhook_after_sidebar_2' );
			delete_option( 'openhook_after_sidebar_2_php' );
			delete_option( 'openhook_before_footer' );
			delete_option( 'openhook_before_footer_php' );
			delete_option( 'openhook_after_footer' );
			delete_option( 'openhook_after_footer_php' );
			delete_option( 'openhook_footer' );
			delete_option( 'openhook_footer_php' );
			delete_option( 'openhook_footer_thesis_attribution' );
			delete_option( 'openhook_footer_debug_info' );
			delete_option( 'openhook_footer_honeypot' );
			delete_option( 'openhook_wp_footer' );
			delete_option( 'openhook_wp_footer_php' );
			delete_option( 'openhook_footer_admin_link' );
	
			echo '<div id="setting-error-settings_updated" class="updated settings-error"> 
	<p><strong>' . __( 'Old options deleted.', 'openhook' ) . '</strong></p></div>';
		}
		else {
			delete_option( 'openhook_general' );
			delete_option( 'openhook_thesis' );
			delete_option( 'openhook_wordpress' );

			echo '<div id="setting-error-settings_updated" class="updated settings-error"> 
	<p><strong>' . __( 'OpenHook has been uninstalled.', 'openhook' ) . '</strong></p></div>';
		}
	}
	else {
		wp_die( __( 'You do not have permission to do this.', 'openhook' ) );
	}
}

/**
 * An array of available WordPress hooks
 */
function openhook_thesis_hooks() {
	$thesis_hooks = array(
		'thesis_hook_before_html' => array(
			'name' => 'thesis_hook_before_html',
		),
		'thesis_hook_after_html' => array(
			'name' => 'thesis_hook_after_html',
		),
		'thesis_hook_before_header' => array(
			'name' => 'thesis_hook_before_header',
			'unhook' => array(
				'thesis_nav_menu' => array(
					'name' => 'thesis_nav_menu',
					'desc' => __( 'The default navigation menu', 'openhook' ),
				),
			),
		),
		'thesis_hook_after_header' => array(
			'name' => 'thesis_hook_after_header',
		),
		'thesis_hook_header' => array(
			'name' => 'thesis_hook_header',
			'unhook' => array(
				'thesis_default_header' => array(
					'name' => 'thesis_default_header',
					'desc' => __( 'Default header', 'openhook' ),
				),
			),
		),
		'thesis_hook_before_title' => array(
			'name' => 'thesis_hook_before_title',
		),
		'thesis_hook_after_title' => array(
			'name' => 'thesis_hook_after_title',
		),
		'thesis_hook_first_nav_item' => array(
			'name' => 'thesis_hook_first_nav_item',
		),
		'thesis_hook_last_nav_item' => array(
			'name' => 'thesis_hook_last_nav_item',
		),
		'thesis_hook_before_content_box' => array(
			'name' => 'thesis_hook_before_content_box',
		),
		'thesis_hook_after_content_box' => array(
			'name' => 'thesis_hook_after_content_box',
		),
		'thesis_hook_content_box_top' => array(
			'name' => 'thesis_hook_content_box_top',
		),
		'thesis_hook_content_box_bottom' => array(
			'name' => 'thesis_hook_content_box_bottom',
		),
		'thesis_hook_before_content' => array(
			'name' => 'thesis_hook_before_content',
		),
		'thesis_hook_after_content' => array(
			'name' => 'thesis_hook_after_content',
			'unhook' => array(
				'thesis_post_navigation' => array(
					'name' => 'thesis_post_navigation',
					'desc' => __( 'Displays links to the next or previous post', 'openhook' ),
				),
				'thesis_prev_next_posts' => array(
					'name' => 'thesis_prev_next_posts',
					'desc' => __( 'Displays links to the next or previous pages of an archive listing', 'openhook' ),
				),
			),
		),
		'thesis_hook_before_content_area' => array(
			'name' => 'thesis_hook_before_content_area',
		),
		'thesis_hook_after_content_area' => array(
			'name' => 'thesis_hook_after_content_area',
		),
		'thesis_hook_feature_box' => array(
			'name' => 'thesis_hook_feature_box',
		),
		'thesis_hook_before_post_box' => array(
			'name' => 'thesis_hook_before_post_box',
		),
		'thesis_hook_after_post_box' => array(
			'name' => 'thesis_hook_after_post_box',
		),
		'thesis_hook_after_content_box' => array(
			'name' => 'thesis_hook_after_content_box',
		),
		'thesis_hook_post_box_top' => array(
			'name' => 'thesis_hook_post_box_top',
		),
		'thesis_hook_post_box_bottom' => array(
			'name' => 'thesis_hook_post_box_bottom',
		),
		'thesis_hook_before_teasers_box' => array(
			'name' => 'thesis_hook_before_teasers_box',
		),
		'thesis_hook_after_teasers_box' => array(
			'name' => 'thesis_hook_after_teasers_box',
		),
		'thesis_hook_before_post' => array(
			'name' => 'thesis_hook_before_post',
		),
		'thesis_hook_after_post' => array(
			'name' => 'thesis_hook_after_post',
			'unhook' => array(
				'thesis_post_tags' => array(
					'name' => 'thesis_post_tags',
					'desc' => __( 'Displays a list of tags applied to the post', 'openhook' ),
				),
				'thesis_comments_link' => array(
					'name' => 'thesis_comments_link',
					'desc' => __( 'Displays a link to the comments of a post', 'openhook' ),
				),
			),
		),
		'thesis_hook_before_teaser_box' => array(
			'name' => 'thesis_hook_before_teaser_box',
		),
		'thesis_hook_after_teaser_box' => array(
			'name' => 'thesis_hook_after_teaser_box',
		),
		'thesis_hook_before_teaser' => array(
			'name' => 'thesis_hook_before_teaser',
		),
		'thesis_hook_after_teaser' => array(
			'name' => 'thesis_hook_after_teaser',
		),
		'thesis_hook_before_headline' => array(
			'name' => 'thesis_hook_before_headline',
		),
		'thesis_hook_after_headline' => array(
			'name' => 'thesis_hook_after_headline',
		),
		'thesis_hook_before_teaser_headline' => array(
			'name' => 'thesis_hook_before_teaser_headline',
		),
		'thesis_hook_after_teaser_headline' => array(
			'name' => 'thesis_hook_after_teaser_headline',
		),
		'thesis_hook_byline_item' => array(
			'name' => 'thesis_hook_byline_item',
		),
		'thesis_hook_before_comment_meta' => array(
			'name' => 'thesis_hook_before_comment_meta',
		),
		'thesis_hook_after_comment_meta' => array(
			'name' => 'thesis_hook_after_comment_meta',
		),
		'thesis_hook_after_comment' => array(
			'name' => 'thesis_hook_after_comment',
		),
		'thesis_hook_after_comments' => array(
			'name' => 'thesis_hook_after_comments',
		),
		'thesis_hook_comment_form_top' => array(
			'name' => 'thesis_hook_comment_form_top',
		),
		'thesis_hook_comment_field' => array(
			'name' => 'thesis_hook_comment_field',
		),
		'thesis_hook_after_comment_box' => array(
			'name' => 'thesis_hook_after_comment_box',
			'unhook' => array(
				'show_subscription_checkbox' => array(
					'name' => 'show_subscription_checkbox',
					'desc' => __( 'The Subscribe to Comments plugin subscription checkbox', 'openhook' ),
				),
			),
		),
		'thesis_hook_comment_form_bottom' => array(
			'name' => 'thesis_hook_comment_form_bottom',
		),
		'thesis_hook_archives_template' => array(
			'name' => 'thesis_hook_archives_template',
			'unhook' => array(
				'thesis_archives_template' => array(
					'name' => 'thesis_archives_template',
					'desc' => __( 'Default output for the archives template', 'openhook' ),
				),
			),
		),
		'thesis_hook_custom_template' => array(
			'name' => 'thesis_hook_custom_template',
			'unhook' => array(
				'thesis_custom_template_sample' => array(
					'name' => 'thesis_custom_template_sample',
					'desc' => __( 'Default sample output for the custom template', 'openhook' ),
				),
			),
		),
		'thesis_hook_faux_admin' => array(
			'name' => 'thesis_hook_faux_admin',
		),
		'thesis_hook_404_title' => array(
			'name' => 'thesis_hook_404_title',
			'unhook' => array(
				'thesis_404_title' => array(
					'name' => 'thesis_404_title',
					'desc' => __( 'Displays default 404 error page title', 'openhook' ),
				),
			),
		),
		'thesis_hook_404_content' => array(
			'name' => 'thesis_hook_404_content',
			'unhook' => array(
				'thesis_404_content' => array(
					'name' => 'thesis_404_content',
					'desc' => __( 'Displays default 404 error page content', 'openhook' ),
				),
			),
		),
		'thesis_hook_before_sidebars' => array(
			'name' => 'thesis_hook_before_sidebars',
		),
		'thesis_hook_after_sidebars' => array(
			'name' => 'thesis_hook_after_sidebars',
		),
		'thesis_hook_multimedia_box' => array(
			'name' => 'thesis_hook_multimedia_box',
		),
		'thesis_hook_after_multimedia_box' => array(
			'name' => 'thesis_hook_after_multimedia_box',
		),
		'thesis_hook_before_sidebar_1' => array(
			'name' => 'thesis_hook_before_sidebar_1',
		),
		'thesis_hook_after_sidebar_1' => array(
			'name' => 'thesis_hook_after_sidebar_1',
		),
		'thesis_hook_before_sidebar_2' => array(
			'name' => 'thesis_hook_before_sidebar_2',
		),
		'thesis_hook_after_sidebar_2' => array(
			'name' => 'thesis_hook_after_sidebar_2',
		),
		'thesis_hook_before_footer' => array(
			'name' => 'thesis_hook_before_footer',
		),
		'thesis_hook_after_footer' => array(
			'name' => 'thesis_hook_after_footer',
		),
		'thesis_hook_footer' => array(
			'name' => 'thesis_hook_footer',
			'unhook' => array(
				'thesis_attribution' => array(
					'name' => 'thesis_attribution',
					'desc' => __( 'Displays DIYthemes attribution &amp; link', 'openhook' ),
				),
			),
		),
	);

	return $thesis_hooks;
}

/**
 * An array of available WordPress hooks
 */
function openhook_wordpress_hooks() {
	$wordpress_hooks = array(
		'wp_head' => array(
			'name' => 'wp_head',
			'desc' => __( 'Executes within your site&rsquo;s <code>head</code> section. Useful for outputting, for example, meta tags.', 'openhook' ),
			'unhook' => array(
				'feed_links' => array(
					'name' => 'feed_links',
					'desc' => __( 'Many themes support the automatic addition of <code>link</code> tags pointing to your site&rsquo;s primary &amp; comments feeds. Disable to prevent this behavior.', 'openhook' ),
					'priority' => 2,
				),
				'feed_links_extra' => array(
					'name' => 'feed_links_extra',
					'desc' => __( 'Similar to <code>feed_links</code>, except the <code>link</code> tags output by this hook point to secondary feeds, such as category feeds.', 'openhook' ),
					'priority' => 3,
				),
				'wp_enqueue_scripts' => array(
					'name' => 'wp_enqueue_scripts',
					'desc' => __( 'Is used to queue &amp; output various scripts. Disabling this prevents most scripts from appearing in your header, allowing you to manage them all manually, if you so choose.', 'openhook' ),
					'priority' => 1,
					'advanced' => 1,
				),
				'wp_generator' => array(
					'name' => 'wp_generator',
					'desc' => sprintf( __( 'Is used to output a <code>meta</code> tag crediting WordPress as the software powering your site. Removing this is a form of <a href="%s">security through obscurity</a>.', 'openhook' ), 'http://en.wikipedia.org/wiki/Security_through_obscurity' ),
				), 
			),
		),
		'wp_footer' => array(
			'name' => 'wp_footer',
			'desc' => __( 'Executed either within the footer section (<code>div#footer</code> or <code>footer</code>) or immediately before the closing <code>html</code> tag, this hook is commonly used to output stats tracking scripts or other asyncronously loaded JavaScript.', 'openhook' ),
			'unhook' => array(
				'wp_print_footer_scripts' => array(
					'name' => 'wp_print_footer_scripts',
					'desc' => __( 'Is used to queue &amp; output barious scripts. Disabling this prevents most scripts from appearing in your footer, allowing you to manage them all manually, if you so choose.', 'openhook' ),
					'priority' => 20,
					'advanced' => 1,
				),
			),
		),
	);

	return $wordpress_hooks;
}


/*
add_action( 'wp_head',             'rsd_link'                               );
add_action( 'wp_head',             'wlwmanifest_link'                       );
add_action( 'wp_head',             'adjacent_posts_rel_link_wp_head', 10, 0 );
add_action( 'wp_head',             'locale_stylesheet'                      );
add_action( 'publish_future_post', 'check_and_publish_future_post',   10, 1 );
add_action( 'wp_head',             'noindex',                          1    );
add_action( 'wp_head',             'wp_print_styles',                  8    );
add_action( 'wp_head',             'wp_print_head_scripts',            9    );
add_action( 'wp_head',             'wp_generator'                           );
add_action( 'wp_head',             'rel_canonical'                          );
add_action( 'wp_head',             'wp_shortlink_wp_head',            10, 0 );
*/

}

# Processes OpenHook in the global domain
$openhook = new OpenHook();