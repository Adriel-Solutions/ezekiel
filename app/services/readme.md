This folder (/services) is meant to contain all the services of the server.
Services are responsible (only, or almost only) for requesting data from the
database. Each Service inherits the base Service class that has generic functions
for 99% use cases (CRUD / Filter / Pagination).
