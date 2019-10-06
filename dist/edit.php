<?php

$mtm=new \Mitama\Mitama();
$settings=$mtm->handler("get_setting");
$me=$mtm->session();
if($me===FALSE){
    header("HTTP/1.0 401 Unauthorized");
    $mtm->login();
    exit;
}
$title=$_GET["slug"]!="main"?urldecode($_GET["slug"]):$settings->title;
$data=$mtm->handler("open",["slug"=>$_GET["slug"]]);
if($data["meta"]["editor"]!=null&&$me->permission<100&&array_search($me->id,$data["meta"]["editor"]->user)&&array_count_values(array_merge($belong,$data["meta"]["editor"]->team))==0){
    header("HTTP/1.0 403 Forbidden");
    if(preg_match("/(http|https)\:\/\/{$_SERVER["HTTP_HOST"]}/",$_SERVER["HTTP_REFERER"])){
        header("Location: ".$_SERVER["HTTP_REFERER"]);
    }else{
        header("Location: {$mtm->appinfo["application_root"]}");
    }
    exit;
}
if($_SERVER["REQUEST_METHOD"]=="POST"){
    if($_POST["token"]!=$_COOKIE["token"]){
        header("HTTP/1.0 403 Forbidden");
        exit;
    }
    $mtm->handler("save",[
        "content"=>$_POST["content"],
        "slug"=>$_GET["slug"]
    ]);
    if($_GET["slug"]=="main"){
        header("Location: {$mtm->appinfo["application_root"]}");
    }else if($_GET["slug"]=="sidebar"){
        header("Location: {$mtm->appinfo["application_root"]}");
    }else{
        header("Location: {$mtm->appinfo["application_root"]}{$_GET["slug"]}");
    }
    exit;
}else{
    $token=sha1(uniqid().mt_rand());
    setcookie("token",$token);
    $_COOKIE["token"]=$token;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <base href="<?=$mtm->appinfo["application_root"];?>">
    <link rel="stylesheet" href="style.css">
    <title>ヰキ</title>
</head>
<body>
    <header>
        <h1 id="logo"><img src="img/logo.svg" alt="ヰキ"></h1>
        <div id="menu">
            <input type="checkbox" id="status">
            <button id="save_btn">
                保存
            </button>
        </div>
    </header>
    <main>
        <h1 id="title"><?=$title;?></h1>
        <hr>
        <form method="POST" action="edit.php?slug=<?=$_GET["slug"];?>">
            <textarea name="content" placeholder="マークダウン記法/HTMLが利用できます。
新しい項目を作成する場合は、
【MD記法】　[リンク文字](スラッグ文字)
【HTML】　<a href='スラッグ文字'>リンク文字</a>
という形式で記述してください"><?=$data["content"];?></textarea>
            <input type="hidden" name="token" value="<?=$_COOKIE["token"];?>" />
        </form>
    </main>
    <script>
        if(location.href.match(/edit\.php\?slug\=(.*)$/)){
            if("<?=$_GET["slug"];?>"!="main"){
                history.replaceState(null,null,"<?=$_GET["slug"];?>/edit");
            }else{
                history.replaceState(null,null,"edit");
            }
        }
        document.querySelector("#save_btn").onclick=()=>{
            document.querySelector("form").submit();
        };
    </script>
</body>
</html>
