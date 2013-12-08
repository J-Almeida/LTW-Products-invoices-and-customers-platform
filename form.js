function populateForm(data) {
    var $inputs = $('form input');
    $.each(data, function(key, value) {
        $inputs.filter(function() {
            return key == this.name;
        }).val(value);
    });

    var $selects = $('form select');
    $.each(data, function(key, value) {
        $selects.filter(function() {
            return key == this.name;
        }).val(value);
    });

    if ( data.InvoiceNo ) {
        loadInvoiceLines(data);
    }
}

function loadInvoiceLines(invoiceData) {
    var lines = invoiceData['Line'];
    for (var index = 1; index < lines.length; ++index) {
        addRow();
    }
    var jsonLines = new Object();
    for(var line in lines) {
        var lineNumber = 'Line[' + lines[line].LineNumber + ']';
        for(var field in lines[line]) {
            jsonLines[lineNumber + '.' + field] = lines[line][field];
        }
    }
    populateForm(jsonLines);
    updateAllLines();
}

function getProduct(productCode) {

    $.ajax("./api/getProduct.php?ProductCode=" + productCode, {
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
        async: false,
        type: "GET",
        data: "",
        success: function(data)
        {
            var customer = JSON.parse(data);
            for(var field in customer['BillingAddress']){
                customer[field] = customer['BillingAddress'][field];
            }
            populateForm(customer);
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
    objectFields['customer'] = 'CustomerID';
    objectFields['product'] = 'ProductCode';
    objectFields['invoice'] = 'InvoiceNo';
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

    $.ajax($('form').attr('data-action'), {
        async: false,
        type: "POST",
        data: information,
        success: function(data)
        {
            var answer = JSON.parse(data);
            if (answer.error) {
                alert('Code: ' + answer.error.code + "\n" + answer.error.reason);
            } else {
                window.location = './' + objectName +'_detailed.php?' + objectID[objectName] + '=' + answer[objectFields[objectName]];
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
        if (field.indexOf("Line") > -1) {
            var lineNumber = parseInt(field.match(/\d+/)) - 1;
            var lineField = field.split('.')[1];
            if (!lineArray[lineNumber])
                lineArray[lineNumber] = new Object();
            lineArray[lineNumber][lineField] = invoiceJson[field];
            delete invoiceJson[field];
        }
    }
    // clear the lines that were deleted
    lineArray.clean(null);
    invoiceJson['Line'] = lineArray;
    return invoiceJson;
}

Array.prototype.clean = function(deleteValue) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == deleteValue) {
            this.splice(i, 1);
            i--;
        }
    }
    return this;
};

function getFormData($form){
    var unIndexedArray = $form.serializeArray();
    var indexedArray = {};

    $.map(unIndexedArray, function(n, i){
        indexedArray[n['name']] = n['value'];
    });

    return indexedArray;
}

function warnReadOnly(input){
    var value = input.val();
    if (value == "") {
        alert("The database will automatically handle this field on insertion.");
    } else {
        alert("The " + input.attr('name') + " cannot be modified. It is a unique reference and is calculated on insertion in the database.");
    }
}