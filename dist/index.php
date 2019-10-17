<?php

$mtm=new \Mitama\Mitama();
$settings=$mtm->handler("get_setting");
$me=$mtm->session();
if($settings->closed&&$me===FALSE){
    header("HTTP/1.0 401 Unauthorized");
    $mtm->login();
    exit;
}
$seps=explode("/",urldecode($_GET["slug"]));
$title=$_GET["slug"]!=""?array_pop($seps):$settings->title;
$data=$mtm->handler("open",["slug"=>$_GET["slug"]!=""?$_GET["slug"]:"main"]);
$kz=$seps;
$sidebar=$mtm->handler("open",["slug"=>"sidebar"]);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <base href="<?=$mtm->appinfo["application_root"];?>">
    <link rel="stylesheet" href="style.css">
    <title><?=$title?> | ヰキ</title>
    <script src="marked.min.js"></script>
</head>
<body>
    <input type="checkbox" id="sidemenu_toggle">
    <div id="wrapper">
        <div id="sidebar">
<?php if($sidebar["content"]!=""):?>
            <script id="sidebar_content" type="text/markdown"><?=$sidebar["content"];?></script>
            <?php if(!($sidebar["meta"]["editor"]!=null&&($sidebar["meta"]["editor"]->admin&&$me->permission<100)&&array_search($me->id,$data["meta"]["editor"]->user)&&array_count_values(array_merge($belong,$sidebar["meta"]["editor"]->team))==0)):?>
            <a href="sidebar/edit">編集</a>
            <?php endif; ?>
            <div class="inner"></div>
<?php else:?>
            <div class="inner">
                サイドバーはまだ内容が書き込まれていません。<br>
<?php if(!($sidebar["meta"]["editor"]!=null&&($sidebar["meta"]["editor"]->admin&&$me->permission<100)&&array_search($me->id,$data["meta"]["editor"]->user)&&array_count_values(array_merge($belong,$sidebar["meta"]["editor"]->team))==0)):?>
                <a href="sidebar/edit">こちら</a>から編集をはじめましょう。
<?php endif;?>
            </div>
<?php endif;?>
            <label id="overlay" for="sidemenu_toggle"></label>
        </div>
        <div id="container">
            <header>
                <label for="sidemenu_toggle"><h1 id="logo"><img src="img/logo.svg" alt="ヰキ"></h1></label>
                <div id="menu">
                    <input type="checkbox" id="status">
                    <label id="trigger" for="status">
                        <span></span>
                    </label>
                    <menu>
<?php if(!($data["meta"]["editor"]!=null&&($data["meta"]["editor"]->admin&&$me->permission<100)&&array_search($me->id,$data["meta"]["editor"]->user)&&array_count_values(array_merge($belong,$data["meta"]["editor"]->team))==0)):?>
                        <a href="<?=$_GET["slug"].($_GET["slug"]!=""?"/":"");?>edit">項目を編集</a>
                        <a href="<?=$_GET["slug"].($_GET["slug"]!=""?"/":"main/");?>setting">項目の設定</a>
<?php endif;?>
<?php if($me->permission>=100):?>
                        <a href="setting">ヰキの設定</a>
<?php endif;?>
                    </menu>
                </div>
            </header>
            <main>
<?php if(count($kz)>0):
$str="";
?>
                <div class="kz">
<?php foreach($kz as $k):
$str.=$k;
?>
                    <a href="<?=$str;?>"><?=$k;?></a> &gt;
<?php endforeach;?>
                </div>
<?php endif;?>
                <h1 id="title"><?=$title;?></h1>
                <hr>
<?php if($data["content"]!=""):?>
                    <script id="content" type="text/markdown"><?=$data["content"];?></script>
                    <section id="view"></section>
<?php else:?>
                    <section id="view" class="nocontent">
                        この項目はまだ内容が書き込まれていません。<br>
<?php if(!($data["meta"]["editor"]!=null&&($data["meta"]["editor"]->admin&&$me->permission<100)&&array_search($me->id,$data["meta"]["editor"]->user)&&array_count_values(array_merge($belong,$data["meta"]["editor"]->team))==0)):?>
                        <a href="<?=$_GET["slug"].($_GET["slug"]!=""?"/":"");?>edit">こちら</a>から編集をはじめましょう。
<?php endif;?>
                    </section>
<?php endif;?>
            </main>
        </div>
    </div>
    <script>
        if(location.href.match(/index\.php\?slug\=(.*)$/)){
            history.replaceState(null,null,"<?=$_GET["slug"];?>");
        }
        <?php if($sidebar["content"]!=""):?>
        document.querySelector("#sidebar .inner").innerHTML=marked(document.querySelector("#sidebar_content").innerHTML);
        <?php endif;?>
        <?php if($data["content"]!=""):?>
        document.querySelector("#view").innerHTML=marked(document.querySelector("#content").innerHTML);
        <?php endif;?>
    </script>
</body>
</html>
