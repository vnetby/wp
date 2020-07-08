<?php

add_action('init', 'about_set_info');

$about_set = [
  'prefix_phone'    => 'tel:',
  'prefix_viber'    => 'viber://chat?number=',
  'prefix_whatsapp' => 'whatsapp://send?phone=',
  'prefix_telegram' => 'tg://resolve?domain=',
  'prefix_email'    => 'mailto:',
  'prefix_skype'    => 'skype:'
];


$about_operator = [
  'velcom'    => 'Velcom',
  'mts'       => 'МТС',
  'urban_fax' => 'факс',
  'life'      => 'Life',
  'urban'     => 'Городской'
];



function about_set_info()
{
  global $about, $about_set, $about_operator;
  if (!$about) return;
  foreach ($about as $key => &$info) {

    if (!$info) continue;

    if ($key === 'logo') {
      about_set_logo($info, $about_set);
      continue;
    }


    if ($key === 'phones') {
      about_set_phones($info, $about_set, $about_operator);
      continue;
    }


    if ($key === 'messenger') {
      about_set_messenger($info, $about_set);
      continue;
    }


    if ($key === 'emails') {
      about_set_emails($info, $about_set);
      continue;
    }


    if ($key === 'socials') {
      about_set_socials($info, $about_set);
      continue;
    }
  }
}




function about_set_phones(&$phones, &$about_set, &$about_operator)
{
  foreach ($phones as &$item) {
    $item['phone'] = $item['phone'];
    $item['link'] = about_get_phone_link($item['phone'], $about_set);
    $item['title'] = isset($about_operator[$item['operator']]) ? $about_operator[$item['operator']] : '';
  }
}






function about_set_logo(&$logo, &$about_set)
{
  if (isset($logo['img'])) {
    if (is_array($logo['img'])) {
      $logo = '<img src="' . $logo['img']['url'] . '" alt="' . $logo['img']['alt'] . '" class="logo-img">';
      return;
    }
  }
  if (isset($logo['text'])) {
    if ($logo['text']) {
      $logo = $logo['text'];
      return;
    }
  }
  $logo = '';
  return;
}






function about_set_messenger(&$messengers, &$about_set)
{
  foreach ($messengers as &$messenger) {
    $res = [];
    if (!$messenger['messenger']) continue;
    foreach ($messenger['messenger'] as &$item) {
      $link = $messenger['link'];
      $res[$item] = about_get_messenger_link($item, $link, $about_set);
    }
    $messenger = $res;
  }
}






function about_set_emails(&$emails, &$about_set)
{
  foreach ($emails as &$email) {
    $email['email'] = $email['email'];
    $email['link'] = about_get_email_link($email['email'], $about_set);
  }
}



function about_get_email_link($email, &$about_set)
{
  return $about_set['prefix_email'] . $email;
}






function about_set_socials(&$socials, &$about_set)
{
  $res = [];

  foreach ($socials as &$social) {
    $res[$social['social']] = $social['link'];
  }
  $socials = $res;
}






function about_get_phone_link($phone, &$about_set)
{
  $res = preg_replace('/[^0-9]+/u', '', $phone);
  return $about_set['prefix_phone'] . '+' . $res;
}





function about_get_messenger_link($mess, $link, &$about_set)
{
  if ($mess === 'viber') {
    $res = preg_replace('/[^0-9]+/u', '', $link);
    return $about_set['prefix_viber'] . '+' . $res;
  }

  if ($mess === 'whatsapp') {
    $res = preg_replace('/[^0-9]+/u', '', $link);
    return $about_set['prefix_whatsapp'] . '+' . $res;
  }

  if ($mess === 'telegram') {
    $res = preg_replace('/[^0-9]+/u', '', $link);
    return $about_set['prefix_telegram'] . '+' . $res;
  }

  if ($mess === 'skype') {
    $res = preg_replace('/[^0-9]+/u', '', $link);
    return $about_set['prefix_skype'] . '+' . $res . '?call';
  }
}


