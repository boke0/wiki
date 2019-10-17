<?php

if(!file_exists($_ARG["slug"].".md")){
    $settings=json_decode(file_get_contents("setting.json"));
    if(empty($settings->default_editor)){
        $dirs=explode("/",urldecode($_ARG["slug"]));
        $cd="";
        $filename=array_pop($dirs);
        foreach($dirs as $dir){
            $cd.=($dir."/");
            if(!is_dir($cd)) mkdir($cd);
        }
        $fp=fopen($cd.$filename.".md","w");
        fwrite($fp,"editor=".json_encode($settings->default_editor)."\n+++\n");
        fclose($fp);
    }
}
$return=[];
$data=explode("+++\n",file_get_contents(urldecode($_ARG["slug"]).".md"));
if(count($data)>1){
    $metas=explode("\n",trim($data[0]));
    $return["meta"]=[];
    foreach($metas as $meta){
        $a=explode("=",trim($meta));
        $return["meta"][trim($a[0])]=json_decode(trim($a[1]));
    }
    $return["content"]=$data[1];
}else{
    $return["content"]=$data[0];
}
return $return;
