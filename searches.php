<?php

function getSearchPage($title, $fields) {
    $html = file_get_contents("searches.html");

    $loginForm = getLoginForm();

    $html = str_replace("{{loginForm}}", $loginForm, $html);

    $menuItems = getMenuItems();

    $html = str_replace("{{menuItems}}", $menuItems, $html);

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

    $object = substr($title, 0, -1); $object = lcfirst($object);
    $html = str_replace("{{object}}", $object, $html);

    return $html;
}

function getLoginForm()
{
    $loginForm = "";
    if (empty($_SESSION["username"])) {
        $loginForm .= '<form method="post" action="login.php">';
        $loginForm .= '<ul id="loginMenu">';
        $loginForm .= '<li><input type="text" name="login" value="" placeholder="Username or Email"></li>';
        $loginForm .= '<li><input type="password" name="password" value="" placeholder="Password"></li>';
        $loginForm .= '<li class="submit"><input type="submit" name="commit" value="Login"></li>';
        $loginForm .= '<li class="loginHelp"> <a href="register.html">Register</a></li>';
        $loginForm .= '</ul>';
        $loginForm .= '</form>';
        return $loginForm;
    } else {
        $loginForm .= 'Welcome back, <strong>' . $_SESSION["username"] . '</strong>!';
        $loginForm .= ' <a href="logout.php">Logout</a>';
        return $loginForm;
    }
}

function getMenuItems() {
    $menuItems = "";
    $menuItems .= '<li><a href="index.php">Home</a></li>';

    if (!empty($_SESSION["username"])) {
        if (comparePermissions(array('read'))) {
            $menuItems .= '<li><a href="invoices.php">Invoices</a></li>';
            $menuItems .= '<li><a href="customers.php">Customers</a></li>';
            $menuItems .= '<li><a href="products.php">Products</a></li>';
        }

        if (comparePermissions(array('promote'))) {
            $menuItems .= '<li><a href="users.php">Users</a></li>';
        }

        if (comparePermissions(array('write'))) {
            $menuItems .= '<li><a href="importAndExport.php">Import and Export</a></li>';
            return $menuItems;
        }
        return $menuItems;
    }
    return $menuItems;
}