<!doctype html>
<html dir="ltr" lang="en" class="no-js">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" href="invoiceStyle.css">
    <link rel="icon" type="image/ico" href="favicon.ico"/>

    <title>Invoice</title>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="form.js"></script>
    <script>
        var invoiceNo = "<?php echo ( isset( $_GET['InvoiceNo'] ) && $_GET['InvoiceNo'] != '') ? $_GET['InvoiceNo'] : '';?>";
    </script>

    <?php include_once('api/utilities.php'); ?>
</head>
<body onload="getInvoice(invoiceNo)">

<div id="loadingInvoice">
    <span>Loading invoice</span><br>
    <img src='ajax-loader.gif' alt='loading' />
</div>

<div id="invoice">
    <form id="invoiceForm" action="./api/updateInvoice.php" method="POST" autocomplete="off">

        <div class="invoiceTitle">
            <strong>Invoice</strong>
        </div>

        <header id="invoiceHeader">
            <ul class="invoiceInfo">
                <li>Invoice no: <span id="invoiceNo">
                        <input id="invoiceNoInput" type="text" name="invoiceNo" readonly>
                </span></li>

                <li>Invoice date: <span id="invoiceDate">
                        <input type="date" name="invoiceDate">
                </span></li>
            </ul>
        </header>

        <section id="invoiceConcerned">
            <div class="invoiceCustomer" id="invoiceCustomer">
                <h2>Invoice To:</h2>
                <div id="invoiceTo" class="concernedInfo">
                    <select name="customerId">
                        <?php
                        $searchCustomersUrl = searchAPIUrl('Customer', 'listAll', 'customerId', 'invoice_form');
                        $customers = json_decode(file_get_contents($searchCustomersUrl), true);
                        foreach($customers as $customer){
                            echo '<option value='.$customer['customerId'].'>';
                            echo $customer['companyName'] . ' - Tax ID ' . $customer['customerTaxId'];
                            echo '</option>';
                        }
                        ?>
                    </select>
                    <div class="concerned" id="invoiceToName"></div>
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
                    <tr>
                        <th>
                        <select name="line[0].productCode">
                            <option value="2">2</option>
                        </select>
                        </th>
                    </tr>
                    <tr>
                        <th>
                            <select name="line[1].productCode">
                                <option value="3">3</option>
                            </select>
                        </th>
                    </tr>
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

        <div id="submitButton">
            <input type="submit" value="Submit" onclick="submitForm('invoice'); return false;">
        </div>
    </form>
</div>

<br><br><div id="invoiceFooter"></div>
</body>

</html>