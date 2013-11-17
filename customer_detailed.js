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

function drawCustomerStructure(customerData) {
    var json = JSON.parse(customerData);

    $("#customerID").html(json.customerId);
    $("#customerTaxID").html(json.customerTaxId);
    $("#companyName").html(json.companyName);
    $("#billingAddress").html(json.addressDetail + "<br>" + json.postalCode + " " + json.cityName + ", " + json.countryName);
    $("#emailAddress").html(json.email);
}

function displayCustomer(customerID) {
    $("#customer").hide();

    $.ajax("./api/getCustomer.php?CustomerID=" + customerID, {
        type: "GET",
        data: "",
        success: function(data)
        {
            drawCustomerStructure(data);
        },
        error: function(a, b, c)
        {
            console.log(a + ", " + b + ", " + c);
        }
    })

    $("#loadingCustomer").fadeOut(400, function() {
        $("#customer").fadeIn('slow', function() {});
    });
}