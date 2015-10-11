--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: d_ertek; Type: DOMAIN; Schema: public; Owner: gyuri
--

CREATE DOMAIN d_ertek AS numeric(5,3) NOT NULL
	CONSTRAINT hibas_keszultsegi_fok_szorzo CHECK ((VALUE >= (0)::numeric));


ALTER DOMAIN public.d_ertek OWNER TO gyuri;

--
-- Name: d_esz; Type: DOMAIN; Schema: public; Owner: gyuri
--

CREATE DOMAIN d_esz AS character varying(25) DEFAULT NULL::character varying;


ALTER DOMAIN public.d_esz OWNER TO gyuri;

--
-- Name: d_jarmutipus; Type: DOMAIN; Schema: public; Owner: gyuri
--

CREATE DOMAIN d_jarmutipus AS character varying(20) NOT NULL;


ALTER DOMAIN public.d_jarmutipus OWNER TO gyuri;

--
-- Name: d_jog; Type: DOMAIN; Schema: public; Owner: gyuri
--

CREATE DOMAIN d_jog AS integer NOT NULL DEFAULT 1
	CONSTRAINT hibas_jogosutsag_ertek CHECK (((VALUE >= 0) AND (VALUE <= 9)));


ALTER DOMAIN public.d_jog OWNER TO gyuri;

--
-- Name: d_munka; Type: DOMAIN; Schema: public; Owner: gyuri
--

CREATE DOMAIN d_munka AS character varying(75) NOT NULL;


ALTER DOMAIN public.d_munka OWNER TO gyuri;

--
-- Name: d_mutato; Type: DOMAIN; Schema: public; Owner: gyuri
--

CREATE DOMAIN d_mutato AS integer NOT NULL
	CONSTRAINT hibas_keszultseg_mutato_ertek CHECK (((VALUE >= 0) AND (VALUE <= 4)));


ALTER DOMAIN public.d_mutato OWNER TO gyuri;

--
-- Name: d_psz; Type: DOMAIN; Schema: public; Owner: gyuri
--

CREATE DOMAIN d_psz AS character varying(10) NOT NULL;


ALTER DOMAIN public.d_psz OWNER TO gyuri;

--
-- Name: d_sap8; Type: DOMAIN; Schema: public; Owner: gyuri
--

CREATE DOMAIN d_sap8 AS character varying(8) DEFAULT NULL::character varying;


ALTER DOMAIN public.d_sap8 OWNER TO gyuri;

--
-- Name: alapjelszo(text); Type: FUNCTION; Schema: public; Owner: gyuri
--

CREATE FUNCTION alapjelszo(text) RETURNS integer
    LANGUAGE plpgsql
    AS $_$

declare

begin

    perform id from felhasznalo where md5(id)=md5($1);

    if not found then

        return 0;

    else

        update felhasznalo set jelszo=md5('init') where md5(id)=md5($1);

        return 1;

    end if;

end;

$_$;


ALTER FUNCTION public.alapjelszo(text) OWNER TO gyuri;

--
-- Name: bejelentkezes(text, text); Type: FUNCTION; Schema: public; Owner: gyuri
--

CREATE FUNCTION bejelentkezes(text, text) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
declare
begin
  perform id from felhasznalo where md5(lower($1))=md5(lower(id)) and jelszo = md5($2);
  if not found then
    return 1;
  else
    return 0;
  end if;
end;
$_$;


ALTER FUNCTION public.bejelentkezes(text, text) OWNER TO gyuri;

--
-- Name: jelszocsere(text, text, text, text); Type: FUNCTION; Schema: public; Owner: gyuri
--

CREATE FUNCTION jelszocsere(text, text, text, text) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
declare
begin
    if trim($3) <> trim($4) or trim($1) = '' then
        return 2;
    end if;
    perform id from felhasznalo where md5($1) = md5(id) and md5($2) = jelszo;
    if not found then
        return 1;
    else
        update felhasznalo set jelszo=md5($3) where md5(id)=md5($1);
        return 0;
    end if;
end;
$_$;


ALTER FUNCTION public.jelszocsere(text, text, text, text) OWNER TO gyuri;

--
-- Name: tipus_torlese(text); Type: FUNCTION; Schema: public; Owner: gyuri
--

CREATE FUNCTION tipus_torlese(text) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
declare
begin
  perform distinct jarmutipus,tipus.id from jarmu_alap right join tipus 
    on(jarmutipus=tipus.id) where jarmutipus is null and tipus.id=upper($1);
  if found then
    delete from tipus where upper(id)=upper($1);
    return 1;
  else 
    return 0;
  end if;
end;
$_$;


ALTER FUNCTION public.tipus_torlese(text) OWNER TO gyuri;

--
-- Name: uj_felhasznalo(text, text); Type: FUNCTION; Schema: public; Owner: gyuri
--

CREATE FUNCTION uj_felhasznalo(text, text) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
declare
begin
  if trim($1) = '' or trim($2) = '' then
    return 1;
  end if;
  perform id from felhasznalo where id = trim($1);
  if not found then
    insert into felhasznalo(id,nev,jog) values(trim($1),trim($2),1);
    return 0;
  else
    return 2;
  end if;  
end;
$_$;


ALTER FUNCTION public.uj_felhasznalo(text, text) OWNER TO gyuri;

