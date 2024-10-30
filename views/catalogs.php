<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<h2>All catalog sliders</h2>

<a href="<?= admin_url('admin.php?page=catalog_slider&task=new') ?>">Add a new catalog slider</a><br />
<?php


	if(sizeof($catalogs) > 0)
	{

		foreach($catalogs as $catalog)

		{

			echo '<div class="beautiful_chart"><h3>'.$catalog->name.'</h3>
			<a href="'.admin_url('admin.php?page=catalog_slider&task=manage&id='.$catalog->id).'" title="Manage icons"><img src="'.plugins_url( 'images/manage.png', dirname(__FILE__) ).'" /></a>
			<a href="'.admin_url('admin.php?page=catalog_slider&task=edit&id='.$catalog->id).'" title="Edit circle content"><img src="'.plugins_url( 'images/edit.png', dirname(__FILE__) ).'" /></a>
			<a href="'.admin_url('admin.php?page=catalog_slider&task=remove&id='.$catalog->id).'" title="Remove circle content"><img src="'.plugins_url( 'images/remove.png', dirname(__FILE__) ).'" /></a>
			<br />

			<b>Shortcode: </b>

			<input type="text" value="[catalog-slider id='.$catalog->id.']" readonly onClick="this.select()" />

			</div>';

		}

	}

	else

		echo 'No catalog slider created yet!';



?>

<h3>You need more options? <a href="https://www.info-d-74.com/en/demo-catalog-slider-plugin-wordpress/#pro" target="_blank">Look at Pro version of this plugin<br />
<img src="<?php echo plugins_url( 'images/pro.png', dirname(__FILE__)) ?>" alt="" /></a><br />
	Like InfoD74 to check new plugins: <a href="https://www.facebook.com/infod74/" target="_blank"><img src="<?php echo plugins_url( 'images/fb.png', dirname(__FILE__)) ?>" alt="" /></a></h3>