<?php
/**
 * Partial template for content in single-pokemon.php
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

	echo get_the_post_thumbnail( $post->ID, 'large' );
	?>

	<div class="entry-content">
		<?php
		the_content();

		$metas = get_post_meta( $post->ID, );

		if( isset($metas['pokemon_weight']) ){
			echo '<p>Weight.'.$metas['pokemon_weight'][0]. ' Kg</p>';
		}

		if( isset($metas['pokemon_primary']) && $metas['pokemon_primary'][0]  ){
			echo '<p>The meta field "pokemon_primary" exists.'.$metas['pokemon_primary'][0]. '</p>';
		}

		if( isset($metas['pokemon_secondary']) && $metas['pokemon_secondary'][0] ){
			echo '<p>The meta field "pokemon_secondary" exists.'.$metas['pokemon_secondary'][0]. '</p>';
		}

		if( isset($metas['pokemon_podekedex_num_old']) && $metas['pokemon_podekedex_num_old'][0] ){
			echo '<p>The meta field "pokemon_podekedex_num_old" exists.'.$metas['pokemon_podekedex_num_old'][0]. '</p>';
		}

		if( isset($metas['pokemon_podekedex_num_new']) && $metas['pokemon_podekedex_num_new'][0] ){
			echo '<p>The meta field "pokemon_podekedex_num_new" exists.'.$metas['pokemon_podekedex_num_new'][0]. '</p>';
		}
		understrap_link_pages();
		?>

	</div><!-- .entry-content -->

	<footer class="entry-footer">

		<?php understrap_edit_post_link(); ?>

	</footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->
