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

    if (productCode != '') {
        $('#productCodeInput').prop('readonly', true);
    }
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

    if (customerID != '') {
        $('#customerIDInput').prop('readonly', true);
    }
}

String.prototype.firstLetterToLower = function() {
    return this.charAt(0).toLowerCase() + this.slice(1);
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

    $.ajax($('form').attr('action'), {
        type: "POST",
        data: information,
        success: function(data)
        {
            var answer = JSON.parse(data);
            if (answer.error) {
                alert('Code: ' + answer.error.code + "\n" + answer.error.reason);
            } else {
                //window.location =
                console.log(answer);
                console.log(objectID[objectName].firstLetterToLower());
                    alert('./' + objectName +'_detailed.html?' + objectID[objectName] + '=' + answer[objectID[objectName]]);
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