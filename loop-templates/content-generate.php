<?php
/**
 * Partial template for content in generate-pokemon.php
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<?php
	if ( ! is_page_template( 'page-templates/no-title.php' ) ) {
		the_title(
			'<header class="entry-header"><h1 class="entry-title">',
			'</h1></header><!-- .entry-header -->'
		);
	}
	?>

	<div class="entry-content">

		<?php
		the_content();
		?>
		<div class="d-flex  gap-3">
		<a href="#" class="get-random-pokemon-button btn btn-outline-primary"><?php esc_html_e( 'Insert Random Pokémon')?></a>
		<div class="insert-random-pokemon-message"></div>
</div>
		<?
		understrap_link_pages();
		?>

	</div><!-- .entry-content -->

	<footer class="entry-footer">

		<?php understrap_edit_post_link(); ?>

	</footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->
