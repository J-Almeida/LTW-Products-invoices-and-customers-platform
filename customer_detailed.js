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

    $("#customerID").html(json.CustomerID);
    $("#customerTaxID").html(json.CustomerTaxID);
    $("#companyName").html(json.CompanyName);
    $("#billingAddress").html(json.AddressDetail + "<br>" + json.PostalCode + " " + json.CityName + ", " + json.CountryName);
    $("#emailAddress").html(json.Email);
}

function displayCustomer(customerID) {

    $.ajax("./api/getCustomer.php?CustomerID=" + customerID, {
        async: false,
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

function setCustomerID() {
    var customerID = getParameter(document.location.search).CustomerID;
    $("#customerIDInput").val(customerID);
}

function setDeleteParameters() {
    var customerID = getParameter(document.location.search).CustomerID;
    $("#tableDel").val('Customer');
    $("#fieldDel").val('CustomerID');
    $("#valueDel").val(customerID);
}