<?php
$mtm=new \Mitama\Mitama();
$appid=$mtm->appid;
$path=substr(explode("?",$_SERVER["REQUEST_URI"])[0],strlen($mtm->appinfo["application_root"]));
$seps=explode("/",$path);
switch($seps[0]){
    case "edit":
        header("Location: {$mtm->appinfo["application_root"]}edit.php?slug=main");
        break;
    case "setting":
        header("Location: {$mtm->appinfo["application_root"]}setting.php");
        break;
    default:
        switch(array_pop($seps)){
            case "edit":
                $qd=[
                    "slug"=>implode("/",$seps)
                ];
                foreach($_GET as $k=>$v) $qd[$k]=$v;
                $qs=http_build_query($qd);
                header("Location: {$mtm->appinfo["application_root"]}edit.php?$qs");
                break;
            case "setting":
                $qd=[
                    "slug"=>implode("/",$seps)
                ];
                foreach($_GET as $k=>$v) $qd[$k]=$v;
                $qs=http_build_query($qd);
                header("Location: {$mtm->appinfo["application_root"]}item_setting.php?$qs");
                break;
            default:
                $qd=[
                    "slug"=>$path
                ];
                foreach($_GET as $k=>$v) $qd[$k]=$v;
                $qs=http_build_query($qd);
                header("Location: {$mtm->appinfo["application_root"]}index.php?$qs");
                break;
        }
        break;
}
