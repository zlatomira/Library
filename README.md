This is an application which has user interface connected with PostgreSQL database.
The interface is very simple:
1. Displaying database data table with books titles and 
authors 
2. Search form which makes a search to the database by an author name. To make a simple search just put an author name in the search filed and the result will show below the search form.The search will work no matter of the encoding.

First open the index file on the browser. That will connect to the database.
To run the script that will fill the database table with the books just open the script.php file in a browser.
If you don't see an error then it is a good sign. After that refresh the index page.

In the application folder you can find a directory called 'books'. It has subfolders and in each subfolder there are xml files. All the xml files contain books with author and title.

The models.php file is making the database connection and working with it.When inseting the books data there I use "ON CONFLICT" method of postgresql with UPDATE option. This means that if a record already existing, is trying to be inserted it, it will not insert new one but just update a field holding the current timestamp.
The insert happens with a prepared statement. It allows commands that will be used repeatedly to be parsed and planned just once, rather than each time they are executed.


The password, the name and the host of the database are saved in env file and not shown in the file it's self for a better security.
