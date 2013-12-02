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

    if (productCode != '') {
        $('#productCodeInput').prop('readonly', true);
    }
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

    if (customerID != '') {
        $('#customerIDInput').prop('readonly', true);
    }
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

    var form = JSON.stringify(getFormData($('form')));

    var information = objectName;
    information += "=";
    information += form;

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

function getFormData($form){
    var unIndexedArray = $form.serializeArray();
    var indexedArray = {};

    $.map(unIndexedArray, function(n, i){
        indexedArray[n['name']] = n['value'];
    });

    return indexedArray;
}