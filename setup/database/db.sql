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
    pk INT GENERATED ALWAYS AS IDENTITY,
    name TEXT NOT NULL,

    PRIMARY KEY (pk)
);

-- jobs
CREATE TABLE jobs (
    pk INT GENERATED ALWAYS AS IDENTITY,
    class TEXT NOT NULL,

    is_running BOOLEAN NOT NULL,
    is_exclusive BOOLEAN NOT NULL,
    context TEXT NULL DEFAULT NULL,
    report TEXT NULL DEFAULT NULL,
    progress FLOAT NOT NULL DEFAULT 0,

    scheduled_for TIMESTAMPTZ NULL DEFAULT NULL,
    schedule_from TIMESTAMPTZ NULL DEFAULT NULL,
    schedule_frequency TEXT NULL DEFAULT NULL,

    last_run_at TIMESTAMPTZ NULL DEFAULT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    PRIMARY KEY (pk)
);
