<?php
/**
 * Partial template for content in single-pokemon.php
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<?php $metas = get_post_meta( $post->ID, ); ?>
<article <?php post_class('container'); ?> id="post-<?php the_ID(); ?>">
	<div class="row">
		<div class="col-sm">
		<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>
		</div>
		<div class="col-sm shadow-lg p-5">
		<?php
		if ( ! is_page_template( 'page-templates/no-title.php' ) ) {
			the_title(
				'<header class="entry-header"><h1 class="display-1">',
				'</h1></header><!-- .entry-header -->'
			);
		}
		if( isset($metas['pokemon_podekedex_num_new']) && $metas['pokemon_podekedex_num_new'][0] ){
			$fourDigits = sprintf('%04s', $metas['pokemon_podekedex_num_new'][0]);
			echo '<p class="fs-2 text-secondary">#'.$fourDigits. '</p>';
		}
		?>

		<div class="entry-content">
			<?php
			the_content();

			
			if( isset($metas['pokemon_weight']) ){

				$kg = $metas['pokemon_weight'][0] / 10;

				echo '<p>Weight: '.$kg. ' Kg</p>';
			}

			if( isset($metas['pokemon_primary']) && $metas['pokemon_primary'][0]  ){
				echo '<span class="badge rounded-pill text-bg-primary fs-4">'.$metas['pokemon_primary'][0]. '</span>';
			}

			if( isset($metas['pokemon_secondary']) && $metas['pokemon_secondary'][0] ){

				echo ' / <span class="badge rounded-pill text-bg-secondary fs-4">'.$metas['pokemon_secondary'][0]. '</span>';
				

			}

			if( isset($metas['pokemon_podekedex_num_old']) && $metas['pokemon_podekedex_num_old'][0] ){
				
				$nonce = wp_create_nonce("my_user_like_nonce");
				$link = admin_url('admin-ajax.php?action=my_user_like&post_id='.$post->ID.'&nonce='.$nonce);
				?>
				<div class="d-flex pt-4 gap-2">
					<a id="old-pokedex-button" class="btn btn-outline-primary btn-sm" data-nonce="<?php echo $nonce ?>" data-post_id="<?php echo $post->ID ?>" href="#"><?php _e('Load oldest Pokedex number')?></a>
					<div id="old-pokedex-text" class="invisible"> </div>
				</div>

				<?php

			}
			?>
			
			<div class="pt-4"><a target="_blank" class="btn btn-outline-warning btn-sm" href="<?php echo get_bloginfo( 'url' ). '/wp-json/pokedex/v1/pokemon/'.get_the_ID() ?>" rel="noreferrer noopener">
				<?php _e('View in JSON format')?>
			</a></div>
			<?php

			understrap_link_pages();
			?>

	<?php
		
	?>

		</div><!-- .entry-content -->

		<footer class="entry-footer">

			<?php understrap_edit_post_link(); ?>

		</footer><!-- .entry-footer -->
		</div>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
