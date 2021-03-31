CREATE TABLE gene (
    gene_id bigserial PRIMARY KEY,
    gene_name varchar(80) UNIQUE NOT NULL,
    gene_version varchar(80) NOT NULL,
    gene_lookup jsonb
);

CREATE TABLE annotation (
    annotation_id bigserial PRIMARY KEY,
    annotation_term varchar(80) UNIQUE,
    annotation_desc text NOT NULL,
    annotation_type varchar(80) NOT NULL
);

CREATE TABLE gene_annotation (
    gene_annotation_id bigserial PRIMARY KEY,
    gene_id bigserial REFERENCES gene(gene_id),
    annotation_id bigserial REFERENCES annotation(annotation_id)
);


--
-- Name: annotation_phonetic; Type: TABLE;  Owner: web_usr--


GRANT ALL PRIVILEGES ON gene TO web_usr;
GRANT ALL PRIVILEGES ON annotation TO web_usr;
GRANT ALL PRIVILEGES ON gene_annotation TO web_usr;

-- Index
CREATE EXTENSION IF NOT EXISTS pg_trgm;
CREATE INDEX annotation_idx ON annotation USING gin(annotation_desc gin_trgm_ops);
CREATE INDEX idx_lower_gname ON gene (lower(gene_name))