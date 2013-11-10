PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS Customer;
DROP TABLE IF EXISTS BillingAddress;
DROP TABLE IF EXISTS City;
DROP TABLE IF EXISTS Country;
DROP TABLE IF EXISTS InvoiceLine;
DROP TABLE IF EXISTS Tax;
DROP TABLE IF EXISTS Permission;
DROP TABLE IF EXISTS Product;
DROP TABLE IF EXISTS Invoice;

CREATE TABLE Customer (
	customerId INTEGER PRIMARY KEY AUTOINCREMENT,
	customerTaxId INTEGER UNIQUE NOT NULL,
	companyName TEXT NOT NULL,
	billingAddressId INTEGER REFERENCES BillingAddress(billingAddressId) ON DELETE CASCADE,
	email TEXT,
	permissionId INTEGER REFERENCES Permission(permissionId) ON DELETE CASCADE
);

CREATE TABLE BillingAddress (
	billingAddressId INTEGER PRIMARY KEY AUTOINCREMENT,
	addressDetail TEXT NOT NULL,
	cityId INTEGER REFERENCES City(cityId) ON DELETE CASCADE,
	countryId INTEGER REFERENCES Country(countryId) ON DELETE CASCADE,
	postalCode TEXT NOT NULL
);

CREATE TABLE City (
	cityId INTEGER PRIMARY KEY AUTOINCREMENT,
	cityName TEXT UNIQUE NOT NULL
);

CREATE TABLE Country (
	countryId INTEGER PRIMARY KEY AUTOINCREMENT,
	countryName TEXT UNIQUE NOT NULL
);

CREATE TABLE Product (
	productId INTEGER PRIMARY KEY AUTOINCREMENT,
	productCode TEXT,
	productDescription TEXT NOT NULL,
	unitPrice REAL NOT NULL,
	unitOfMeasure TEXT NOT NULL
);

CREATE TABLE Invoice (
	invoiceId INTEGER PRIMARY KEY AUTOINCREMENT,
	invoiceNo TEXT UNIQUE NOT NULL,
	invoiceDate DATE NOT NULL,
	customerId INTEGER REFERENCES Customer(customerId) ON DELETE CASCADE,
	/* Document Totals */
	taxPayable REAL NOT NULL, /* Sum of taxes of all lines */ 
	netTotal REAL NOT NULL,   /* Sum of price of all lines w/o tax */ 
	grossTotal REAL NOT NULL /* netTotal + taxPayable */
);

CREATE TABLE InvoiceLine (
	invoiceLineId INTEGER PRIMARY KEY AUTOINCREMENT,
	invoiceId INTEGER REFERENCES Invoice(invoiceId) ON DELETE CASCADE,
	lineNumber INTEGER NOT NULL,
	productId INTEGER REFERENCES Product(productId) ON DELETE CASCADE,
	quantity INTEGER NOT NULL,
	creditAmount REAL NOT NULL,
	taxId INTEGER REFERENCES Tax(taxId) ON DELETE CASCADE
);

CREATE TABLE Tax (
	taxId INTEGER PRIMARY KEY AUTOINCREMENT,
	taxType TEXT UNIQUE NOT NULL,
	taxPercentage REAL NOT NULL
);

CREATE TABLE Permission (
	permissionId INTEGER PRIMARY KEY AUTOINCREMENT,
	permissionType TEXT UNIQUE NOT NULL,
	permissionRead INTEGER NOT NULL,
	permissionWrite INTEGER NOT NULL,
	promote INTEGER NOT NULL
);

INSERT INTO Permission(permissionType, permissionRead, permissionWrite, promote) VALUES ( "admin", 1, 1, 1);
INSERT INTO Permission(permissionType, permissionRead, permissionWrite, promote) VALUES ("editor", 1, 1, 0);
INSERT INTO Permission(permissionType, permissionRead, permissionWrite, promote) VALUES ("reader", 1, 0, 0);

INSERT INTO Country(countryName) VALUES ("Portugal");
INSERT INTO City(cityName) VALUES ("Porto");

INSERT INTO BillingAddress(addressDetail, cityId, countryId, postalCode)
            VALUES ("Rua Doutor Roberto Frias", 1, 1, "4200-465");

INSERT INTO BillingAddress(addressDetail, cityId, countryId, postalCode)
            VALUES ("Via Panorâmica", 1, 1, "4150-564");

INSERT INTO Customer(customerTaxId, companyName, billingAddressId, email, permissionId)
            VALUES (1234567, "Feup", 1, "feup@fe.up.pt", 3);

INSERT INTO Customer(customerTaxId, companyName, billingAddressId, email, permissionId)
            VALUES (7654321, "Flup", 2, "flup@fl.up.pt", 3);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, taxPayable, netTotal, grossTotal)
            VALUES ("FT SEQ/1", "2013-09-27", 1, 165.6, 720, 885.6);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, taxPayable, netTotal, grossTotal)
            VALUES ("FT SEQ/12", "2013-09-29", 2, 0.0, 0.0, 432.5);

INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
            VALUES(125, "Carimbos MIEIC", 90.0, "unidades");

INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
            VALUES(126, "Camisola FEUP", 450.0, "unidades");

INSERT INTO Tax(taxType, taxPercentage) VALUES ("IVA", 23.00);

INSERT INTO InvoiceLine(invoiceId, lineNumber, productId, quantity, creditAmount, taxId)
            VALUES (1, 1, 1, 3, 270.0, 1);

INSERT INTO InvoiceLine(invoiceId, lineNumber, productId, quantity, creditAmount, taxId)
            VALUES (1, 2, 2, 1, 450.0, 1);