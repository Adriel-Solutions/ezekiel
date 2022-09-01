-- default database
CREATE DATABASE project;

\c project

-- updated_at refresh function (to be called inside a trigger)
CREATE OR REPLACE FUNCTION refresh_updated_at()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = NOW();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- migrations
CREATE TABLE migrations (
    pk BYTEA NOT NULL,
    name BYTEA NOT NULL,

    PRIMARY KEY (pk)
);

-- jobs
CREATE TABLE jobs (
    pk BYTEA NOT NULL,
    class BYTEA NOT NULL,

    is_running BYTEA NOT NULL,
    is_unique BYTEA NOT NULL,
    context BYTEA NOT NULL,

    scheduled_for BYTEA NOT NULL,
    schedule_frequency BYTEA NOT NULL,
    schedule_unit BYTEA NOT NULL,

    last_run_at BYTEA NOT NULL,
    created_at BYTEA NOT NULL,

    PRIMARY KEY (pk)
);
