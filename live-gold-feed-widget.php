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
		$this->load_parser();
        $this->updateRates();
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

        $data_array = unserialize(get_option('gold_widget_data'));

        $rate = $data_array['rate'];
		$operator = $data_array['operator'];
		$difference_rate = $data_array['difference_rate'];
        $value = $data_array['value'];

        echo '<div class="gold-feed-container">
            	<span class="gold-feed-title"> GOLD PRICE USD/OZ </span>
                <span class="gold-feed-values-container">
            		<span class="gold-feed-rate">'.$rate.'</span>
                    <span class="gold-feed-mark"> <img src="'.$this->plugin_dir_url.'/img/fa-arrow-'. ( ($operator == 'pos') ? 'up' : 'down' ) .'.png" > </span>
                	<span class="gold-feed-difference-rate gold-'.$operator.'-val"> '. ( ( $operator == 'pos' ) ? '+'.$difference_rate : $difference_rate ) .' </span>
                	<span class="gold-feed-percentage gold-'.$operator.'-val">'. ( ( $operator == 'pos' ) ? '+'.$value : $value ) .'%</span>
                </span>
        </div>';

    }

    /**
     * Dom parse required to scrap the data
     */
    public function load_parser() {
		require('domparser/simple_html_dom.php');
	}

    /**
     * Access domparse and hit request www.goldbroker.com to get record and save it in database
     *
     * @param   [type]  $action    [$action description]
     * @param   [type]  $currency  [$currency description]
     *
     * @return  [type]             [return description]
     */
	public function updateRates() { 
        
        $update_in_database = false;
        $data_array = get_option('gold_widget_data');
        
        if($data_array != false) {

			$data_array = unserialize($data_array);

			if(time() - $data_array['time'] >= 1800) {

				$update_in_database = true;			

			}

		} else {

			$update_in_database = true;			

        }
        
        if($update_in_database == true) {


			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://www.goldbroker.com/widget/live-price/XAU?currency=USD');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);

			$domHtml = str_get_html($output);

			$the_feed = str_replace( [' ', 'Gold', 'oz'] , ['', '', ''], $domHtml->find('div', 0)->plaintext);

			$break_feed = explode('/', $the_feed);

			$rate = $break_feed[0];
			$value = $break_feed[1];


			$percentageChange = (float) $value;

			// Our original number.
			$originalNumber = floatval(preg_replace('/[^\d.]/', '', $rate ));

			// Get 2.25% of 100.
			$numberToAdd = ($originalNumber / 100) * $percentageChange;
			 
			// Finish it up with some simple addition
			$newNumber = $originalNumber + $numberToAdd;
			 
			// Result is 102.25
			$difference_rate =  number_format((float)$newNumber - $originalNumber, 2, '.', '');

			$operator = ($value >= 0) ? 'pos' : 'neg';

			$data_array = serialize([
				'rate' => $rate,
				'operator' => $operator,
				'difference_rate' => $difference_rate,
				'value' => $value,
				'time' => time()
			]);

			update_option('gold_widget_data', $data_array);

		}

    }
       
}

// widget class
class Live_Gold_Feed_Widget extends WP_Widget { 
    
    public $plugin_dir_url;

    // class constructor
	public function __construct() {

        $this->plugin_dir_url = plugin_dir_url(__FILE__);

		$widget_ops = array( 
			'classname' => 'live_gold_feed_widget',
			'description' => 'A plugin for Kinsta blog readers',
		);
		parent::__construct( 'live_gold_feed_widget', 'Live Gold Feed Widget', $widget_ops );

    }
    
	public function widget( $args, $instance ) { 
        
        $data_array = unserialize(get_option('gold_widget_data'));

        $rate = $data_array['rate'];
		$operator = $data_array['operator'];
		$difference_rate = $data_array['difference_rate'];
        $value = $data_array['value'];

        echo '<div class="gold-feed-container">
            	<span class="gold-feed-title"> GOLD PRICE USD/OZ </span>
                <span class="gold-feed-values-container">
            		<span class="gold-feed-rate">'.$rate.'</span>
                    <span class="gold-feed-mark"> <img src="'.$this->plugin_dir_url.'/img/fa-arrow-'. ( ($operator == 'pos') ? 'up' : 'down' ) .'.png" > </span>
                	<span class="gold-feed-difference-rate gold-'.$operator.'-val"> '. ( ( $operator == 'pos' ) ? '+'.$difference_rate : $difference_rate ) .' </span>
                	<span class="gold-feed-percentage gold-'.$operator.'-val">'. ( ( $operator == 'pos' ) ? '+'.$value : $value ) .'%</span>
                </span>
        </div>';

    }

}

new LiveFeed;