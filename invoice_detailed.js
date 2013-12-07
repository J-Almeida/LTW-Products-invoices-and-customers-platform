function getParameter(urlQuery) {
    urlQuery = urlQuery.split("+").join(" ");

    var params = {};
    var tokens;
    var regex = /[?&]?([^=]+)=([^&]*)/g;

    while (tokens = regex.exec(urlQuery)) {
        params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
    }

    return params;
}

function drawCustomerDetails(customerId, content) {
    $.getJSON("./api/getCustomer.php?CustomerID=" + customerId, function(data) {
        var details = data.CompanyName + " (T.ID  " + data.CustomerTaxID +  ")<br>";
        details += data.AddressDetail + "<br>" + data.PostalCode + " " + data.CityName + ", " + data.CountryName;
        content.html(details);
    });
}

function getProductDetails(productCode) {
    $.ajaxSetup({
        async: false
    });
    var details = "";
    $.getJSON("./api/getProduct.php?ProductCode=" + productCode, function(data) {
        details = data;
    });

    $.ajaxSetup({
        async: true
    });
    return details;
}

function drawInvoiceStructure(invoiceData) {
    var json = JSON.parse(invoiceData);

    $("#invoiceNo").html(json.InvoiceNo);
    $("#invoiceDate").html(json.InvoiceDate);
    drawCustomerDetails(json.CustomerID, $("#invoiceToName"));

    $("#invoiceCustomer").click(function() {
        window.open("customer_detailed.php?CustomerID=" + json.CustomerID);
    });

    var lines = "";
    for(result in json.Line) {
        var object = json.Line[result];
        var productData;
        lines += "<tr id=";
        lines += object.LineNumber;
        lines += ">";
        for(var field in object) {
            if(field == 'TaxID'){
                continue;
            }
            if(field != "LineNumber") {
                if(field == "Tax") {
                    for(taxField in object[field]) {
                        lines += "<td>";
                        lines += object[field][taxField];
                        lines += "</td>";
                    }
                }
                else if(field == "ProductCode") {
                    productData = getProductDetails(object[field]);
                    lines += "<td>";
                    lines += "[";
                    lines += productData.ProductCode;
                    lines += "] ";
                    lines += productData.ProductDescription;
                    lines += "</td>";
                }
                else {
                    lines += "<td>";
                    if(field == "UnitPrice" || field == "CreditAmount")
                        lines += "€ "; 
                    lines += object[field];
                    lines += "</td>";
                }
            }

            if(field == "Quantity") {
                lines += "<td>";
                lines += productData.UnitOfMeasure;
                lines += "</td>";
            }
        }
        lines += "</tr>";
    }

    $("#invoiceLines").html(lines);

//Load onClick events for table rows
var rowProd = {};
for(result in json.Line) {
    var object = json.Line[result];
    var rowID = "#" + object.LineNumber;
    var pCode = object.ProductCode;
    rowProd[object.LineNumber] = pCode;
    $(rowID).click(function() {
        window.open("product_detailed.php?ProductCode=" + rowProd[this.id]);
    });
}

var documentTotals = json.DocumentTotals;

$("#taxPay").html("€ " + documentTotals.TaxPayable);
$("#netTotal").html("€ " + documentTotals.NetTotal);
$("#grossTotal").html("€ " + documentTotals.GrossTotal);

$("#invoiceFooter").html(json.InvoiceNo + "     |     " + json.InvoiceDate);
}

function displayInvoice(invoiceNo) {
    
    $.ajax("./api/getInvoice.php?InvoiceNo=" + invoiceNo, {
        async: false,
        type: "GET",
        data: "",
        success: function(data)
        {
            drawInvoiceStructure(data);
        },
        error: function(a, b, c)
        {
            console.log(a + ", " + b + ", " + c);
        }
    })

    $("#loadingInvoice").fadeOut(400, function() {
        $("#invoice").fadeIn('slow', function() {});
    });
}

function setInvoiceNo() {
    var invoiceNo = getParameter(document.location.search).InvoiceNo;
    $("#invoiceNoInput").val(invoiceNo);
}

function setDeleteParameters() {
    var invoiceNo = getParameter(document.location.search).InvoiceNo;
    $("#tableDel").val('Invoice');
    $("#fieldDel").val('invoiceNo');
    $("#valueDel").val(invoiceNo);
}