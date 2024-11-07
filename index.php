<?php
require_once 'include.php';

$template = $twig->load('base_template.html.twig');

echo $template->render(array(
    'description' => "Je fais mes tests"
)); ?>

