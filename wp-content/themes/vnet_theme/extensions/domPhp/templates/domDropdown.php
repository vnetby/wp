<?php
namespace DomPhp;

global $svg; 

$content = get_from_array($args, 'content');
$label = get_from_array($args, 'label');
$hasArrow = get_from_array($args, 'arrow');
$customArrow = get_from_array($args, 'custom_arrow');

$btnClass = get_from_array($args, 'btnClass');
$contentClass = get_from_array($args, 'contentClass');
$containerClass = get_from_array($args, 'containerClass');

$direction = get_from_array($args, 'direction', 'left');
?>

<div class="dropdown<?= $containerClass ? ' ' . $containerClass : ''; ?>">
  <button type="button" class="open-dropdown<?= $btnClass ? ' ' . $btnClass : ''; ?><?= $hasArrow ? ' has-arrow' : ''; ?>">
    <?= $label; ?>
    <?php
    if ($hasArrow) {
      if ($customArrow) {
        echo $svg->get_ico($customArrow);
      } else {
    ?>
        <span class="ico">
          <svg width="7px" height="5px" viewBox="0 0 7 5" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <g transform="translate(-1221.000000, -57.000000)" stroke="#FFFFFF">
                <g transform="translate(40.000000, 30.000000)">
                  <g transform="translate(1152.000000, 5.000000)">
                    <g transform="translate(0.000000, 18.000000)">
                      <g transform="translate(29.000000, 4.000000)">
                        <path class="svg-stroke" d="M0.66723028,0.918896947 L3.82867007,4.08033674"></path>
                        <path class="svg-stroke" d="M3.66723028,0.918896947 L6.82867007,4.08033674" transform="translate(5.000000, 2.500000) scale(-1, 1) translate(-5.000000, -2.500000) "></path>
                      </g>
                    </g>
                  </g>
                </g>
              </g>
            </g>
          </svg>
        </span>
    <?php
      }
    }
    ?>
  </button>
  <div class="dropdown-content<?= $contentClass ? ' ' . $contentClass : ''; ?> <?= $direction; ?>">
    <?= $content; ?>
  </div>
</div>