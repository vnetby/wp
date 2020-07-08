<?php

namespace DomPhp;

$sets = get_from_array($args, 'sets');
$sets = $sets ? ' data-slider-sets=\'' . json_encode($sets) . '\'' : '';
$id = get_from_array($args, 'id');
$slides = get_from_array($args, 'slides');
$sliderClass = get_from_array($args, 'sliderClass');
$sliderClass = $sliderClass ? ' ' . $sliderClass : '';

$slideClass = get_from_array($args, 'slideClass');
$slideClass = $slideClass ? ' ' . $slideClass : '';

?>
<div class="dom-slider" <?= $id ? ' id="' . $id . '"' : ''; ?> <?= $sets; ?>>
  <?php
  foreach ($slides as $item) {
    echo $item;
  }
  ?>
</div>
<?php
