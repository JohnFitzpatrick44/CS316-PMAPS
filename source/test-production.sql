-- Get all comments

SELECT p.name, c.text, c.type, c.timestamp
FROM Person as p, Comment as c
WHERE p.pid = c.pid AND c.validity = 'true';


-- Get comments from user Shayna

SELECT p.name, c.text, c.type, c.timestamp
FROM Person as p, Comment as c
WHERE p.pid = c.pid AND c.validity = 'true' AND p.name = 'Shayna';


-- Get comments about water

SELECT p.name, c.text, c.type, c.timestamp
FROM Person as p, Comment as c
WHERE p.pid = c.pid AND c.validity = 'true' AND c.type='water';


-- Get comments relevant to trips in march

SELECT p.name, c.text, c.type, c.timestamp
FROM Person as p, Comment as c, RelevantFor as r
WHERE p.pid = c.pid AND c.validity = 'true' AND r.cid = c.cid AND r.trip = 'march';


-- Find number of comments left by Shayna

SELECT count(*) FROM Comment as c, Person as p WHERE c.pid=p.pid AND p.name='Shayna';


-- Find the longitudes and lattitudes of all comments about water

SELECT p.longitude, p.lattitude
FROM Comment as c, Place as p
WHERE p.lid=c.lid AND c.validity='true' AND c.type='water';


-- Update queries are still being developed
