<?php

/**
 *
 * The plugin bootstrap file
 *
 * This file is responsible for starting the plugin using the main plugin class file.
 *
 * @since 0.0.1
 * @package Plugin_Name
 *
 * @wordpress-plugin
 * Plugin Name:     Dev Challenge
 * Description:     Plugin para Desafio de  Desarrollador WordPress WeRemote.
 * Version:         0.0.1
 * Author:          Roniel Escorcia
 * Author URI:      https://www.example.com
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     dev-challenge
 * Domain Path:     /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access not permitted.' );
}

require_once plugin_dir_path( __FILE__ ) . 'includes/hooks/activator.php';
 
// Create tables on plugin activation
register_activation_hook( __FILE__, 'dev_challenge_activate' );

register_deactivation_hook(__FILE__, 'deactivateCron');

if ( ! class_exists( 'DevChallenge' ) ) {

	/*
	 * main plugin_name class
	 *
	 * @class plugin_name
	 * @since 0.0.1
	 */
	class DevChallenge {

		/*
		 * plugin_name plugin version
		 *
		 * @var string
		 */
		public $version = '4.7.5';

		/**
		 * The single instance of the class.
		 *
		 * @var plugin_name
		 * @since 0.0.1
		 */
		protected static $instance = null;

		/**
		 * Main plugin_name instance.
		 *
		 * @since 0.0.1
		 * @static
		 * @return plugin_name - main instance.
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * plugin_name class constructor.
		 */
		public function __construct() {
			$this->load_plugin_textdomain();
			$this->define_constants();
			$this->includes();
			$this->define_actions();
		}

		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'dev-challenge', false, basename( dirname( __FILE__ ) ) . '/lang/' );
		}

		/**
		 * Include required core files
		 */
		public function includes() {
            // Example
			//require_once __DIR__ . '/includes/loader.php';

			// Load custom functions and hooks
			require_once __DIR__ . '/includes/includes.php';
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}


		/**
		 * Define plugin_name constants
		 */
		private function define_constants() {
			define( 'DEV_CHALLENGE_PLUGIN_FILE', __FILE__ );
			define( 'DEV_CHALLENGE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			define( 'DEV_CHALLENGE_VERSION', $this->version );
			define( 'DEV_CHALLENGE_PATH', $this->plugin_path() );
		}

		/**
		 * Define plugin_name actions
		 */
		public function define_actions() {
			//
		}

		/**
		 * Define plugin_name menus
		 */
		public function define_menus() {
            //
		}
	}

	$dev_challenge = new DevChallenge();
}
