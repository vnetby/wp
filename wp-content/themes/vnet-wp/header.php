<?php
global $about, $svg;
$reSets = get_re_sets();
?>
<!DOCTYPE html>
<html lang="<?= LANG; ?>">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <?php
  wp_head();
  ?>

  <link rel="apple-touch-icon" sizes="180x180" href="<?= CURRENT_SRC; ?>favicons/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= CURRENT_SRC; ?>favicons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= CURRENT_SRC; ?>favicons/favicon-16x16.png">
  <link rel="manifest" href="<?= CURRENT_SRC; ?>favicons/site.webmanifest">
  <link rel="mask-icon" href="<?= CURRENT_SRC; ?>favicons/safari-pinned-tab.svg" color="#0058a3">
  <meta name="msapplication-TileColor" content="#fbd914">
  <meta name="theme-color" content="#0058a3">

  <link rel="stylesheet" href="<?= CURRENT_SRC; ?>css/head.min.css?version=<?= ASSETS_VERSION; ?>">
  <link rel="stylesheet" href="<?= CURRENT_SRC; ?>assets/assets.min.css?version=<?= ASSETS_VERSION; ?>">
  <link rel="stylesheet" href="<?= CURRENT_SRC; ?>css/main.min.css?version=<?= ASSETS_VERSION; ?>">

  <?php
  $validateMsg = [
    'invalidExtension' => 'недопустимый формат файла',
    'required' => 'Заполните поле',
    'checkboxRequired' => 'Выберите значение',
    'invalidEmail' => 'Неверный формат e-mail',
    'invlidCompare' => 'Значения не совпадают',
    'imgFormat' => 'Выберите изображение',
    'maxFileSize' => 'Макимальный размер фала: $1мб', // $1 - max file size
    'minLength' => 'Минимальное количество символов: $1' // $1 - min number
  ];
  ?>
  <script>
    window.calcSettings = JSON.parse('<?= json_encode(get_calculator_settings()); ?>');
    back_dates.validateMsg = JSON.parse('<?= json_encode($validateMsg); ?>');
    back_dates.orderPage = '<?= get_order_page(); ?>';
    back_dates.checkoutPage = '<?= get_checkout_page(); ?>';
    back_dates.reSets = JSON.parse('<?= json_encode($reSets); ?>');
  </script>
  <script src="https://www.google.com/recaptcha/api.js?render=<?= get_from_array($reSets, 'site_key'); ?>" async></script>
</head>

<body class="<?= implode(' ', get_body_class()); ?>">
  <script src="<?= CURRENT_SRC; ?>js/head.min.js"></script>
  <?= vnet_get_template('template-nearest_delivery'); ?>