//*********************************************************** */
//                          HTML
//*********************************************************** */



function about_phones_html($limit = false)
{
  global $about, $svg;
  if (isset($about['phones'])) {
    if (is_array($about['phones'])) {
      $total = $limit ? $limit : count($about['phones']);
?>
      <div class="contacts-block phones-block ico-left">
        <div class="ico">
          <?= $svg->get_ico('phone'); ?>
        </div>
        <?php
        $count = 0;
        foreach ($about['phones'] as $phone) {
          if ($count === $total) break;
        ?>
          <div class="contacts-row phone-row">
            <a href="<?= $phone['link']; ?>" target="_blank" title="<?= $phone['title']; ?>"><?= $phone['phone']; ?></a>
          </div>
        <?php
          $count++;
        }
        ?>
      </div>
  <?php
    }
  }
}






function about_emails_html($limit = false)
{
  global $about, $svg;
  if (!isset($about['emails'])) return;
  if (!is_array($about['emails'])) return;
  $total = $limit ? $limit : count($about['emails']);
  ?>
  <div class="contacts-block emails-block ico-left">
    <div class="ico">
      <?= $svg->get_ico('email'); ?>
    </div>
    <?php
    $count = 0;
    foreach ($about['emails'] as &$email) {
      if ($count === $total) break;
    ?>
      <div class="contacts-row phone-row">
        <a href="<?= $email['link']; ?>" target="_blank" title="<?= $email['email']; ?>"><?= $email['email']; ?></a>
      </div>
    <?php
      $count++;
    }
    ?>
  </div>
<?php
}







function about_socials_html($limit = false)
{
  global $about, $svg;
  if (!isset($about['socials'])) return;
  if (!is_array($about['socials'])) return;
  $total = $limit ? $limit : count($about['socials']);
?>
  <div class="socials-container contacts-block">
    <div class="socials-row">
      <?php
      $count = 0;
      foreach ($about['socials'] as $key => $link) {
        if ($count === $total) break;
      ?>
        <div class="social-item">
          <a href="<?= $link; ?>" target="_blank" class="ico social-ico <?= $key; ?>">
            <?= $svg->get_ico($key); ?>
          </a>
        </div>
      <?php
        $count++;
      }
      ?>
    </div>
  </div>
<?php
}






function about_has($key)
{
  global $about;

  if (!isset($about[$key])) return false;
  if (!$about[$key]) return false;

  $arrayKeys = ['phones', 'emails', 'socials'];

  if (in_array($key, $arrayKeys)) {
    if (!is_array($about[$key])) return false;
    if (!count($about[$key])) return false;
  }

  return true;
}





function about_address_html()
{
  global $about, $svg;
  if (!isset($about['adress'])) return;
  if (!$about['adress']) return;
  $addr = $about['adress'];
?>
  <div class="adress-block contacts-block ico-left">
    <div class="ico">
      <?= $svg->get_ico('addr'); ?>
    </div>
    <div class="address contacts-row">
      <?= $addr; ?>
    </div>
  </div>
<?php
}




function about_time_work_html()
{
  global $about, $svg;
  if (!isset($about['time_work'])) return;
  if (!is_array($about['time_work'])) return;

?>
  <div class="work-time-container ico-left">
    <div class="ico">
      <?= $svg->get_ico('time'); ?>
    </div>
    <div class="work-time-block contacts-block">
      <?php
      foreach ($about['time_work'] as &$item) {
      ?>
        <div class="time-row contacts-row">
          <?= isset($item['days']) ? '<span class="days">' . $item['days'] . '</span>' : ''; ?>
          <?= isset($item['time']) ? '<span class="time">' . $item['time'] . '</span>' : ''; ?>
        </div>
      <?php
      }
      ?>
    </div>
  </div>
<?php
}
