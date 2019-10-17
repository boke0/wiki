<?php

$slug=$_ARG["slug"];
$dirs=explode("/",urldecode($_ARG["slug"]));
$cd="";
$filename=array_pop($dirs);
foreach($dirs as $dir){
    $cd.=($dir."/");
    if(!is_dir($cd)) mkdir($cd);
}
$text=file_get_contents("{$cd}{$filename}.md");
if(preg_match("/\+\+\+\n/",$text)){
    $meta=explode("+++\n",$text)[0];
    $content=$_ARG["content"];
    file_put_contents("{$cd}{$filename}.md","{$meta}+++\n{$content}");
}else{
    $content=$_ARG["content"];
    file_put_contents("{$cd}{$filename}.md",$content);
}