--
-- Name: ujtipus(text); Type: FUNCTION; Schema: public; Owner: gyuri
--

CREATE FUNCTION ujtipus(text) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
declare
begin
  perform id from tipus where upper($1)=upper(id);
  if not found then
    insert into tipus values(upper($1));
    return 1;
  else
    return 0;
  end if;
end;
$_$;


ALTER FUNCTION public.ujtipus(text) OWNER TO gyuri;

SET default_tablespace = '';

SET default_with_oids = true;

--
-- Name: felhasznalo; Type: TABLE; Schema: public; Owner: gyuri; Tablespace: 
--

CREATE TABLE felhasznalo (
    id character varying(25) NOT NULL,
    nev character varying(50) NOT NULL,
    jog d_jog,
    jelszo text DEFAULT md5('init'::text) NOT NULL
);


ALTER TABLE public.felhasznalo OWNER TO gyuri;

--
-- Name: seq_jarmu_alap; Type: SEQUENCE; Schema: public; Owner: gyuri
--

CREATE SEQUENCE seq_jarmu_alap
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_jarmu_alap OWNER TO gyuri;

--
-- Name: jarmu_alap; Type: TABLE; Schema: public; Owner: gyuri; Tablespace: 
--

CREATE TABLE jarmu_alap (
    jarmutipus d_jarmutipus,
    ev integer,
    sorszam integer,
    psz d_psz,
    esz d_esz,
    sd_al d_sap8,
    sd_op d_sap8,
    pp_al d_sap8,
    pp_op d_sap8,
    erkezett date,
    munkabavetel date,
    allapotfelvetel date,
    reszatvetel date,
    vegatvetel date,
    hazaadas date,
    szamlazas date,
    megjegyzes text,
    terv_atfutasi_ido integer DEFAULT 1 NOT NULL,
    id integer DEFAULT nextval('seq_jarmu_alap'::regclass) NOT NULL,
    CONSTRAINT ervenytelen_ev CHECK ((ev >= 2014)),
    CONSTRAINT ervenytelen_sorszam CHECK ((sorszam > 0)),
    CONSTRAINT korai_allapotfelvetel CHECK ((allapotfelvetel >= erkezett)),
    CONSTRAINT korai_hazaadas CHECK ((hazaadas >= erkezett)),
    CONSTRAINT korai_munkabavetel CHECK ((munkabavetel >= erkezett)),
    CONSTRAINT korai_reszszamla CHECK ((reszatvetel >= erkezett)),
    CONSTRAINT korai_szamlazas CHECK ((szamlazas >= hazaadas)),
    CONSTRAINT korai_vegatvetel CHECK ((vegatvetel >= erkezett))
);


ALTER TABLE public.jarmu_alap OWNER TO gyuri;

--
-- Name: seq_kcsv_munka; Type: SEQUENCE; Schema: public; Owner: gyuri
--

CREATE SEQUENCE seq_kcsv_munka
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_kcsv_munka OWNER TO gyuri;

--
-- Name: kcsv_munka; Type: TABLE; Schema: public; Owner: gyuri; Tablespace: 
--

CREATE TABLE kcsv_munka (
    sorszam integer,
    munka d_munka,
    ertek d_ertek,
    id integer DEFAULT nextval('seq_kcsv_munka'::regclass) NOT NULL
);


ALTER TABLE public.kcsv_munka OWNER TO gyuri;

--
-- Name: seq_t5c5k2mod_munka; Type: SEQUENCE; Schema: public; Owner: gyuri
--

CREATE SEQUENCE seq_t5c5k2mod_munka
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_t5c5k2mod_munka OWNER TO gyuri;

--
-- Name: seq_tw6000_j1_munka; Type: SEQUENCE; Schema: public; Owner: gyuri
--

CREATE SEQUENCE seq_tw6000_j1_munka
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_tw6000_j1_munka OWNER TO gyuri;

--
-- Name: seq_volvo_7000_munka; Type: SEQUENCE; Schema: public; Owner: gyuri
--

CREATE SEQUENCE seq_volvo_7000_munka
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_volvo_7000_munka OWNER TO gyuri;

--
-- Name: t5c5k2mod_munka; Type: TABLE; Schema: public; Owner: gyuri; Tablespace: 
--

CREATE TABLE t5c5k2mod_munka (
    sorszam integer,
    munka d_munka,
    ertek d_ertek,
    id integer DEFAULT nextval('seq_t5c5k2mod_munka'::regclass) NOT NULL
);


ALTER TABLE public.t5c5k2mod_munka OWNER TO gyuri;

--
-- Name: tipus; Type: TABLE; Schema: public; Owner: gyuri; Tablespace: 
--

CREATE TABLE tipus (
    id d_jarmutipus NOT NULL
);


ALTER TABLE public.tipus OWNER TO gyuri;

--
-- Name: tw6000_j1_munka; Type: TABLE; Schema: public; Owner: gyuri; Tablespace: 
--

CREATE TABLE tw6000_j1_munka (
    sorszam integer,
    munka d_munka,
    ertek d_ertek,
    id integer DEFAULT nextval('seq_tw6000_j1_munka'::regclass) NOT NULL
);


ALTER TABLE public.tw6000_j1_munka OWNER TO gyuri;

--
-- Name: volvo_7000_munka; Type: TABLE; Schema: public; Owner: gyuri; Tablespace: 
--

