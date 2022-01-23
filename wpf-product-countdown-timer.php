<?php
/**
 * Plugin Name:       WPF Product Countdown Timer
 * Plugin URI:        https://github.com/arif123456/wpf-product-countdown-timer
 * Description:       WPF Product Countdown Timer plugin helps you display for single product page.
 * Author:            WPFound
 * Author URI         https://github.com/arif123456
 * Version:           1.1.0
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wpf-product-countdown-timer
 */

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPFound_Product_Countdown_Timer class
 *
 * @class WPFound_Product_Countdown_Timer The class that holds the entire WPFound_Product_Countdown_Timer plugin
 */
class WPFound_Product_Countdown_Timer {

    /**
     * Singleton pattern
     *
     * @var bool $instance
     */
    private static $instance = false;
    
    /**
     * Initializes the WPFound_Product_Countdown_Timer class
     *
     * Checks for an existing WPFound_Product_Countdown_Timer instance
     * and if it cant't find one, then creates it.
     */
    public static function init() {

        if ( ! self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor for the WPFound_Product_Countdown_Timer class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @return void
     */
    public function __construct() {

        // define constants
        $this->define_constants();

        // includes
        $this->includes();

    }

    /**
     * Define all files constant
     *
     * @since  1.0
     *
     * @return void
     */
    public function define_constants() {
        define( 'WPFPCT_DIR_FILE', plugin_dir_url( __FILE__ ) );
        define( 'WPFPCT_ASSETS', WPFPCT_DIR_FILE . '/assets' );
    }

    /**
     * Load all includes file
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function includes() {
        add_filter( 'woocommerce_product_data_tabs', [ $this, 'wpfpct_countdown_timer_tab' ] );
        add_action( 'woocommerce_product_data_panels', [ $this, 'wpfpct_countdown_timer_product_data_panels' ] );
        add_action( 'woocommerce_process_product_meta', [ $this, 'wpfpct_countdown_timer_save_fields' ] );
        add_action( 'woocommerce_single_product_summary', [ $this, 'wpfpct_display_countdown_timer' ], 20);
        add_action( 'wp_enqueue_scripts', array( $this, 'wpfpct_load_enqueue' ) );
    
    }

    /**
     * Countdown Tab Function
     *
     * @since 1.0
     *
     * @return void
    */
    public function wpfpct_countdown_timer_tab( $product_data_tabs ) {
        $product_data_tabs['wpfound_tab'] = array(
            'label'     =>  __( 'Countdown Timer', 'wpf-product-countdown-timer' ),
            'target'    => 'wpfound_tab_settings',
        );
        return $product_data_tabs;
    }

    /**
     * Product Data Panels Function
     *
     * @since 1.0
     *
     * @return void
    */
    public function wpfpct_countdown_timer_product_data_panels() {
        ?>
            <div id='wpfound_tab_settings' class='panel woocommerce_options_panel'>
                <div class='options_group'>
                    <?php

                        woocommerce_wp_checkbox( array(
                            'id' 		=> 'wpfound_enable_timer',
                            'label' 	=> __( 'Enable Timer', 'wpf-product-countdown-timer' ),
                        ) );

                        woocommerce_wp_text_input( array(
                            'id'				=> 'wpfound_timer_heading_text',
                            'label'				=> __( 'Timer Heading Text', 'wpf-product-countdown-timer' ),
                            'desc_tip'			=> 'true',
                            'type' 				=> 'text',
                        ) );

                        woocommerce_wp_text_input( array(
                            'id'				=> 'wpfound_date_range',
                            'label'				=> __( 'End Date', 'wpf-product-countdown-timer' ),
                            'desc_tip'			=> 'true',
                            'type' 				=> 'date',
                        ) );
                        woocommerce_wp_text_input( array(
                            'id'				=> 'wpfound_date_time',
                            'label'				=> __( 'Time', 'wpf-product-countdown-timer' ),
                            'desc_tip'			=> 'true',
                            'type' 				=> 'time',
                        ) );

                    ?>
                </div>
            </div>
        <?php
    }

    /**
     * Field Save Function
     *
     * @since 1.0
     *
     * @return void
    */
    public function wpfpct_countdown_timer_save_fields( $post_id ) {
        $wpfound_date_range             = isset( $_POST[ 'wpfound_date_range' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'wpfound_date_range' ] ) ) : '';
        $wpfound_date_time              = isset( $_POST[ 'wpfound_date_time' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'wpfound_date_time' ] )  ) : '';
        $wpfound_timer_heading_text     = isset( $_POST[ 'wpfound_timer_heading_text' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'wpfound_timer_heading_text' ] )  ) : '';
        $wpfound_enable_timer           = isset($_POST['wpfound_enable_timer']) ? 'yes' : 'no';

