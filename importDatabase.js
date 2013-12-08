var invalidURL = true;
var authenticatedURL = false;

function importDatabase() {
    var otherURL = $('#otherDatabaseURL').val();
    if (otherURL == "") {
        alert('Please enter a valid URL, for example:\ngnomo.fe.up.pt/~ei11XXX/ltw');
    }

    if (otherURL.slice(-1) == "/")
        otherURL.substr(0, otherURL.length - 1);

    // test URL
    $.ajax(otherURL + "/api/searchInvoicesByField.php?op=contains&field=InvoiceNo&value[]= " , {
        async: false,
        dataType: "json",
        data: "",
        success: function(data){
            invalidURL = false;
            if (data.error){
                alert("You are not logged on the specified website.\nPlease log in first and then try to import again.")
                authenticatedURL = false;
            } else
                authenticatedURL = true;
        },
        error: function()
        {
            invalidURL = true;
            alert("The supplied URL is invalid.\nPlease input a website that supports our API.");
        }
    });

    if (invalidURL || !authenticatedURL) return;

    var proceed = confirm("This will delete the current database and import the new one.\nThe process might take a few seconds.\nDo you wish to continue?");

    if (proceed == false) {
        return;
    }

    var ourURL = document.URL;
    ourURL = ourURL.substring(0, ourURL.lastIndexOf('/'));

    var tables = new Object();
    tables.Customer = 'CustomerID';
    tables.Product = 'ProductCode';
    tables.Invoice = 'InvoiceNo';

    var IDs = new Object();
    IDs.Customer = new Array();
    IDs.Product = new Array();
    IDs.Invoice = new Array();

    for(var table in tables) {
        $.ajax(ourURL + "/api/deleteFrom.php?table=" + table , {
            async: false,
            type: "GET",
            data: "",
            success: function(data){},
            error: function(a, b, c)
            {
                console.log(a + ", " + b + ", " + c);
            }
        });

        $.ajax(otherURL + "/api/search" + table + "sByField.php?op=contains&field=" + tables[table] + "&value[]=" , {
            async: false,
            dataType: "json",
            data: "",
            success: function(data){
                for(var index in data){
                    IDs[table].push(data[index][tables[table]]);
                }
            },
            error: function(a, b, c)
            {
                console.log(a + ", " + b + ", " + c);
            }
        });
    }

    for(var table in IDs) {
        for (var index in IDs[table]) {
            $.ajax(otherURL + "/api/get" + table + ".php?" + tables[table] + "=" + IDs[table][index] , {
                async: false,
                dataType: "json",
                data: "",
                success: function(data){
                    delete data[tables[table]];
                    $.ajax(ourURL + "/api/update" + table + ".php"  , {
                        async: false,
                        type: "POST",
                        data: table.toLowerCase() + "=" + JSON.stringify(data),
                        success: function(data){
                            console.log(data);
                        },
                        error: function(a, b, c)
                        {
                            console.log(a + ", " + b + ", " + c);
                        }
                    });

                },
                error: function(a, b, c)
                {
                    console.log(a + ", " + b + ", " + c);
                }
            });
        }
    }

    alert('Database import is complete!');

}