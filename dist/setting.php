<?php
$mtm=new \Mitama\Mitama();
$settings=$mtm->handler("get_setting");
if(($me=$mtm->session())===FALSE){
    header("HTTP/1.0 401 Unauthorized");
    $mtm->login();
    exit;
}
if($_SERVER["REQUEST_METHOD"]=="POST"){
    if($_POST["token"]!=$_COOKIE["token"]){
        header("HTTP/1.0 403 Forbidden");
        exit;
    }
    $closed=$_POST["closed"]=="on";
    if($_POST["noneditable"]!="on"){
        $editor=NULL;
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
    }
    $title=$_POST["title"];
    $mtm->handler("setting",[
        "editor"=>$editor,
        "closed"=>$closed,
        "title"=>$title
    ]);
    if($_GET["backto"]!=""){
        header("Location: /{$mtm->appid}/".$_GET["backto"]);
    }else{
        header("Location: /{$mtm->appid}/");
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
    <script type="module" src="api/mitama.js"></script>
    <title>設定 | ヰキ</title>
</head>
<body>
    <header>
        <h1 id="logo"><img src="img/logo.svg" alt="ヰキ"></h1>
        <div id="menu">
            <input type="checkbox" id="status">
            <button id="save_btn">
                変更
            </button>
        </div>
    </header>
    <main>
        <h1 id="title">設定</h1>
        <hr>
        <form method="POST" action="setting.php" id="setting_form">
            <div>
                <input type="checkbox" id="closed" name="closed" <?=$settings->closed?"checked":"";?>>
                <label class="check" for="closed">
                    ログインしていない人の閲覧を許可しない
                </label>
            </div>
            <div>
                <input type="checkbox" id="noneditable" name="noneditable" <?=$settings->default_editor!=NULL?"checked":"";?>>
                <label class="check" for="noneditable">
                    新しい項目の編集を制限
                </label>
                <div id="default_editor">
                    <h4>デフォルトの編集者</h4>
                    <input type="checkbox" id="admin" name="admin" value="[admin]" <?=$settings->default_editor->admin?"checked":""?>>
                    <label for="admin">管理者権限を持つユーザ</label>
                    <input type="checkbox" id="team" value="[team]" <?=count((array)$settings->default_editor->team)>0?"checked":""?>>
                    <label for="team">指定のチーム</label>
                    <div id="team_list">
<?php foreach($teams as $team):?>
                        <input type="checkbox" name="teams[]" value="<?=$team->id;?>" id="team_<?=$team->id;?>" <?=array_search($team->id,(array)$data["meta"]["editor"]->team)!==FALSE?"checked":""?>>
                        <label for="team_<?=$team->id?>">
                            <mtm-team-list screen_name="<?=$team->screen_name?>" name="<?=$team->name?>"></mtm-team-list>
                        </label>
<?php endforeach;?>
                    </div>
                    <input type="checkbox" id="user" value="[user]" <?=count((array)$settings->default_editor->user)>0?"checked":""?>>
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
            <div>
                <h4>トップページのタイトル</h4>
                <input type="text" name="title" value="<?=$settings->title;?>" placeholder="タイトル"/>
            </div>
            <input type="hidden" name="token" value="<?=$_COOKIE["token"];?>" />
        </form>
    </main>
    <script>
        if(location.href.match(/edit\.php\?slug\=(.*)$/)){
            history.replaceState(null,null,"<?=$_GET["slug"];?>/edit");
        }
        document.querySelector("#save_btn").onclick=()=>{
            document.querySelector("form").submit();
        };
    </script>
</body>
</html>
