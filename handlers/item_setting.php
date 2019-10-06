<?php

$slug=$_ARG["slug"];
$text=file_get_contents("{$slug}.md");
if(preg_match("/\+\+\+\n/",$text)){
    $content=explode("+++\n",$text)[1];
}else{
    $content=$text;
}
$meta="editor=".json_encode($_ARG["editor"])."\n";
file_put_contents("{$slug}.md",$meta."+++\n".$content);
