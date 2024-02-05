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

/**
 * Add Setting UI
 */
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

/**
 * Add callback endpoint
 */
add_action( 'rest_api_init', function() {
    register_rest_route(
        'stripe-apps/v1',
        '/callback',
        [
            'methods' => 'GET',
            'callback' => 'stripe_sample_callback_handler'
        ]
    );
    register_rest_route(
        'stripe-apps/v1',
        '/customers',
        [
            'methods' => 'GET',
            'callback' => 'stripe_sample_list_customers',
        ]
    );
} );

/**
 * List Stripe Customers
 */
function stripe_sample_list_customers( WP_REST_Request $request ) {
    $options = get_option( 'stripe_sample_oauth' );
    if ( ! isset( $options) || empty( $options ) ) {
        $error_response = new WP_REST_Response([
            'error' => 'Permission denined.'
        ] );
        $error_response->set_status( 400 );
        return $error_response;
    }
    $settings = json_decode( $options, true );
    $refresh_token = $settings[ 'refresh_token' ];

    $refreshed_data = stripe_sample_update_oauth_token( 'refresh_token', $refresh_token );
    if ( is_wp_error( $refreshed_data ) ) {
        return new WP_REST_Response( $refreshed_data );
        $error_response = new WP_REST_Response([
            'error' => $refreshed_data->get_error_message,
            'code' => $refreshed_data->get_error_code,
        ] );
        $error_response->set_status( 400 );
        return $error_response;
    }
    
    $access_token = $refreshed_data[ 'access_token' ];
    $livemode = $refreshed_data[ 'livemode' ];
    $base_url = 'https://api.stripe.com/v1/'; 
    $response = wp_remote_get( $base_url . 'customers', [
        'headers' => [
            'Authorization' => 'BASIC ' . base64_encode( $access_token ),
        ]
    ] );
    $response_body = wp_remote_retrieve_body( $response );

    return new WP_REST_Response( json_decode( $response_body, true )[ 'data' ] );
}

/**
 * Get or refresh Stripe API access token
 * 
 * @param $grant_type 'authorization_code' | 'refresh_token': Grant type
 * @param $code_or_token string: Authorization code of refresh token
 */
function stripe_sample_update_oauth_token( $grant_type, $code_or_token ) {
    $stripe_secret_key = get_option( 'secret_api_key' );
    if ( ! isset( $stripe_secret_key ) || empty( $stripe_secret_key ) ) {
        return new WP_Error( 'stripe_api_key_required', 'You need to save Stripe API key at first' );
    }
    $auth_headers = [
        'Content-Type' => 'application/x-www-form-urlencoded',
        'Authorization' => 'BASIC ' . base64_encode( $stripe_secret_key ),
    ];
    $auth_body = [
        'grant_type' => $grant_type,
    ];
    switch ( $grant_type ) {
        case 'authorization_code':
            $auth_body[ 'code' ] = $code_or_token;
            break;
        case 'refresh_token':
            $auth_body[ 'refresh_token' ] = $code_or_token;
            break;
        default:
            return new WP_Error( 'stripe_invalid_grant_type', $grant_type . ' is unsupported.' );
    }
    $auth_result = wp_remote_post( 'https://api.stripe.com/v1/oauth/token', [
        'headers' => $auth_headers,
        'body' => $auth_body,
    ]);

    if ( is_wp_error( $auth_result ) ) {
        return $auth_result;
        $error_message = $auth_result->get_error_message();
        return new WP_Error( 'stripe_api_error', $error_message );
    }
    $auth_result_body = wp_remote_retrieve_body( $auth_result );
    update_option( 'stripe_sample_oauth', $auth_result_body );
    return json_decode( $auth_result_body, true );
}

/**
 * OAuth callback handler
 */
function stripe_sample_callback_handler( WP_REST_Request $request ) {
    return new WP_REST_Response( [
        'api_key' => get_option( 'secret_api_key' ),
        'stripe_sample_oauth_code' => get_option( 'stripe_sample_oauth_code' ),
        'stripe_sample_oauth' => get_option( 'stripe_sample_oauth' )
    ] );


    $params = $request->get_query_params();
    if ( ! isset( $params[ 'code' ] ) || empty( $params[ 'code' ] ) ) {
        $error_response = new WP_REST_Response([
            'error' => 'Missing required parameter'
        ] );
        $error_response->set_status( 400 );
        return $error_response;
    }
    $code = $params[ 'code' ];
    update_option( 'stripe_sample_oauth_code', $code );
    $auth_result = stripe_sample_update_oauth_token( 'authorization_code', $code );
    if ( is_wp_error( $auth_result ) ) {
        $error_response = new WP_REST_Response([
            'error' => $auth_result->get_error_message,
            'code' => $auth_result->get_error_code,
        ] );
        $error_response->set_status( 400 );
        return $error_response;
    }

    $response = new WP_REST_Response( $auth_result );
    $response->set_status( 200 );
    return $response;
}