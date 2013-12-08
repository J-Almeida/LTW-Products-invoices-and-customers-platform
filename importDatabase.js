function importDatabase() {
    var otherURL = $('#otherDatabaseURL').val();
    if (otherURL == "") {
        alert('Please enter a valid URL, for example:\ngnomo.fe.up.pt/~ei11XXX/ltw/');
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
            //console.log(table);
            //console.log(IDs[table][index]);

            console.log(otherURL + "/api/get" + table + "?" + tables[table] + "=" + IDs[table][index]);
            $.ajax(otherURL + "/api/get" + table + "?" + tables[table] + "=" + IDs[table][index] , {
                async: false,
                dataType: "json",
                data: "",
                success: function(data){
                    delete data[tables[table]];
                    console.log(data);
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

}