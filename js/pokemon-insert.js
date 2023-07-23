class InsertPokemon {
  constructor() {
    this.url = `${wordpressData.root_url}/wp-json/pokedex/v1/create`;
    this.events();
  }

  events() {
    // console.log(wordpressData.nonce);
    // console.log(this.url);

    const insertButton = document.querySelector('.get-random-pokemon-button');

    if (insertButton) {
      insertButton.addEventListener(
        'click',
        this.ourClickDispatcher.bind(this)
      );
    }
  }

  ourClickDispatcher() {
    console.log('hemos hecho clic');

    // Primer carregarem un random

    // Un cop rebut, enviarem a fer el pokemon

    const pokemonObject = {
      name: 'New poketemon',
    };

    this.createPokemon(pokemonObject);
  }

  async createPokemon(pokemonObject) {
    try {
      console.log(`comencem peticio ${wordpressData.nonce}`);

      const petition = await fetch(this.url, {
        method: 'POST',
        headers: {
          'X-WP-Nonce': wordpressData.nonce,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          //   name: pokemonObject.name,
          title: 'pokemita',
        }),
      });
      if (!petition.ok) throw new Error('Problem getting connection');
      console.log(petition);

      const answer = await petition.json();
      //   currentLikeBox.dataset.exists = 'yes';
      //   let likeCount = +currentLikeBox.querySelector('.like-count').textContent;

      //   currentLikeBox.querySelector('.like-count').textContent = ++likeCount;
      //   currentLikeBox.dataset.like = answer;

      console.log(answer);
    } catch (err) {
      console.log(`Something went wrong 🤬 ${err.message}`);
    }
  }
}

const insertPokemon = new InsertPokemon();
