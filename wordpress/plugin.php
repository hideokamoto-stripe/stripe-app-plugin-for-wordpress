<?php
/**
 * Plugin Name:       Stripe integration example
 * Description:       Example WordPress plugin
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Hidetaka Okamoto
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       example-wp-stripe
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'admin_menu', 'stripe_sample_admin_menu' );
add_action( 'admin_init', 'stripe_sample_admin_init' );
function stripe_sample_admin_init() {
    register_setting( 'stripe_samples_app_options', 'app_redirect_url' );
    register_setting( 'stripe_samples_app_options', 'secret_api_key' );
    add_settings_section(
        'stripe_samples_app_section',
        'Sample App Settings',
        'stripe_samples_app_section_callback',
        'stripe_samples_app'
    );

    add_settings_field(
        'app_redirect_url',
        'App Redirect URL',
        'app_redirect_url_callback',
        'stripe_samples_app',
        'stripe_samples_app_section'
    );

    add_settings_field(
        'secret_api_key',
        'Secret API Key',
        'secret_api_key_callback',
        'stripe_samples_app',
        'stripe_samples_app_section'
    );
}

function stripe_samples_app_section_callback(){
    echo '<p>Enter settings below</p>';
}

function app_redirect_url_callback(){ 
    $app_redirect_url = esc_attr( get_option('app_redirect_url') );
    echo "<input type='text' name='app_redirect_url' value='{$app_redirect_url}' />";
}

function secret_api_key_callback(){
    $secret_api_key = esc_attr( get_option('secret_api_key') );
    echo "<input type='text' name='secret_api_key' value='{$secret_api_key}' />";
}


function stripe_sample_admin_menu() {
    add_menu_page(
        'Stripe Apps for plugin demo Setting page',
        'Stripe Apps',
        'manage_options',
        'stripe-app',
        'stripe_sample_settings_page'
    );
}

function stripe_sample_settings_page() {
?>
    <div class='wrap'>
        <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
        <form action='options.php' method='post'>
            <?php
                settings_fields( 'stripe_samples_app_options' );
                do_settings_sections( 'stripe_samples_app' );
                submit_button( 'Save settings' );
            ?>
        </form>
    </div>
<?
}