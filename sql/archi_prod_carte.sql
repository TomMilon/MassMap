--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: aesn_syntaxon_dpt; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE aesn_syntaxon_dpt (
    id_aesn character varying NOT NULL,
    lb_syntaxon character varying,
    statut character varying,
    dpt integer NOT NULL,
    region character varying,
    geom geometry(MultiPolygon,2154)
);


--
-- Name: aesn_syntaxon_reg; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE aesn_syntaxon_reg (
    id_aesn character varying NOT NULL,
    id_aesn_supra character varying,
    source character varying,
    cle_tri integer,
    niveau integer,
    lb_niveau character varying,
    lb_syntaxon_ok character varying,
    in_bassin character varying,
    in_pic character varying,
    in_hn character varying,
    in_bou character varying,
    in_cen character varying,
    in_ca character varying,
    in_idf character varying,
    in_bn character varying,
    in_lorraine character varying,
    num_fiche character varying
);


--
-- Name: carte_degnat; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE carte_degnat (
    cd_ref_referentiel text,
    nom_complet_liste text,
    code_territoire text,
    code_type_territoire text,
    degnat text
);


--
-- Name: carte_eee; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE carte_eee (
    cd_ref_referentiel text,
    nom_complet_liste text,
    code_territoire text,
    code_type_territoire text,
    eee text
);


--
-- Name: carte_indigenat; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE carte_indigenat (
    cd_ref_referentiel text,
    nom_complet_liste text,
    code_territoire text,
    code_type_territoire text,
    indigenat text
);


--
-- Name: carte_lr; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE carte_lr (
    cd_ref_referentiel text,
    nom_complet_liste text,
    code_territoire text,
    code_type_territoire text,
    liste_rouge text
);


--
-- Name: aesn_syntaxon_dpt_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY aesn_syntaxon_dpt
    ADD CONSTRAINT aesn_syntaxon_dpt_pkey PRIMARY KEY (id_aesn, dpt);


--
-- Name: aesn_syntaxon_reg_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY aesn_syntaxon_reg
    ADD CONSTRAINT aesn_syntaxon_reg_pkey PRIMARY KEY (id_aesn);


--
-- PostgreSQL database dump complete
--

