PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS InvoiceLine;
DROP TABLE IF EXISTS Tax;
DROP TABLE IF EXISTS Permission;
DROP TABLE IF EXISTS Product;
DROP TABLE IF EXISTS Invoice;
DROP TABLE IF EXISTS Supplier;
DROP TABLE IF EXISTS Customer;
DROP TABLE IF EXISTS User;

CREATE TABLE Customer (
	customerId INTEGER PRIMARY KEY AUTOINCREMENT,
	customerTaxId INTEGER UNIQUE NOT NULL,
	companyName TEXT NOT NULL,
	addressDetail TEXT NOT NULL,
	cityName TEXT NOT NULL,
	countryName TEXT NOT NULL,
	postalCode TEXT NOT NULL,
	email TEXT
);

CREATE TABLE Supplier (
	supplierId INTEGER PRIMARY KEY AUTOINCREMENT,
	supplierTaxId INTEGER UNIQUE NOT NULL,
	companyName TEXT NOT NULL,
	addressDetail TEXT NOT NULL,
	cityName TEXT NOT NULL,
	countryName TEXT NOT NULL,
	postalCode TEXT NOT NULL,
	email TEXT
);

CREATE TABLE Product (
	productId INTEGER PRIMARY KEY AUTOINCREMENT,
	productCode INTEGER UNIQUE,
	productDescription TEXT NOT NULL,
	unitPrice REAL NOT NULL,
	unitOfMeasure TEXT NOT NULL
);

CREATE TABLE Invoice (
	invoiceId INTEGER PRIMARY KEY AUTOINCREMENT,
	invoiceNo TEXT UNIQUE NOT NULL,
	invoiceDate DATE NOT NULL,
	customerId INTEGER REFERENCES Customer(customerId) ON DELETE CASCADE,
	supplierId INTEGER REFERENCES Supplier(supplierId) ON DELETE CASCADE,
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
	taxPercentage REAL NOT NULL,
	taxDescription TEXT
);

CREATE TABLE Permission (
	permissionId INTEGER PRIMARY KEY AUTOINCREMENT,
	permissionType TEXT UNIQUE NOT NULL,
	permissionRead INTEGER NOT NULL,
	permissionWrite INTEGER NOT NULL,
	promote INTEGER NOT NULL
);

