<?php
$settings=[
    "closed"=>$_ARG["closed"],
    "title"=>$_ARG["title"],
    "default_editor"=>$_ARG["editor"]
];
file_put_contents("setting.json",json_encode($settings));