CREATE TABLE volvo_7000_munka (
    sorszam integer,
    munka d_munka,
    ertek d_ertek,
    id integer DEFAULT nextval('seq_volvo_7000_munka'::regclass) NOT NULL
);


ALTER TABLE public.volvo_7000_munka OWNER TO gyuri;

--
-- Data for Name: felhasznalo; Type: TABLE DATA; Schema: public; Owner: gyuri
--

COPY felhasznalo (id, nev, jog, jelszo) FROM stdin;
szikoragy	Szikora György	9	39c92ceb92bb80e5f44c35cc70fe3595
vir	VIR	1	590f35821fbed7b2ab58a9dbaf36c42d
ballaa	Balla Ádám	8	1d7c2923c1684726dc23d2901c4d8157
\.


--
-- Data for Name: jarmu_alap; Type: TABLE DATA; Schema: public; Owner: gyuri
--

COPY jarmu_alap (jarmutipus, ev, sorszam, psz, esz, sd_al, sd_op, pp_al, pp_op, erkezett, munkabavetel, allapotfelvetel, reszatvetel, vegatvetel, hazaadas, szamlazas, megjegyzes, terv_atfutasi_ido, id) FROM stdin;
T5C5K2MOD	2014	1	4043	\N	20001715	\N	71018361	\N	2014-01-30	\N	2014-05-23	\N	2014-11-15	2014-11-20	\N	\N	98	1
T5C5K2MOD	2014	2	4042	\N	20001711	\N	71018359	\N	2014-01-30	\N	2014-05-20	\N	2014-11-15	2014-11-20	\N	\N	98	2
T5C5K2MOD	2014	3	4079	\N	20001756	\N	71018459	\N	2014-04-28	\N	2014-06-18	\N	2014-11-22	2014-11-24	\N	\N	98	3
T5C5K2MOD	2014	4	4099	\N	20001757	\N	71018461	\N	2014-04-28	\N	2014-06-06	\N	2014-11-22	2014-11-24	\N	\N	98	4
T5C5K2MOD	2014	5	4092	\N	20001758	\N	71018485	\N	2014-04-28	\N	2014-07-09	\N	2014-11-27	2014-11-27	\N	\N	98	5
T5C5K2MOD	2014	6	4138	\N	20001759	\N	71018483	\N	2014-04-28	\N	2014-06-20	\N	2014-11-27	2014-11-27	\N	\N	98	6
T5C5K2MOD	2014	7	4088	\N	20001761	\N	71018487	\N	2014-05-09	\N	2014-07-14	\N	2014-12-03	2014-12-03	\N	\N	98	7
T5C5K2MOD	2014	8	4078	\N	20001762	\N	71018489	\N	2014-05-09	\N	2014-07-23	\N	2014-12-06	2014-12-06	\N	\N	98	8
T5C5K2MOD	2014	9	4084	\N	20001763	\N	71018491	\N	2014-05-09	\N	2014-07-21	\N	2014-12-06	2014-12-06	\N	\N	98	9
T5C5K2MOD	2014	10	4143	\N	20001764	\N	71018493	\N	2014-05-09	\N	2014-07-07	\N	2014-12-03	2014-12-03	\N	\N	98	10
T5C5K2MOD	2014	11	4023	\N	20001786	\N	71018505	\N	2014-06-23	\N	2014-08-15	\N	2014-12-10	2014-12-10	\N	\N	98	11
T5C5K2MOD	2014	12	4022	\N	20001787	\N	71018507	\N	2014-06-23	\N	2014-08-08	\N	2014-12-10	2014-12-10	\N	\N	98	12
T5C5K2MOD	2014	13	4041	\N	20001788	\N	71018509	\N	2014-06-23	\N	2014-08-28	\N	2014-12-15	2014-12-15	\N	\N	98	13
T5C5K2MOD	2014	14	4090	\N	20001789	\N	71018511	\N	2014-06-23	\N	2014-08-29	\N	2014-12-15	2014-12-15	\N	\N	98	14
T5C5K2MOD	2014	15	4213	\N	20001805	\N	71018521	\N	2014-08-06	\N	2014-09-22	\N	2014-12-18	2014-12-18	\N	\N	98	15
T5C5K2MOD	2014	17	4020	\N	20001835	\N	71018551	\N	2014-09-04	\N	2014-09-30	\N	2014-12-21	2014-12-21	\N	\N	98	17
T5C5K2MOD	2014	18	4215	\N	20001836	\N	71018553	\N	2014-09-04	\N	2014-10-06	\N	2014-12-21	2014-12-21	\N	\N	98	18
T5C5K2MOD	2014	19	4071	\N	20001838	\N	71018559	\N	2014-09-12	\N	2014-10-20	\N	2014-12-31	2014-12-30	\N	\N	98	19
T5C5K2MOD	2014	20	4081	\N	20001839	\N	71018561	\N	2014-09-12	\N	2014-10-27	\N	2014-12-31	2014-12-30	\N	\N	98	20
T5C5K2MOD	2015	1	4209	\N	20001870	\N	71018699	\N	2014-12-17	\N	2015-02-06	\N	2015-04-28	2015-05-05	\N	\N	98	21
T5C5K2MOD	2015	2	4256	\N	20001869	\N	71018697	\N	2014-12-15	\N	2015-02-13	\N	2015-04-28	2015-05-05	\N	\N	98	22
T5C5K2MOD	2015	3	4144	\N	20001859	\N	71018675	\N	2014-12-04	\N	2015-01-23	\N	2015-04-09	2015-04-10	\N	\N	98	23
T5C5K2MOD	2015	4	4208	\N	20001857	\N	71018673	\N	2014-12-04	\N	2015-01-30	\N	2015-04-09	2015-04-10	\N	\N	98	24
T5C5K2MOD	2015	5	4039	\N	20001856	\N	71018671	\N	2015-02-06	\N	2015-03-09	\N	2015-05-27	2015-05-28	\N	\N	98	25
T5C5K2MOD	2015	6	4038	\N	20001854	\N	71018667	\N	2015-02-06	\N	2015-03-02	\N	2015-05-27	2015-05-28	\N	\N	98	26
T5C5K2MOD	2015	7	4253	\N	20001877	\N	71018713	\N	2015-03-03	\N	2015-04-23	\N	2015-06-30	2015-06-30	\N	\N	98	27
T5C5K2MOD	2015	8	4244	\N	20001880	\N	71018719	\N	2015-03-03	\N	2015-04-01	\N	2015-06-30	2015-06-30	\N	\N	98	28
T5C5K2MOD	2015	9	4040	\N	20001879	\N	71018717	\N	2015-04-03	\N	2015-05-04	\N	2015-07-31	2015-07-31	\N	\N	98	29
T5C5K2MOD	2015	10	4212	\N	20001878	\N	71018715	\N	2015-04-03	\N	2015-05-11	\N	2015-07-31	2015-07-31	\N	\N	98	30
T5C5K2MOD	2015	11	4096	\N	20001874	\N	71018707	\N	2015-05-07	\N	2015-06-19	\N	2015-08-14	2015-08-14	\N	\N	98	31
T5C5K2MOD	2015	12	4082	\N	20001866	\N	71018691	\N	2015-05-07	\N	2015-06-11	\N	2015-08-14	2015-08-14	\N	\N	98	32
T5C5K2MOD	2015	13	4251	\N	20001855	\N	71018669	\N	2015-06-01	\N	2015-07-06	\N	2015-09-25	2015-09-29	\N	\N	98	33
T5C5K2MOD	2015	14	4233	\N	20001852	\N	71018663	\N	2015-06-02	\N	2015-07-15	\N	2015-09-25	2015-09-29	\N	\N	98	34
T5C5K2MOD	2015	15	4224	\N	20001876	\N	71018711	\N	2015-06-12	\N	2015-08-03	\N	2015-09-25	2015-09-29	\N	\N	98	35
T5C5K2MOD	2015	16	4247	\N	20001867	\N	71018693	\N	2015-06-12	\N	2015-08-10	\N	2015-09-25	2015-09-29	\N	\N	98	36
T5C5K2MOD	2015	17	4100	\N	20001873	\N	71018705	\N	2015-07-01	\N	2015-08-14	\N	2015-09-30	2015-09-30	\N	\N	98	37
T5C5K2MOD	2015	18	4106	\N	20001875	\N	71018709	\N	2015-07-01	\N	2015-08-24	\N	2015-09-30	2015-09-30	\N	\N	98	38
T5C5K2MOD	2015	19	4276	\N	20001872	\N	71018703	\N	2015-07-14	\N	\N	\N	\N	\N	\N	\N	98	39
T5C5K2MOD	2015	20	4243	\N	20001868	\N	71018695	\N	2015-07-14	\N	2015-09-01	\N	\N	\N	\N	\N	98	40
T5C5K2MOD	2015	21	4237	\N	20001853	\N	71018665	\N	2015-08-24	\N	\N	\N	\N	\N	\N	\N	98	41
T5C5K2MOD	2015	22	4123	\N	20001871	\N	71018701	\N	2015-08-24	\N	\N	\N	\N	\N	\N	\N	98	42
TW6000 J1	2015	1	1533	\N	20001862	\N	71018681	\N	2014-12-17	\N	2015-02-20	\N	2015-04-30	2015-05-05	\N	\N	100	44
TW6000 J1	2015	2	1545	\N	20001861	\N	71018679	\N	2014-12-09	\N	2015-02-10	\N	2015-04-17	2015-04-15	\N	\N	100	45
TW6000 J1	2015	3	1527	\N	20001864	\N	71018685	\N	2015-02-04	\N	2015-03-12	\N	2015-05-29	2015-05-29	\N	\N	100	46
TW6000 J1	2015	4	1536	\N	20001863	\N	71018683	\N	2015-02-04	\N	2015-03-30	\N	2015-06-23	2015-06-26	\N	\N	100	47
TW6000 J1	2015	5	1528	\N	20001865	\N	71018687	\N	2015-02-24	\N	2015-05-11	\N	2015-06-30	2015-06-30	\N	\N	100	48
VOLVO	2015	2	NCZ551	1187	20001911	\N	71018743	71018744	2015-04-17	\N	2015-04-17	\N	2015-09-15	2015-09-21	2015-09-22	\N	35	50
VOLVO	2015	3	NCZ549	YV3R7G71121001179	20001914	\N	71018752	71018753	2015-04-17	\N	2015-04-20	\N	2015-09-16	2015-09-16	2015-09-17	ablakcsere megtörtént	35	51
VOLVO	2015	4	NCZ540	YV3R7G71121001160	20001912	\N	71018746	71018747	2015-04-17	\N	2015-04-21	\N	2015-09-15	2015-09-15	2015-09-17	ablakcsere megtörtént	35	52
VOLVO	2015	5	NCZ553	1189	20001915	\N	71018759	71018760	2015-05-20	\N	2015-05-20	\N	2015-09-15	2015-09-16	2015-09-17	\N	35	53
VOLVO	2015	6	NCZ554	YV3R7G71021001190	20001918	\N	71018756	71018757	2015-05-20	\N	2015-05-20	\N	2015-09-15	2015-09-15	2015-09-17	ablakcsere megtörtént	35	54
VOLVO	2015	7	NCZ542	1164	20001920	\N	71018789	71018790	2015-06-01	\N	2015-06-02	\N	2015-09-16	2015-09-21	2015-09-22	\N	35	55
VOLVO	2015	8	NCZ568	YV3R7G71521001203	20001917	\N	71018765	71018766	2015-05-20	\N	2015-05-21	\N	2015-09-16	2015-09-16	2015-09-17	ablakcsere megtörtént	35	56
VOLVO	2015	9	NCZ567	YV3R7G81X31001391	20001916	\N	71018762	71018763	2015-05-20	\N	2015-05-20	\N	2015-09-17	2015-09-18	2015-09-22	ablakcsere megtörtént	35	57
VOLVO	2015	10	NCZ547	YV3R7G71821001177	20001921	\N	71018786	71018787	2015-06-03	\N	2015-06-04	\N	2015-09-17	2015-09-18	2015-09-22	ablakcsere megtörtént	35	58
VOLVO	2015	11	NCZ552	YV3R7G71221001188	20001923	\N	71018794	71018795	2015-06-18	\N	2015-06-19	\N	2015-09-17	2015-09-18	2015-09-22	ablakcsere megtörtént	35	59
VOLVO	2015	12	NCZ539	YV3R7G71421001158	20001929	\N	71018812	71018813	2015-06-24	\N	2015-06-25	\N	2015-09-18	2015-09-21	2015-09-25	ablakcsere megtörtént	35	60
VOLVO	2015	13	NCZ544	1173	20001930	\N	71018815	71018816	2015-06-24	\N	2015-06-29	\N	2015-09-18	2015-09-21	2015-09-25	\N	35	61
VOLVO	2015	14	NCZ557	YV3R7G71921001205	20001927	\N	71018806	71018807	2015-06-29	\N	2015-06-30	\N	2015-09-18	2015-09-21	2015-09-25	ablakcsere megtörtént	35	62
VOLVO	2015	15	NCZ561	1223	20001932	\N	71018821	71018822	2015-06-24	\N	2015-06-30	\N	2015-09-21	2015-09-21	2015-09-25	\N	35	63
VOLVO	2015	16	NCZ573	1398	20001931	\N	71018818	71018819	2015-06-24	\N	2015-06-29	\N	2015-09-21	2015-09-21	2015-09-25	\N	35	64
VOLVO	2015	17	NCZ570	1395	20001934	\N	71018829	71018830	2015-07-10	\N	2015-07-10	\N	2015-09-22	2015-09-24	2015-09-29	\N	35	65
VOLVO	2015	18	NCZ576	1405	20001940	\N	71018838	71018839	2015-07-17	\N	2015-07-17	\N	2015-09-22	2015-09-22	2015-09-25	\N	35	66
VOLVO	2015	19	NCZ543	1165	20001939	\N	71018835	71018836	2015-07-20	\N	2015-07-20	\N	2015-09-22	2015-09-24	2015-09-29	\N	35	67
VOLVO	2015	20	NCZ558	1213	20001933	\N	71018826	71018827	2015-07-21	\N	2015-07-21	\N	2015-09-24	2015-09-25	2015-09-29	\N	35	68
VOLVO	2015	21	NCZ569	1394	20001943	\N	71018841	71018842	2015-08-06	\N	2015-08-07	\N	2015-09-24	2015-09-24	2015-09-29	\N	35	69
VOLVO	2015	22	NCZ562	1225	20001935	\N	71018846	71018847	2015-08-12	\N	2015-08-12	\N	2015-09-24	2015-09-25	2015-09-29	\N	35	70
VOLVO	2015	23	NCZ546	1175	20001941	\N	71018849	71018850	2015-08-13	\N	2015-08-13	\N	2015-09-25	2015-09-28	2015-09-30	\N	35	71
VOLVO	2015	24	NCZ566	1390	20001937	\N	71018852	71018853	2015-08-14	\N	2015-08-17	\N	2015-09-26	2015-09-28	2015-09-30	\N	35	72
T5C5K2MOD	2014	16	4098	\N	20001806	\N	71018523	\N	2014-08-06	\N	2014-09-12	\N	2014-12-18	2014-12-18	\N	\N	98	16
VOLVO	2015	25	NCZ564	1227	20001936	\N	71018855	71018856	2015-08-14	\N	2015-08-18	\N	2015-09-26	2015-09-28	2015-09-30	\N	35	73
VOLVO	2015	26	NCZ575	1403	20001971	\N	71018939	71018940	2015-09-02	\N	2015-09-02	\N	2015-09-26	2015-09-29	2015-09-30	\N	35	74
VOLVO	2015	27	NCZ571	1396	20001942	\N	71018933	71018934	2015-09-02	\N	2015-09-02	\N	2015-09-30	2015-09-30	2015-09-30	\N	35	75
VOLVO	2015	28	NCZ559	1214	20001944	\N	71018936	71018937	2015-09-03	\N	2015-09-03	\N	2015-09-30	2015-10-02	2015-10-06	\N	35	76
VOLVO	2015	29	NCZ560	1222	20001972	\N	71018942	71018943	2015-09-09	\N	2015-09-14	\N	2015-09-29	2015-09-30	2015-10-01	\N	35	77
VOLVO	2015	30	NCZ572	YV3R7G81031001397	20001974	\N	71018950	71018951	2015-09-18	\N	2015-09-18	\N	2015-09-29	2015-09-30	2015-09-30	ablakcsere megtörtént	35	78
VOLVO	2015	2000	NCZ545	YV3R7G71221001174	20001924	\N	71018797	71018798	\N	\N	\N	\N	\N	\N	\N	ablakcsere megtörtént	35	79
VOLVO	2015	2001	NCZ556	YV3R7G81131001392	20001925	\N	71018800	71018801	\N	\N	\N	\N	\N	\N	\N	ablakcsere megtörtént	35	80
VOLVO	2015	2002	NCZ555	YV3R7G71221001191	20001926	\N	71018803	71018804	\N	\N	\N	\N	\N	\N	\N	ablakcsere megtörtént	35	81
VOLVO	2015	2003	NCZ565	YV3R7G71031001241	20001928	\N	71018809	71018810	\N	\N	\N	\N	\N	\N	\N	ablakcsere megtörtént	35	82
VOLVO	2015	2004	NCZ550	YV3R7G71821001180	20001938	\N	71018832	71018833	\N	\N	\N	\N	\N	\N	\N	ablakcsere megtörtént	35	83
T5C5K2MOD	2015	23	4108	\N	20001975	\N	71018954	\N	2015-09-23	2015-09-28	\N	\N	\N	\N	\N	\N	98	43
T5C5K2MOD	2015	24	4134	\N	20001977	\N	71018958	\N	2015-09-22	2015-09-28	\N	\N	\N	\N	\N	\N	98	84
T5C5K2MOD	2015	25	4204	\N	\N	\N	\N	\N	2015-09-25	2015-10-05	\N	\N	\N	\N	\N	\N	98	85
KCSV7	2015	2	1328	\N	20001922	\N	71018824	71018825	2015-07-03	2015-07-03	\N	\N	\N	\N	\N	\N	180	87
VOLVO	2015	1	NCZ548	YV3R7G71X21001178	20001913	\N	71018749	71018750	2015-04-17	\N	2015-04-17	\N	2015-09-15	2015-09-15	2015-09-17	ablakcsere megtörtént	35	49
KCSV7	2015	1	1350	\N	20001919	\N	71018781	71018782	2015-04-22	2015-04-30	\N	\N	\N	\N	\N	Prototípus 1325 mintájára.	250	86
\.


