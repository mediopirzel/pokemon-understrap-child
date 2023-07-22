(function ($) {
  $(document).on('click', '#old-pokedex-button', function (e) {
    e.preventDefault();
    link = $(this);
    id = link.attr('data-post_id');
    pokedexText = $('#old-pokedex-text');

    // console.log(cuenca);

    $.ajax({
      url: pokemonData.ajaxurl,
      type: 'post',
      data: {
        action: 'pokemon-ajax',
        id_post: id,
      },
      beforeSend: function () {
        // link.html('Cargando ...');
        // $(this).html('loading...');
        // console.log('loading');
      },
      success: function (result) {
        link.removeClass('btn-outline-primary');
        link.addClass('btn-outline-success');
        link.html(pokemonData.loadedText);

        pokedexText.removeClass('invisible');
        pokedexText.html(result);
      },
    });
  });
})(jQuery);
