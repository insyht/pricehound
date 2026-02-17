Database resetten:
- ddev artisan migrate:fresh && ddev artisan db:seed --class=Test
Scheduler draaien (die regelmatig FetchScheduler aanroept; zie routes/console.php):
- ddev artisan schedule:work
Queue verwerken:
- ddev artisan queue:work
FetchScheduler handmatig runnen:
- ddev artisan app:fetch-scheduler

Todo:

- Een route maken waarbij je de prijsgeschiedenis van een product kunt opvragen, 1 tabel per shop (zie ook prices.index voor een visueel voorbeeld)




##########################
Aanpassingen voor de Hounds:
- Een gebruiker is gekoppeld aan een Hound, met een api key en api url --> API endpoint om een api key te verifiëren (HoundEndpoints::profile, waarbij we active=true terugkrijgen als de key geldig is)
- Een gebruiker heeft een interval wat bepaalt hoe vaak we een fetch doen (dit interval krijgen we van de Hound, we halen dit dagelijks op), dit is een column op de users tabel (fetch_interval) ---> API endpoint om een fetch interval op te halen van een user (HoundEndpoints::profile, waarbij we interval=[int] terugkrijgen)

Huidige (incorrecte) volgorde: console.php > FetchPrices > FetchPricesByHound > FetchPrice
