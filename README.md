
# Maréa - Gestione Ombrelloni, Ordini e Prodotti

Marea è una webapp PHP/MySQL per la gestione digitale di uno stabilimento balneare: i clienti ordinano dal proprio ombrellone tramite PIN, lo staff gestisce ordini, prodotti e ombrelloni da un pannello amministrativo moderno e responsive.

## Funzionalità principali

### Area Clienti
- Login tramite PIN ombrellone
- Visualizzazione e gestione del carrello
- Ordine di prodotti (cibo, bevande, ecc.)
- Visualizzazione stato ordini e storico

### Area Amministratore
- Login amministratore sicuro
- Dashboard con statistiche (ordini, prodotti, ombrelloni)
- Gestione ordini (stato, dettaglio, eliminazione)
- Gestione prodotti (CRUD, immagini, categorie)
- Gestione ombrelloni (aggiunta, modifica, eliminazione)

## Struttura del progetto

```
/admin/           → Area amministrativa (dashboard, gestione ordini/prodotti/ombrelloni, login admin)
/clienti/         → Area clienti (login PIN, home, carrello, ordini, prodotti)
/includes/        → Funzioni PHP, connessione DB, template header/footer
/assets/          → Risorse statiche (css, js, immagini, favicon)
```

## Tecnologie utilizzate

- PHP 7+
- MySQL
- Bootstrap 5 & Bootstrap Icons
- HTML5, CSS3, JavaScript

## Installazione

1. Clona il repository
  ```sh
  git clone https://github.com/yacktonit/marea.git
  ```
2. Configura il database
  - Importa lo schema SQL fornito (non incluso qui).
  - Modifica `/includes/db.php` con le tue credenziali MySQL.
3. Configura XAMPP o un webserver compatibile
  - Assicurati che la cartella sia accessibile da Apache (es: `/Applications/XAMPP/xamppfiles/htdocs/marea`).
4. Imposta i permessi di scrittura (per upload immagini prodotti):
  - `chmod 777 uploads/` (solo in ambiente di sviluppo!)
5. Accedi
  - Area clienti: `/clienti/index.php`
  - Area admin: `/admin/login.php`

## Sicurezza

- Password admin con hash sicuro
- Sessioni per autenticazione
- Dati sanificati per prevenire XSS/SQLi
- Area admin protetta da login

## Personalizzazione

- Modifica i template in `/includes/template/` per cambiare header/footer
- Aggiungi/rimuovi prodotti e categorie dall’area admin
- Personalizza i colori modificando le variabili CSS nei template

## Autore

- Sviluppato da [yacktonit](https://github.com/yacktonit)

---

Per segnalare bug o proporre miglioramenti, apri una issue su GitHub!
