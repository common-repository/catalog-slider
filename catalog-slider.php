<?php

/*

Plugin Name: Catalog slider
Plugin URI: 
Version: 1.02
Description: Create nice catalog slider
Author: Manu225
Author URI: 
Network: false
Text Domain: catalog-slider
Domain Path: 

*/

register_activation_hook( __FILE__, 'catalog_slider_install' );
register_uninstall_hook(__FILE__, 'catalog_slider_desinstall');

function catalog_slider_install() {

	global $wpdb;

	$contents_table = $wpdb->prefix . "catalog_slider";
	$contents_data_table = $wpdb->prefix . "catalog_slider_slides";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$sql = "
        CREATE TABLE `".$contents_table."` (
          id int(11) NOT NULL AUTO_INCREMENT,          
          name varchar(50) NOT NULL,
          width int(11) NOT NULL,
          height int(11) NOT NULL,
          border varchar(10) NOT NULL,
          timeout int(2) NOT NULL,
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    ";

    dbDelta($sql);

    $sql = "
        CREATE TABLE `".$contents_data_table."` (
          id int(11) NOT NULL AUTO_INCREMENT,        
          image varchar(500) NOT NULL,
          title varchar(100) NOT NULL,
          link varchar(255) NOT NULL,
          blank int(1) NOT NULL,
          `order` int(5) NOT NULL,
          id_catalog int(11),
          PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    ";   

    dbDelta($sql);

}

function catalog_slider_desinstall() {

	global $wpdb;

	$contents_table = $wpdb->prefix . "catalog_slider";
	$contents_data_table = $wpdb->prefix . "catalog_slider_slides";

	//suppression des tables
	$sql = "DROP TABLE ".$contents_table.";";
	$wpdb->query($sql);

    $sql = "DROP TABLE ".$contents_data_table.";";   
	$wpdb->query($sql);

}

add_action( 'admin_menu', 'register_catalog_slider_menu' );

function register_catalog_slider_menu() {

	add_menu_page('Catalog slider', 'Catalog slider', 'edit_pages', 'catalog_slider', 'catalog_slider', plugins_url( 'images/icon.png', __FILE__ ), 38);

}



add_action('admin_print_styles', 'catalog_slider_css' );

function catalog_slider_css() {

    wp_enqueue_style( 'catalog_slider_css', plugins_url('css/admin.css', __FILE__) );
    wp_enqueue_style( 'wp-color-picker' );

}


add_action( 'admin_enqueue_scripts', 'load_script_catalog_slider' );
function load_script_catalog_slider() {

	wp_enqueue_media();
    wp_enqueue_script( 'wp-color-picker');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-sortable');

}

function catalog_slider() {

	global $wpdb;

	$contents_table = $wpdb->prefix . "catalog_slider";
	$contents_data_table = $wpdb->prefix . "catalog_slider_slides";

	if(current_user_can('edit_pages'))
	{

		if(isset($_GET['task']))
		{

			switch($_GET['task'])
			{

				case 'new':

				case 'edit':

					if(sizeof($_POST))
					{
						if(wp_verify_nonce($_POST['_wpnonce'], 'catalog_slide_edit'))
						{
							$query = "REPLACE INTO ".$contents_table." (`id`, `name`, `width`, `height`, `border`, `timeout`)
							VALUES (%d, %s, %d, %d, %s, %d)";
							$query = $wpdb->prepare( $query, (int)$_POST['id'], sanitize_text_field(stripslashes_deep($_POST['name'])), sanitize_text_field($_POST['width']), sanitize_text_field($_POST['height']), sanitize_text_field($_POST['border']), sanitize_text_field($_POST['timeout']) );
							$wpdb->query( $query );


							//on affiche tous les catalog slider
							$catalogs = $wpdb->get_results("SELECT * FROM ".$contents_table." ORDER BY name");
							include(plugin_dir_path( __FILE__ ) . 'views/catalogs.php');
						}
						else
							die('Wrong nonce security!');

					}
					else
					{
						//édition d'un circle content existant ?
						if(is_numeric($_GET['id']))
						{
							$q = "SELECT * FROM ".$contents_table." WHERE id = %d";
							$query = $wpdb->prepare( $q, $_GET['id']);
							$catalog = $wpdb->get_row( $query );
						}

						if(empty($catalog))
							$catalog = (object)'';

						include(plugin_dir_path( __FILE__ ) . 'views/edit.php');

					}


				break;

				case 'manage':

					if(is_numeric($_GET['id']))
					{

						$q = "SELECT * FROM ".$contents_table." WHERE id = %d";
						$query = $wpdb->prepare( $q, $_GET['id']);
						$catalog = $wpdb->get_row( $query );

						if($catalog)
						{
							$q = "SELECT * FROM ".$contents_data_table." WHERE id_catalog = %d ORDER BY `order` ASC";
							$query = $wpdb->prepare( $q, $_GET['id']);
							$slides = $wpdb->get_results( $query );

							if(is_numeric($_GET['id_slide']))
							{
								foreach ($slides as $slide) {
									if($slide->id == $_GET['id_slide'])
										break;
								}
							}

							include(plugin_dir_path( __FILE__ ) . 'views/manage.php');
						}					
					}

				break;

				case 'remove':

					if(is_numeric($_GET['id']))
					{
						//on supprime les données et le graph
						$q = "DELETE FROM ".$contents_data_table." WHERE id_catalog = %d";
						$query = $wpdb->prepare( $q, $_GET['id']);
						$wpdb->query( $query );

						$q = "DELETE FROM ".$contents_table." WHERE id = %d";
						$query = $wpdb->prepare( $q, $_GET['id']);
						$wpdb->query( $query );

					}

					//on affiche tous les graphs
					$catalogs = $wpdb->get_results("SELECT * FROM ".$contents_table." ORDER BY name");
					include(plugin_dir_path( __FILE__ ) . 'views/catalogs.php');

				break;

			}

		}
		else
		{
			if(!is_numeric($_GET['id']))
			{
				//on affiche tous les graphs
				$catalogs = $wpdb->get_results("SELECT * FROM ".$contents_table." ORDER BY name");
				include(plugin_dir_path( __FILE__ ) . 'views/catalogs.php');
			}

		}

	}

}

add_shortcode('catalog-slider', 'display_catalog_slider');

function display_catalog_slider($atts) {

	if(is_numeric($atts['id']))
	{

		global $wpdb;
		$contents_table = $wpdb->prefix . "catalog_slider";
		$contents_data_table = $wpdb->prefix . "catalog_slider_slides";

		$q = "SELECT * FROM ".$contents_table." WHERE id = %d";
		$query = $wpdb->prepare( $q, $atts['id']);
		$catalog = $wpdb->get_row( $query );

		if($catalog)
		{
			$q = "SELECT * FROM ".$contents_table." WHERE id = %d";
			$query = $wpdb->prepare( $q, $atts['id'] );
			$catalog = $wpdb->get_row( $query );

			$q = "SELECT * FROM ".$contents_data_table." WHERE id_catalog = %d ORDER BY `order` ASC";
			$query = $wpdb->prepare( $q, $atts['id'] );
			$catalog->slides = $wpdb->get_results( $query );

			wp_enqueue_script( 'jquery');
			wp_enqueue_script( 'catalog_slider_js', plugins_url( 'js/script.js', __FILE__ ));
			wp_enqueue_style( 'catalog_slider_css', plugins_url('css/style.css', __FILE__) );			

			$html = '';

			if($atts['show_title'] == true)
				$html .= '<h3>'.$circle->name.'</h3>';

			ob_start();
			include(plugin_dir_path( __FILE__ ) . 'views/tpl.php');
			$html .= ob_get_clean();

			return $html;

		}
		else

			return 'Catalog slider ID '.$atts['id'].' not found!';		

	}

}

//Ajax : sauvegarde d'une icone
add_action( 'wp_ajax_catalog_slider_save_icon', 'catalog_slider_save_icon' );

function catalog_slider_save_icon() {

	if(current_user_can('edit_pages'))
	{
		check_ajax_referer( 'cs_save_icon' );

		if(!empty($_POST['image']))
		{
			global $wpdb;

			$contents_data_table = $wpdb->prefix . "catalog_slider_slides";

			if(@empty($_POST['id']))
			{
				//trouve le max order
				$query = "SELECT MAX(`order`)+1 as max FROM ".$contents_data_table." WHERE id_catalog = %d";
				$query = $wpdb->prepare( $query, $_POST['id_catalog'] );
				$max = $wpdb->get_row( $query );

				$query = "REPLACE INTO ".$contents_data_table." ( `image`, `title`, `link`, `blank`, `order`, `id_catalog`)
				VALUES (%s, %s, %s, %d, %d, %d)";

				$query = $wpdb->prepare( $query, sanitize_text_field(stripslashes_deep($_POST['image'])), sanitize_text_field(stripslashes_deep($_POST['title'])), sanitize_text_field(stripslashes_deep($_POST['link'])), sanitize_text_field($_POST['blank']), $max->max, (int)$_POST['id_catalog'] );

			}
			else
			{

				$query = "UPDATE ".$contents_data_table."
				SET `image` = %s, `title` = %s, `link` = %s, `blank` = %d, `id_catalog` = %d WHERE `id` = %d";
				$query = $wpdb->prepare( $query, sanitize_text_field(stripslashes_deep($_POST['image'])), sanitize_text_field(stripslashes_deep($_POST['title'])), sanitize_text_field(stripslashes_deep($_POST['link'])), sanitize_text_field($_POST['blank']), sanitize_text_field($_POST['id_catalog']), (int)$_POST['id']);

			}

			$wpdb->query( $query );
		}
	}

	wp_die();

}

//Ajax : autocomplète icons
add_action( 'wp_ajax_catalog_slider_remove_icon', 'catalog_slider_remove_icon' );

function catalog_slider_remove_icon() {

	check_ajax_referer( 'cs_remove_icon' );

	if(is_numeric($_POST['id']))
	{
		global $wpdb;
		$contents_data_table = $wpdb->prefix . "catalog_slider_slides";

		//on reddéfini le order correctement avant suppresion.
		$query = "UPDATE ".$contents_data_table." AS a SET a.`order` = a.`order`-1 WHERE a.`order` > (SELECT * FROM (SELECT b.`order` FROM ".$contents_data_table." AS b WHERE b.id = %d) tmp)";
		$query = $wpdb->prepare( $query, $_POST['id'] );
		$wpdb->query( $query );
		// on supprime le slide
		$query = "DELETE FROM ".$contents_data_table." WHERE id = %d";
		$query = $wpdb->prepare( $query, $_POST['id'] );
		$wpdb->query( $query );
	}

	wp_die();

}

//Ajax : changement de position d'une icone
add_action( 'wp_ajax_catalog_slider_order_slider', 'catalog_slider_order_slider' );

function catalog_slider_order_slider() {

	check_ajax_referer( 'cs_order_slider' );

	if (is_admin()) {
		global $wpdb;

		$contents_data_table = $wpdb->prefix . "catalog_slider_slides";

		if(is_numeric($_POST['id']) && is_numeric($_POST['order']))
		{
			$slider = $wpdb->get_row( $wpdb->prepare( "SELECT id_catalog, `order` FROM ".$contents_data_table." WHERE id = %d", $_POST['id'] ));
			if($_POST['order'] > $slider->order)
				$wpdb->query( $wpdb->prepare( "UPDATE ".$contents_data_table." SET `order` = `order` - 1 WHERE id_catalog = %d AND `order` <= %d AND `order` > %d", $slider->id_catalog, $_POST['order'], $slider->order ));
			else
				$wpdb->query( $wpdb->prepare( "UPDATE ".$contents_data_table." SET `order` = `order` + 1 WHERE id_catalog = %d AND `order` >= %d AND `order` < %d", $slider->id_catalog, $_POST['order'], $slider->order ));
			$wpdb->query( $wpdb->prepare( "UPDATE ".$contents_data_table." SET `order` = %d WHERE id = %d", $_POST['order'], $_POST['id'] ));
			
		}
		wp_die(); // this is required to terminate immediately and return a proper response
	}
}

?>