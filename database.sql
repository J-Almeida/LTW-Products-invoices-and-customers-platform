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
INSERT INTO Country(countryName) VALUES ("Espanha");
INSERT INTO Country(countryName) VALUES ("Inglaterra");

INSERT INTO City(cityName) VALUES ("Porto");
INSERT INTO City(cityName) VALUES ("Madrid");
INSERT INTO City(cityName) VALUES ("Londres");
INSERT INTO City(cityName) VALUES ("Lisboa");

INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
            VALUES(125, "Carimbos MIEIC", 90.0, "unidades");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
            VALUES(126, "Camisola FEUP", 450.0, "unidades");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (110,"Caderno Capa Preta",9.0,"unidades");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (115,"Caderno Capa Branca",15.0,"unidades");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (116,"Caderno Capa Pokemon",5.0,"unidades");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (117,"Caneta BIC",1.0,"unidades");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (099,"Borracha Seriva - PVC-Free",0.7,"unidades");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (100,"Borracha Seriva - PVC Included",0.5,"unidades");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (101,"Lapis Staedtler HB 2",0.9,"unidades");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (048,"Caneta Reynolds - Azul",1.50,"unidades");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (049,"Caneta Reynolds - Preta",1.50,"unidades");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (050,"Caneta Reynolds - Vermelha",1.50,"unidades");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (051,"Caneta Reynolds - Verde",1.50,"unidades");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (052,"Caneta Reynolds - Azul Claro",1.50,"unidades");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (053,"Caneta Reynolds - Roxa",1.50,"unidades");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (054,"Caneta Reynolds - Amarela",1.52,"unidades");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (001,"Borracha Quenaoapaga",2.50,"unidades");
INSERT INTO Product(productCode,productDescription,unitPrice,unitOfMeasure)
			VALUES (335,"Camisola FEUP Autografada",300.50,"unidades");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (330,"Panuelos Renova - 6 unidades",1.50,"unidades");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (339,"Nada",0.10,"kilo");


INSERT INTO BillingAddress(addressDetail, cityId, countryId, postalCode)
            VALUES ("Rua Doutor Roberto Frias", 1, 1, "4200-465");

INSERT INTO BillingAddress(addressDetail, cityId, countryId, postalCode)
            VALUES ("Via Panorâmica", 1, 1, "4150-564");

INSERT INTO BillingAddress(addressDetail, cityId, countryId, postalCode)
            VALUES ("Rua Das Flores", 1, 1, "4510-145");

INSERT INTO BillingAddress(addressDetail, cityId, countryId, postalCode)
            VALUES ("Sr. D. Alvaro Blanco Ruiz", 2, 2, "28300");

INSERT INTO BillingAddress(addressDetail, cityId, countryId, postalCode)
            VALUES ("Potatos Street", 3, 3, "ML1 2DA");

INSERT INTO BillingAddress(addressDetail, cityId, countryId, postalCode)
            VALUES (" 30 Leicester Square", 3, 3, "WC2H 7LA");



INSERT INTO Customer(customerTaxId, companyName, billingAddressId, email, permissionId)
            VALUES (1234567, "Feup", 1, "feup@fe.up.pt", 3);

INSERT INTO Customer(customerTaxId, companyName, billingAddressId, email, permissionId)
            VALUES (7654321, "Flup", 2, "flup@fl.up.pt", 3);

INSERT INTO Customer(customerTaxId, companyName, billingAddressId, email, permissionId)
            VALUES (9874567, "Hounslow", 5, "houwnslow@hotmail.com", 3);

INSERT INTO Customer(customerTaxId, companyName, billingAddressId, email, permissionId)
            VALUES (4457812, "Kingston", 6, "kingstone@gmail.com", 3);

INSERT INTO Customer(customerTaxId, companyName, billingAddressId, email, permissionId)
            VALUES (9875669, "La Tienda", 4, "tienda@lojita.com", 3);


INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, taxPayable, netTotal, grossTotal)
            VALUES ("FT SEQ/1", "2013-09-27", 1, 165.6, 720, 885.6);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, taxPayable, netTotal, grossTotal)
            VALUES ("FT SEQ/12", "2013-09-29", 2, 0.0, 0.0, 432.5);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, taxPayable, netTotal, grossTotal)
            VALUES ("FT SEQ/3", "2013-10-11", 3, 3.25, 12.5, 15.75);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, taxPayable, netTotal, grossTotal)
            VALUES ("FT SEQ/14", "2013-09-30", 2, 78.13, 300.50, 378.63);


INSERT INTO Tax(taxType, taxPercentage) VALUES ("IVA", 23.00);

INSERT INTO InvoiceLine(invoiceId, lineNumber, productId, quantity, creditAmount, taxId)
            VALUES (1, 1, 1, 3, 270.0, 1);

INSERT INTO InvoiceLine(invoiceId, lineNumber, productId, quantity, creditAmount, taxId)
            VALUES (1, 2, 2, 1, 450.0, 1);

INSERT INTO InvoiceLine(invoiceId, lineNumber, productId, quantity, creditAmount, taxId)
            VALUES (3, 1, 10, 5, 7.5, 1);

INSERT INTO InvoiceLine(invoiceId, lineNumber, productId, quantity, creditAmount, taxId)
            VALUES (3, 2, 17, 2, 5, 1);

INSERT INTO InvoiceLine(invoiceId, lineNumber, productId, quantity, creditAmount, taxId)
            VALUES (4, 1, 18, 1, 300.50, 1);