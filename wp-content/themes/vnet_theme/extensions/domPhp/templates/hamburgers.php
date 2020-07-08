<?php
namespace DomPhp;

$type = get_from_array($args, 'type');
$type = $type ? $type : 'slider';

$close = get_from_array($args, 'close');
$isActiveClass = $close ? ' is-active' : '';

$size = get_from_array($args, 'size');
$size = $size ? ' ' . $size . '-hamburger' : '';

$btnClass = get_from_array($args,'btnClass');
$btnClass = $btnClass ? ' ' . $btnClass : '';

if ($type === 'slider') {
?>
  <div class="hamburger hamburger--slider js-hamburger<?= $isActiveClass; ?><?= $size; ?><?= $btnClass; ?>">
    <div class="hamburger-box">
      <div class="hamburger-inner"></div>
    </div>
  </div>
<?php
  return;
}


if ($type === 'squeeze') {
?>
  <div class="hamburger hamburger--squeeze js-hamburger<?= $isActiveClass; ?><?= $size; ?><?= $btnClass; ?>">
    <div class="hamburger-box">
      <div class="hamburger-inner"></div>
    </div>
  </div>
<?php
  return;
}
