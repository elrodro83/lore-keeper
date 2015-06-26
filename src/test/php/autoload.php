<?php
spl_autoload_register(
    function($class) {
    	include 'src/main/php/LoreKeeper/metadata/' . $class . '.php';
    }
);
