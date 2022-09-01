This folder is meant to contain what we tend to call "external Services".
In other words, this folder contains a one-per-thirdparty file, that itself
contains code specific to the thirdparty's API / SDK.
For instance, this is where we would put interactions with the Shopify API, or
with the Stripe API, or even Sending Emails.
We wanted to make a difference between the "internal Services" that are responsible
for interacting with the database, and the "external Services" that are responsible
for interacting with remote players.
Note of implementation : external Services are built just like internal Services, so
the controller calls them, but they " should not " communicate with each other directly
