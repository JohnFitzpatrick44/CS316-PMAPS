CREATE TABLE Person
(
 pid int NOT NULL,
 name VARCHAR(256) NOT NULL,
 year DECIMAL(4,0),
 email VARCHAR(256) UNIQUE,
 PRIMARY KEY (pid)
);

CREATE TABLE Place 
(
 lid int NOT NULL,
 longitude DECIMAL(19,16) NOT NULL,
 lattitude DECIMAL(19,16) NOT NULL,
 pid int NOT NULL,
 PRIMARY KEY (lid),
 FOREIGN KEY (pid) REFERENCES Person (pid)
);


CREATE TABLE Comment
(
 cid int NOT NULL,
 text VARCHAR(2048),
 type VARCHAR(256) DEFAULT 'general',
 timestamp timestamp DEFAULT CURRENT_TIMESTAMP,
 validity CHAR(5) DEFAULT 'true',
 pid int NOT NULL,
 lid int NOT NULL,
 PRIMARY KEY (cid),
 FOREIGN KEY (pid) REFERENCES Person (pid),
 FOREIGN KEY (lid) REFERENCES Place (lid),
 CHECK (type = 'general' OR type = 'campsite' OR type = 'water' OR type = 'vam' OR type = 'tip' OR type = 'safety' OR type = 'solos'), -- Change types - new table? set types?
 CHECK (validity = 'true' OR validity = 'false')
);

CREATE TABLE RelevantFor
(
 cid int NOT NULL,
 trip CHAR(7) NOT NULL, -- add year?
 PRIMARY KEY (cid, trip),
 FOREIGN KEY (cid) REFERENCES Comment (cid),
 CHECK (trip = 'unknown' OR trip = 'august' OR trip = 'march' OR trip = 'step')
);



--Route ( lid, length, blaze, email )
CREATE TABLE Route (
 rid int NOT NULL,
 length DECIMAL (4,1),
 blaze VARCHAR(256),
 pid int NOT NULL,
 PRIMARY KEY (rid),
 FOREIGN KEY (pid) REFERENCES Person (pid)
);

CREATE TABLE Contains (
 rid int NOT NULL,
 lid int NOT NULL,
 PRIMARY KEY (rid, lid),
 FOREIGN KEY (rid) REFERENCES Route (rid),
 FOREIGN KEY (lid) REFERENCES Place (lid)
);
