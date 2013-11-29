<?php

function getSearchPage($title, $fields) {
    $html = file_get_contents("searches.html");

    $tableFields = "";
    foreach($fields as $field => $fieldName){
        $tableFields .= " \"$field\" : \"$fieldName\", ";
    }
    $tableFields = substr($tableFields, 0, -1); // remove the last comma

    $searchFields = "";
    foreach($fields as $field => $searchField){
        $searchFields .= "<option value=\"$field\">$searchField</option>";
    };

    $html = str_replace("{{title}}", $title, $html);
    $html = str_replace("{{tableFields}}", $tableFields, $html);
    $html = str_replace("{{searchFields}}", $searchFields, $html);

    session_start();

    $loginForm = "";
    $sessionEmpty = empty($_SESSION["username"]);
    if($sessionEmpty) {
        $loginForm .= '<form method="post" action="api/login.php">';
            $loginForm .= '<ul id="loginMenu">';
                $loginForm .= '<li><input type="text" name="login" value="" placeholder="Username or Email"></li>';
                $loginForm .= '<li><input type="password" name="password" value="" placeholder="Password"></li>';
                $loginForm .= '<li class="submit"><input type="submit" name="commit" value="Login"></li>';
                $loginForm .= '<li class="loginHelp"> <a href="index.html">Forgot password?</a></li>';
            $loginForm .= '</ul>';
        $loginForm .= '</form>';
    }
    else {
        $loginForm .= 'Welcome back, <strong>' . $_SESSION["username"] . '</strong>!';
        $loginForm .= ' <a href="api/logout.php">Logout</a>';
    }

    $html = str_replace("{{loginForm}}", $loginForm, $html);

    return $html;
}