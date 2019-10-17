<?php

$slug=$_ARG["slug"];
$dirs=explode("/",urldecode($_ARG["slug"]));
$cd="";
$filename=array_pop($dirs);
foreach($dirs as $dir){
    $cd.=($dir."/");
    if(!is_dir($cd)) mkdir($cd);
}
$fp=fopen($cd.$filename.".md","w");
$text=file_get_contents("{$cd}{$filename}.md");
if(preg_match("/\+\+\+\n/",$text)){
    $content=explode("+++\n",$text)[1];
}else{
    $content=$text;
}
$meta="editor=".json_encode($_ARG["editor"])."\n";
file_put_contents("{$cd}{$filename}.md",$meta."+++\n".$content);
