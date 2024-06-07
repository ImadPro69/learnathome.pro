<?php

/**
 * Categories: List Layout.
 *
 * @link    https://plugins360.com
 * @since   1.0.0
 *
 * @package All_In_One_Video_Gallery
 */
?>

<div class="aiovg aiovg-categories aiovg-categories-template-list">
	<?php
	// Title
	if ( ! empty( $attributes['title'] ) ) : ?>
		<h2 class="aiovg-header">
			<?php echo esc_html( $attributes['title'] ); ?>
		</h2>
	<?php endif; ?>

	<ul class="aiovg-categories-list">
		<?php echo $categories_li; ?>
	</ul>
</div>