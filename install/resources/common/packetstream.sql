CREATE TABLE public.main (
  id SERIAL PRIMARY KEY,
  packettimestamp TIMESTAMP NOT NULL,
  machineid TEXT NOT NULL,
  sourcemac VARCHAR(100) NOT NULL,
  destinationmac VARCHAR(100) NOT NULL,
  sourceip VARCHAR(100) NULL DEFAULT NULL,
  destinationip VARCHAR(100) NULL DEFAULT NULL,
  protocol VARCHAR(100) NULL DEFAULT NULL,
  sourceport INT NULL DEFAULT NULL,
  destinationport INT NULL DEFAULT NULL,
  info TEXT NULL DEFAULT NULL
);

GRANT CONNECT ON DATABASE packetstream TO pcapagent;
GRANT USAGE ON SCHEMA public TO pcapagent;
GRANT INSERT ON TABLE public.main TO pcapagent;
GRANT USAGE,UPDATE ON SEQUENCE main_id_seq TO pcapagent;

GRANT CONNECT ON DATABASE packetstream TO selectuser;
GRANT USAGE ON SCHEMA public TO selectuser;
GRANT SELECT ON TABLE public.main TO selectuser;
GRANT USAGE ON SEQUENCE main_id_seq TO selectuser;

