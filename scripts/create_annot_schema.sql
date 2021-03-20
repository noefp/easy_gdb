CREATE TABLE gene_version (
    gene_version_id bigserial PRIMARY KEY,
    gene_version varchar(80) UNIQUE
);

CREATE TABLE gene (
    gene_id bigserial PRIMARY KEY,
    gene_name varchar(80) NOT NULL,
    gene_version_id bigserial REFERENCES gene_version(gene_version_id)
);

CREATE TABLE gene_gene (
    gene_gene_id bigserial PRIMARY KEY,
    gene_id1 bigserial REFERENCES gene(gene_id),
    gene_id2 bigserial REFERENCES gene(gene_id)
);

CREATE TABLE annotation_type (
    annotation_type_id bigserial PRIMARY KEY,
    annotation_type varchar(80) UNIQUE NOT NULL
);

CREATE TABLE annotation (
    annotation_id bigserial PRIMARY KEY,
    annot_term varchar(80),
    annot_desc text NOT NULL,
    annotation_type_id bigserial REFERENCES annotation_type(annotation_type_id)
);

CREATE TABLE gene_annotation (
    gene_annotation_id bigserial PRIMARY KEY,
    gene_id bigserial REFERENCES gene(gene_id),
    annotation_id bigserial REFERENCES annotation(annotation_id)
);


--
-- Name: annotation_phonetic; Type: TABLE;  Owner: web_usr--


GRANT ALL PRIVILEGES ON gene TO web_usr;
GRANT ALL PRIVILEGES ON gene_version TO web_usr;
GRANT ALL PRIVILEGES ON gene_gene TO web_usr;
GRANT ALL PRIVILEGES ON annotation_type TO web_usr;
GRANT ALL PRIVILEGES ON annotation TO web_usr;
GRANT ALL PRIVILEGES ON gene_annotation TO web_usr;

-- Index
CREATE EXTENSION IF NOT EXISTS pg_trgm;
CREATE INDEX annotation_idx ON annotation USING gin(annot_desc gin_trgm_ops);
CREATE INDEX idx_lower_gname ON gene (lower(gene_name))