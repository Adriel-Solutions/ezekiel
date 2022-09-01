This folder (/middlewares) is meant to contain all the the middlewares of the server.
Middlewares act as an intermediate layer between / before controllers. These are
very useful for authentication, authorization, caching, cors, etc. Middlewares are
implemented as single-responsibility functions. No classes, no fancy patterns, just
plain functions referenced by their name, or called to generate a parametric middleware
