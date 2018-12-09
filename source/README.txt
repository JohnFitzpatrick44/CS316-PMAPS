HOW TO CREATE THE PRODUCTION DATABASE

1) Acquire the csv file containing the dataset (included). It should be renamed to 'base.csv' if it isn't named that already.
2) Create a PostgreSQL database and run 'create.sql' to set up the tables.
3) Run the python script 'loaddb.py' in the same directory as base.csv.
4) This should generate a 'load.sql' file, run it.
5) The production database should be all set up.
 
