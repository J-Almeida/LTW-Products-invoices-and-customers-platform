function populateForm(data) {
    var $inputs = $('form input');
    $.each(data, function(key, value) {
        $inputs.filter(function() {
            return key == this.name;
        }).val(value);
    });
}

function getProduct(productCode) {
    $("#product").hide();

    $.ajax("./api/getProduct.php?ProductCode=" + productCode, {
        type: "GET",
        data: "",
        success: function(data)
        {
            populateForm(JSON.parse(data));
        },
        error: function(a, b, c)
        {
            console.log(a + ", " + b + ", " + c);
        }
    })

    $("#loadingProduct").fadeOut(400, function() {
        $("#product").fadeIn('slow', function() {});
    });

    /*
    // make the ID field read only
    if (productCode != '') {
        $('#productCodeInput').prop('readonly', true);
    }
    */
}

function getCustomer(customerID) {
    $("#customer").hide();

    $.ajax("./api/getCustomer.php?CustomerID=" + customerID, {
        type: "GET",
        data: "",
        success: function(data)
        {
            populateForm(JSON.parse(data));
        },
        error: function(a, b, c)
        {
            console.log(a + ", " + b + ", " + c);
        }
    })

    $("#loadingCustomer").fadeOut(400, function() {
        $("#customer").fadeIn('slow', function() {});
    });

    /*
    if (customerID != '') {
        $('#customerIDInput').prop('readonly', true);
    }
    */
}

function getInvoice(invoiceNo) {
    $("#invoice").hide();

    $.ajax("./api/getInvoice.php?InvoiceNo=" + invoiceNo, {
        async: false,
        type: "GET",
        data: "",
        success: function(data)
        {
            populateForm(JSON.parse(data));
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

function submitForm(objectName) {

    var json = getFormData($('form'));

    if (objectName == 'invoice') {
        json = parseLines(json);
    }

    var objectID = new Object();
    objectID['customer'] = 'CustomerID';
    objectID['product'] = 'ProductCode';
    objectID['invoice'] = 'InvoiceNo';

    var objectFields = new Object();
    objectFields['customer'] = 'customerId';
    objectFields['product'] = 'productCode';
    objectFields['invoice'] = 'invoiceNo';

    // clean empty fields from form
    for(var field in objectFields) {
        if (json[objectFields[field]] == "") {
            delete json[objectFields[field]];
        }
    }

    var form = JSON.stringify(json);

    var information = objectName;
    information += "=";
    information += form;

    console.log(information);

    $.ajax($('form').attr('action'), {
        type: "POST",
        data: information,
        success: function(data)
        {
            var answer = JSON.parse(data);
            if (answer.error) {
                alert('Code: ' + answer.error.code + "\n" + answer.error.reason);
            } else {
                window.location = './' + objectName +'_detailed.html?' + objectID[objectName] + '=' + answer[objectFields[objectName]];
            }
        },
        error: function(a, b, c)
        {
            console.log(a + ", " + b + ", " + c);
        }
    })

}

function parseLines(invoiceJson) {
    var lineArray = new Array();
    for(var field in invoiceJson) {
        if (field.indexOf("line") > -1) {
            var lineField = field.split('.')[1];
            var line = new Object();
            line[lineField] = invoiceJson[field];
            delete invoiceJson[field];
            lineArray.push(line);
        }
    }
    invoiceJson['line'] = lineArray;
    return invoiceJson;
}

function getFormData($form){
    var unIndexedArray = $form.serializeArray();
    var indexedArray = {};

    $.map(unIndexedArray, function(n, i){
        indexedArray[n['name']] = n['value'];
    });

    return indexedArray;
}