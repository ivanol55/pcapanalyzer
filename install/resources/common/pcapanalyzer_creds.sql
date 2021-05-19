CREATE TABLE public.apikeys (
key text primary key,
creation_date timestamp not null
);
CREATE TABLE public.users (
username text primary key,
password text not null
);
