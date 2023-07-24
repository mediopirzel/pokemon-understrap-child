<?php

add_action('rest_api_init', 'pokemonRoutes');

function pokemonRoutes(){

    // END POINT 1 - showing list of all pokémon as ID the pokédex number in the most recent version of the game
    // Ex: your-domain/wp-json/pokedex/v1/ids
    register_rest_route( 'pokedex/v1', 'ids', array(
        'methods'=> WP_REST_SERVER::READABLE,
        'callback'=> 'pokedexIDResults'
    ) );

    // END POINT 2 - passing the wordpress id shows a pokémon with all info 
    // Ex: your-domain/wp-json/pokedex/v1/pokemon/{id} 
    register_rest_route( 'pokedex/v1', 'pokemon/(?P<id>\d+)', array(
        'methods'=> WP_REST_SERVER::READABLE,
        'callback'=> 'pokemonFieldsResults',
        'args' => array(
            'id' => array(
              'validate_callback' => function($param, $request, $key) {
                return is_numeric( $param );
                }
                ),
            ),
        ) );

    // END POINT 3 - Insert a Random Pokémon from pokeAPI 
    // Ex: your-domain/wp-json/pokedex/v1/create 
    register_rest_route( 'pokedex/v1', 'create', array(
        'methods' => 'POST',
        'callback' => 'insertPokemon'
    ));


    function pokedexIDResults(){
        $pokemons = new WP_Query(array(
            'post_type' => 'pokemon',
            'post_per_page' => -1
        ));
    
        $pokemonResults = array();
        
        // Associative array generates a json object
        while($pokemons->have_posts()){
            $pokemons->the_post();
            array_push($pokemonResults, array(
            'id' => (int)get_post_meta(get_the_ID(), 'pokemon_podekedex_num_new', true),
            'wordpress_id' => get_the_ID(),
            'name' => get_the_title(),
            'permalink' => get_the_permalink(),
            'desription' => wp_strip_all_tags(get_the_content())
            ));
        }
        return $pokemonResults;
        }




        function pokemonFieldsResults($data){
            $dataId = $data['id'];
            $pokemons = new WP_Query(array(
                'post_type' => 'pokemon',
                'post_per_page' => 1,
                'p' => sanitize_text_field($data['id'])
            ));
        
           
            // Associative array generates a json object
            if($pokemons->have_posts()){
        
                while($pokemons->have_posts()){
                    $pokemons->the_post();
                    $id = get_the_ID();
                        
                    $primary = get_post_meta($id, 'pokemon_primary', true);
                    $secondary = get_post_meta($id, 'pokemon_secondary', true);
                    $weight = get_post_meta($id, 'pokemon_weight', true);
                    $pokedex_num = get_post_meta($id, 'pokemon_podekedex_num_new', true);
                    $first_pokedex_num = get_post_meta($id, 'pokemon_podekedex_num_old', true);
                    $first_pokedex_version = get_post_meta($id, 'pokemon_podekedex_name_old', true);
                    $image = get_the_post_thumbnail_url(0, 'full');
        
                    $singlePokemon = array(
                        'id' => $id,
                        'name' => get_the_title(),
                        'permalink' => get_the_permalink(),
                        'description' => wp_strip_all_tags(get_the_content()),
        
                        'test' => $data['id'],
                    );
        
                    // Adding items only if there is data
                    if($primary){ $singlePokemon['primary'] =  $primary; }
                    if($secondary){ $singlePokemon['secondary'] =  $secondary; }
                    if($weight){ $singlePokemon['weight'] =  (int)$weight; }
                    if($pokedex_num){ $singlePokemon['pokedex_num'] =  (int)$pokedex_num; }
                    if($first_pokedex_num){ $singlePokemon['first_pokedex_num'] =  (int)$first_pokedex_num; }
                    if($first_pokedex_version){ $singlePokemon['first_pokedex_version'] =  $first_pokedex_version; }
                    if($image){ $singlePokemon['image'] =  $image; }
                }
        
            } else{
                // show error message if no results
                $singlePokemon = array(
                    'code'=> 'rest_post_invalid_id',
                    'message'=> 'Invalid pokemon ID.',
                    'data' => array('status' => 404)
                );
        
            }
        
            return $singlePokemon;
          }

          function insertPokemon($data){

            if(current_user_can( 'edit_posts' )){
                // API id and pokedex num can change in newest versions of game
                // Use oldest pokedex num as id to check if pokemon is on database.
                $existPokemon =  new WP_Query(array(
                        'post_type' => 'pokemon',
                        'meta_query'    => array(
                            array(           
                              'key'       => 'pokemon_podekedex_num_old',
                              'value'     => $data['pokemonPodekedexNumOld'],
                              'compare'   => '='
                              )
                            )
                        )) ;

                if($existPokemon->found_posts == 0 ){
                    $newPokemon = wp_insert_post( array(
                        'post_type' => 'pokemon',  
                        'post_status'=> 'publish',
                        'post_title'=> $data['postTitle'],
                        'post_content' => $data['postContent'],
                        'meta_input' => array(
                            'pokemon_weight' => $data['pokemonWeight'],
                            'pokemon_primary' => $data['pokemonPrimary'],
                            'pokemon_secondary' => $data['pokemonSecondary'],
                            'pokemon_podekedex_num_new' => $data['pokemonPodekedexNumNew'],
                            'pokemon_podekedex_num_old' => $data['pokemonPodekedexNumOld'],
                            'pokemon_podekedex_name_old' => $data['pokemonPodekedexNameOld'],
                        )
                        
                    )  );
    
                    if($newPokemon >= 1 && $data['pokemonImage'] ){
                       insertFeaturedImage($newPokemon, $data['pokemonImage'],$data['postTitle']);
                    }
    
            
                    // IF SUCCESS RETURN New pokemon ID.
                    return $newPokemon;
                } else {
                    die('This Pokemon is already on database');
                }
                

                
                // $pokemon = sanitize_text_field($data['name']);

        
                // $existPokemonm =  new WP_Query(array(
                //     'post_type' => 'pokemon',
                //     'numberposts'   => -1,
                //     'name' => $pokemon,
                //     'name' => $pokemon,
                //     )) ;
        
                // // ens assegurem que no existeixi cap votació i que el id de professor és de tipus professor.
                // if($existPokemonm->found_posts == 0 ){
                //     // Retornarà el ID del post nou insertat com a resposta
                //     return wp_insert_post( array(
                //         'post_type' => 'pokemon',  
                //         'post_status'=> 'publish',
                //         'post_title'=> 'New Poquemon',
                //         /*
                //         'meta_input' => array(
                //             'liked_professor_id' => $professor
                //         )
                //         */
                //     )  );
                // } else {
                //     die('invalid professor id');
                // }
        
        
        
            } else {
                die('Only logged in user can create a like.');
            }
        
            
        }
        
    
}




