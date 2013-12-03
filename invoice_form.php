<!doctype html>
<html dir="ltr" lang="en" class="no-js">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" href="invoiceStyle.css">
    <link rel="icon" type="image/ico" href="favicon.ico"/>

    <title>Invoice</title>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="form.js"></script>
    <script src="invoice_form.js"></script>
    <script>
        var invoiceNo = "<?php echo ( isset( $_GET['InvoiceNo'] ) && $_GET['InvoiceNo'] != '') ? $_GET['InvoiceNo'] : '';?>";
    </script>

    <?php include_once('api/utilities.php'); ?>
</head>
<body onload="getInvoice(invoiceNo); updateAllLines(); updateTotals();">

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
                        <th>Unit price</th>
                        <th>Credit amount</th>
                        <th>Tax type</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="invoiceLines">
                    <tr class="invoiceLine" id="1">
                        <th>
                            <select class="productCode" name="line[1].productCode" onchange="updateLine($(this));">
                                <?php
                                $searchUrl = searchAPIUrl('Product', 'listAll', 'productCode', 'invoice_form');
                                $products = json_decode(file_get_contents($searchUrl), true);
                                foreach($products as $product){
                                    echo '<option value='.$product['productCode'].' data-unitprice="'.$product['unitPrice'].'">';
                                    echo '['. $product['productCode'] . '] ' . $product['productDescription'];
                                    echo '</option>';
                                }
                                ?>
                            </select>
                        </th>
                        <th>
                            <input class="quantity" type="number" name="line[1].quantity" value="1" onchange="updateLine($(this));">
                        </th>
                        <th>
                            <input class="unitPrice" type="number" name="line[1].unitPrice" value="1" readonly>
                        </th>
                        <th>
                            <input class="creditAmount" type="number" name="line[1].creditAmount" value="1" readonly>
                        </th>
                        <th>
                            <select class="taxId" name="line[1].taxId" onchange="updateTotals();">
                                <?php
                                $parameters['operation'] = 'listAll';
                                $parameters['field'] = 'taxId';
                                $parameters['table'] = 'Tax';
                                $parameters['rows'] = array('taxId', 'taxType', 'taxPercentage');
                                $taxes = executeSearch($parameters);
                                foreach($taxes as $tax){
                                    echo '<option value='.$tax['taxId'].' data-taxpercentage="'.$tax['taxPercentage'].'">';
                                    echo $tax['taxType'] . ' - ' . $tax['taxPercentage'] . '%';
                                    echo '</option>';
                                }
                                ?>
                            </select>
                        </th>
                        <th>
                            <button class="removeRow" onclick="return false;">
                                Remove
                            </button>
                        </th>
                    </tr>
                    </tbody>
                </table>
            </div><br><br>

            <button class="addRow" onclick="addRow(); return false;">
                Add line
            </button>

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