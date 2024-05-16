create unique index if not exists dbparamname_idx on dbparam (dbparam_name);
insert into dbparam (dbparam_name, dbparam_value, dbparam_description, dbparam_description_en)
values (
'APPLI_code', 
'APP_code',
'Code de l''application, pour les exportations',
'Code of the application, to export data'
) 
on conflict do nothing;
