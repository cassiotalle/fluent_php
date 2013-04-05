<html>
  <head>
    <? //=$asset->load_css('arquivo1','pasta/arquivo2')?>
    <?= $asset->css() ?>
    <?= $asset->js() ?>
    <?= $load_head ?>
  </head>
  <body>
    <div id="page" class="wrapper">
      <?= $asset->img('logo.png', 'logo'); ?>
      <?= $Session->flash() ?>
      <? include($layout['menu']) ?>
      <div id="main1"><? include($layout['main']) ?></div>
    </div>
  </body>
</html>