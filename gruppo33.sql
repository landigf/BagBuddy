--
-- PostgreSQL database dump
--

-- Dumped from database version 14.15 (Homebrew)
-- Dumped by pg_dump version 14.15 (Homebrew)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: oggetti; Type: TABLE; Schema: public; Owner: www
--

CREATE TABLE public.oggetti (
    id integer NOT NULL,
    nome character varying(30) NOT NULL,
    imgpath character varying(50) NOT NULL,
    dettagli jsonb NOT NULL,
    score integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.oggetti OWNER TO www;

--
-- Name: oggetti_id_seq; Type: SEQUENCE; Schema: public; Owner: www
--

CREATE SEQUENCE public.oggetti_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.oggetti_id_seq OWNER TO www;

--
-- Name: oggetti_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: www
--

ALTER SEQUENCE public.oggetti_id_seq OWNED BY public.oggetti.id;


--
-- Name: utenti; Type: TABLE; Schema: public; Owner: www
--

CREATE TABLE public.utenti (
    id integer NOT NULL,
    username character varying(50) NOT NULL,
    password character varying(255) NOT NULL,
    email character varying(100) NOT NULL,
    domanda_sicurezza character varying(255),
    risposta_sicurezza character varying(255),
    info jsonb,
    preferences jsonb,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.utenti OWNER TO www;

--
-- Name: utenti_id_seq; Type: SEQUENCE; Schema: public; Owner: www
--

CREATE SEQUENCE public.utenti_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.utenti_id_seq OWNER TO www;

--
-- Name: utenti_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: www
--

ALTER SEQUENCE public.utenti_id_seq OWNED BY public.utenti.id;


--
-- Name: valigie; Type: TABLE; Schema: public; Owner: www
--

CREATE TABLE public.valigie (
    id integer NOT NULL,
    nome character varying(100) NOT NULL,
    id_utente integer NOT NULL,
    preferenze jsonb NOT NULL,
    lista_oggetti jsonb NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.valigie OWNER TO www;

--
-- Name: valigie_id_seq; Type: SEQUENCE; Schema: public; Owner: www
--

CREATE SEQUENCE public.valigie_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.valigie_id_seq OWNER TO www;

--
-- Name: valigie_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: www
--

ALTER SEQUENCE public.valigie_id_seq OWNED BY public.valigie.id;


--
-- Name: oggetti id; Type: DEFAULT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.oggetti ALTER COLUMN id SET DEFAULT nextval('public.oggetti_id_seq'::regclass);


--
-- Name: utenti id; Type: DEFAULT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.utenti ALTER COLUMN id SET DEFAULT nextval('public.utenti_id_seq'::regclass);


--
-- Name: valigie id; Type: DEFAULT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.valigie ALTER COLUMN id SET DEFAULT nextval('public.valigie_id_seq'::regclass);


--
-- Data for Name: oggetti; Type: TABLE DATA; Schema: public; Owner: www
--

COPY public.oggetti (id, nome, imgpath, dettagli, score) FROM stdin;
9	Cappello	cappello.png	{"relax": {"no": 0, "si": 0}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": 1}, "spiaggia": {"no": 0, "si": 1}, "campeggio": {"no": 0, "si": 1}, "compagnia": {"solo": 0, "amici": 0, "coppia": 0, "famiglia": 0}, "dimensione": {"media": 1, "grande": 1, "piccola": 0}, "destinazione": {"isHot": 1, "isCold": 1, "isRainy": 0, "isSnowy": 1, "isSunny": 1, "isWindy": 1, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 1}, "tipoDiViaggio": {"altro": 0, "lavoro": 0, "studio": 0, "vacanza": 1}}	0
4	Tenda da campeggio	tenda.png	{"relax": {"no": 0, "si": -1}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": 0}, "spiaggia": {"no": 0, "si": 0}, "campeggio": {"no": -3, "si": 3}, "compagnia": {"solo": 0, "amici": 1, "coppia": 0, "famiglia": 1}, "dimensione": {"media": 1, "grande": 1, "piccola": -3}, "destinazione": {"isHot": 0, "isCold": 0, "isRainy": 0, "isSnowy": 0, "isSunny": 0, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 1}, "tipoDiViaggio": {"altro": 0, "lavoro": -1, "studio": -2, "vacanza": 1}}	0
5	Scarpe da trekking	scarpeTrekking.png	{"relax": {"no": 0, "si": -1}, "sport": {"no": 0, "si": 1}, "montagna": {"no": -1, "si": 2}, "spiaggia": {"no": 1, "si": -1}, "campeggio": {"no": -1, "si": 1}, "compagnia": {"solo": 1, "amici": 1, "coppia": 0, "famiglia": 0}, "dimensione": {"media": 0, "grande": 1, "piccola": -1}, "destinazione": {"isHot": 0, "isCold": 0, "isRainy": 0, "isSnowy": 0, "isSunny": 0, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": -1, "si": 3}, "tipoDiViaggio": {"altro": 0, "lavoro": -1, "studio": -1, "vacanza": 1}}	0
14	Torcia elettrica	torcia.png	{"relax": {"no": 0, "si": -1}, "sport": {"no": 0, "si": 1}, "montagna": {"no": -1, "si": 2}, "spiaggia": {"no": 1, "si": -1}, "campeggio": {"no": 0, "si": 2}, "compagnia": {"solo": 1, "amici": 1, "coppia": 0, "famiglia": 0}, "dimensione": {"media": 0, "grande": 1, "piccola": 0}, "destinazione": {"isHot": 0, "isCold": 0, "isRainy": 1, "isSnowy": 1, "isSunny": 0, "isWindy": 1, "isCloudy": 1}, "escursionismo": {"no": -1, "si": 2}, "tipoDiViaggio": {"altro": 0, "lavoro": -1, "studio": -1, "vacanza": 1}}	0
17	Scarpe da ginnastica	scarpeGinnastica.png	{"relax": {"no": 0, "si": 1}, "sport": {"no": -1, "si": 2}, "montagna": {"no": 0, "si": 1}, "spiaggia": {"no": 1, "si": 0}, "campeggio": {"no": 0, "si": 1}, "compagnia": {"solo": 0, "amici": 0, "coppia": 0, "famiglia": 0}, "dimensione": {"media": 0, "grande": 1, "piccola": -1}, "destinazione": {"isHot": 1, "isCold": 1, "isRainy": 1, "isSnowy": 0, "isSunny": 1, "isWindy": 1, "isCloudy": 0}, "escursionismo": {"no": -1, "si": 1}, "tipoDiViaggio": {"altro": 0, "lavoro": 0, "studio": 0, "vacanza": 1}}	0
21	Spazzolino e dentifricio	spazzolino_dentifricio.png	{"relax": {"no": 0, "si": 1}, "sport": {"no": 0, "si": 1}, "montagna": {"no": 0, "si": 0}, "spiaggia": {"no": 0, "si": 0}, "campeggio": {"no": 0, "si": 1}, "compagnia": {"solo": 1, "amici": 1, "coppia": 1, "famiglia": 2}, "dimensione": {"media": 1, "grande": 2, "piccola": 1}, "destinazione": {"isHot": 1, "isCold": 1, "isRainy": 1, "isSnowy": 1, "isSunny": 1, "isWindy": 1, "isCloudy": 1}, "escursionismo": {"no": 0, "si": 0}, "tipoDiViaggio": {"altro": 1, "lavoro": 1, "studio": 1, "vacanza": 1}}	0
16	Infradito	infradito.png	{"relax": {"no": 0, "si": 1}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": -2}, "spiaggia": {"no": -2, "si": 3}, "campeggio": {"no": 0, "si": -1}, "compagnia": {"solo": 0, "amici": 0, "coppia": 0, "famiglia": 0}, "dimensione": {"media": 1, "grande": 2, "piccola": 0}, "destinazione": {"isHot": 2, "isCold": -2, "isRainy": 0, "isSnowy": -2, "isSunny": 2, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 0, "si": -1}, "tipoDiViaggio": {"altro": 0, "lavoro": -1, "studio": -1, "vacanza": 1}}	0
24	Caricabatterie	caricabatterie.png	{"relax": {"no": 0, "si": 2}, "sport": {"no": 1, "si": 0}, "montagna": {"no": 0, "si": 1}, "spiaggia": {"no": 0, "si": 0}, "campeggio": {"no": 1, "si": 1}, "compagnia": {"solo": 2, "amici": 1, "coppia": 0, "famiglia": 2}, "dimensione": {"media": 2, "grande": 3, "piccola": 1}, "destinazione": {"isHot": 0, "isCold": 1, "isRainy": 0, "isSnowy": 0, "isSunny": 0, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 1, "si": 0}, "tipoDiViaggio": {"altro": 0, "lavoro": 3, "studio": 3, "vacanza": 1}}	0
22	Ciabatte	ciabatte.png	{"relax": {"no": 0, "si": 1}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": 1}, "spiaggia": {"no": 0, "si": 0}, "campeggio": {"no": 0, "si": 0}, "compagnia": {"solo": 0, "amici": 0, "coppia": 0, "famiglia": 0}, "dimensione": {"media": 1, "grande": 2, "piccola": 0}, "destinazione": {"isHot": 0, "isCold": 1, "isRainy": 0, "isSnowy": 0, "isSunny": 0, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 0}, "tipoDiViaggio": {"altro": 0, "lavoro": 0, "studio": 0, "vacanza": 0}}	0
18	Pigiama	pigiama.png	{"relax": {"no": 0, "si": 1}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": 0}, "spiaggia": {"no": 0, "si": 0}, "campeggio": {"no": 0, "si": 1}, "compagnia": {"solo": 1, "amici": 1, "coppia": 0, "famiglia": 1}, "dimensione": {"media": 2, "grande": 3, "piccola": 1}, "destinazione": {"isHot": 1, "isCold": 3, "isRainy": 1, "isSnowy": 1, "isSunny": 1, "isWindy": 1, "isCloudy": 1}, "escursionismo": {"no": 0, "si": 0}, "tipoDiViaggio": {"altro": 0, "lavoro": 1, "studio": 1, "vacanza": 2}}	0
27	Tuta	tuta.png	{"relax": {"no": 1, "si": 1}, "sport": {"no": 0, "si": 1}, "montagna": {"no": 1, "si": 2}, "spiaggia": {"no": 1, "si": -1}, "campeggio": {"no": 1, "si": 1}, "compagnia": {"solo": 1, "amici": 1, "coppia": 0, "famiglia": 1}, "dimensione": {"media": 0, "grande": 1, "piccola": 0}, "destinazione": {"isHot": -1, "isCold": 2, "isRainy": 1, "isSnowy": 2, "isSunny": 0, "isWindy": 2, "isCloudy": 1}, "escursionismo": {"no": 0, "si": 2}, "tipoDiViaggio": {"altro": 0, "lavoro": -1, "studio": 1, "vacanza": 1}}	0
25	Asciugamani	asciugamani.png	{"relax": {"no": -1, "si": 1}, "sport": {"no": 0, "si": 1}, "montagna": {"no": 0, "si": 0}, "spiaggia": {"no": -1, "si": 2}, "campeggio": {"no": 1, "si": 1}, "compagnia": {"solo": 1, "amici": 1, "coppia": 1, "famiglia": 1}, "dimensione": {"media": 1, "grande": 2, "piccola": -1}, "destinazione": {"isHot": 0, "isCold": 0, "isRainy": 0, "isSnowy": 0, "isSunny": 0, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 0}, "tipoDiViaggio": {"altro": 0, "lavoro": -1, "studio": 0, "vacanza": 1}}	0
32	Sciarpa	sciarpa.png	{"relax": {"no": 0, "si": 1}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": 3}, "spiaggia": {"no": 0, "si": -2}, "campeggio": {"no": 0, "si": 0}, "compagnia": {"solo": 0, "amici": 0, "coppia": 0, "famiglia": 0}, "dimensione": {"media": 1, "grande": 1, "piccola": 0}, "destinazione": {"isHot": -2, "isCold": 3, "isRainy": 1, "isSnowy": 3, "isSunny": -2, "isWindy": 2, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 0}, "tipoDiViaggio": {"altro": 0, "lavoro": 0, "studio": 0, "vacanza": 0}}	0
31	Macchina fotografica	macchinaFotografica.png	{"relax": {"no": 0, "si": 1}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": 1}, "spiaggia": {"no": 0, "si": -1}, "campeggio": {"no": 0, "si": 1}, "compagnia": {"solo": 1, "amici": 1, "coppia": 2, "famiglia": 1}, "dimensione": {"media": 0, "grande": 2, "piccola": -1}, "destinazione": {"isHot": 0, "isCold": 0, "isRainy": 0, "isSnowy": 0, "isSunny": 0, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 1}, "tipoDiViaggio": {"altro": 0, "lavoro": 0, "studio": 0, "vacanza": 3}}	0
15	Laptop	laptop.png	{"relax": {"no": 1, "si": 1}, "sport": {"no": 0, "si": -1}, "montagna": {"no": 0, "si": -1}, "spiaggia": {"no": 0, "si": -2}, "campeggio": {"no": 2, "si": -2}, "compagnia": {"solo": 1, "amici": 0, "coppia": 0, "famiglia": 0}, "dimensione": {"media": 1, "grande": 1, "piccola": -1}, "destinazione": {"isHot": 0, "isCold": 0, "isRainy": 0, "isSnowy": 0, "isSunny": 0, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 0, "si": -2}, "tipoDiViaggio": {"altro": 0, "lavoro": 15, "studio": 15, "vacanza": -1}}	0
26	Ombrello	ombrello.png	{"relax": {"no": 0, "si": 0}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": 0}, "spiaggia": {"no": 0, "si": -1}, "campeggio": {"no": 0, "si": 0}, "compagnia": {"solo": 0, "amici": 0, "coppia": 0, "famiglia": 0}, "dimensione": {"media": 0, "grande": 1, "piccola": -5}, "destinazione": {"isHot": -2, "isCold": 1, "isRainy": 10, "isSnowy": 1, "isSunny": -2, "isWindy": 0, "isCloudy": 1}, "escursionismo": {"no": 0, "si": 0}, "tipoDiViaggio": {"altro": 0, "lavoro": 0, "studio": 0, "vacanza": 1}}	0
2	Occhiali da sole	occhialiDaSole.png	{"relax": {"no": 0, "si": 0}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": 1}, "spiaggia": {"no": 0, "si": 1}, "campeggio": {"no": 0, "si": 1}, "compagnia": {"solo": 0, "amici": 0, "coppia": 0, "famiglia": 0}, "dimensione": {"media": 0, "grande": 1, "piccola": 0}, "destinazione": {"isHot": 1, "isCold": -1, "isRainy": -1, "isSnowy": 1, "isSunny": 2, "isWindy": 0, "isCloudy": -1}, "escursionismo": {"no": 0, "si": 1}, "tipoDiViaggio": {"altro": 0, "lavoro": 0, "studio": -1, "vacanza": 1}}	0
6	Borraccia	borraccia.png	{"relax": {"no": 0, "si": 0}, "sport": {"no": 0, "si": 2}, "montagna": {"no": 0, "si": 1}, "spiaggia": {"no": 0, "si": 1}, "campeggio": {"no": 0, "si": 2}, "compagnia": {"solo": 1, "amici": 1, "coppia": 0, "famiglia": 1}, "dimensione": {"media": 1, "grande": 2, "piccola": 0}, "destinazione": {"isHot": 2, "isCold": 1, "isRainy": 0, "isSnowy": 0, "isSunny": 1, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 1}, "tipoDiViaggio": {"altro": 0, "lavoro": 0, "studio": 0, "vacanza": 1}}	0
33	Adattatore Universale	adattatoreUniversale.png	{"relax": {"no": 0, "si": 1}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": 0}, "spiaggia": {"no": 0, "si": 0}, "campeggio": {"no": 0, "si": -1}, "compagnia": {"solo": 1, "amici": 0, "coppia": 1, "famiglia": 0}, "dimensione": {"media": 0, "grande": 1, "piccola": 3}, "destinazione": {"isHot": 0, "isCold": 0, "isRainy": 0, "isSnowy": 0, "isSunny": 0, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 0, "si": -1}, "tipoDiViaggio": {"altro": 0, "lavoro": 2, "studio": 1, "vacanza": 1}}	0
37	Maschera da scii	mascheraScii.png	{"relax": {"no": 0, "si": 0}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": 3}, "spiaggia": {"no": 0, "si": -5}, "campeggio": {"no": 0, "si": 0}, "compagnia": {"solo": 0, "amici": 0, "coppia": 0, "famiglia": 0}, "dimensione": {"media": 0, "grande": 1, "piccola": -4}, "destinazione": {"isHot": -4, "isCold": 1, "isRainy": 0, "isSnowy": 5, "isSunny": 0, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 0}, "tipoDiViaggio": {"altro": 0, "lavoro": -2, "studio": -2, "vacanza": 1}}	0
19	Intimo	intimo.png	{"relax": {"no": 0, "si": 1}, "sport": {"no": 0, "si": 1}, "montagna": {"no": 0, "si": 1}, "spiaggia": {"no": 1, "si": 1}, "campeggio": {"no": 0, "si": 1}, "compagnia": {"solo": 1, "amici": 1, "coppia": 1, "famiglia": 1}, "dimensione": {"media": 2, "grande": 3, "piccola": 1}, "destinazione": {"isHot": 1, "isCold": 1, "isRainy": 1, "isSnowy": 1, "isSunny": 1, "isWindy": 1, "isCloudy": 1}, "escursionismo": {"no": 0, "si": 0}, "tipoDiViaggio": {"altro": 0, "lavoro": 0, "studio": 0, "vacanza": 0}}	0
3	Crema solare	cremaSolare.png	{"relax": {"no": 0, "si": 0}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": 1}, "spiaggia": {"no": -1, "si": 4}, "campeggio": {"no": 0, "si": 0}, "compagnia": {"solo": 1, "amici": 0, "coppia": 0, "famiglia": 1}, "dimensione": {"media": 0, "grande": 2, "piccola": -3}, "destinazione": {"isHot": 1, "isCold": -1, "isRainy": -1, "isSnowy": 0, "isSunny": 5, "isWindy": 0, "isCloudy": -5}, "escursionismo": {"no": 0, "si": 0}, "tipoDiViaggio": {"altro": 0, "lavoro": 0, "studio": 0, "vacanza": 2}}	0
34	Medicine	medicine.png	{"relax": {"no": 0, "si": 1}, "sport": {"no": 0, "si": 1}, "montagna": {"no": 0, "si": 2}, "spiaggia": {"no": 0, "si": 0}, "campeggio": {"no": 0, "si": 2}, "compagnia": {"solo": 2, "amici": 0, "coppia": 0, "famiglia": 3}, "dimensione": {"media": 1, "grande": 2, "piccola": -1}, "destinazione": {"isHot": 0, "isCold": 0, "isRainy": 0, "isSnowy": 0, "isSunny": 0, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 1}, "tipoDiViaggio": {"altro": 0, "lavoro": 1, "studio": 1, "vacanza": 1}}	0
36	Abbigliamento da sci	abbigliamentoScii.png	{"relax": {"no": 0, "si": 0}, "sport": {"no": 0, "si": 1}, "montagna": {"no": 0, "si": 2}, "spiaggia": {"no": 0, "si": -3}, "campeggio": {"no": 0, "si": -2}, "compagnia": {"solo": 0, "amici": 0, "coppia": 0, "famiglia": 0}, "dimensione": {"media": 0, "grande": 1, "piccola": -4}, "destinazione": {"isHot": -2, "isCold": 1, "isRainy": -1, "isSnowy": 5, "isSunny": 0, "isWindy": 1, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 0}, "tipoDiViaggio": {"altro": 0, "lavoro": -2, "studio": -2, "vacanza": 2}}	0
35	Carte da gioco	carteDaGioco.png	{"relax": {"no": 0, "si": 2}, "sport": {"no": 0, "si": -2}, "montagna": {"no": 0, "si": 1}, "spiaggia": {"no": 0, "si": 1}, "campeggio": {"no": 0, "si": 2}, "compagnia": {"solo": -7, "amici": 3, "coppia": 1, "famiglia": 2}, "dimensione": {"media": 0, "grande": 1, "piccola": -1}, "destinazione": {"isHot": 0, "isCold": 0, "isRainy": 1, "isSnowy": 1, "isSunny": 0, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 0}, "tipoDiViaggio": {"altro": 0, "lavoro": -2, "studio": -2, "vacanza": 3}}	0
11	Costume da bagno	costumeBagno.png	{"relax": {"no": -1, "si": 1}, "sport": {"no": 0, "si": 1}, "montagna": {"no": 0, "si": -2}, "spiaggia": {"no": -3, "si": 5}, "campeggio": {"no": 0, "si": 0}, "compagnia": {"solo": 0, "amici": 0, "coppia": 0, "famiglia": 0}, "dimensione": {"media": 0, "grande": 1, "piccola": 0}, "destinazione": {"isHot": 7, "isCold": -7, "isRainy": -2, "isSnowy": -4, "isSunny": 2, "isWindy": -1, "isCloudy": -1}, "escursionismo": {"no": 0, "si": 0}, "tipoDiViaggio": {"altro": 0, "lavoro": -1, "studio": -1, "vacanza": 1}}	0
7	Guanti termici	guantiTermici.png	{"relax": {"no": 0, "si": 0}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": 1}, "spiaggia": {"no": 0, "si": -2}, "campeggio": {"no": 0, "si": 1}, "compagnia": {"solo": 0, "amici": 0, "coppia": 0, "famiglia": 0}, "dimensione": {"media": 0, "grande": 1, "piccola": 0}, "destinazione": {"isHot": -2, "isCold": 2, "isRainy": 1, "isSnowy": 3, "isSunny": -1, "isWindy": 1, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 1}, "tipoDiViaggio": {"altro": 0, "lavoro": 0, "studio": 0, "vacanza": 1}}	0
20	Portafoglio	portafoglio.png	{"relax": {"no": 1, "si": 1}, "sport": {"no": 1, "si": 1}, "montagna": {"no": 1, "si": 1}, "spiaggia": {"no": 1, "si": 1}, "campeggio": {"no": 1, "si": 1}, "compagnia": {"solo": 2, "amici": 1, "coppia": 4, "famiglia": 1}, "dimensione": {"media": 1, "grande": 2, "piccola": 0}, "destinazione": {"isHot": 0, "isCold": 0, "isRainy": 0, "isSnowy": 0, "isSunny": 0, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 1, "si": 1}, "tipoDiViaggio": {"altro": 1, "lavoro": 1, "studio": 1, "vacanza": 1}}	0
29	Vestito elegante	vestitoElegante.png	{"relax": {"no": 0, "si": 1}, "sport": {"no": 1, "si": -2}, "montagna": {"no": 1, "si": -1}, "spiaggia": {"no": 1, "si": -2}, "campeggio": {"no": 1, "si": -2}, "compagnia": {"solo": 0, "amici": 1, "coppia": 2, "famiglia": 1}, "dimensione": {"media": 1, "grande": 1, "piccola": 0}, "destinazione": {"isHot": 0, "isCold": 0, "isRainy": 0, "isSnowy": 0, "isSunny": 0, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 1, "si": -2}, "tipoDiViaggio": {"altro": 2, "lavoro": 3, "studio": -1, "vacanza": 1}}	0
1	Zaino	zaino.png	{"relax": {"no": 0, "si": -1}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": 1}, "spiaggia": {"no": 0, "si": 1}, "campeggio": {"no": 0, "si": 1}, "compagnia": {"solo": 1, "amici": 0, "coppia": 0, "famiglia": 1}, "dimensione": {"media": 0, "grande": 1, "piccola": -8}, "destinazione": {"isHot": 1, "isCold": 0, "isRainy": 0, "isSnowy": 0, "isSunny": -1, "isWindy": 1, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 1}, "tipoDiViaggio": {"altro": 0, "lavoro": 1, "studio": 1, "vacanza": 0}}	0
23	Preservativi	preservativi.png	{"relax": {"no": 0, "si": 2}, "sport": {"no": 1, "si": 0}, "montagna": {"no": 0, "si": 0}, "spiaggia": {"no": 0, "si": 0}, "campeggio": {"no": 0, "si": 0}, "compagnia": {"solo": 0, "amici": 1, "coppia": 3, "famiglia": -5}, "dimensione": {"media": 0, "grande": 1, "piccola": 0}, "destinazione": {"isHot": 0, "isCold": 0, "isRainy": 0, "isSnowy": 0, "isSunny": 0, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 0}, "tipoDiViaggio": {"altro": 2, "lavoro": -1, "studio": -1, "vacanza": -1}}	0
8	Telo mare	teloMare.png	{"relax": {"no": 0, "si": 1}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": 0}, "spiaggia": {"no": -2, "si": 4}, "campeggio": {"no": 0, "si": 1}, "compagnia": {"solo": 0, "amici": 0, "coppia": 0, "famiglia": 0}, "dimensione": {"media": 0, "grande": 0, "piccola": 0}, "destinazione": {"isHot": 2, "isCold": -1, "isRainy": -1, "isSnowy": -2, "isSunny": 1, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 0}, "tipoDiViaggio": {"altro": 0, "lavoro": -1, "studio": -1, "vacanza": 1}}	0
38	Saponi	saponi.png	{"relax": {"no": 0, "si": 0}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": 0}, "spiaggia": {"no": 0, "si": 0}, "campeggio": {"no": 0, "si": 6}, "compagnia": {"solo": 1, "amici": 1, "coppia": 1, "famiglia": 1}, "dimensione": {"media": 1, "grande": 2, "piccola": -2}, "destinazione": {"isHot": 0, "isCold": 0, "isRainy": 0, "isSnowy": 0, "isSunny": 0, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 0}, "tipoDiViaggio": {"altro": 0, "lavoro": 0, "studio": 0, "vacanza": 0}}	0
39	Smartwatch	smartwatch.png	{"relax": {"no": 0, "si": -2}, "sport": {"no": 0, "si": 3}, "montagna": {"no": 0, "si": 0}, "spiaggia": {"no": 0, "si": 0}, "campeggio": {"no": 0, "si": 2}, "compagnia": {"solo": 0, "amici": 0, "coppia": 0, "famiglia": 0}, "dimensione": {"media": 0, "grande": 0, "piccola": 0}, "destinazione": {"isHot": 0, "isCold": 0, "isRainy": 0, "isSnowy": 0, "isSunny": 0, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 3}, "tipoDiViaggio": {"altro": 0, "lavoro": 1, "studio": 1, "vacanza": 1}}	0
40	Sacco a pelo	saccoAPelo.png	{"relax": {"no": 0, "si": 0}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": 1}, "spiaggia": {"no": 0, "si": -1}, "campeggio": {"no": 0, "si": 10}, "compagnia": {"solo": 0, "amici": 0, "coppia": 0, "famiglia": 0}, "dimensione": {"media": -4, "grande": 2, "piccola": -10}, "destinazione": {"isHot": 0, "isCold": 0, "isRainy": 0, "isSnowy": 0, "isSunny": 0, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 1}, "tipoDiViaggio": {"altro": 0, "lavoro": -1, "studio": -1, "vacanza": 0}}	0
41	Makeup	makeup.png	{"relax": {"no": 0, "si": 0}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": 0}, "spiaggia": {"no": 0, "si": 0}, "campeggio": {"no": 0, "si": 0}, "compagnia": {"solo": 1, "amici": 1, "coppia": 0, "famiglia": 1}, "dimensione": {"media": 1, "grande": 2, "piccola": 0}, "destinazione": {"isHot": 0, "isCold": 0, "isRainy": 0, "isSnowy": 0, "isSunny": 0, "isWindy": 0, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 0}, "tipoDiViaggio": {"altro": 0, "lavoro": 2, "studio": -1, "vacanza": 3}}	0
30	Libro	libro.png	{"relax": {"no": 0, "si": 1}, "sport": {"no": 0, "si": -1}, "montagna": {"no": 0, "si": 1}, "spiaggia": {"no": 0, "si": 1}, "campeggio": {"no": 0, "si": 1}, "compagnia": {"solo": 4, "amici": 0, "coppia": 0, "famiglia": 1}, "dimensione": {"media": 1, "grande": 2, "piccola": 0}, "destinazione": {"isHot": 0, "isCold": 0, "isRainy": 0, "isSnowy": 0, "isSunny": 0, "isWindy": 0, "isCloudy": 1}, "escursionismo": {"no": 0, "si": 0}, "tipoDiViaggio": {"altro": 1, "lavoro": 1, "studio": 2, "vacanza": 0}}	0
28	Giubbino	giubbino.png	{"relax": {"no": 0, "si": 0}, "sport": {"no": 0, "si": 0}, "montagna": {"no": 0, "si": 3}, "spiaggia": {"no": 0, "si": -2}, "campeggio": {"no": 0, "si": 1}, "compagnia": {"solo": 0, "amici": 0, "coppia": 0, "famiglia": 0}, "dimensione": {"media": 0, "grande": 2, "piccola": -1}, "destinazione": {"isHot": -7, "isCold": 7, "isRainy": 3, "isSnowy": 3, "isSunny": -1, "isWindy": 2, "isCloudy": 0}, "escursionismo": {"no": 0, "si": 1}, "tipoDiViaggio": {"altro": 0, "lavoro": 0, "studio": 0, "vacanza": 0}}	0
\.


--
-- Data for Name: utenti; Type: TABLE DATA; Schema: public; Owner: www
--

COPY public.utenti (id, username, password, email, domanda_sicurezza, risposta_sicurezza, info, preferences, created_at) FROM stdin;
2	landigf	$2y$10$bO6pHAZgl6iAlD0x.wvyIeAVdJLTcMbI/hy2IRc8fp3du91p/7i4S	g.landi83@studenti.unisa.it	firstPetName	Lucky	{"nome": "Gennaro Francesco", "cognome": "Landi"}	{"sortOrder": "DESC", "sortCriteria": "created_at"}	2025-01-31 12:39:15.870593
3	elli	$2y$10$e9aJ3zr3PkG7./9zjYTLS.3Y91g8q2ZZcQF16MZ6OOuLdzAekZaGu	e.palmisano1@studenti.unisa.it	favoriteColor	Rosa	{"nome": "Elettra", "cognome": "Palmisano"}	{"sortOrder": "DESC", "sortCriteria": "created_at"}	2025-01-31 12:42:12.58907
4	mauro03	$2y$10$utG0ccxPGhp6vfsbhhtmoOpRIMWGBtH5yRlbx4HGDjwiSXy9u4tpG	m.melillo34@studenti.unisa.it	favoriteColor	Rosso	{"nome": "Maurizio", "cognome": "Melillo"}	{"sortOrder": "DESC", "sortCriteria": "created_at"}	2025-01-31 12:44:51.244937
5	mrossi	$2y$10$aJVXJ7rBZLAY5HWzkR8lFeRMrBl81gTBs0qt9GCf.KM0BUawLlLvi	mario.rossi@gmail.com	favoriteColor	Rosso	{"nome": "Mario", "cognome": "Rossi"}	{"sortOrder": "DESC", "sortCriteria": "created_at"}	2025-01-31 12:46:23.309396
\.


--
-- Data for Name: valigie; Type: TABLE DATA; Schema: public; Owner: www
--

COPY public.valigie (id, nome, id_utente, preferenze, lista_oggetti, created_at) FROM stdin;
6	Barcellona con Alice	5	{"date": ["2025-01-31", "2025-02-02"], "attivita": ["relax"], "compagnia": "coppia", "dimensione": "piccola", "destinazione": "Barcelona", "tipoDiViaggio": "vacanza"}	{"Tuta": 2, "Intimo": 2, "Portafoglio": 1, "Preservativi": 1, "Carte da gioco": 1, "Vestito elegante": 1, "Macchina fotografica": 1, "Spazzolino e dentifricio": 1}	2025-01-31 12:51:50.592693
7	Sacco	5	{"date": ["2025-02-28", "2025-03-14"], "attivita": ["montagna", "escursionismo", "campeggio"], "compagnia": "famiglia", "dimensione": "grande", "destinazione": "Sacco", "tipoDiViaggio": "altro"}	{"Intimo": 2, "Giubbino": 1, "Medicine": 1, "Borraccia": 1, "Sacco a pelo": 1, "Carte da gioco": 1, "Scarpe da trekking": 1, "Tenda da campeggio": 1, "Macchina fotografica": 1}	2025-01-31 12:53:28.441281
2	Salerno	2	{"date": ["2025-01-31", "2025-02-05"], "attivita": ["relax"], "compagnia": "famiglia", "dimensione": "media", "destinazione": "Salerno", "tipoDiViaggio": "vacanza"}	{"Pigiama": 1, "Giubbino": 1, "Ombrello": 2, "Caricabatterie": 1, "Carte da gioco": 1, "Spazzolino e dentifricio": 1}	2025-01-31 12:40:33.638822
3	Tokyo	3	{"date": ["2025-02-05", "2025-02-08"], "attivita": ["escursionismo", "relax"], "compagnia": "coppia", "dimensione": "grande", "destinazione": "Tokyo", "tipoDiViaggio": "vacanza"}	{"Tuta": 1, "Pigiama": 1, "Cappello": 1, "Borraccia": 1, "Asciugamani": 1, "Portafoglio": 1, "Scarpe da ginnastica": 1, "Spazzolino e dentifricio": 1}	2025-01-31 12:43:42.698162
4	Avellino	4	{"date": ["2025-01-31", "2025-02-01"], "attivita": ["montagna", "sport", "campeggio"], "compagnia": "solo", "dimensione": "piccola", "destinazione": "Avellino", "tipoDiViaggio": "studio"}	{"Tuta": 1, "Libro": 1, "Laptop": 1, "Giubbino": 1, "Smartwatch": 1, "Caricabatterie": 1, "Scarpe da ginnastica": 1}	2025-01-31 12:45:41.588141
5	Parigi per lavoro	5	{"date": ["2025-02-04", "2025-02-09"], "attivita": ["sport", "relax"], "compagnia": "amici", "dimensione": "media", "destinazione": "Paris", "tipoDiViaggio": "lavoro"}	{"Tuta": 1, "Intimo": 2, "Laptop": 1, "Pigiama": 1, "Giubbino": 1, "Portafoglio": 1, "Caricabatterie": 1, "Vestito elegante": 1, "Spazzolino e dentifricio": 1}	2025-01-31 12:48:47.575186
\.


--
-- Name: oggetti_id_seq; Type: SEQUENCE SET; Schema: public; Owner: www
--

SELECT pg_catalog.setval('public.oggetti_id_seq', 41, true);


--
-- Name: utenti_id_seq; Type: SEQUENCE SET; Schema: public; Owner: www
--

SELECT pg_catalog.setval('public.utenti_id_seq', 5, true);


--
-- Name: valigie_id_seq; Type: SEQUENCE SET; Schema: public; Owner: www
--

SELECT pg_catalog.setval('public.valigie_id_seq', 7, true);


--
-- Name: oggetti oggetti_nome_key; Type: CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.oggetti
    ADD CONSTRAINT oggetti_nome_key UNIQUE (nome);


--
-- Name: oggetti oggetti_pkey; Type: CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.oggetti
    ADD CONSTRAINT oggetti_pkey PRIMARY KEY (id);


--
-- Name: utenti utenti_email_key; Type: CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.utenti
    ADD CONSTRAINT utenti_email_key UNIQUE (email);


--
-- Name: utenti utenti_pkey; Type: CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.utenti
    ADD CONSTRAINT utenti_pkey PRIMARY KEY (id);


--
-- Name: valigie valigie_pkey; Type: CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.valigie
    ADD CONSTRAINT valigie_pkey PRIMARY KEY (id);


--
-- Name: valigie valigie_id_utente_fkey; Type: FK CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.valigie
    ADD CONSTRAINT valigie_id_utente_fkey FOREIGN KEY (id_utente) REFERENCES public.utenti(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

