<?php

 function pokemon_post_types() {

  // Create Pokémon Post Type
  $pokemon_labels = array(
    'name' => __('Pokémons'),
    'add_new_item' => __('Add New Pokémon'),
    'edit_item' => __('Edit Pokémon'),
    'all_items' => __('All Pokémons'),
    'singular_name' => __('Pokémon')
  );

   $pokemon_args = array(
    'map_meta_cap' => true,
    'show_in_rest' => true,
    'supports' => array('title', 'editor', 'thumbnail','custom-fields', 'revisions' ),
    'rewrite' => array('slug' => 'pokemon'),
    'has_archive' => true,
    'public' => true,
    'labels' => $pokemon_labels,
    "menu_icon" => "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAwcHgiIGhlaWdodD0iODAwcHgiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0zIDEyQzMgNy4wMjk0NCA3LjAyOTQ0IDMgMTIgM0MxNi45NzA2IDMgMjEgNy4wMjk0NCAyMSAxMkMyMSAxNi45NzA2IDE2Ljk3MDYgMjEgMTIgMjFDNy4wMjk0NCAyMSAzIDE2Ljk3MDYgMyAxMlpNNS4wNzA4OSAxM0M1LjU1NjEyIDE2LjM5MjMgOC40NzM1MyAxOSAxMiAxOUMxNS41MjY1IDE5IDE4LjQ0MzkgMTYuMzkyMyAxOC45MjkxIDEzSDE0LjgyOTNDMTQuNDE3NCAxNC4xNjUyIDEzLjMwNjIgMTUgMTIgMTVDMTAuNjkzOCAxNSA5LjU4MjUxIDE0LjE2NTIgOS4xNzA2OCAxM0g1LjA3MDg5Wk0xOC45MjkxIDExQzE4LjQ0MzkgNy42MDc3MSAxNS41MjY1IDUgMTIgNUM4LjQ3MzUzIDUgNS41NTYxMiA3LjYwNzcxIDUuMDcwODkgMTFIOS4xNzA2OEM5LjU4MjUxIDkuODM0ODEgMTAuNjkzOCA5IDEyIDlDMTMuMzA2MiA5IDE0LjQxNzQgOS44MzQ4MSAxNC44MjkzIDExSDE4LjkyOTFaTTEyIDEzQzEyLjU1MjMgMTMgMTMgMTIuNTUyMyAxMyAxMkMxMyAxMS40NDc3IDEyLjU1MjMgMTEgMTIgMTFDMTEuNDQ3NyAxMSAxMSAxMS40NDc3IDExIDEyQzExIDEyLjU1MjMgMTEuNDQ3NyAxMyAxMiAxM1oiIGZpbGw9IiMwMDAwMDAiLz4KPC9zdmc+"
  );
  register_post_type('pokemon', $pokemon_args);
    
  // Create Attacks taxonomy
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
    'show_in_rest'       => true,
		'rewrite'           => array( 'slug' => 'attack' )
	);

	register_taxonomy( 'attack', array( 'pokemon' ), $attack_args );


  // Create metabox for aditional fields
  function pokemon_metabox() {
    add_meta_box( 'pokemon-fields', __('Pokémon Additional Data'), 'pokemon_meta_box_content', 'pokemon', 'normal', 'high' );
  }
  add_action( 'add_meta_boxes', 'pokemon_metabox' );
  

  function pokemon_meta_box_content( $post ) {
    // Array for primary and secondary values
    // TODO - Find a better solution
    $pokemon_types = array('normal','fighting','flying','poison','ground','rock','bug','ghost','steel', 'fire','water', 'grass','electric', 'psychic', 'ice', 'dragon', 'dark', 'fairy');
    
    // Custom Fields
    $weight = get_post_meta( $post->ID, 'pokemon_weight', true );
    $primary = get_post_meta( $post->ID, 'pokemon_primary', true );
    $secondary = get_post_meta( $post->ID, 'pokemon_secondary', true );
    $old_pokedex = get_post_meta( $post->ID, 'pokemon_podekedex_num_old', true );
    $old_pokedex_name = get_post_meta( $post->ID, 'pokemon_podekedex_name_old', true );
    $new_pokedex = get_post_meta( $post->ID, 'pokemon_podekedex_num_new', true );

    // nonce
    wp_nonce_field( 'save_poke', 'poke_nonce' );
    ?>
    <p>
        <label for="pokemon_weight"><?php esc_html_e('Weight') ?></label>
        <input type="number" name="pokemon_weight" id="pokemon_weight" value="<?php echo  esc_attr($weight); ?>" />
    </p>
    <h3><?php esc_html_e('Pokemon Type') ?></h3>
    <p>
    <label for="pokemon_primary"><?php esc_html_e('Primary') ?></label>
    <select id="pokemon_primary" name="pokemon_primary">
      <option value=""><?php esc_html_e('Select...'); ?></option>
      <?php 
          foreach ($pokemon_types as $type) {
            echo '<option value="'.$type.'" '.selected( $type, $primary, false ).'>'.ucfirst($type).'</option>';
          } 
      ?>
    </select>
    <p>
    <p>
    <label for="pokemon_secondary"><?php esc_html_e('Secondary') ?></label>
    <select id="pokemon_secondary" name="pokemon_secondary">
      <option value=""><?php esc_html_e('Select...') ;?></option>
      <?php 
          foreach ($pokemon_types as $type) {
            echo '<option value="'.$type.'" '.selected( $type, $secondary, false ).'>'.ucfirst($type).'</option>';
          } 
      ?>
    </select>
    <p>
    <h3><?php esc_html_e('Pokedex Numbers') ?></h3>
    <p>
        <label for="pokemon_podekedex_num_new"><?php esc_html_e('Newest') ?></label>
        <input type="number" name="pokemon_podekedex_num_new" id="pokemon_podekedex_num_new" value="<?php echo  esc_attr($new_pokedex); ?>" />
    </p>
    <p>
        <label for="pokemon_podekedex_num_old"><?php esc_html_e('Oldest') ?></label>
        <input type="number" name="pokemon_podekedex_num_old" id="pokemon_podekedex_num_old" value="<?php echo  esc_attr($old_pokedex); ?>" />
    </p>
    <p>
        <label for="pokemon_podekedex_name_old"><?php esc_html_e('Version name') ?></label>
        <input type="text" name="pokemon_podekedex_name_old" id="pokemon_podekedex_name_old" value="<?php echo  esc_attr($old_pokedex_name); ?>" />
    </p>
    <?php
}

add_action( 'save_post', 'pokemon_save_meta', 10, 2 );

  function pokemon_save_meta( $post_id, $post ) {

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


    if( isset( $_POST[ 'pokemon_podekedex_name_old' ] ) ) {
      update_post_meta( $post_id, 'pokemon_podekedex_name_old', sanitize_text_field( $_POST[ 'pokemon_podekedex_name_old' ] ) );
    } else {
      delete_post_meta( $post_id, 'pokemon_podekedex_name_old' );
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