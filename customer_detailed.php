<?php
require_once 'bootstrap.php';
require_once './api/authenticationUtilities.php';
$neededPermissions = array('read');
evaluateSessionPermissions($neededPermissions);
?>
<!doctype html>
<html dir="ltr" lang="en" class="no-js">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/ico" href="favicon.ico"/>

    <title>Customer</title>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="customer_detailed.js"></script>

</head>
<body onload="displayCustomer(getParameter(document.location.search).CustomerID); setCustomerID(); setDeleteParameters();">

    <div id="loadingCustomer">
        <span>Loading customer</span><br>
        <img src='ajax-loader.gif' alt='loading' />
    </div>

    <div id="customer" style="display: none; /*Jquery deals with showing the element after everything is loaded */">

        <div class="customerTitle">
            <strong>Customer</strong>
        </div>

        <header id="customerHeader">
            <ul class="customerInfo">
                <li>ID Number: <span id="customerID"></span></li>

                <li>Tax identification: <span id="customerTaxID"></span></li>
            </ul>
        </header>

        <section id="customerDetail">
            <ul class="customerDetail">
                <li>Name:
                    <p id="companyName"></p>
                </li>

                <li>Billing Address:
                    <p id="billingAddress"></p>
                </li>

                <li>Email Address:
                    <p id="emailAddress"></p>
                </li>
            </ul>
        </section>

        <?php
        if(comparePermissions(array('write'))) {
            echo '<div id="editButtons">';
                echo '<form id="edit" method="get" action="./customer_form.php">';
                    echo '<input id="customerIDInput" type="number" name="CustomerID" style="display: none;">';
                    echo '<input type="submit" value="Edit">';
                echo '</form>';

                echo '<form id="delete" method="get" action="./api/deleteFrom.php" onsubmit="return confirm(\'Confirm deletion?\')">';
                    echo '<input id="tableDel" type="text" name="table" style="display: none;">';
                    echo '<input id="fieldDel" type="text" name="field" style="display: none;">';
                    echo '<input id="valueDel" type="text" name="value" style="display: none;">';
                    echo '<input type="submit" value="Delete">';
                echo '</form>';
            echo '</div>';
        }
        ?>
    </div>
</body>

</html>