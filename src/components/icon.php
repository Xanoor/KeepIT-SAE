<?php

function icon($name) {
    return file_get_contents(__DIR__ . '/../../node_modules/lucide-static/icons/' . $name . '.svg');
}