
-- Table: public.user

-- DROP TABLE IF EXISTS public."user";

CREATE TABLE IF NOT EXISTS public."user"
(
    id integer NOT NULL DEFAULT nextval('user_id_seq'::regclass),
    firstname character varying(50) COLLATE pg_catalog."default",
    lastname character varying(100) COLLATE pg_catalog."default",
    email character varying(320) COLLATE pg_catalog."default" NOT NULL,
    pwd character varying(255) COLLATE pg_catalog."default" NOT NULL,
    is_active boolean DEFAULT false,
    date_created date NOT NULL,
    date_updated date,
    CONSTRAINT user_pkey PRIMARY KEY (id)
    )

    TABLESPACE pg_default;

ALTER TABLE IF EXISTS public."user"
    OWNER to devuser;

-- Table users
CREATE TABLE IF NOT EXISTS public.users
(
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(320) UNIQUE NOT NULL,
    role VARCHAR(20) DEFAULT 'user' CHECK (role IN ('admin', 'user')),
    password VARCHAR(255) NOT NULL,
    confirmed BOOLEAN DEFAULT false,
    is_active BOOLEAN DEFAULT true,
    reset_token VARCHAR(100),
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP
);

ALTER TABLE users ADD COLUMN confirmation_token VARCHAR(100);

-- Table pages
CREATE TABLE IF NOT EXISTS public.pages
(
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content TEXT,
    meta_description TEXT,
    meta_keywords TEXT,
    is_published BOOLEAN DEFAULT false,
    author_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP
);

ALTER TABLE IF EXISTS public.users OWNER to devuser;
ALTER TABLE IF EXISTS public.pages OWNER to devuser;

INSERT INTO public.users (username, email, password, role, confirmed, is_active) VALUES ('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', true, true) ON CONFLICT (username) DO NOTHING;
