SELECT p.name, c.text, c.type, c.timestamp
FROM Person as p, Comment as c
WHERE p.pid = c.pid AND c.validity = 'true';
