<?php

$slug=$_ARG["slug"];
$text=file_get_contents("{$slug}.md");
if(preg_match("/\+\+\+\n/",$text)){
    $meta=explode("+++\n",$text)[0];
    $content=$_ARG["content"];
    file_put_contents("{$slug}.md","{$meta}+++\n{$content}");
}else{
    $content=$_ARG["content"];
    file_put_contents("{$slug}.md",$content);
}
