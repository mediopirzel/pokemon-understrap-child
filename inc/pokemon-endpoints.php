<?php

add_action('rest_api_init', 'pokemonRoutes');

function pokemonRoutes(){

    // END POINT 1 - showing list of all pokémon as ID the pokédex number in the most recent version of the game
    // Ex: your-domain/wp-json/pokedex/v1/ids
    register_rest_route( 'pokedex/v1', 'ids', array(
        'methods'=> WP_REST_SERVER::READABLE,
        'callback'=> 'pokedexIDResults'
    ) );

    // END POINT 2 - showing a pokémon with all info 
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
            'methods' => 'GET',
            'callback' => 'createPokemon'
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

          function createPokemon($data){
            $user = wp_get_current_user();
            die(var_dump($user));
            if(is_user_logged_in()){
                die('User is logged in');
            }   
            else {
                die('Only logged in user can create a like.');
            }

          }
    
}



