<?php

namespace DomPhp;

$num = get_from_array($args, 'num');
$step = get_from_array($args, 'step', 20);
$margin = get_from_array($args, 'margin', '-100');
$interval = get_from_array($args, 'interval', 100);
?>
<div class="dom-count-on-scroll" data-max="<?= $num; ?>" data-margin="<?= $margin; ?>" data-interval="<?= $interval; ?>" data-step="<?= $step; ?>">
  0
</div>