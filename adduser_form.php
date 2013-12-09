<?php
require_once 'bootstrap.php';
require_once './api/authenticationUtilities.php';
$neededPermissions = array('promote');
evaluateSessionPermissions($neededPermissions);
?>
<!doctype html>
<html dir="ltr" lang="en" class="no-js">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/ico" href="favicon.ico"/>

    <title>Manage user</title>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="adduser_form.js"></script>
    <script>
    var username = "<?php echo ( isset( $_GET['UsernameInput'] ) && $_GET['UsernameInput'] != '') ? $_GET['UsernameInput'] : '';?>";
    </script>

</head>
<body onload="getUser(username)" >

    <div id="loadingUser">
        <span>Loading user</span><br>
        <img src='ajax-loader.gif' alt='loading' />
    </div>

    <div id="user" style="display: none; /*Jquery deals with showing the element after everything is loaded */">
        <form onsubmit="submitForm('user'); return false;" data-action="./api/insertUser.php" method="POST" autocomplete="off">

            <div class="userTitle">
                <strong>Manage user</strong>
            </div>

            <header id="userHeader">
                <ul class="userInfo">
                    <li>Username: <span id="username"> <input type="text" pattern="^[A-Za-z][A-Za-z0-9]{4,29}$" name="username">
                    </span></li>
                </ul>
            </header>

            <section id="userDetail">
                <ul class="userDetail">
                    <li>Name:
                        <p id="name">
                            <input type="text" pattern="^[a-zA-Z0-9 \u00A0-\u018F &amp;$%!@,'#.-]{1,50}$" name="name">
                        </p>
                    </li>

                    <li>Email Address:
                        <p id="emailAddress">
                            <input type="email" name="Email">
                        </p>
                    </li>

                    <li>Password:
                        <p id="password">
                            <input type="password" pattern="^[a-zA-Z0-9 \u00A0-\u018F &amp;$%!@,'#.-]{1,50}$" name="password">
                        </p>
                    </li>

                    <li>Permission level:
                        <p id="permissionType">
                            <select name="permissionId" pattern="^[0-9]{1,20}$">
                                <?php
                                $permissions = getAllPermissions();
                                foreach($permissions as $permission) {
                                    echo '<option value=' . $permission['permissionId'] . '>';
                                    echo $permission['permissionType'];
                                    echo '</option>';
                                }
                                ?>
                            </select>
                        </p>
                    </li>
                </ul>
            </section>

            <div id="submitButton">
                <input type="submit" value="Submit">
            </div>
        </form>
    </div>

</body>

</html>