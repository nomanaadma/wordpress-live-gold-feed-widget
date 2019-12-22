<?php 

/*
*Plugin Name: Live Gold Feed 
*Description: Live gold Feed widget. Once activated go to Widgets and drag the Live gold Feed widget in the widget area or use shortcode [live-Gold-Feed].
*Version: 1
*Author: Noman Shoukat
*Author URI: https://github.com/nomanaadma
*/


// prevent from any kind of direct accessing to prevent hacking
if ( ! defined( 'ABSPATH' ) ) exit; 


// all plugin functionality will be in this class
Class LiveFeed {

    public $plugin_dir_url;

    
    /**
     * here we will load our basic functions like registering styles and widgets
     */
    public function __construct() {

        $this->plugin_dir_url = plugin_dir_url(__FILE__);
		$this->register_style();
		$this->register_widget();
        add_shortcode( 'live-Gold-Feed', array($this, 'live_gold_feed') );

    }

    /**
     * add feed.css
     */
    public function register_style() {

		add_action('wp_enqueue_scripts', 'feed_css_callback');
		function feed_css_callback() {

			$plugin_dir_url = plugin_dir_url(__FILE__);
		    wp_register_style( 'feed-css', $plugin_dir_url.'css/feed.css' );
		    wp_enqueue_style( 'feed-css' );
		}

    }
    

    /**
     *  register the widget class
     */
    public function register_widget() { 

		add_action( 'widgets_init', function() {
			register_widget( 'Live_Gold_Feed_Widget' );
		});

    }


    /**
     *  register the shortcode
     */
    public function live_gold_feed() { 

        echo '<div class="gold-feed-container">
            	<span class="gold-feed-title"> GOLD PRICE USD/OZ </span>
                <small>
                <span class="gold-feed-values-container">
            		<span class="gold-feed-rate">$1546.72</span>
                	<span class="gold-feed-mark"> <img src="'.$this->plugin_dir_url.'/img/fa-arrow-down.png" > </span>
                	<span class="gold-feed-difference-rate gold-neg-val"> +17.32 </span>
                	<span class="gold-feed-percentage gold-neg-val">16%</span>
                </span>
                </small>
        </div>';

    }

       
}

// widget class
class Live_Gold_Feed_Widget extends WP_Widget { 
    
    
    // class constructor
	public function __construct() {

		$widget_ops = array( 
			'classname' => 'live_gold_feed_widget',
			'description' => 'A plugin for Kinsta blog readers',
		);
		parent::__construct( 'live_gold_feed_widget', 'Live Gold Feed Widget', $widget_ops );

    }
    
	public function widget( $args, $instance ) { 
        echo 'hello wordpress widget';
    }


}

new LiveFeed;