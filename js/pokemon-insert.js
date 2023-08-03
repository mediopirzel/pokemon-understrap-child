class InsertPokemon {
  constructor() {
    // endpoint
    this.url = `${wordpressData.root_url}/wp-json/pokedex/v1/create`;
    this.totalPokemons = 640; // Higher ids doesn't have pokedex numbers
    this.pokemonApiUrl = `https://pokeapi.co/api/v2/pokemon`;
    this.pokemonDescriptionUrl = `https://pokeapi.co/api/v2/pokemon-species`;
    this.insertButton = document.querySelector('.get-random-pokemon-button');
    this.insertMessage = document.querySelector(
      '.insert-random-pokemon-message'
    );
    this.events();
  }

  events() {
    if (this.insertButton) {
      this.insertButton.addEventListener(
        'click',
        this.getRandomPokemon.bind(this)
      );
    }
  }

  // Fetch the API
  getJSON(url, errorMsg = 'Something when wrong') {
    return fetch(url).then((response) => {
      if (!response.ok) throw new Error(`${errorMsg}(${response.stauts})`);
      return response.json();
    });
  }

  // Get a random pokemon from pokeAPI
  async getRandomPokemon() {
    try {
      const randomPokemonId = Math.trunc(
        Math.random() * this.totalPokemons + 1
      );
      const data = await this.getJSON(
        `${this.pokemonApiUrl}/${randomPokemonId}`
      );

      const capitalizedName =
        data.name.charAt(0).toUpperCase() + data.name.slice(1);

      // Generate an object with info
      const pokemonObject = {
        postTitle: capitalizedName,
        pokemonWeight: data.weight,
        pokemonPrimary: data.types[0]?.type.name,
        pokemonSecondary: data.types[1]?.type.name,
        pokemonPodekedexNumNew: data.id,
        pokemonPodekedexNumOld: data.game_indices[0]?.game_index,
        pokemonPodekedexNameOld: data.game_indices[0]?.version.name,
        pokemonImage: data.sprites.other['official-artwork'].front_default,
      };

      // Pokemon description needs another endpoint
      // TODO use Promise.allSettled() for both endpoints
      const description = await this.getJSON(
        `${this.pokemonDescriptionUrl}/${data.id}`
      );
      // Get first english description
      const englishDescriptions = description.flavor_text_entries.filter(
        (desc) => {
          return desc.language.name == 'en';
        }
      )[0];

      // Clean strange breakspaces
      const cleanPokemonDescription = englishDescriptions.flavor_text.replace(
        /[\n\r]/g,
        ' '
      );
      // Add descriptiom to object
      pokemonObject.postContent = cleanPokemonDescription;

      // call
      this.createPokemon(pokemonObject);
    } catch (err) {
      console.log(err);
    }
  }
  // Send Info to endpoint for create a post
  async createPokemon(pokemonInfo) {
    try {
      const petition = await fetch(this.url, {
        method: 'POST',
        headers: {
          'X-WP-Nonce': wordpressData.nonce,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(pokemonInfo),
      });
      if (!petition.ok) throw new Error('Problem getting connection');

      const answer = await petition.json();

      console.log(answer);

      if (typeof answer == 'number' && answer > 0) {
        this.insertMessage.innerHTML = `<a href="/?p=${answer}" class="btn btn-outline-success">Visit New Pokemon (${answer})</a>`;
      }
    } catch (err) {
      this.insertMessage.innerHTML = 'Please try Again';
    }
  }
}

const insertPokemon = new InsertPokemon();