        update_post_meta( $post_id, 'wpfound_date_range', esc_attr( $wpfound_date_range ) );
        update_post_meta( $post_id, 'wpfound_date_time',  esc_attr( $wpfound_date_time ) );
        update_post_meta( $post_id, 'wpfound_timer_heading_text',  esc_attr( $wpfound_timer_heading_text ) );
        update_post_meta( $post_id, 'wpfound_enable_timer',  esc_attr( $wpfound_enable_timer ) );
        
    }

    /**
     * Display Data Showing Function
     *
     * @since 1.0
     *
     * @return void
    */
    public function wpfpct_display_countdown_timer() {
        $wpfound_date_range         = get_post_meta( get_the_ID(), 'wpfound_date_range', true );
        $wpfound_date_time          = get_post_meta( get_the_ID(), 'wpfound_date_time', true );
        $wpfound_timer_heading_text = get_post_meta( get_the_ID(), 'wpfound_timer_heading_text', true );
        $wpfound_enable_timer       = get_post_meta( get_the_ID(), 'wpfound_enable_timer', true );

        ?>
           <?php if( 'yes' === $wpfound_enable_timer && ! empty( $wpfound_date_range ) ) {
               ?> 
                <p id="wpfound_view_timer"></p>
               <?php
           } ?>
            
            <script>
                var countDownDate = new Date("<?php echo esc_js( $wpfound_date_range .' '. $wpfound_date_time );?>").getTime();
                var wpfound_timer = setInterval(function() {
                    var now = new Date().getTime();
                    var wpfound_distance = countDownDate - now;
                    var wpfound_days = Math.floor(wpfound_distance / (1000 * 60 * 60 * 24));
                    var wpfound_hours = Math.floor((wpfound_distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var wpfound_minutes = Math.floor((wpfound_distance % (1000 * 60 * 60)) / (1000 * 60));
                    var wpfound_seconds = Math.floor((wpfound_distance % (1000 * 60)) / 1000);

                    // Display the result in the element with id="demo"
                    document.getElementById("wpfound_view_timer").innerHTML = 
                        `<div class="wpfound_countdown_wrap">
                            <p><?php echo esc_html( $wpfound_timer_heading_text ); ?></p>
                        
                            <div class="wpfound_countdown_timer">
                                <span class="wpfound_countdown-single-item day">
                                    <span class="date">${wpfound_days}</span>Days
                                </span>
                                <span class="wpfound_countdown-single-item hours">
                                    <span class="date">${wpfound_hours}</span>Hours
                                </span>
                                <span class="wpfound_countdown-single-item mins">
                                    <span class="date">${wpfound_minutes}</span>Mins
                                </span>
                                <span class="wpfound_countdown-single-item secs">
                                    <span class="date">${wpfound_seconds}</span>Secs
                                </span>
                            </div>
                        </div>
                        `

                    // If the count down is finished, write some text
                    if (wpfound_distance < 0) {
                        clearInterval(wpfound_timer);
                        document.getElementById("wpfound_view_timer").innerHTML = "<span class='wpfound_expire_texxt'><?php esc_html_e('DATE EXPIRED', 'wpf-product-countdown-timer'); ?></span>";
                    }
                }, 1000);
            </script>

        <?php
    }

    /**
     * Add enqueue required by the plugin
     *
     * @since 1.0
     *
     * @return void
     */
    public function wpfpct_load_enqueue() {

        wp_enqueue_style( 'wpfound-countdown-timer-style', WPFPCT_ASSETS . '/css/style.css' );
        
    }

}

/**
 * Init the wpf countdown timer plugin
 *
 * @return WPFound_Product_Countdown_Timer the plugin object
 */
function wpfound_product_countdown_timer() {

    return WPFound_Product_Countdown_Timer::init();

}

wpfound_product_countdown_timer();
