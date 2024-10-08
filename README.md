# Orizon 

Orizon è un’agenzia di viaggi nata per far scoprire alle persone tipologie di viaggio sostenibili, in grado di ampliare i loro orizzonti. L’obiettivo del progetto è quello di realizzare delle API JSON RESTful le quali aiuteranno l’agenzia a sponsorizzare le offerte dell’ultimo minuto.
L’obiettivo delle API consentono l’inserimento, la modifica, e la cancellazione di un paese che avrà una sola caratteristica: il nome e l’inserimento, la modifica e la cancellazione di un viaggio che avrà le seguenti caratteristiche: i paesi che coinvolgono il viaggio, il numero di posti disponibili. Esse infine consentono di visualizzare tutti i viaggi inseriti, filtrarli per paesi e per numero di posti disponibili.

## Requisiti

Prima di iniziare, assicurati di avere installato:

- **PHP** >= 7.4
- **MySQL** >= 5.7
- **Apache** (o server locale come XAMPP, MAMP, WAMP, LAMP)
- **Postman o REST Client APIsHub** o un altro strumento per testare API REST

## Installazione

1. **Clona il repository**:

   ```bash
   git clone https://github.com/frafiore96/orizon.git
   cd orizon

2. **Configura il database**:

Crea un database MySQL chiamato 'Orizon';
Importa il file migrations.sql per creare le tabelle necessarie.
Modifica confid/database.php con le tue credenziali.

3. **Testalo**:

Testalo utilizzando Postman o REST Client APIsHub.

