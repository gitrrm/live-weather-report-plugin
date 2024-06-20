<?php
/**
 * Plugin Name: RRM live weather Report
 * Plugin URI: https://wordpress.org
 * Description: A plugin to display the live weather report using the Open Weather Map API
 * Version: 1.0
 * Author: Rashmi Ranjan Muduli
 * Author URI: http://wordpress.org
 * Text Domain: rrm-live-weather-report
 * License: GPL 2
 */ 

//  Exit if access directly
// if(!defined(' ABSPATH' )){
//     exit;
// }

// Adding styles and scripts
function rrm_live_weather_report_scripts_styles(){
    wp_register_style( 'main-style', get_template_directory_uri( '/css/styles.css' ), [], '1.0.0', 'all' );
    wp_enqueue_style( 'main-style' );
}
add_action( 'wp_enqueue_scripts', 'rrm_live_weather_report_scripts_styles' );

// load text domain
function rrm_live_weather_report_load_textdomain() {
    load_plugin_textdomain(
        'rrm-live-weather-report', // Text domain
        false,
        dirname(plugin_basename(__FILE__)) . '/languages' // Path to the languages folder
    );
}
add_action('plugins_loaded', 'rrm_live_weather_report_load_textdomain');

// Create a shortcode
function rrm_live_weather_report_shortcode( $atts){
    $atts = shortcode_atts( 
        array(
            'city'      => 'Hyderabad',
            'apikey'    => 'b2b450d8ebe87f1d0beec4f939460cba',
        ),
        $atts,
        'rrm_live_weather_report'
    );
    // API endpoints
    $api_url = 'https://api.openweathermap.org/data/2.5/weather?q='.$atts['city'] . '&appid=' . $atts['apikey'] . '&units=metric';
    $response = wp_remote_get( $api_url );

    //  check errors
    if(is_wp_error( $response )){
        return 'Unable ti retrieve weather data';
    }

    $weather_data = json_decode( wp_remote_retrieve_body( $response ));
    print_r(json_encode($weather_data));
    // print_r($weather_data->main->feels_like);

    if($weather_data->cod != 200 ){
        return 'Error: ' . $weather_data->message;
    }
    // Extract weather details
    $temperature = $weather_data->main->temp;
    $weather = $weather_data->weather[0]->description;
    $city = $weather_data->name;
    
    /* $temperature = $weather_data['main'][0]['temp'];
    $weather = $weather_data['weather'][0]['description'];
    $city = $weather_data['name']; */

    // Create the output
    $output = '<div class="rrm-live-weather-report">';
    $output .= '<h3>Weather in ' .esc_html( $city ). '</h3>';
    $output .= '<h2>Temperature: ' . esc_html( $temperature ) . '</h2>';
    $output .= '<p>Weather Condition: ' . esc_html( strtoupper($weather) ) . '</p>';
    $output .= '</div>';

    return $output;
}
add_shortcode( 'rrm_live_weather_report', 'rrm_live_weather_report_shortcode' );