CREATE TABLE User (
	userId INTEGER PRIMARY KEY AUTOINCREMENT,
	username TEXT UNIQUE NOT NULL,
	name TEXT NOT NULL,
	password TEXT,
	email TEXT UNIQUE NOT NULL,
	permissionId INTEGER REFERENCES Permission(permissionId) ON DELETE CASCADE
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

INSERT INTO User(username, name, password, email, permissionId) VALUES ("MHawk", "Michael Hawk", "supercalifragilisticexpialidocious", "mhawk@hotmail.com", 1);
INSERT INTO User(username, name, password, email, permissionId) VALUES ("Jakim", "Joaquim Esteves", "1234abcd", "jakim@ltw.pt", 1);
INSERT INTO User(username, name, password, email, permissionId) VALUES ("AnaMaria12", "Ana Maria Santos", "abcd1234", "ana@ltw.pt", 2);
INSERT INTO User(username, name, password, email, permissionId) VALUES ("Sususu", "Susana Isabel Barros", "12345678abcdefgh", "flores@ltw.pt", 2);
INSERT INTO User(username, name, password, email, permissionId) VALUES ("LunaSol", "Luis Miguel", "sol974", "solluna@gmail.com", 3);
INSERT INTO User(username, name, password, email, permissionId) VALUES ("HenriqueLuis", "Henrique Luis Pimenta", "1990motocicleta", "henri1990@gmail.com", 3);
INSERT INTO User(username, name, password, email, permissionId) VALUES ("Mikki", "Maria Ines Sousa", "sousaesousa9090", "mari.ines@gmail.com", 3);

INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (001,"Borracha Quenaoapaga",2.50,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (048,"Caneta Reynolds - Azul",1.50,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (049,"Caneta Reynolds - Preta",1.50,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (050,"Caneta Reynolds - Vermelha",1.50,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (051,"Caneta Reynolds - Verde",1.50,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (052,"Caneta Reynolds - Azul Claro",1.50,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (053,"Caneta Reynolds - Roxa",1.50,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (054,"Caneta Reynolds - Amarela",1.52,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (099,"Borracha Seriva - PVC-Free",0.7,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (100,"Borracha Seriva - PVC Included",0.5,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (110,"Caderno Capa Preta",9.0,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (115,"Caderno Capa Branca",15.0,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (116,"Caderno Capa Pokemon",5.0,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (117,"Caneta BIC",1.0,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (101,"Lapis Staedtler HB 2",0.9,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
      VALUES (125, "Carimbos MIEIC", 90.0123, "Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
      VALUES (126, "Camisola FEUP", 450.055, "Un");
INSERT INTO Product(productCode,productDescription,unitPrice,unitOfMeasure)
			VALUES (335,"Camisola FEUP Autografada",300.50,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (330,"Panuelos Renova - 6 Un",1.50,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (339,"Nada",0.10,"Kg");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (340,"Tinteiro Pelican (cor preta) - A melhor tinta para canetas, nao seca",11999.99,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (341,"Tinteiro Pelican (cor azul) - A melhor tinta para canetas, nao seca",99.99,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (342,"Tinteiro Pelican (cor vermelha) - A melhor tinta para canetas, nao seca",11.99,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (343,"Tinteiro Pelican (cor amarela) - A melhor tinta para canetas, nao seca",11.99,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (344,"Tinteiro Pelican (cor roxa) - A melhor tinta para canetas, nao seca",11.99,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (345,"Tinteiro Pelican (cor dourada) - A melhor tinta para canetas, nao seca",11.99,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (346,"Tinteiro Pelican (cor verde) - A melhor tinta para canetas, nao seca",111.99,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (347,"Tinteiro Pelican (cor castanha) - A melhor tinta para canetas, nao seca",11.99,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (348,"Tinteiro Pelican (cor prateada) - A melhor tinta para canetas, nao seca",0.99,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (349,"Tinteiro Pelican (cor laranja) - A melhor tinta para canetas, nao seca",211.99,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (350,"Tinteiro Pelican (cor-de-rosa) - A melhor tinta para canetas, nao seca",11.99,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (351,"Tinteiro Pelican (cor cinzenta) - A melhor tinta para canetas, nao seca",11.99,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (352,"Caneta FEUP - Autografada",98607879.99,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (353,"Caneta FEUP - Sem Autografos",999999.99,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (354,"Saquinho de Compras FEUP - Autografado",98607.99,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (355,"Folhas A4 (250 Un) - Marca AEFEUP, lapis gratis",30.56,"Emb");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (356,"Folhas A5 (250 Un) - Marca AEFEUP, lapis gratis",20.56,"Emb");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (357,"Queijos FEUP",999999.99,"Kg");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (358,"Calculadora Texas TI-84",300.99,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (359,"Calculadora Texas TI-84 Plus",301.00,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (360,"Régua Staples - 15 cm",0.50,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (361,"Régua Staples - 20 cm",0.60,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (362,"Régua Staples - 30 cm",0.70,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (363,"Esquadro Staples - Inquebravel, 45graus, 25cm",1.00,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (364,"Esquadro Staples - Inquebravel, 60graus, 25cm",1.01,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (365,"Transferidor Staples",1.00,"Un");
INSERT INTO Product(productCode, productDescription, unitPrice, unitOfMeasure)
			VALUES (366,"Esquadro Plastico Cristal Liderpapel - 60graus, 25cm",1.05,"Un");



INSERT INTO Customer(customerTaxId, companyName, addressDetail, cityName, countryName, postalCode, email)
            VALUES (1234567, "Feup", "Rua Doutor Roberto Frias", "Porto", "Portugal", "4200-465", "feup@fe.up.pt");

INSERT INTO Customer(customerTaxId, companyName, addressDetail, cityName, countryName, postalCode, email)
            VALUES (7654321, "Flup", "Via Panorâmica", "Porto", "Portugal", "4150-564", "flup@fl.up.pt");

INSERT INTO Customer(customerTaxId, companyName, addressDetail, cityName, countryName, postalCode, email)
            VALUES (9874567, "Hounslow", "Potatos Street", "Londres", "Inglaterra", "ML1 2DA", "houwnslow@hotmail.com");

INSERT INTO Customer(customerTaxId, companyName, addressDetail, cityName, countryName, postalCode, email)
            VALUES (4457812, "Kingston", "30 Leicester Square", "Londres", "Inglaterra", "WC2H 7LA", "kingstone@gmail.com");

INSERT INTO Customer(customerTaxId, companyName, addressDetail, cityName, countryName, postalCode, email)
            VALUES (9875669, "La Tienda", "Sr. D. Alvaro Blanco Ruiz", "Madrid", "Espanha", "28300", "tienda@lojita.com");

INSERT INTO Customer(customerTaxId, companyName, addressDetail, cityName, countryName, postalCode, email)
            VALUES (4565453, "Green Park", "Mary's Street", "Londres", "Inglaterra", "AB3C 3LA", "greensales@gmail.com");

INSERT INTO Customer(customerTaxId, companyName, addressDetail, cityName, countryName, postalCode, email)
            VALUES (3453259, "Ouro e Prata", "Avenida da Liberdade", "Lisboa", "Portugal", "1268-121", "prata@ouro.pt");

INSERT INTO Customer(customerTaxId, companyName, addressDetail, cityName, countryName, postalCode, email)
            VALUES (9574891, "Bolachas Inc.", "Rua Das Flores", "Porto", "Portugal", "4510-145", "cookies@lojaporto.pt");

INSERT INTO Customer(customerTaxId, companyName, addressDetail, cityName, countryName, postalCode, email)
            VALUES (00056010, "Sir Francis Burdett & Son", "St. James's Place", "Londres", "Inglaterra", "SW1 E17", "francis@sir.com");

INSERT INTO Customer(customerTaxId, companyName, addressDetail, cityName, countryName, postalCode, email)
            VALUES (9593205891, "Jaquet-Droz's Automata", "Draughtsman Street", "Londres", "Inglaterra", "SW19 3RQ", "epicdolls@gmail.com");




INSERT INTO Supplier(supplierTaxId, companyName, addressDetail, cityName, countryName, postalCode, email)
            VALUES (4457812, "Kingston", "30 Leicester Square", "Londres", "Inglaterra", "WC2H 7LA", "kingstone@gmail.com");

INSERT INTO Supplier(supplierTaxId, companyName, addressDetail, cityName, countryName, postalCode, email)
            VALUES (4457816, "Kingston Junior", "Mary's Street", "Londres", "Inglaterra", "AB3C 3LA", "kingjunior@gmail.com");

INSERT INTO Supplier(supplierTaxId, companyName, addressDetail, cityName, countryName, postalCode, email)
            VALUES (6767565, "Pague Ja", "Avenida da Liberdade", "Lisboa", "Portugal", "1268-121", "pagueja@hotmail.com");




INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/1", "2013-09-27", 1, 1);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/3", "2013-10-11", 3, 1);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/14", "2013-09-30", 2, 1);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/11", "2013-11-01", 5, 2);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/2", "2013-09-29", 4, 3);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/15", "2013-11-02", 1, 1);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/16", "2013-11-05", 3, 2);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/17", "2013-11-07", 4, 3);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/18", "2013-11-09", 1, 3);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/19", "2013-11-09", 5, 3);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/20", "2013-11-10", 3, 1);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/21", "2013-11-11", 10, 1);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/22", "2013-11-11", 9, 1);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/23", "2013-11-11", 8, 2);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/24", "2013-11-15", 5, 2);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/25", "2013-11-15", 10, 2);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/26", "2013-11-16", 10, 1);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/27", "2013-11-17", 7, 3);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/28", "2013-11-17", 6, 3);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/29", "2013-11-17", 8, 1);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/30", "2013-11-17", 5, 1);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/31", "2013-11-17", 3, 2);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/32", "2013-11-18", 3, 1);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/33", "2013-11-19", 1, 1);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/34", "2013-11-19", 1, 1);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/35", "2013-11-20", 5, 2);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/36", "2013-11-21", 3, 3);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/37", "2013-11-23", 5, 1);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/38", "2013-11-25", 1, 1);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/39", "2013-11-25", 2, 1);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/40", "2013-11-25", 3, 2);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/41", "2013-11-25", 4, 1);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/42", "2013-11-27", 1, 3);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/43", "2013-11-29", 1, 2);

INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
            VALUES ("FT SEQ/44", "2013-11-30", 5, 1);


INSERT INTO Tax(taxType, taxPercentage, taxDescription) VALUES ("IVA 1", 23.00, "Taxa Normal");
INSERT INTO Tax(taxType, taxPercentage, taxDescription) VALUES ("IVA 2", 13.00, "Taxa Intermédia");
INSERT INTO Tax(taxType, taxPercentage, taxDescription) VALUES ("IVA 3",  6.00, "Taxa Reduzida");


INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 1, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 2, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 3, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 4, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 5, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 6, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 7, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 8, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 9, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 10, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 11, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 12, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 13, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 14, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 15, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 16, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 17, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 18, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 19, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 20, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 21, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 22, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 23, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 24, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 25, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 26, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 27, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 28, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 29, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 30, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 31, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 32, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 33, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 34, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 35, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 36, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 37, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 38, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 39, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 40, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 41, 2, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 42, 2, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 43, 2, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 44, 2, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 45, 2, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (1, 46, 1, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (2, 43, 2, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (2, 44, 2, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (2, 45, 2, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (2, 46, 1, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (3, 10, 5, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (3, 17, 2, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (4, 18, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (4, 15, 10, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (4, 10, 2, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (5, 1, 2, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (5, 9, 9, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (5, 11, 2, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (5, 2, 3, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (6, 1, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (6, 2, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (6, 3, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (6, 4, 5, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (6, 5, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (6, 10, 2, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (6, 11, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (6, 12, 7, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (7, 5, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (7, 6, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (7, 7, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (7, 8, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (7, 11, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (7, 9, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (7, 14, 10, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (7, 15, 1, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (8, 1, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (8, 7, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (8, 2, 15, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (9, 1, 1, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (10, 1, 23, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (10, 2, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (10, 3, 10, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (10, 4, 10, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (10, 5, 10, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (10, 10, 45, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (10, 11, 40, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (10, 12, 50, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (11, 13, 5, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (11, 14, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (11, 17, 15, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (12, 18, 1, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (13, 18, 50, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (14, 2, 5, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (14, 10, 3, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (14, 11, 1, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (15, 10, 10, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (16, 16, 7, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (17, 16, 37, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (17, 15, 37, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (17, 14, 37, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (18, 6, 50, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (18, 7, 20, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (18, 8, 20, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (19, 1, 2, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (19, 2, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (19, 3, 2, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (20, 4, 20, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (20, 5, 10, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (20, 6, 20, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (21, 7, 20, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (21, 8, 10, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (21, 9, 20, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (22, 7, 10, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (22, 8, 20, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (22, 9, 10, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (23, 10, 10, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (23, 1, 2, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (24, 2, 2, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (24, 1, 2, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (25, 10, 11, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (25, 11, 11, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (26, 3, 10, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (26, 4, 10, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (26, 5, 10, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (26, 6, 20, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (26, 7, 20, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (26, 8, 20, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (27, 7, 99, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (27, 8, 99, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (28, 5, 20, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (28, 6, 20, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (28, 7, 20, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (29, 7, 99, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (29, 8, 99, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (30, 2, 5, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (31, 1, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (31, 2, 20, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (31, 3, 20, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (31, 4, 20, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (31, 5, 20, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (32, 1, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (32, 2, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (32, 3, 5, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (32, 4, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (32, 5, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (32, 6, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (32, 7, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (32, 8, 5, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (32, 9, 1, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (32, 10, 1, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (33, 3, 9, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (34, 1, 99, 1);

INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (35, 14, 50, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (35, 14, 5, 1);
INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
            VALUES (35, 5, 14, 1);