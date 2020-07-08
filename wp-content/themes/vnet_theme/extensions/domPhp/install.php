<?php

namespace DomPhp;

define('TEMPLATES_PATH', dirname(__FILE__) . '/templates/');



function getTemplate($name = '', $args = [])
{
  $file = TEMPLATES_PATH . $name . '.php';
  if (!file_exists($file)) return false;
  require $file;
}
