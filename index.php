<!DOCTYPE html>
<?php session_start(); ?>
<html>
<head>
    <title>Invoice Database</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/ico" href="favicon.ico"/>
</head>
<body>

    <div id="header">
        <h1>Linguagens e Tecnologias Web</h1>
        <h2>Invoice database</h2>
    </div>

    <div id="menu">
        <ul id="menuList">
            <li><a href="index.html">Home</a></li>
            <li><a href="invoices.php">Invoices</a></li>
            <li><a href="customers.php">Customers</a></li>
            <li><a href="products.php">Products</a></li>
        </ul>

        <div class="login">
            <?php
            $sessionEmpty = empty($_SESSION["username"]);
                if($sessionEmpty) {
                    echo '<form method="post" action="api/login.php">';
                        echo '<ul id="loginMenu">';
                            echo '<li><input type="text" name="login" value="" placeholder="Username or Email"></li>';
                            echo '<li><input type="password" name="password" value="" placeholder="Password"></li>';
                            echo '<li class="submit"><input type="submit" name="commit" value="Login"></li>';
                            echo '<li class="loginHelp"> <a href="index.html">Forgot password?</a></li>';
                        echo '</ul>';
                    echo '</form>';
                }
                else {
                    echo 'Welcome back, <strong>' . $_SESSION["username"] . '</strong>!';
                    echo ' <a href="api/logout.php">Logout</a>';
                }
            ?>
        </div>
</div>

<div id="content">
    <h2>Mestrado Integrado em Engenharia Informática e Computação<br>2013 / 2014</h2>
    <h2>Group:</h2>
    Diogo Ribeiro Gomes dos Santos <br><strong> ei11089@fe.up.pt</strong> <br><br>
    João Fernando de Sousa Almeida <br><strong> ei10099@fe.up.pt</strong> <br><br>
    Pedro Ricardo Oliveira Fernandes <br><strong> ei11122@fe.up.pt</strong> <br><br>
    Sara Filipa Mendes da Silva <br><strong> ei11096@fe.up.pt</strong> <br><br>
</div>

</body>
</html>