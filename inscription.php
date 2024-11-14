<?php

require_once 'include.php';

$template = $twig->load('inscription.html.twig');


echo $template->render(array(
    'description' => "Je fais mes tests"
)); ?>