--
-- Data for Name: kcsv_munka; Type: TABLE DATA; Schema: public; Owner: gyuri
--

COPY kcsv_munka (sorszam, munka, ertek, id) FROM stdin;
1	fődarab kiépítés	0.020	1
2	bontás	0.060	2
3	lakatos	0.060	3
4	alvázvédelem	0.020	4
5	fényezés	0.100	5
6	padló javítás csúszásmentes	0.080	6
7	szárazjég	0.005	7
8	kábeljavítás pótlás	0.050	8
9	tetőszigetelés oldaldekor	0.040	9
10	tetőellenállás	0.005	10
11	armatúra	0.050	11
12	keretek szerelvények ládák konvektorok	0.020	12
13	fűtőtest	0.010	13
14	ablakok vez.állás utastér	0.050	14
15	ülés. kapaszkodó	0.030	15
16	ajtók	0.050	16
17	vonókészülék	0.010	17
18	áramszedő	0.005	18
19	fődarab	0.050	19
20	homokszóró	0.010	20
21	külső világítás belső nyomógombok	0.020	21
22	áramátalakító	0.005	22
23	szaggató	0.020	23
24	pult. vez. állás	0.030	24
25	számítógép	0.010	25
26	matrica	0.005	26
27	mérés üzembe helyezés	0.100	27
28	VJSZ MEO próbapálya utca	0.060	28
29	ZRT MEO vizsga ajtózás	0.020	29
\.


