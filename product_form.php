<?php
session_start();
require_once './api/authenticationUtilities.php';
$neededPermissions = array('write');
evaluateSessionPermissions($neededPermissions);
?>
<!doctype html>
<html dir="ltr" lang="en" class="no-js">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/ico" href="favicon.ico"/>

    <title>Product Form</title>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="form.js"></script>
    <script>
        var productCode = "<?php echo ( isset( $_GET['ProductCode'] ) && $_GET['ProductCode'] != '') ? $_GET['ProductCode'] : '';?>";
    </script>

</head>

<body onload="getProduct(productCode)">

<div id="loadingProduct">
    <span>Loading product</span><br>
    <img src='ajax-loader.gif' alt='loading' />
</div>

<div id="product" style="display: none; /*Jquery deals with showing the element after everything is loaded */">
    <form action="./api/updateProduct.php" method="POST" autocomplete="off">
        <div class="productTitle">
            <strong>Product Form</strong>
        </div>

        <header id="productHeader">
            <ul class="productInfo">
                <li>Product code: <span id="productCode"></span>
                    <input id="productCodeInput" type="number" name="productCode" readonly>
                </li>
            </ul>
        </header>

        <section id="productDetail">
            <ul class="productDetail">
                <li>Description:
                    <p id="productDescription"></p>
                    <input type="text" name="productDescription">
                </li>

                <li>Unit Price:
                    <p id="unitPrice"></p>
                    <input type="number" name="unitPrice">
                </li>

                <li>Unit of measure:
                    <p id="unitOfMeasure"></p>
                    <input type="text" name="unitOfMeasure">
                </li>
            </ul>
        </section>

        <div id="submitButton">
            <input type="submit" value="Submit" onclick="submitForm('product'); return false;">
        </div>
    </form>
</div>

</body>

</html>