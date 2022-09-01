This folder (/controllers) is meant to contain all the controllers of the server.
Controllers are responsible for validating user input, call Services to retrieve data
from the Database, and display that data to the end user, either via JSON or via HTML views.
Every Controller inherits a very minimal base Controller class that stores a list of
Services and Adapters for easy usage and self-documenting.