--
-- Name: seq_jarmu_alap; Type: SEQUENCE SET; Schema: public; Owner: gyuri
--

SELECT pg_catalog.setval('seq_jarmu_alap', 88, true);


--
-- Name: seq_kcsv_munka; Type: SEQUENCE SET; Schema: public; Owner: gyuri
--

SELECT pg_catalog.setval('seq_kcsv_munka', 29, true);


--
-- Name: seq_t5c5k2mod_munka; Type: SEQUENCE SET; Schema: public; Owner: gyuri
--

SELECT pg_catalog.setval('seq_t5c5k2mod_munka', 50, true);


--
-- Name: seq_tw6000_j1_munka; Type: SEQUENCE SET; Schema: public; Owner: gyuri
--

SELECT pg_catalog.setval('seq_tw6000_j1_munka', 53, true);


--
-- Name: seq_volvo_7000_munka; Type: SEQUENCE SET; Schema: public; Owner: gyuri
--

SELECT pg_catalog.setval('seq_volvo_7000_munka', 17, true);


--
-- Data for Name: t5c5k2mod_munka; Type: TABLE DATA; Schema: public; Owner: gyuri
--

COPY t5c5k2mod_munka (sorszam, munka, ertek, id) FROM stdin;
1	fődarabok kiépítése	0.010	1
2	bontás	0.060	2
3	szemcseszórás	0.030	3
4	alváz- szekrényváz javítás	0.080	4
5	alvázvédelem	0.020	5
6	fényezés	0.060	6
7	erősáramú kábelszerelés	0.020	7
8	gyengeáramú kábelszerelés	0.030	8
9	padló fektetés	0.020	9
10	csúszásmentes burkolat	0.020	10
11	keretek	0.040	11
12	hő- és hangszigetelő anyag felhelyezése	0.010	12
13	oldaldekoritok felszerelése	0.010	13
14	mennyezeti dekoritok felhelyezése	0.020	14
15	fűtőtestek	0.020	15
16	armatúralemezek felhelyezése	0.020	16
17	tetőellenállás	0.020	17
18	hajtásvezérlő elektronika beépítése	0.040	18
19	vezetőállás elektromos szerelvényei	0.040	19
20	vezetői műszerasztal (pult)	0.020	20
21	MATADOR ablaktörlő	0.010	21
22	utastéri ablakok	0.020	22
23	kapaszkodórendszer	0.030	23
24	utasülések	0.030	24
25	szélvédő üvegek	0.010	25
26	vezetőállás szellőzőablak	0.010	26
27	vezetőállás leválasztás	0.020	27
28	vonókészülék	0.005	28
29	ajtólapok beszerelése	0.020	29
30	ajtóvezérlő elektronika beszerelése	0.030	30
31	ajtóhajtómű beszerelése	0.005	31
32	forgóváz	0.010	32
33	jegykezelők	0.010	33
34	áramszedő	0.005	34
35	erősáramú mérések	0.020	35
36	gyengeáramú mérések	0.020	36
37	áramátalakító	0.010	37
38	szaggató	0.010	38
39	homokszóró	0.010	39
40	külső feliratok hatósági jelzések	0.005	40
41	belső feliartok utastájékoztató elemek	0.005	41
42	elektronikus utastájékoztató (FUTÁR)	0.010	42
43	hajtásvezérlés üzembe helyezése SEDULITAS	0.040	43
44	vezetőülés	0.005	44
45	állóhelyi próba	0.010	45
46	próbapálya	0.010	46
47	házi vizsga	0.010	47
48	utcai próba	0.010	48
49	VJSZ MEO	0.015	49
50	ZRT MEO	0.005	50
\.


