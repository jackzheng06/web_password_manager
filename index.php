<!doctype html>
<html>
    <head>
        <title></title>
        <meta charset="utf-8">
        <link href="less/main.less" type="text/less" rel="stylesheet" />
        <link href="lib/font-awesome-4.6.3/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
        <script src="lib/jquery-2.2.4.min.js"></script>
        <script src="https://bitwiseshiftleft.github.io/sjcl/sjcl.js"></script>
        <script src="js/ui-framework.js"></script>
        <script src="js/authentication-framework.js"></script>
        <script src="js/encryption-framework.js"></script>
        <script src="js/md5.js"></script>
        <script src="lib/less.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <div id="control-bar">
            <ul>
                <a href="#main"><li><i class="fa fa-list" aria-hidden="true"></i></li></a>
                <li id="logout-nav-button"><i class="fa fa-sign-out" aria-hidden="true"></i></li>
            </ul>
        </div>
        <div id="page-container"></div>
        <div id="loading-indicator"><i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i> Loading</div>
    </body>
</html>

<script>
    var ui;
    var auth;
    $(function(){
        ui = new UiFramework();
        auth = new AuthenticationFramework();
        encrypt = new EncryptionFramework();
        ui.window_hashchange();
        auth.initialize();
        $("#logout-nav-button").click(function(){
            if(confirm("Do you want to logout?")){
                auth.revoke_token();
                window.location.reload();
            }
        });
    });
</script>