<?php

require __DIR__.'/../config.php';
require __DIR__.'/../Preambula.php';

$preambula = new Preambula($preambula_settings);
$preambula->display($_SERVER['REQUEST_URI']);