<?php
/**
 * Template Name: Random Pokemon
 *
 * Template for redirecting to a published random pokemon.
 *
 * @package Understrap
 */


// Get a random published Pokémon 
 $random_query = new WP_Query( array(
    'posts_per_page' => 1,
    'post_type'      =>  'pokemon',
    'post_status' => 'publish',
    'orderby' => 'rand',
 ));
?>

<?php 
if ( $random_query->have_posts() ) : while ( $random_query->have_posts() ) : $random_query->the_post();
 
    $random_pokemon = get_the_ID();
    wp_safe_redirect(get_permalink($random_pokemon),301);
    exit;

 endwhile; 
    wp_reset_postdata();
 else : ?>
    <p><?php esc_html_e( 'Sorry, no Pokémons available.' ); ?></p>
 <?php endif; ?>