function insertFeaturedImage($post_id,$image_url,$name){
    // Add Featured Image to Post

//$image_url  = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/45.png'; // Define the image URL here
$extension = pathinfo($image_url, PATHINFO_EXTENSION);
$image_name       = $name.'.'.$extension;
$upload_dir       = wp_upload_dir(); // Set upload folder
$image_data       = file_get_contents($image_url); // Get image data
$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
$filename         = basename( $unique_file_name ); // Create image file name

// Check folder permission and define file location
if( wp_mkdir_p( $upload_dir['path'] ) ) {
    $file = $upload_dir['path'] . '/' . $filename;
} else {
    $file = $upload_dir['basedir'] . '/' . $filename;
}

// Create the image  file on the server
file_put_contents( $file, $image_data );

// Check image file type
$wp_filetype = wp_check_filetype( $filename, null );

// Set attachment data
$attachment = array(
    'post_mime_type' => $wp_filetype['type'],
    'post_title'     => sanitize_file_name( $filename ),
    'post_content'   => '',
    'post_status'    => 'inherit'
);

// Create the attachment
$attach_id = wp_insert_attachment( $attachment, $file, $post_id );

// Include image.php
require_once(ABSPATH . 'wp-admin/includes/image.php');

// Define attachment metadata
$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

// Assign metadata to attachment
wp_update_attachment_metadata( $attach_id, $attach_data );

// And finally assign featured image to post
set_post_thumbnail( $post_id, $attach_id );
}