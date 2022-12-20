## CE SCRIPT TRANSFERE LES DONNEES DE L'API EN LIGNE VERS LA BD
import requests as requests
from string import Template

def main():
    r = requests.get(url="https://opendata.paris.fr/api/records/1.0/search/?dataset=fontaines-a-boire&q=&rows=10000&facet=type_objet&facet=modele&facet=commune&facet=dispo")
    fontaines = r.json()

    sql = Template('''INSERT INTO GEOML(ID,Disponible,Rue,Coords,ID_Groupe) VALUES(NULL,$dispo,"$rue",ST_GeomFromText('POINT($lat $long)'),NULL);
    INSERT INTO FONTAINE(ID,Disponible,Rue,Coords,ID_Groupe) VALUES(NULL,$dispo,"$rue",ST_GeomFromText('POINT($lat $long)'),NULL);''')

    with open('sqlFiles/data.sql', 'w') as file:

        for fontaine in fontaines['records']:
            rue = ""
            if ('no_voirie_impair' in fontaine['fields']) :
                rue += fontaine['fields']['no_voirie_impair']
            elif ('no_voirie_pair' in fontaine['fields']) :
                rue += fontaine['fields']['no_voirie_pair']
            if ('voie' in fontaine['fields']) : 
                rue += " " + fontaine['fields']['voie']
            rue.strip()

            file.write(sql.substitute(
                dispo = ('TRUE' if fontaine['fields']['dispo'] == "OUI" else 'FALSE'),
                rue = rue,
                lat = fontaine['fields']['geo_shape']['coordinates'][0],
                long = fontaine['fields']['geo_shape']['coordinates'][1]
            ))
            file.write("\n")

main()
