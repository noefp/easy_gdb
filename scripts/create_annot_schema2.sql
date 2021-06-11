CREATE TABLE species (
    species_id bigserial PRIMARY KEY,
    species_name varchar(80) UNIQUE NOT NULL,
    jbrowse_folder varchar(80) UNIQUE
);

CREATE TABLE annotation_version (
    annotation_version_id bigserial PRIMARY KEY,
    annotation_version varchar(80) NOT NULL
);

CREATE TABLE gene (
    gene_id bigserial PRIMARY KEY,
    gene_name varchar(80) UNIQUE NOT NULL,
    annotation_version_id bigserial REFERENCES annotation_version(annotation_version_id),
    species_id bigserial REFERENCES species(species_id)
);

CREATE TABLE annotation_type (
    annotation_type_id bigserial PRIMARY KEY,
    annotation_type varchar(80) UNIQUE NOT NULL
);

CREATE TABLE annotation (
    annotation_id bigserial PRIMARY KEY,
    annotation_term varchar(80) UNIQUE,
    annotation_desc text NOT NULL,
    annotation_type_id bigserial REFERENCES annotation_type(annotation_type_id)
);

CREATE TABLE gene_annotation (
    gene_annotation_id bigserial PRIMARY KEY,
    gene_id bigserial REFERENCES gene(gene_id),
    annotation_id bigserial REFERENCES annotation(annotation_id)
);


--
GRANT ALL PRIVILEGES ON species TO web_usr;
GRANT ALL PRIVILEGES ON annotation_version TO web_usr;
GRANT ALL PRIVILEGES ON annotation_type TO web_usr;
GRANT ALL PRIVILEGES ON gene TO web_usr;
GRANT ALL PRIVILEGES ON annotation TO web_usr;
GRANT ALL PRIVILEGES ON gene_annotation TO web_usr;

-- Index
CREATE EXTENSION IF NOT EXISTS pg_trgm;
CREATE INDEX annotation_idx ON annotation USING gin(annotation_desc gin_trgm_ops);
CREATE INDEX idx_lower_gname ON gene (lower(gene_name))
