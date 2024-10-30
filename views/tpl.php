<style>

	#slide_catalog-<?= $catalog->id ?> .slide {
		<?php
		if(!empty($catalog->border))
			echo 'border: 2px solid '.$catalog->border.';';
		else
			echo 'border: none;';
		?>
	}

</style>
<div class="slide_catalog" id="slide_catalog-<?= $catalog->id ?>" style="width: <?= $catalog->width ?>px; height: <?= $catalog->height ?>px" rel="<?= $catalog->timeout ?>">
	<?php

	$nb = sizeof($catalog->slides)-1;

	foreach($catalog->slides as $i => $slide)
	{
		echo '<div class="slide slide-'.($nb-$i).'">
			<a href="'.$slide->link.'" '.($slide->blank == 1 ? 'target="_blank"' : '').'><span>'.$slide->title.'</span></a>
			<img src="'.$slide->image.'" alt="" />
		</div>';
	}

	?>
</div>