<html>
  <head>
    <?//=$asset->load_css('arquivo1','pasta/arquivo2')?>
    <?=$asset->css()?>
    <?=$asset->js()?>
    <?=$load_head?>
  </head>
  <body>
    <h1>V Raptor</h1>
    <?// pr(get_defined_vars())?>

    <? include($layout['menu']) ?>
    <div id="main1"><? include($layout['main']) ?></div>
  </body>
</html>


