<?php
$mtm=new \Mitama\Mitama();
$appid=$mtm->appid;
$path=array_slice(explode("/",explode("?",$_SERVER["REQUEST_URI"])[0]),2);
switch($path[0]){
    case "edit":
        header("Location: {$mtm->appinfo["application_root"]}edit.php?slug=main");
        break;
    case "setting":
        header("Location: {$mtm->appinfo["application_root"]}setting.php");
        break;
    default:
        $qd=[
            "slug"=>$path[0]
        ];
        foreach($_GET as $k=>$v) $qd[$k]=$v;
        $qs=http_build_query($qd);
        switch($path[1]){
            case "edit":
                header("Location: {$mtm->appinfo["application_root"]}edit.php?$qs");
                break;
            case "setting":
                header("Location: {$mtm->appinfo["application_root"]}item_setting.php?$qs");
                break;
            default:
                header("Location: {$mtm->appinfo["application_root"]}index.php?$qs");
                break;
        }
        break;
}
