update ps_customer c
set email =(case 
when (c.id_customer % 3) = 0 then 'damien.collado@provost.fr'
when (c.id_customer % 3) = 1 then 'antoine.herman@provost.fr'
when (c.id_customer % 3) = 2 then 'thierry.gozdzicki@provost.fr'
end)
where email <> 'damien.collado@provost.fr' 
and email <> 'antoine.herman@provost.fr' 
and email <> 'thierry.gozdzicki@provost.fr';

update ps_supplier c
set emails =(case 
when (c.id_supplier % 3) = 0 then 'damien.collado@provost.fr'
when (c.id_supplier % 3) = 1 then 'antoine.herman@provost.fr'
when (c.id_supplier % 3) = 2 then 'thierry.gozdzicki@provost.fr'
end);

UPDATE `ps_configuration` set value='damien.collado@provost.fr' WHERE name= 'PS_SHOP_EMAIL';