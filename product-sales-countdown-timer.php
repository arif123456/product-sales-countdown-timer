<?php
/**
 * Plugin Name:       Product Sales Countdown Timer
 * Description:       Product Sales Countdown Timer plugin helps you display for single product page.
 * Version:           1.0
 * Author:            Ariful Islam
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       product-sales-countdown-timer
 */

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Product_Sales_Countdown_Timer class
 *
 * @class Product_Sales_Countdown_Timer The class that holds the entire Product_Sales_Countdown_Timer plugin
 */
class Product_Sales_Countdown_Timer {

    /**
     * Singleton pattern
     *
     * @var bool $instance
     */
    private static $instance = false;
    
    /**
     * Initializes the Product_Sales_Countdown_Timer class
     *
     * Checks for an existing Product_Sales_Countdown_Timer instance
     * and if it cant't find one, then creates it.
     */
    public static function init() {

        if ( ! self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor for the Product_Sales_Countdown_Timer class
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
        define( 'PSCT_DIR_FILE', plugin_dir_url( __FILE__ ) );
        define( 'PSCT_ASSETS', PSCT_DIR_FILE . '/assets' );
    }

    /**
     * Load all includes file
     *
     * @since 0.0.1
     * @since 1.0.4 Included erp-helper file
     *
     * @return void
     */
    public function includes() {
        add_filter( 'woocommerce_product_data_tabs', [ $this, 'psct_countdown_timer_tab' ] );
        add_action( 'woocommerce_product_data_panels', [ $this, 'psct_countdown_timer_product_data_panels' ] );
        add_action( 'woocommerce_process_product_meta', [ $this, 'psct_countdown_timer_save_fields' ] );
        add_action( 'woocommerce_single_product_summary', [ $this, 'psct_display_countdown_timer' ], 30);
        add_action( 'wp_enqueue_scripts', array( $this, 'psct_load_enqueue' ) );
    }

    /**
     * Countdown Tab Function
     *
     * @since 1.0
     *
     * @return void
    */
    public function psct_countdown_timer_tab( $product_data_tabs ) {
        $product_data_tabs['psct_tab'] = array(
            'label'     =>  __( 'Product Countdown', 'product-sales-countdown-timer' ),
            'target'    => 'psct_tab_settings',
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
    public function psct_countdown_timer_product_data_panels() {
        ?>
            <div id='psct_tab_settings' class='panel woocommerce_options_panel'>
                <div class='options_group'>
                    <?php

                        woocommerce_wp_checkbox( array(
                            'id' 		=> '_enable_timer',
                            'label' 	=> __( 'Enable Timer', 'product-sales-countdown-timer' ),
                        ) );

                        woocommerce_wp_text_input( array(
                            'id'				=> '_valid_for_timer_text',
                            'label'				=> __( 'Timer Heading Text', 'product-sales-countdown-timer' ),
                            'desc_tip'			=> 'true',
                            'description'		=> __( 'Enter timer header text', 'product-sales-countdown-timer' ),
                            'type' 				=> 'text',
                        ) );

                        woocommerce_wp_text_input( array(
                            'id'				=> '_valid_for_date',
                            'label'				=> __( 'End Date', 'product-sales-countdown-timer' ),
                            'desc_tip'			=> 'true',
                            'description'		=> __( 'Enter the end date here countdown for product sales', 'product-sales-countdown-timer' ),
                            'type' 				=> 'date',
                        ) );
                        woocommerce_wp_text_input( array(
                            'id'				=> '_valid_for_date_time',
                            'label'				=> __( 'Time', 'product-sales-countdown-timer' ),
                            'desc_tip'			=> 'true',
                            'description'		=> __( 'Enter the end date here countdown for product sales', 'product-sales-countdown-timer' ),
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
    public function psct_countdown_timer_save_fields( $post_id ) {
        $valid_for_date         = isset( $_POST[ '_valid_for_date' ] ) ? sanitize_text_field( wp_unslash( $_POST[ '_valid_for_date' ] ) ) : '';
        $valid_for_date_time    = isset( $_POST[ '_valid_for_date_time' ] ) ? sanitize_text_field( wp_unslash( $_POST[ '_valid_for_date_time' ] )  ) : '';
        $valid_for_head_text    = isset( $_POST[ '_valid_for_timer_text' ] ) ? sanitize_text_field( wp_unslash( $_POST[ '_valid_for_timer_text' ] )  ) : '';
        $enable_timer           = isset($_POST['_enable_timer']) ? 'yes' : 'no';

        update_post_meta( $post_id, '_valid_for_date', esc_attr( $valid_for_date ) );
        update_post_meta( $post_id, '_valid_for_date_time',  esc_attr( $valid_for_date_time ) );
        update_post_meta( $post_id, '_valid_for_timer_text',  esc_attr( $valid_for_head_text ) );
        update_post_meta( $post_id, '_enable_timer',  esc_attr( $enable_timer ) );
        
    }

    /**
     * Display Data Showing Function
     *
     * @since 1.0
     *
     * @return void
    */
    public function psct_display_countdown_timer() {
        $date = get_post_meta( get_the_ID(), '_valid_for_date', true );
        $time = get_post_meta( get_the_ID(), '_valid_for_date_time', true );
        $text = get_post_meta( get_the_ID(), '_valid_for_timer_text', true );
        $enable_timer = get_post_meta( get_the_ID(), '_enable_timer', true );

        ?>
           <?php if( 'yes' === $enable_timer && ! empty( $date ) ) {
               ?> <p id="demo"></p><?php
           } ?>
            
            <script>
                var countDownDate = new Date("<?php echo $date .' '. $time;?>").getTime();
                var x = setInterval(function() {
                var now = new Date().getTime();
                var distance = countDownDate - now;
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Display the result in the element with id="demo"
                document.getElementById("demo").innerHTML = 
                    `<div class="psct_countdown_wrap">
                        <p><?php echo $text; ?></p>
                    
                        <div class="psct_countdown_timer">
                            <span class="psct_countdown-single-item day">
                                <span class="date">${days}</span>Days
                            </span>
                            <span class="psct_countdown-single-item hours">
                                <span class="date">${hours}</span>Hours
                            </span>
                            <span class="psct_countdown-single-item mins">
                                <span class="date">${minutes}</span>Mins
                            </span>
                            <span class="psct_countdown-single-item secs">
                                <span class="date">${seconds}</span>Secs
                            </span>
                        </div>
                    </div>
                    `

                // If the count down is finished, write some text
                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById("demo").innerHTML = "<span class='expire-texxt'><?php esc_html_e('EXPIRED', 'product-sales-countdown-timer'); ?></span>";
                }
                }, 1000);
            </script>

        <?php
    }


    /**
     * Add all the enqueue required by the plugin
     *
     * @since 1.0
     *
     * @return void
     */
    public function psct_load_enqueue() {
        wp_enqueue_style( 'woo-countdown-timer-style', PSCT_ASSETS . '/css/style.css' );
        
    }

}

/**
 * Init the wperp plugin
 *
 * @return Product_Sales_Countdown_Timer the plugin object
 */
function psct_product_countdown_timer() {
    return Product_Sales_Countdown_Timer::init();
}

psct_product_countdown_timer();
