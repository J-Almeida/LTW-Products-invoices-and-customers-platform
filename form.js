function populateForm(data) {
    var $inputs = $('form input');
    $.each(data, function(key, value) {
        $inputs.filter(function() {
            return key == this.name;
        }).val(value);
    });
}

function getProduct(productCode) {

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

function getUser(username) {

    $.ajax("./api/getUser.php?Username=" + username, {
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

    $("#loadingUser").fadeOut(400, function() {
        $("#user").fadeIn('slow', function() {});
    });

    if (username != '') {
        $('#usernameInput').prop('readonly', true);
    }
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
    objectID['user'] = 'Username';

    var objectFields = new Object();
    objectFields['customer'] = 'customerId';
    objectFields['product'] = 'productCode';
    objectFields['invoice'] = 'invoiceNo';
    objectFields['user'] = 'username';

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
            var lineNumber = parseInt(field.match(/\d+/)) - 1;
            var lineField = field.split('.')[1];
            if (!lineArray[lineNumber])
                lineArray[lineNumber] = new Object();
            lineArray[lineNumber][lineField] = invoiceJson[field];
            delete invoiceJson[field];
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