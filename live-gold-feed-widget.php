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
    
    /**
     * here we will load our basic functions like registering styles and widgets
     */
    public function __construct() {

		$this->register_widget();

    }

    /**
     *  register the widget class
     */
    public function register_widget() { 

		add_action( 'widgets_init', function() {
			register_widget( 'Live_Gold_Feed_Widget' );
		});

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