--
-- Data for Name: tipus; Type: TABLE DATA; Schema: public; Owner: gyuri
--

COPY tipus (id) FROM stdin;
T5C5K2MOD
TW6000 J1
VOLVO
KCSV7
\.


--
-- Data for Name: tw6000_j1_munka; Type: TABLE DATA; Schema: public; Owner: gyuri
--

COPY tw6000_j1_munka (sorszam, munka, ertek, id) FROM stdin;
1	fődarabok kiépítése	0.020	1
2	bontás	0.100	2
3	alváz- szekrényváz javítás	0.040	3
4	alvázvédelem	0.020	4
5	fényezés	0.050	5
6	erősáramú kábeljavítás	0.020	6
7	gyengeáramú kábeljavítás	0.020	7
8	padló javítás	0.010	8
9	szárazjég	0.005	9
10	csúszásmentes burkolat	0.020	10
11	kontaktorszekrény	0.030	11
12	oldaldekoritok felszerelése	0.020	12
13	tetőellenállás	0.010	13
14	vezetőállás elektromos szerelvényei	0.020	14
15	vezetői műszerasztal (pult)	0.010	15
16	külső világítás	0.010	16
17	ülés alatti dobozok	0.020	17
18	ülések	0.020	18
19	ablaktörlő	0.005	19
20	utastéri ablakok	0.040	20
21	kapaszkodórendszer	0.040	21
22	utastéri világítás	0.020	22
23	felszállásjelző nyomógombok	0.005	23
24	áramszedő	0.005	24
25	vonókészülék	0.005	25
26	erősáramú mérések	0.020	26
27	fűtőtestek beszerelése	0.005	27
28	fotocella	0.005	28
29	gyengeáramú mérések	0.020	29
30	vezetőállás leválasztás	0.020	30
31	ajtólapok beszerelése	0.020	31
32	ajtóhajtómű beszerelése	0.005	32
33	ajtóvezérlő elektronika beszerelése	0.005	33
34	statikus áramátalakító	0.005	34
35	homokszóró	0.010	35
36	külső feliratok. hatósági jelzések	0.005	36
37	belső feliartok. utastájékoztató elemek	0.005	37
38	lépcsőbeszerelés	0.040	38
39	forgóváz beépítés	0.020	39
40	szaggató felszerelés	0.005	40
41	HZY tartályok beszerelése	0.005	41
42	WSG ZSG elektronika beépítése	0.005	42
43	jegykezelők felszerelése	0.005	43
44	járműélesztés	0.085	44
45	vezetőülés	0.005	45
46	állóhelyi próba	0.020	46
47	próbapálya	0.020	47
48	házi vizsga	0.020	48
49	utcai próba	0.010	49
50	lépcső és ajtóbeállítás	0.020	50
51	VJSZ MEO	0.025	51
52	járműszintezés	0.010	52
53	ZRT MEO	0.015	53
\.


