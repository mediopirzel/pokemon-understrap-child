<?php

 function pokemon_post_types() {

  // Pokemon Post Type
  $pokemon_labels = array(
    'name' => __('Pokemons'),
    'add_new_item' => __('Add New Pokemon'),
    'edit_item' => __('Edit Pokemon'),
    'all_items' => __('All Pokemons'),
    'singular_name' => __('Pokemon')
  );

   $pokemon_args = array(
    'map_meta_cap' => true,
    'show_in_rest' => true,
    'supports' => array('title', 'editor', 'thumbnail','custom-fields', 'revisions' ),
    'rewrite' => array('slug' => 'pokemon'),
    'has_archive' => true,
    'public' => true,
    'labels' => $pokemon_labels,
    // TO DO ICON
    'menu_icon' => 'dashicons-location-alt'
  );


  register_post_type('pokemon', $pokemon_args);
  
	$attack_labels = array(
		'name'              => __('Attacks'),
		'singular_name'     => __('Attacks'),
		'search_items'      => __('Search Attacks'),
		'all_items'         => __('Search Attacks'),
		'parent_item'       => __('Parent Attack'),
		'parent_item_colon' => __('Parent Attack:'),
		'edit_item'         => __('Edit Attacks'),
		'update_item'       => __('Update Attacks'),
		'add_new_item'      => __('New Attack'),
		'new_item_name'     => __('New'),
		'menu_name'         => _('Attacks')
	);

	$attack_args = array(
		'hierarchical'      => false,
		'labels'            => $attack_labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
        'show_in_rest'       => true, //GUTENBERG
		'rewrite'           => array( 'slug' => 'attack' )
	);

	register_taxonomy( 'attack', array( 'pokemon' ), $attack_args );


  function pokemon_metabox() {
    add_meta_box( 'pokemon-fields', __('Pokemon Additional Data'), 'pokemon_meta_box_content', 'pokemon', 'normal', 'high' );
  }
  add_action( 'add_meta_boxes', 'pokemon_metabox' );
  

  function pokemon_meta_box_content( $post ) {
    $pokemon_types = array('normal','fighting','flying','poison','ground','rock','bug','ghost','steel', 'fire','water', 'grass','electric', 'psychic', 'ice', 'dragon', 'dark', 'fairy');
    $weight = get_post_meta( $post->ID, 'pokemon_weight', true );
    $primary = get_post_meta( $post->ID, 'pokemon_primary', true );
    $secondary = get_post_meta( $post->ID, 'pokemon_secondary', true );
    $old_pokedex = get_post_meta( $post->ID, 'pokemon_podekedex_num_old', true );
    $new_pokedex = get_post_meta( $post->ID, 'pokemon_podekedex_num_new', true );


    wp_nonce_field( 'save_poke', 'poke_nonce' );
    ?>
    <p>
        <label for="pokemon_weight"><?php _e('Weight') ?></label>
        <input type="number" name="pokemon_weight" id="pokemon_weight" value="<?php echo  esc_attr($weight); ?>" />
    </p>
    <h3><?php _e('Pokemon Type') ?></h3>
    <p>
    <label for="pokemon_primary"><?php _e('Primary') ?></label>
    <select id="pokemon_primary" name="pokemon_primary">
      <option value="">Select...</option>
      <?php 
          foreach ($pokemon_types as $type) {
            echo '<option value="'.$type.'" '.selected( $type, $primary, false ).'>'.ucfirst($type).'</option>';
          } 
      ?>
    </select>
    <p>
    <p>
    <label for="pokemon_secondary"><?php _e('Secondary') ?></label>
    <select id="pokemon_secondary" name="pokemon_secondary">
      <option value="">Select...</option>
      <?php 
          foreach ($pokemon_types as $type) {
            echo '<option value="'.$type.'" '.selected( $type, $secondary, false ).'>'.ucfirst($type).'</option>';
          } 
      ?>
    </select>
    <p>
    <h3><?php _e('Pokedex Numbers') ?></h3>
    <p>
        <label for="pokemon_podekedex_num_old"><?php _e('Oldest') ?></label>
        <input type="number" name="pokemon_podekedex_num_old" id="pokemon_podekedex_num_old" value="<?php echo  esc_attr($old_pokedex); ?>" />
    </p>
    <p>
        <label for="pokemon_podekedex_num_new"><?php _e('Newest') ?></label>
        <input type="number" name="pokemon_podekedex_num_new" id="pokemon_podekedex_num_new" value="<?php echo  esc_attr($new_pokedex); ?>" />
    </p>

    <?php
    // echo 'values: ' . $values['pokemon_weight'];  
}

add_action( 'save_post', 'misha_save_meta', 10, 2 );
// or add_action( 'save_post_{post_type}', 'misha_save_meta', 10, 2 );

  function misha_save_meta( $post_id, $post ) {

    // nonce check
    if ( ! isset( $_POST[ 'poke_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'poke_nonce' ], 'save_poke' ) ) {
      return $post_id;
    }

    // check current user permissions
    $post_type = get_post_type_object( $post->post_type );

    if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
      return $post_id;
    }

    // Do not save the data if autosave
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
      return $post_id;
    }

    // define your own post type here
    if( 'pokemon' !== $post->post_type ) {
      return $post_id;
    }

    if( isset( $_POST[ 'pokemon_weight' ] ) ) {
      update_post_meta( $post_id, 'pokemon_weight', sanitize_text_field( $_POST[ 'pokemon_weight' ] ) );
    } else {
      delete_post_meta( $post_id, 'pokemon_weight' );
    }

    if( isset( $_POST[ 'pokemon_primary' ] ) ) {
      update_post_meta( $post_id, 'pokemon_primary', sanitize_text_field( $_POST[ 'pokemon_primary' ] ) );
    } else {
      delete_post_meta( $post_id, 'pokemon_primary' );
    }

    if( isset( $_POST[ 'pokemon_secondary' ] ) ) {
      update_post_meta( $post_id, 'pokemon_secondary', sanitize_text_field( $_POST[ 'pokemon_secondary' ] ) );
    } else {
      delete_post_meta( $post_id, 'pokemon_secondary' );
    }

    if( isset( $_POST[ 'pokemon_podekedex_num_old' ] ) ) {
      update_post_meta( $post_id, 'pokemon_podekedex_num_old', sanitize_text_field( $_POST[ 'pokemon_podekedex_num_old' ] ) );
    } else {
      delete_post_meta( $post_id, 'pokemon_podekedex_num_old' );
    }

    if( isset( $_POST[ 'pokemon_podekedex_num_new' ] ) ) {
      update_post_meta( $post_id, 'pokemon_podekedex_num_new', sanitize_text_field( $_POST[ 'pokemon_podekedex_num_new' ] ) );
    } else {
      delete_post_meta( $post_id, 'pokemon_podekedex_num_new' );
    }

    return $post_id;

  }
}

add_action('init', 'pokemon_post_types');