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

function drawCustomerStructure(customer) {
    $("#customerID").html(customer.CustomerID);
    $("#customerTaxID").html(customer.CustomerTaxID);
    $("#companyName").html(customer.CompanyName);
    $("#billingAddress").html(customer.AddressDetail + "<br>" + customer.PostalCode + " " + customer.CityName + ", " + customer.CountryName);
    $("#emailAddress").html(customer.Email);
}

function displayCustomer(customerID) {

    $.ajax("./api/getCustomer.php?CustomerID=" + customerID, {
        async: false,
        type: "GET",
        data: "",
        success: function(data)
        {
            var customer = JSON.parse(data);

            if (customer.error) {
                document.body.innerHTML = "<p>Error loading customer</p>" + "<p>Code " + customer.error.code + ": " + customer.error.reason + "</p>";
            }
            else {
                for(var field in customer['BillingAddress']){
                    customer[field] = customer['BillingAddress'][field];
                }
                drawCustomerStructure(customer);
            }
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