--
-- Data for Name: volvo_7000_munka; Type: TABLE DATA; Schema: public; Owner: gyuri
--

COPY volvo_7000_munka (sorszam, munka, ertek, id) FROM stdin;
1	lakatos	0.100	1
2	fényezés	0.140	2
3	ülés kárpitozás	0.050	3
4	indításjelző	0.040	4
5	monitor felszerelés	0.060	5
6	vészjelző kialakítás	0.040	6
7	ülések kapaszkodók	0.160	7
8	sárga csúszásmentes	0.040	8
9	vezetőállás leválasztása	0.060	9
10	jegykezelők. e-ticket	0.040	10
11	VULTRON utastájékoztató	0.040	11
12	állapotfelvétel szerinti munkák	0.100	12
13	fóliázás	0.040	13
14	matricák	0.020	14
15	NANO	0.020	15
16	4 db A3 keret	0.010	16
17	MEO	0.040	17
\.


--
-- Name: felhasznalo_pkey; Type: CONSTRAINT; Schema: public; Owner: gyuri; Tablespace: 
--

ALTER TABLE ONLY felhasznalo
    ADD CONSTRAINT felhasznalo_pkey PRIMARY KEY (id);


--
-- Name: jarmu_alap_pkey; Type: CONSTRAINT; Schema: public; Owner: gyuri; Tablespace: 
--

