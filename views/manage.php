<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<script>

	jQuery(document).ready(function(){

		//choix d'une image dans la librairie Wordpress
		jQuery('.form_cs .choose_img').click(function(e) {
		    	var _this = this;
		        e.preventDefault();
		        var image = wp.media({ 
		            title: 'Upload Image',
		            // mutiple: true if you want to upload multiple files at once
		            multiple: false
		        }).open()
		        .on('select', function(e){
		            // This will return the selected image from the Media Uploader, the result is an object
		            var uploaded_image = image.state().get('selection').first();
		            // We convert uploaded_image to a JSON object to make accessing it easier
		            // Output to the console uploaded_image
		            var image_url = uploaded_image.toJSON().url;
		            // Let's assign the url value to the input field
		            jQuery('.form_cs input[name='+jQuery(_this).attr('rel')+']').val(image_url);
		        });
		});

		jQuery('.form_cs').submit(function(){

			var image = jQuery(this).find('input[name=image]').val();

			if(image == "")
				alert('Please choose an image!');
			else
				jQuery.post(ajaxurl, jQuery(this).serialize(), function(){

					window.location.href = "<?= admin_url('admin.php?page=catalog_slider&task=manage&saved=1&id='.$catalog->id) ?>";

				});

			return false;

		});

		jQuery('.cs_sliders_list .remove').click(function(){

			var id = jQuery(this).attr('rel');

			jQuery.post(ajaxurl, { action: 'catalog_slider_remove_icon', id: id, _ajax_nonce: '<?= wp_create_nonce( "cs_remove_icon" ); ?>' }, function(){

				jQuery('.cs_sliders_list li[rel='+id+']').remove();

			});

		});


		//changement d'ordre des sliders
		jQuery('.cs_sliders_list').sortable({
			update: function( event, ui ) {
				//effectuer le changement de position en BDD par Ajax
				jQuery.post(ajaxurl, {action: 'catalog_slider_order_slider', id: jQuery(ui.item).attr('rel'), order: (ui.item.index()), _ajax_nonce: '<?= wp_create_nonce( "cs_order_slider" ); ?>' });
			}
		});

	});

</script>

<h2>Manage catalog slider "<?= $catalog->name ?>"</h2>

<form action="" method="post" class="form_cs">

	<input type="hidden" name="id" value="<?= $slide->id ?>" />
	<input type="hidden" name="id_catalog" value="<?= $catalog->id ?>" />
	<input type="hidden" name="action" value="catalog_slider_save_icon" />
	<?php wp_nonce_field( "cs_save_icon" ); ?>
	<div class="name_line">
		<label for="">Image*:</label> 
		<input type="text" name="image" value="<?= $slide->image ?>" /> 
		<button class="choose_img" rel="image">Browser library</button>
	</div>
	<label for="">Title:</label> <input type="text" name="title" value="<?= $slide->title ?>" /><br />
	<label for="">Link:</label> <input type="text" name="link"  value="<?= $slide->link ?>" /><br />
	<label for="">Blank:</label> <input type="checkbox" name="blank" value="1" <?= ($slide->blank ? 'checked="checked"' : '') ?> /><br />
	<input type="submit" value="Save slider" />

	<a href="<?= admin_url('admin.php?page=catalog_slider'); ?>">Back to catalog sliders list </a>

</form>

<?php if(isset($_GET['saved'])) : ?>
	<h3>Slider saved!</h3>
<?php endif; ?>

<?php

	if(sizeof($slides) > 0)
	{
		echo '<ul class="cs_sliders_list">';

		foreach( $slides as $slide )
		{
			echo '<li rel="'.$slide->id.'">
			<img src="'.$slide->image.'" />
			<a href="'.admin_url('admin.php?page=catalog_slider&task=manage&id='.$catalog->id).'&id_slide='.$slide->id.'"><img src="'.plugins_url( 'images/edit.png', dirname(__FILE__) ).'" /></a>
			<a href="#" rel="'.$slide->id.'" class="remove"><img src="'.plugins_url( 'images/remove.png', dirname(__FILE__) ).'" /></a>
			</li>';

		}

		echo '</ul>';

	}
	else {
	
		echo '<p>No slide yet.</p>';

		}	

?>
