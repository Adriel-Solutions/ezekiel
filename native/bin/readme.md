This folder (/bin) was initially introduced to mimic the Express-like project architecture,
but so far we can say that this is the entrypoint of the server. Every single request,
if served by PHP, goes through this files, that calls "$app->dispatch()", introduced
by a require of the index.php at the root of the project, which serves as a configuration
enrtypoint itself.
