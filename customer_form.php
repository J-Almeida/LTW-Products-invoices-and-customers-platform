<?php
require_once 'bootstrap.php';
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

    <title>Customer Form</title>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="form.js"></script>
    <script>
        var customerID = "<?php echo ( isset( $_GET['CustomerID'] ) && $_GET['CustomerID'] != '') ? $_GET['CustomerID'] : '';?>";
    </script>

</head>
<body onload="getCustomer(customerID)">

<div id="loadingCustomer">
    <span>Loading customer</span><br>
    <img src='ajax-loader.gif' alt='loading' />
</div>

<div id="customer" style="display: none; /*Jquery deals with showing the element after everything is loaded */">
    <form onsubmit="submitForm('customer'); return false;" data-action="./api/updateCustomer.php" method="POST" autocomplete="off">

        <div class="customerTitle">
            <strong>Customer Form</strong>
        </div>

        <header id="customerHeader">
            <ul class="customerInfo">
                <li>ID Number: <span id="customerID">
                        <input id="customerIDInput" type="number" name="CustomerID" readonly
                               onclick="warnReadOnly($(this))">
                </span></li>

                <li>Tax identification: <span id="customerTaxID">
                        <input type="number" pattern="^[0-9]{1,20}$" name="CustomerTaxID">
                </span></li>
            </ul>
        </header>

        <section id="customerDetail">
            <ul class="customerDetail">
                <li>Name:
                    <p id="companyName">
                        <input type="text" pattern="^[a-zA-Z0-9 \u00C0-\u018F &amp;$%!@,'#.-]{1,50}$" name="CompanyName">
                    </p>
                </li>

                <li>Billing Address:
                    <p id="billingAddress">
                        <label for="addressDetail">Address</label> <br/>
                        <input type="text" pattern="^[a-zA-Z0-9 \u00C0-\u018F &amp;$%!@,'#.-]{1,200}$" name="AddressDetail"> <br/>
                        <label for="cityName">City</label> <br/>
                        <input type="text" pattern="^[a-zA-Z0-9 \u00C0-\u018F &amp;$%!@,'#.-]{1,20}$" name="City"> <br/>
                        <label for="countryName">Country</label> <br/>
                        <select name="CountryID" pattern="^[0-9]{1,20}$">
                            <?php
                            require_once './api/search.php';
                            $search = new ListAllSearch('Country', 'CountryID', array(), array('*'));
                            $countries = $search->getResults();
                            foreach($countries as $country){
                                echo '<option value='.$country['CountryID'].'>';
                                echo $country['CountryName'] . ' - ' . $country['Country'];
                                echo '</option>';
                            }
                            ?>
                        </select><br/>
                        <label for="postalCode">Postal Code</label> <br/>
                        <input type="text" pattern="^[a-zA-Z0-9 \u00C0-\u018F &amp;$%!@,'#.-]{1,20}$" name="PostalCode">
                    </p>
                </li>

                <li>Email Address:
                    <p id="emailAddress">
                        <input type="email" name="Email">
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