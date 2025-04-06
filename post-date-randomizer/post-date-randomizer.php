<?php
/*
Plugin Name: Post Date Randomizer
Description: Simple plugin that bulk changes the date/s of published posts and/or approved comments to any random date within a specified range.
Version:     1.4.1
Author:      Anty
Author URI:  https://profiles.wordpress.org/wellbeingtips
License:     GPL3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Text Domain: post-date-randomizer
Domain Path: /languages
Donate link: https://www.paypal.com/paypalme/wellbeingsupport/
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// --- Constants ---
define('PDR_VERSION', '1.4');
define('PDR_PLUGIN_FILE', __FILE__); // Define plugin file path

// --- Load Text Domain ---
add_action( 'plugins_loaded', 'pdr_load_textdomain' );
function pdr_load_textdomain() {
    load_plugin_textdomain( 'post-date-randomizer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

// --- Settings Page Setup ---
add_action( 'admin_menu', 'pdr_add_menu_page' );
function pdr_add_menu_page() {
	add_menu_page(
		__( 'Date Randomizer', 'post-date-randomizer' ), // Page Title
		__( 'Date Randomizer', 'post-date-randomizer' ), // Menu Title
		'manage_options',                                // Capability
		'pdr_randomizer_settings',                       // Menu Slug (unique)
		'pdr_render_settings_page',                      // Callback function
		'dashicons-calendar-alt'                         // Icon
	);
}

// --- Render Settings Page HTML ---
function pdr_render_settings_page() {
    // Display any feedback notices from the randomization action
    pdr_display_admin_notice();

	?>
    <div class="wrap">
        <h1><?php _e('Post Date Randomizer Settings', 'post-date-randomizer'); ?></h1>
         <div class="notice notice-warning inline" style="margin-bottom: 15px;">
             <p><strong><?php _e( 'Warning:', 'post-date-randomizer' ); ?></strong> <?php _e( 'This action is irreversible. Please backup your database before randomizing dates.', 'post-date-randomizer' ); ?></p>
        </div>

        <form method="post" action="options.php">
			<?php
            // Output security fields for the registered setting section
			settings_fields( "pdr_settings_section" );

            // Output the settings sections (we only have one)
			do_settings_sections( "pdr_settings_page" ); // Match page slug used in add_settings_section/field

            // Output save settings button
			submit_button( __('Save Settings', 'post-date-randomizer') );
			?>
        </form>
        <hr>
        <h2><?php _e('Run Randomizer', 'post-date-randomizer'); ?></h2>
        <p><?php _e('Click the button below to randomize dates according to the currently saved settings. Ensure you have saved any setting changes first.', 'post-date-randomizer'); ?></p>
        <a class="button button-primary" style="background-color: #d63638; border-color: #b02a2c; color: #fff;"
           href="<?php echo wp_nonce_url( admin_url( 'admin.php?action=pdr_do_randomize' ), 'pdr_randomize_action_nonce', 'pdr_nonce_field' ) ?>"
           onclick="return confirm('<?php echo esc_js(__('Are you absolutely sure you want to randomize dates? This action cannot be undone and requires a database backup!', 'post-date-randomizer')); ?>');">
           <?php _e('Randomize Selected Item Dates Now', 'post-date-randomizer'); ?>
        </a>

        <?php // --- Donation Section --- ?>
        <hr style="margin-top: 30px;">
        <div style="margin-top: 20px; padding: 15px; background-color: #f9f9f9; border: 1px solid #ccd0d4; border-radius: 4px;">
            <h3 style="margin-top: 0;"><?php _e('Support This Plugin', 'post-date-randomizer'); ?></h3>
            <p>
                <?php _e('If you find Post Date Randomizer useful, please consider making a small donation to support its continued development and maintenance.', 'post-date-randomizer'); ?>
            </p>
            <p>
                <a href="<?php echo esc_url('https://www.paypal.com/paypalme/wellbeingsupport/'); ?>" target="_blank" rel="noopener noreferrer" class="button button-secondary">
                    <?php _e('Donate via PayPal', 'post-date-randomizer'); ?>
                </a>
                &nbsp; <?php _e('Thank you for your support!', 'post-date-randomizer'); ?>
            </p>
        </div>
         <?php // --- End Donation Section --- ?>

    </div> <?php // End wrap ?>
	<?php
}

// --- Register Settings, Section, and Fields ---
add_action( "admin_init", "pdr_register_settings" );
function pdr_register_settings() {

    // Register the main setting section
	add_settings_section(
        "pdr_settings_section",                         // ID used in settings_fields()
        __("Randomization Settings", 'post-date-randomizer'), // Title for the section
        null,                                           // Callback for section description (optional)
        "pdr_settings_page"                             // Page slug where this section appears
    );

    // --- Register Settings Fields ---
	add_settings_field( "pdr_date1", __("Start Date:", 'post-date-randomizer'), "pdr_date1_element_callback", "pdr_settings_page", "pdr_settings_section" );
	add_settings_field( "pdr_date2", __("End Date:", 'post-date-randomizer'), "pdr_date2_element_callback", "pdr_settings_page", "pdr_settings_section" );
    add_settings_field( "pdr_randomize_posts", __("Randomize Posts?", 'post-date-randomizer'), "pdr_randomize_posts_element_callback", "pdr_settings_page", "pdr_settings_section" );
    add_settings_field( "pdr_randomize_comments", __("Randomize Comments?", 'post-date-randomizer'), "pdr_randomize_comments_element_callback", "pdr_settings_page", "pdr_settings_section" );
	add_settings_field( "pdr_post_type", __("Post Type:", 'post-date-randomizer'), "pdr_post_type_element_callback", "pdr_settings_page", "pdr_settings_section", ['class' => 'pdr-post-option'] ); // Add class for conditional display
	add_settings_field( "pdr_set_modified_date", __("Set post modified date also?", 'post-date-randomizer'), "pdr_set_modified_date_element_callback", "pdr_settings_page", "pdr_settings_section", ['class' => 'pdr-post-option'] ); // Add class for conditional display

    // --- Register the actual settings options ---
	register_setting( "pdr_settings_section", "pdr_date1", ['sanitize_callback' => 'sanitize_text_field'] );
	register_setting( "pdr_settings_section", "pdr_date2", ['sanitize_callback' => 'sanitize_text_field'] );
    register_setting( "pdr_settings_section", "pdr_randomize_posts", ['type' => 'integer', 'sanitize_callback' => 'absint', 'default' => 1] ); // Default ON
    register_setting( "pdr_settings_section", "pdr_randomize_comments", ['type' => 'integer', 'sanitize_callback' => 'absint', 'default' => 0] ); // Default OFF
	register_setting( "pdr_settings_section", "pdr_post_type", ['sanitize_callback' => 'sanitize_key'] );
	register_setting( "pdr_settings_section", "pdr_set_modified_date", ['type' => 'integer', 'sanitize_callback' => 'absint', 'default' => 1] ); // Default ON
}

// --- Callback Functions to Render Settings Fields ---

function pdr_date1_element_callback() {
    $date_format = 'Y-m-d H:i:s';
	$date1       = get_option( 'pdr_date1', date( $date_format, strtotime( "-1 year" ) ) );
	?>
    <input name="pdr_date1" type="text" id="pdr_date1" value="<?php echo esc_attr($date1); ?>" class="regular-text code" placeholder="<?php echo $date_format; ?>">
    <p class="description"><?php printf(__('Format: %s', 'post-date-randomizer'), $date_format); ?></p>
	<?php
}

function pdr_date2_element_callback() {
    $date_format = 'Y-m-d H:i:s';
	$date2       = get_option( 'pdr_date2', date( $date_format, time() ) );
	?>
    <input name="pdr_date2" type="text" id="pdr_date2" value="<?php echo esc_attr($date2); ?>" class="regular-text code" placeholder="<?php echo $date_format; ?>">
     <p class="description"><?php printf(__('Format: %s', 'post-date-randomizer'), $date_format); ?></p>
	<?php
}

function pdr_randomize_posts_element_callback() {
    $checked = get_option( 'pdr_randomize_posts', 1 );
    ?>
    <input name="pdr_randomize_posts" type="hidden" value="0" />
    <input name="pdr_randomize_posts" type="checkbox" id="pdr_randomize_posts" value="1" <?php checked( 1, $checked ); ?>/>
    <label for="pdr_randomize_posts"><?php _e('Enable randomizing dates for published posts.', 'post-date-randomizer'); ?></label>
    <?php
}

function pdr_randomize_comments_element_callback() {
    $checked = get_option( 'pdr_randomize_comments', 0 );
    ?>
    <input name="pdr_randomize_comments" type="hidden" value="0" />
    <input name="pdr_randomize_comments" type="checkbox" id="pdr_randomize_comments" value="1" <?php checked( 1, $checked ); ?>/>
    <label for="pdr_randomize_comments"><?php _e('Enable randomizing dates for approved comments.', 'post-date-randomizer'); ?></label>
    <?php
}

function pdr_post_type_element_callback() {
    $current_type = get_option( 'pdr_post_type', 'post' );
	$builtin      = ['post', 'page'];
	$cpts         = get_post_types( ['public' => true, '_builtin' => false], 'names', 'and' );
	$post_types   = array_merge( $builtin, $cpts );
    sort($post_types);
	?>
    <select name="pdr_post_type" id="pdr_post_type">
		<?php
		foreach ( $post_types as $pt ) {
            $post_type_object = get_post_type_object( $pt );
            $label = $post_type_object ? $post_type_object->labels->singular_name : ucfirst($pt);
			printf('<option value="%s" %s>%s</option>',
                esc_attr($pt),
                selected( $pt, $current_type, false ),
                esc_html($label) . ' (' . esc_html($pt) . ')'
            );
        } ?>
    </select>
    <p class="description"><?php _e('Select the type of posts whose dates should be randomized.', 'post-date-randomizer'); ?></p>
	<?php
}

function pdr_set_modified_date_element_callback() {
    $checked = get_option( 'pdr_set_modified_date', 1 );
	?>
    <input name="pdr_set_modified_date" type="hidden" value="0" />
    <input name="pdr_set_modified_date" type="checkbox" id="pdr_set_modified_date" value="1" <?php checked( 1, $checked ); ?>/>
    <label for="pdr_set_modified_date"><?php _e('Update the "Last Modified" date to match the new randomized "Published" date.', 'post-date-randomizer'); ?></label>
	<?php
}


// --- Add Javascript for Conditional Fields ---
add_action('admin_footer-toplevel_page_pdr_randomizer_settings', 'pdr_settings_page_footer_scripts');
function pdr_settings_page_footer_scripts() {
    ?>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        var postCheckbox = $('#pdr_randomize_posts');
        // Target the table row containing elements with the class 'pdr-post-option'
        var postOptionsRows = $('.pdr-post-option').closest('tr');

        function togglePostOptions() {
            if (postCheckbox.is(':checked')) {
                postOptionsRows.show();
            } else {
                postOptionsRows.hide();
            }
        }
        // Initial check on page load
        togglePostOptions();
        // Check when checkbox changes
        postCheckbox.on('change', togglePostOptions);
    });
</script>
    <?php
}


// --- Randomization Action Handler ---
add_action( 'admin_action_pdr_do_randomize', 'pdr_handle_randomize_action' );
function pdr_handle_randomize_action() {

    // 1. Security Checks
    if ( ! isset( $_GET['pdr_nonce_field'] ) || ! wp_verify_nonce( $_GET['pdr_nonce_field'], 'pdr_randomize_action_nonce' ) ) {
        wp_die(__( 'Security check failed (Nonce mismatch).', 'post-date-randomizer' ));
    }
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die(__( 'You do not have permission to perform this action.', 'post-date-randomizer' ));
    }

    // 2. Get Settings & Validate Date Range
    $date_format        = 'Y-m-d H:i:s';
    $date1_str          = get_option( 'pdr_date1', date( $date_format, strtotime( "-1 year" ) ) );
    $date2_str          = get_option( 'pdr_date2', date( $date_format, time() ) );
    $randomize_posts    = get_option( 'pdr_randomize_posts', 1 );
    $randomize_comments = get_option( 'pdr_randomize_comments', 0 );
    $date1_ts = strtotime( $date1_str );
    $date2_ts = strtotime( $date2_str );
    $error_message = null;
    if ( false === $date1_ts || false === $date2_ts ) {
        $error_message = __('Invalid Start or End Date format. Please use YYYY-MM-DD HH:MM:SS.', 'post-date-randomizer');
    } elseif ( $date1_ts >= $date2_ts ) {
        $error_message = __('Start Date must be earlier than End Date.', 'post-date-randomizer');
    }
    if ($error_message) {
        set_transient( 'pdr_randomize_notice', ['error' => $error_message], 60 );
        wp_safe_redirect( admin_url( 'admin.php?page=pdr_randomizer_settings' ) );
        exit;
    }

    // 3. Prepare Results Array
    $results = [
        'posts' => 0, 'comments' => 0, 'errors' => [],
        'ran_posts' => (bool) $randomize_posts,
        'ran_comments' => (bool) $randomize_comments
    ];

    // --- Randomize Posts ---
    if ( $randomize_posts ) {
        $cr_type           = get_option( 'pdr_post_type', 'post' );
        $set_modified_date = get_option( 'pdr_set_modified_date', 1 );
        $posts_to_update = get_posts( [
            'numberposts' => -1, 'post_status' => 'publish', 'post_type' => $cr_type,
            'fields' => 'ids', 'suppress_filters' => true
        ] );
        if ( ! empty( $posts_to_update ) ) {
            if ( $set_modified_date ) add_filter( 'wp_insert_post_data', 'pdr_filter_set_post_modified_date', 10, 1 );
            foreach ( $posts_to_update as $post_id ) {
                $random_date_ts = mt_rand( $date1_ts, $date2_ts );
                $post_date      = date( $date_format, $random_date_ts );
                $update_args = [
                    'ID' => $post_id, 'post_date' => $post_date,
                    'post_date_gmt' => null, 'edit_date' => true
                ];
                $result = wp_update_post( $update_args, true );
                if ( ! is_wp_error( $result ) ) $results['posts']++;
                else $results['errors'][] = sprintf(__('Error updating post %d: %s', 'post-date-randomizer'), $post_id, $result->get_error_message());
            }
            if ( $set_modified_date ) remove_filter( 'wp_insert_post_data', 'pdr_filter_set_post_modified_date', 10 );
        }
    }

    // --- Randomize Comments ---
    if ( $randomize_comments ) {
        $comments_to_update = get_comments( [
            'status' => 'approve', 'number' => 0, 'fields' => 'ids',
            'update_comment_meta_cache' => false, 'update_comment_post_cache' => false
        ] );
        if ( ! empty( $comments_to_update ) ) {
            foreach ( $comments_to_update as $comment_id ) {
                $random_date_ts = mt_rand( $date1_ts, $date2_ts );
                $comment_date   = date( $date_format, $random_date_ts );
                $update_args = [
                    'comment_ID' => $comment_id, 'comment_date' => $comment_date,
                    'comment_date_gmt' => null,
                ];
                $result = wp_update_comment( $update_args );
                if ( $result === 1 ) $results['comments']++;
                else $results['errors'][] = sprintf(__('Failed to update comment %d.', 'post-date-randomizer'), $comment_id);
            }
        }
    }

    // 4. Set Transient and Redirect Back
    set_transient( 'pdr_randomize_notice', $results, 60 );
    wp_safe_redirect( admin_url( 'admin.php?page=pdr_randomizer_settings' ) );
    exit;
}

// --- Helper Filter Function for Setting Modified Date ---
function pdr_filter_set_post_modified_date( $data ) {
	if ( isset( $data['post_date'] ) ) {
		$data['post_modified'] = $data['post_date'];
        if ( isset( $data['post_date_gmt'] ) && $data['post_date_gmt'] !== null ) {
            $data['post_modified_gmt'] = $data['post_date_gmt'];
        } else {
            $data['post_modified_gmt'] = get_gmt_from_date( $data['post_date'] );
        }
	}
	return $data;
}

// --- Display Admin Notice (Checks Transient) ---
function pdr_display_admin_notice() {
    if ( ! function_exists('get_current_screen') || ! is_admin() || get_current_screen()->id != 'toplevel_page_pdr_randomizer_settings' ) {
        return;
    }
    $notice_data = get_transient( 'pdr_randomize_notice' );
    if ( $notice_data ) {
        delete_transient( 'pdr_randomize_notice' );
        $notice_class = 'notice-success'; $messages = [];
        if ( isset($notice_data['error']) ) {
            $messages[] = $notice_data['error']; $notice_class = 'notice-error';
        } else {
             if ( $notice_data['ran_posts'] && isset($notice_data['posts']) ) {
                 $messages[] = sprintf( _n( 'Randomized %d post date.', 'Randomized %d post dates.', $notice_data['posts'], 'post-date-randomizer' ), $notice_data['posts'] );
            }
             if ( $notice_data['ran_comments'] && isset($notice_data['comments']) ) {
                 $messages[] = sprintf( _n( 'Randomized %d comment date.', 'Randomized %d comment dates.', $notice_data['comments'], 'post-date-randomizer' ), $notice_data['comments'] );
            }
            if ( empty($messages) && ($notice_data['ran_posts'] || $notice_data['ran_comments']) ) {
                 $messages[] = __('Randomization process completed, but no items were found or updated based on current settings.', 'post-date-randomizer'); $notice_class = 'notice-info';
            } elseif ( empty($messages) ) {
                 $messages[] = __('Randomization action triggered, but no operations were performed.', 'post-date-randomizer'); $notice_class = 'notice-info';
            }
        }
        if (!empty($messages)) echo '<div class="notice '. $notice_class .' is-dismissible"><p>' . implode( ' ', array_map('esc_html', $messages) ) . '</p></div>';
        if ( !empty($notice_data['errors']) ) echo '<div class="notice notice-warning is-dismissible"><p><strong>' . __('Processing Errors Encountered:', 'post-date-randomizer') . '</strong><br>' . implode('<br>', array_map('esc_html', $notice_data['errors'])) . '</p></div>';
    }
}

// --- Add Donate Link to Plugins Page ---
function pdr_add_plugin_action_links($links) {
    $donation_link_url = 'https://www.paypal.com/paypalme/wellbeingsupport/';
    $donation_link = '<a href="' . esc_url($donation_link_url) . '" target="_blank" rel="noopener noreferrer" style="color:#3db634; font-weight:bold;">' . __('Donate', 'post-date-randomizer') . '</a>';
    // Add Donate link before other links
    return array_merge(array('donate' => $donation_link), $links);
}
add_filter('plugin_action_links_' . plugin_basename(PDR_PLUGIN_FILE), 'pdr_add_plugin_action_links');

?>