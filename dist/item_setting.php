<?php
$mtm=new \Mitama\Mitama();
$settings=$mtm->handler("get_setting");
$me=$mtm->session();
if($me===FALSE){
    header("HTTP/1.0 401 Unauthorized");
    $mtm->login();
    exit;
}

$data=$mtm->handler("open",["slug"=>$_GET["slug"]!="main"?$_GET["slug"]:""]);
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
    if($_POST["controled"]=="false"){
        $mtm->handler("item_setting",[
            "editor"=>NULL,
            "slug"=>$_GET["slug"]
        ]);
    }else{
        foreach((array)$_POST["teams"] as $k=>$v){
            $_POST["teams"][$k]=intval($v);
        }
        foreach((array)$_POST["users"] as $k=>$v){
            $_POST["users"][$k]=intval($v);
        }
        $editor=[
            "admin"=>$_POST["admin"]=="[admin]",
            "team"=>($_POST["teams"]),
            "user"=>($_POST["users"])
        ];
        $mtm->handler("item_setting",[
            "editor"=>$editor,
            "slug"=>$_GET["slug"]!="main"?$_GET["slug"]:""
        ]);
    }
    if($_GET["slug"]!="main"){
        header("Location: {$mtm->appinfo["application_root"]}{$_GET["slug"]}");
    }else{
        header("Location: {$mtm->appinfo["application_root"]}");
    }
    exit;
}else{
    $token=sha1(uniqid().mt_rand());
    setcookie("token",$token);
    $_COOKIE["token"]=$token;
}
$users=$mtm->users();
$teams=$mtm->teams();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <base href="<?=$mtm->appinfo["application_root"];?>">
    <link rel="stylesheet" href="style.css">
    <script type="module" src="/mitama.js"></script>
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
        <h1 id="title"><?=$_GET["slug"]!="main"?"『".urldecode($_GET["slug"])."』":"トップページ"?>の設定</h1>
        <hr>
        <form method="POST" action="item_setting.php?slug=<?=$_GET["slug"];?>" id="setting_form">
            <div>
                <input type="radio" id="noncontroled" name="controled" value="false" <?=$data["meta"]["editor"]==NULL?"checked":""?>/>
                <label for="noncontroled">編集者を制限しない</label>
            </div>
            <div>
                <input type="radio" id="controled" name="controled" value="true" <?=$data["meta"]["editor"]!=NULL?"checked":""?>/>
                <label for="controled">編集者を制限する</label>
                <div id="editables">
                    <h4>編集可能なユーザー</h4>
                    <input type="checkbox" id="admin" name="admin" value="[admin]" <?=$data["meta"]["editor"]->admin?"checked":""?>>
                    <label for="admin">管理者権限を持つユーザ</label>
                    <input type="checkbox" id="team" value="[team]" <?=count((array)$data["meta"]["editor"]->team)>0?"checked":""?>>
                    <label for="team">指定のチーム</label>
                    <div id="team_list">
<?php foreach($teams as $team):?>
                        <input type="checkbox" name="teams[]" value="<?=$team->id;?>" id="team_<?=$team->id;?>" <?=array_search($team->id,(array)$data["meta"]["editor"]->team)!==FALSE?"checked":""?>>
                        <label for="team_<?=$team->id?>">
                            <mtm-team-list screen_name="<?=$team->screen_name?>" name="<?=$team->name?>"></mtm-team-list>
                        </label>
<?php endforeach;?>
                    </div>
                    <input type="checkbox" id="user" value="[user]" <?=count((array)$data["meta"]["editor"]->user)>0?"checked":""?>>
                    <label for="user">指定のユーザー</label>
                    <div id="user_list">
<?php foreach($users as $user):?>
                        <input type="checkbox" name="users[]" value="<?=$user->id;?>" id="user_<?=$user->id;?>" <?=array_search($user->id,(array)$data["meta"]["editor"]->user)!==FALSE?"checked":""?>>
                        <label for="user_<?=$user->id?>">
                            <mtm-user-list screen_name="<?=$user->screen_name?>" name="<?=$user->name?>"></mtm-user-list>
                        </label>
<?php endforeach;?>
                    </div>
                </div>
            </div>
            <input type="hidden" name="token" value="<?=$_COOKIE["token"];?>" />
        </form>
    </main>
    <script>
        if(location.href.match(/item_setting\.php\?slug\=(.*)$/)){
            history.replaceState(null,null,"<?=$_GET["slug"];?>/setting");
        }
        document.querySelector("#save_btn").onclick=()=>{
            document.querySelector("form").submit();
        };
    </script>
</body>
</html>