ALTER TABLE ONLY jarmu_alap
    ADD CONSTRAINT jarmu_alap_pkey PRIMARY KEY (id);


--
-- Name: kcsv_munka_pkey; Type: CONSTRAINT; Schema: public; Owner: gyuri; Tablespace: 
--

ALTER TABLE ONLY kcsv_munka
    ADD CONSTRAINT kcsv_munka_pkey PRIMARY KEY (id);


--
-- Name: letezo_pp_vagy_sd_rendelesek; Type: CONSTRAINT; Schema: public; Owner: gyuri; Tablespace: 
--

ALTER TABLE ONLY jarmu_alap
    ADD CONSTRAINT letezo_pp_vagy_sd_rendelesek UNIQUE (sd_al, sd_op, pp_al, pp_op);


--
-- Name: mar_letezo_pp_al_rendeles; Type: CONSTRAINT; Schema: public; Owner: gyuri; Tablespace: 
--

ALTER TABLE ONLY jarmu_alap
    ADD CONSTRAINT mar_letezo_pp_al_rendeles UNIQUE (pp_al);


--
-- Name: mar_letezo_pp_op_rendeles; Type: CONSTRAINT; Schema: public; Owner: gyuri; Tablespace: 
--

ALTER TABLE ONLY jarmu_alap
    ADD CONSTRAINT mar_letezo_pp_op_rendeles UNIQUE (pp_op);


--
-- Name: mar_letezo_sd_al_rendeles; Type: CONSTRAINT; Schema: public; Owner: gyuri; Tablespace: 
--

ALTER TABLE ONLY jarmu_alap
    ADD CONSTRAINT mar_letezo_sd_al_rendeles UNIQUE (sd_al);


--
-- Name: mar_letezo_sd_op_rendeles; Type: CONSTRAINT; Schema: public; Owner: gyuri; Tablespace: 
--

ALTER TABLE ONLY jarmu_alap
    ADD CONSTRAINT mar_letezo_sd_op_rendeles UNIQUE (sd_op);


--
-- Name: t5c5k2mod_munka_pkey; Type: CONSTRAINT; Schema: public; Owner: gyuri; Tablespace: 
--

ALTER TABLE ONLY t5c5k2mod_munka
    ADD CONSTRAINT t5c5k2mod_munka_pkey PRIMARY KEY (id);


--
-- Name: tipus_pkey; Type: CONSTRAINT; Schema: public; Owner: gyuri; Tablespace: 
--

ALTER TABLE ONLY tipus
    ADD CONSTRAINT tipus_pkey PRIMARY KEY (id);


--
-- Name: tw6000_j1_munka_pkey; Type: CONSTRAINT; Schema: public; Owner: gyuri; Tablespace: 
--

ALTER TABLE ONLY tw6000_j1_munka
    ADD CONSTRAINT tw6000_j1_munka_pkey PRIMARY KEY (id);


--
-- Name: volvo_7000_munka_pkey; Type: CONSTRAINT; Schema: public; Owner: gyuri; Tablespace: 
--

ALTER TABLE ONLY volvo_7000_munka
    ADD CONSTRAINT volvo_7000_munka_pkey PRIMARY KEY (id);


--
-- Name: jarmu_alap_jarmutipus_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gyuri
--

ALTER TABLE ONLY jarmu_alap
    ADD CONSTRAINT jarmu_alap_jarmutipus_fkey FOREIGN KEY (jarmutipus) REFERENCES tipus(id) ON UPDATE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

