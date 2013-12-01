<!doctype html>
<html dir="ltr" lang="en" class="no-js">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/ico" href="favicon.ico"/>

    <title>Customer Form</title>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="form.js"></script>
    <script>
        var customerID = "<?php echo ( isset( $_GET['CustomerID'] ) && $_GET['CustomerID'] != '') ? $_GET['CustomerID'] : '';?>";
    </script>

</head>
<body onload="getCustomer(customerID)" >

<div id="loadingCustomer">
    <span>Loading customer</span><br>
    <img src='ajax-loader.gif' alt='loading' />
</div>

<div id="customer">
    <form action="./api/updateCustomer.php" method="POST" autocomplete="off">

        <div class="customerTitle">
            <strong>Customer Form</strong>
        </div>

        <header id="customerHeader">
            <ul class="customerInfo">
                <li>ID Number: <span id="customerID">
                        <input id="customerIDInput" type="number" name="customerId">
                </span></li>

                <li>Tax identification: <span id="customerTaxID">
                        <input type="number" name="customerTaxId">
                </span></li>
            </ul>
        </header>

        <section id="customerDetail">
            <ul class="customerDetail">
                <li>Name:
                    <p id="companyName">
                        <input type="text" name="companyName">
                    </p>
                </li>

                <li>Billing Address:
                    <p id="billingAddress">
                        <label for="addressDetail">Address</label> <br/>
                        <input type="text" name="addressDetail"> <br/>
                        <label for="cityName">City</label> <br/>
                        <input type="text" name="cityName"> <br/>
                        <label for="countryName">Country</label> <br/>
                        <input type="text" name="countryName"> <br/>
                        <label for="postalCode">Postal Code</label> <br/>
                        <input type="text" name="postalCode">
                    </p>
                </li>

                <li>Email Address:
                    <p id="emailAddress">
                        <input type="email" name="email">
                    </p>
                </li>
            </ul>
        </section>

        <div id="submitButton">
            <input type="submit" value="Submit" onclick="submitForm('customer'); return false;">
        </div>
    </form>
</div>

</body>

</html>