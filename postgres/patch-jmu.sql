create domain d_mutato as
  integer not null
  constraint hibas_keszultseg_mutato_ertek
  check(value between 0 and 4);

--  jogosultsági szintek
create domain d_jog as
  integer not null default 1
  constraint hibas_jogosutsag_ertek
  check(value between 0 and 9);

-- járműtípus
create domain d_jarmutipus as
  varchar(20) not null;

-- elvégzendő munkafázisok nevei  
create domain d_munka as
  varchar(75) not null;
  
-- az elvégzett munka "értéke" a teljes kocsi elkészüléséhez
create domain d_ertek as
  numeric (5,3) not null constraint hibas_keszultsegi_fok_szorzo  check(value >=0);

-- SAP rendelések (SD, PP)
create domain d_sap8 as
  varchar(8) default null;

-- pályaszám, rendszám
create domain d_psz as
  varchar(10) constraint a_psz_nem_lehet_ures not null;
  
-- egyedi szám, alvázszám, stb.
create domain d_esz as
  varchar(25) default null;
  

create sequence seq_uj_jarmu_alap start 1;
create table uj_jarmu_alap (
  jarmutipus d_jarmutipus references tipus(id) on update cascade,
  ev integer constraint ervenytelen_ev check(ev >= 2014),
  sorszam integer constraint ervenytelen_sorszam check(sorszam > 0),
  psz d_psz,
  esz d_esz,
  sd_al d_sap8 constraint mar_letezo_sd_al_rendeles unique,
  sd_op d_sap8 constraint mar_letezo_sd_op_rendeles unique,
  pp_al d_sap8 constraint mar_letezo_pp_al_rendeles unique,
  pp_op d_sap8 constraint mar_letezo_pp_op_rendeles unique,
  erkezett date,
  munkabavetel date constraint korai_munkabavetel check(munkabavetel >= erkezett),
  allapotfelvetel date constraint korai_allapotfelvetel check(allapotfelvetel >= erkezett),
  reszatvetel date constraint korai_reszszamla check(reszatvetel >= erkezett),
  vegatvetel date constraint korai_vegatvetel check(vegatvetel >= erkezett),
  hazaadas date constraint korai_hazaadas check(hazaadas >= erkezett),
  szamlazas date constraint korai_szamlazas check(szamlazas>=hazaadas),
  megjegyzes text default null,
  terv_atfutasi_ido integer not null default 1,
  id integer default nextval('seq_uj_jarmu_alap') primary key,
  constraint letezo_pp_vagy_sd_rendelesek unique(sd_al,sd_op,pp_al,pp_op)
)with oids;

-- jarmu_alap cseréjéhez
create sequence seq_jarmu_alap start 1;
create table jarmu_alap (
  jarmutipus d_jarmutipus references tipus(id) on update cascade,
  ev integer constraint ervenytelen_ev check(ev >= 2014),
  sorszam integer constraint ervenytelen_sorszam check(sorszam > 0),
  psz d_psz,
  esz d_esz,
  sd_al d_sap8 constraint mar_letezo_sd_al_rendeles unique,
  sd_op d_sap8 constraint mar_letezo_sd_op_rendeles unique,
  pp_al d_sap8 constraint mar_letezo_pp_al_rendeles unique,
  pp_op d_sap8 constraint mar_letezo_pp_op_rendeles unique,
  erkezett date,
  munkabavetel date constraint korai_munkabavetel check(munkabavetel >= erkezett),
  allapotfelvetel date constraint korai_allapotfelvetel check(allapotfelvetel >= erkezett),
  reszatvetel date constraint korai_reszszamla check(reszatvetel >= erkezett),
  vegatvetel date constraint korai_vegatvetel check(vegatvetel >= erkezett),
  hazaadas date constraint korai_hazaadas check(hazaadas >= erkezett),
  szamlazas date constraint korai_szamlazas check(szamlazas>=hazaadas),
  megjegyzes text default null,
  terv_atfutasi_ido integer not null default 1,
  id integer default nextval('seq_jarmu_alap') primary key,
  constraint letezo_pp_vagy_sd_rendelesek unique(sd_al,sd_op,pp_al,pp_op)
)with oids;

-- uj tipus funkció
create or replace function ujtipus(text) returns integer as
$$
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
$$
language plpgsql;

create or replace function tipus_torlese(text) returns integer as
$$
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
$$
language plpgsql;

