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
    <link rel="stylesheet" href="invoiceStyle.css">
    <link rel="icon" type="image/ico" href="favicon.ico"/>
    
    <title>Invoice</title>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="invoice_detailed.js"></script>

</head>
<body onload="displayInvoice(getParameter(document.location.search).InvoiceNo); setInvoiceNo(); setDeleteParameters();">

    <div id="loadingInvoice">
        <span>Loading invoice</span><br>
        <img src='ajax-loader.gif' alt='loading' />
    </div>

    <div id="invoice" style="display: none; /*Jquery deals with showing the element after everything is loaded */">

        <div class="invoiceTitle">
            <strong>Invoice</strong>
        </div>

        <header id="invoiceHeader">
            <ul class="invoiceInfo">
                <li>Invoice no: <span id="invoiceNo"></span></li>

                <li>Invoice date: <span id="invoiceDate"></span></li>
            </ul>
        </header>

        <section id="invoiceConcerned">
            <div class="invoiceCustomer" id="invoiceCustomer">
                <h2>Invoice To:</h2>
                <div id="invoiceTo" class="concernedInfo">
                    <div class="concerned" id="invoiceToName"></div>
                </div>
            </div>

            <div class="invoiceCompany">
                <h2>Invoice From:</h2>
                <div id="invoiceFrom" class="concernedInfo">
                    <div class="concerned" id="invoiceFromName">
                        Totally Legit Sellers, Inc. (T. ID 1539920)</br>
                        Travessa Sta. dos Ludibriados, 117</br>
                        1337-666 Porto, Portugal</br>
                    </div>
                </div>
            </div>
        </section>

        <section class="invoiceFinances">
            <div class="invoiceLines">
                <table>
                    <caption>Invoice details:</caption>
                    <thead>
                        <tr>
                            <th>[code] Product</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Unit price</th>
                            <th>Credit amount</th>
                            <th>Tax type</th>
                            <th>Tax Percentage</th>
                        </tr>
                    </thead>
                    <tbody id="invoiceLines">

                    </tbody>
                </table>
            </div><br><br>


            <div class="invoiceTotals">
                <table>
                    <caption>Totals: </caption>
                    <tbody>
                        <tr>
                            <th>Payable tax:</th>
                            <td id="taxPay"></td>
                        </tr>

                        <tr>
                            <th>Net total:</th>
                            <td id="netTotal"></td>
                        </tr>

                        <tr>
                            <th>Gross total:</th>
                            <td id="grossTotal"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </section>

        <?php
        if(comparePermissions(array('write'))) {
            echo '<div id="editButtons">';
                echo '<form id="edit" method="get" action="./invoice_form.php">';
                    echo '<input id="invoiceNoInput" type="text" name="InvoiceNo" style="display: none;">';
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

    <br><br><div id="invoiceFooter"></div>
</body>

</html>