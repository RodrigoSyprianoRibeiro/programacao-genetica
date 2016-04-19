$(function () {

  // Ação do botão "Buscar solução" 
  $(document).on('click', '#buscar', function(e){
    e.preventDefault();
    buscarSolucao();
  });

  function buscarSolucao() {
    var dados = $('#parametros').serialize();
    $.ajax({
      dataType: "json",
      type: 'POST',
      url: 'library/executar.php',
      async: true,
      data: dados,
      success: function(response) {
        carregaLog();
        $("#resultado").html("<h2><b>Melhor Função: </b>" + response.funcao + "</h2>" +
                             "<h2><b>Aptidão: </b>" + response.aptidao + "</h2>");
      },
      error: function() {
        $("#resultado").html("<h2>Erro ao <b>Buscar solução</b>. Tente novamente.</h2>");
      },
      beforeSend: function(){
        $("#buscar").addClass("disabled");
        $("#tempo").removeClass("hide");
        hora = 0;
        minuto = 0;
        segundo = 0;
        cronometro();
        intervalId = setInterval(cronometro, 1000);
        $("#resultado").html("<img src='images/carregando.gif' alt='Carregando' />");
      },
      complete: function(){
        $("#buscar").removeClass("disabled");
        clearInterval(intervalId);
      }
    });
  };

  $("#populacao_inicial").ionRangeSlider({
    min: 0,
    max: 100,
    from: 50,
    type: 'single',
    step: 5,
    postfix: " indivíduos",
    prettify: false,
    hasGrid: true
  });

  $("#quantidade_geracoes").ionRangeSlider({
    min: 0,
    max: 100,
    from: 50,
    type: 'single',
    step: 5,
    postfix: " gerações",
    prettify: false,
    hasGrid: true
  });

  $("#altura_maxima_arvore").ionRangeSlider({
    min: 0,
    max: 10,
    from: 3,
    type: 'single',
    step: 1,
    hasGrid: true
  });

  $(".percentagem").ionRangeSlider({
    min: 0,
    max: 100,
    type: 'single',
    step: 1,
    postfix: "%",
    prettify: false,
    hasGrid: true
  });

  var hora = 0;
  var minuto = 0;
  var segundo = 0;

  function cronometro() {
    $('#cronometro').html(("0"+hora).slice(-2)+':'+("0"+minuto).slice(-2)+':'+("0"+segundo).slice(-2));
    segundo++;
    if (segundo === 60) {
      minuto++
      segundo = 0;
    }
    if (minuto === 60) {
      hora++
      minuto = 0;
    }
  }

  carregaLog();

  function carregaLog() {
    jQuery.get('log-ultimo.txt', function(data) {
      $('#log-ultimo').html(data.replace('n',''));
    });
    jQuery.get('log-historico.txt', function(data) {
      $('#log-historico').html(data.replace('n',''));
    });
  };
});