Technival inschrijflijst
=========================

Deze webapplicatie werkt als een inschrijflijst voor optionele onderdelen van Technival, maar is in principe ook geschikt voor andere evenementen.
Dit inschrijven kan voor verschillende momenten waarop het onderdeel zal plaatsvinden.
De applicatie kent twee hoofdonderdelen: een inschrijfformulier en een lijst van ingeschreven personen.
Daarnaast is er nog een formulier waarmee aan ieder moment waarop het onderdeel plaatsvindt een beschrijving kan worden toegekend, met daarin bijvoorbeeld de begin- end eindtijd.
Niet-beschreven momenten zullen enkel worden aangeduid met een nummer.


## Technische details

### Database structuur

De database bestaat uit twee tabellen per onderdeel:
 * Een deelnemerstabel, waarin de naam van de deelnemer staat alsmede het nummer corresponderend met het moment waarop deze deelnemer deelneemt.
 * Een momententabel, waarin alle momenten staan die een beschrijving hebben. Deze tabel bevat voor ieder moment het aanduidende nummer en de textuele beschrijving.

Verschillende onderdelen kunnen worden opgeslagen in eigen tabellen in dezelfde database of in verschillende databases, naar inzicht van de front-end ontwikkelaar.


## Huidige status

Momenteel is dit project geschikt als inschrijflijst voor één onderdeel dat meerdere malen plaatsvindt.
Wanneer meerdere onderdelen plaatsvinden, zullen van de formulieren en deelnemerslijst voor ieder onderdeel aparte kopieën moeten worden gemaakt.
De backend kan wel gemeenschappelijk worden gebruikt.
Verder bevat de applicatie uitsluitend functionele GUI-elementen en nog geen decoratie/logos of gestileerde elementen.
Ook de mogelijkheid om ingeschreven deelnemers uit te schrijven ontbreekt nog.
