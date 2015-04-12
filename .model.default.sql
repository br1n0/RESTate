PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
CREATE TABLE test
(
id INTEGER null PRIMARY KEY AUTOINCREMENT,
iTest int NOT NULL,
sTest text NOT NULL,
dTest NOT NULL
);
INSERT INTO "test" VALUES(1,1,'marco','testo');
INSERT INTO "test" VALUES(2,'a','marco','testo');
INSERT INTO "test" VALUES(3,9,'pippo','pluto');
INSERT INTO "test" VALUES(4,4,'daglas','adams');
INSERT INTO "test" VALUES(5,6,'isaac','asimov');
INSERT INTO "test" VALUES(6,7,'konrad','ppp');
INSERT INTO "test" VALUES(7,7,'fabio','volo');
CREATE TABLE sample1
(
id INTEGER null PRIMARY KEY AUTOINCREMENT,
iTest int NOT NULL,
sTest text NOT NULL,
dTest NOT NULL
);
INSERT INTO "sample1" VALUES(5,7,'81','9');
INSERT INTO "sample1" VALUES(9,8,'7','6');
INSERT INTO "sample1" VALUES(91,'','','');
DELETE FROM sqlite_sequence;
INSERT INTO "sqlite_sequence" VALUES('test',7);
INSERT INTO "sqlite_sequence" VALUES('test',7);
INSERT INTO "sqlite_sequence" VALUES('sample1',91);
COMMIT;
