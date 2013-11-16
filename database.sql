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
	taxPayable REAL DEFAULT 0, /* Sum of taxes of all lines */ 
	netTotal REAL DEFAULT 0,   /* Sum of price of all lines w/o tax */ 
	grossTotal REAL DEFAULT 0  /* netTotal + taxPayable */
);

CREATE TABLE InvoiceLine (
	invoiceLineId INTEGER PRIMARY KEY AUTOINCREMENT,
	invoiceId INTEGER REFERENCES Invoice(invoiceId) ON DELETE CASCADE,
	lineNumber INTEGER DEFAULT 0,
	productId INTEGER REFERENCES Product(productId) ON DELETE CASCADE,
	quantity INTEGER NOT NULL,
	creditAmount REAL,
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

CREATE TRIGGER updateInvoiceTotals
AFTER INSERT ON InvoiceLine
FOR EACH ROW
BEGIN
	UPDATE InvoiceLine SET lineNumber = 
	1 + (SELECT max(lineNumber) FROM InvoiceLine WHERE invoiceLine.invoiceId = NEW.invoiceId)
	WHERE invoiceLine.invoiceLineId = NEW.invoiceLineId;

	UPDATE InvoiceLine SET creditAmount = NEW.quantity * (SELECT unitPrice FROM Product WHERE productId = NEW.productId)
	WHERE invoiceLine.invoiceLineId = NEW.invoiceLineId;

	UPDATE Invoice SET taxPayable = 
	taxPayable + (SELECT taxPercentage FROM Tax WHERE taxId = NEW.taxId) * 0.01
	* (SELECT creditAmount FROM InvoiceLine WHERE InvoiceLine.invoiceLineId = NEW.invoiceLineId) 
	WHERE Invoice.invoiceId = NEW.invoiceId; 

	UPDATE Invoice SET netTotal = 
	netTotal + (SELECT creditAmount FROM InvoiceLine WHERE InvoiceLine.invoiceLineId = NEW.invoiceLineId)
	WHERE Invoice.invoiceId = NEW.invoiceId;

	UPDATE Invoice SET grossTotal =
	(SELECT taxPayable FROM Invoice WHERE Invoice.invoiceId = NEW.invoiceId) 
	+ (SELECT netTotal FROM Invoice WHERE Invoice.invoiceId = NEW.invoiceId)
	WHERE Invoice.invoiceId = NEW.invoiceId;
END;

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


INSERT INTO Invoice (invoiceNo, invoiceDate, customerId)
            VALUES ("FT SEQ/1", "2013-09-27", 1);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId)
            VALUES ("FT SEQ/12", "2013-09-29", 2);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId)
            VALUES ("FT SEQ/3", "2013-10-11", 3);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId)
            VALUES ("FT SEQ/14", "2013-09-30", 2);


INSERT INTO Tax(taxType, taxPercentage) VALUES ("IVA", 23.00);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 1, 3, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 2, 1, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (3, 10, 5, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (3, 17, 2, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (4, 18, 1, 1);