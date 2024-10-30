<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<h2>Add/edit catalog slider</h2>

<form action="" method="post" class="form_cs">
	<input type="hidden" name="id" value="<?= $catalog->id ?>" />
	<?php wp_nonce_field('catalog_slide_edit') ?>
	<label for="">Name: </label> <input type="text" name="name" value="<?= $catalog->name ?>" /><br />
	<label for="">Width: </label> <input type="text" name="width" value="<?= $catalog->width ?>" />px<br />
	<label for="">Height: </label> <input type="text" name="height" value="<?= $catalog->height ?>" />px<br />
	<label for="">Border color: </label> <input type="text" name="border" value="<?= $catalog->border ?>" /><br />
	<label for="">Timeout: </label> <input type="number" min="2000" name="timeout" value="<?= $catalog->timeout ?>" />ms<br />
	<input type="submit" value="Save catalog slider" /> <a href="<?= admin_url('admin.php?page=catalog_slider'); ?>">Back to catalog slider list</a>
</form>

<script>

	jQuery(document).ready(function(){

		jQuery('.form_cs input[name="border"]').wpColorPicker();

	});

</script>