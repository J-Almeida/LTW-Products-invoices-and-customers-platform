<?php
require_once 'bootstrap.php';
require_once './api/authenticationUtilities.php';
$neededPermissions = array('write');
evaluateSessionPermissions($neededPermissions);
require_once 'searches.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Import and Export</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/ico" href="favicon.ico"/>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="importDatabase.js"></script>

</head>
<body>

<div id="header">
    <h1>Import and Export</h1>
    <h2>Saft and Databases</h2>
</div>

<div id="menu">
    <ul id="menuList">
        <?php echo getMenuItems(); ?>
    </ul>

    <div class="login">
        <?php echo getLoginForm(); ?>
    </div>
</div>

<div id="searchMenu">
    <form id="importSaft" action="./api/importSaft.php" method="post"
          enctype="multipart/form-data">
        <label for="file">Import a SAFT-PT file:</label>
        <input id="fileButton" type="file" name="file" id="file">
        <input id="fileSubmitButton" type="submit" name="submit" value="Submit">
    </form>
    <form id="exportSaft" action="./api/exportSaft.php">
        <input type="submit" value="Export to SAFT-PT">
    </form>
    <form id="importDB">
        <input id="otherDatabaseURL" name="url" type="text" value="http://localhost/ltw2">
        <input type="button" value="Import Database" onclick="importDatabase(); return false;">
    </form>
</div>

<div id="content">
    <div id="results"></div>
</div>

</body>
</html>