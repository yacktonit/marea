# 🏖️ Web App Ordini da Ombrelloni

Questa è una **web app PHP/MySQL** pensata per la gestione degli ordini in uno stabilimento balneare. I clienti possono ordinare direttamente dal loro ombrellone utilizzando un PIN, e il personale può gestire lo stato degli ordini tramite un'interfaccia di amministrazione.

## 📁 Struttura del progetto

```
/ (root)
├── index.php                  # Homepage / accesso con PIN cliente
├── home.php                   # Homepage dopo login cliente
├── ordini_cliente.php         # Lista degli ordini effettuati dal cliente
├── dettaglio_ordine_cliente.php # Dettaglio di un singolo ordine
├── logout.php                 # Logout sessione cliente
├── admin/                     # Area amministrazione (con login protetto)
│   ├── login.php              # Login amministratore
│   ├── dashboard.php          # Pannello gestione ordini
│   ├── ...
├── includes/
│   └── db.php                 # Connessione al database
├── css/
│   └── styles.css             # Stili personalizzati (opzionale)
└── README.md                  # Questo file
```

## 🧑‍💻 Tecnologie utilizzate

- **PHP** 7+
- **MySQL** (phpMyAdmin per la gestione)
- **HTML5/CSS3**
- **JavaScript** (opzionale)
- **Sessioni PHP** per la gestione login/utente

## ⚙️ Setup del progetto

### 1. Requisiti

- XAMPP, MAMP o qualsiasi server Apache con PHP e MySQL
- phpMyAdmin (facoltativo ma consigliato)
- Editor di codice (VS Code, PhpStorm, etc.)

### 2. Importa il database

1. Apri `phpMyAdmin`
2. Crea un nuovo database chiamato ad esempio `marea`
3. Esegui lo script SQL che crea le seguenti tabelle:

```sql
CREATE TABLE ombrelloni (
  id INT AUTO_INCREMENT PRIMARY KEY,
  numero INT NOT NULL,
  pin VARCHAR(10) NOT NULL
);

CREATE TABLE ordini (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ombrellone INT NOT NULL,
  data_ora DATETIME NOT NULL,
  stato VARCHAR(50) NOT NULL DEFAULT 'inviato',
  totale DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (id_ombrellone) REFERENCES ombrelloni(id)
);

CREATE TABLE ordine_dettagli (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ordine INT NOT NULL,
  nome_prodotto VARCHAR(255) NOT NULL,
  quantita INT NOT NULL,
  prezzo DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (id_ordine) REFERENCES ordini(id)
);
```

### 3. Configura la connessione al DB

Modifica `includes/db.php`:

```php
<?php
$host = 'localhost';
$db = 'marea';
$user = 'root';
$pass = ''; // o la tua password MySQL

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connessione fallita: " . $e->getMessage());
}
?>
```

## ✅ Funzionalità principali

### Cliente

- 🔑 Login con PIN ombrellone
- 🧾 Visualizzazione ordini passati (`ordini_cliente.php`)
- 👁️ Dettagli di ogni ordine
- 🚪 Logout

### Admin (se implementata)

- 👨‍💼 Login amministratore
- 📦 Visualizzazione e aggiornamento ordini
- 📊 Statistiche (opzionale)

## 🎨 Styling

Per ora lo stile è semplice e minimale (tabella HTML), ma può essere migliorato con:

- Bootstrap / TailwindCSS
- Font personalizzati
- Responsive design per dispositivi mobili

## 🔒 Sicurezza (consigli)

- Sanificare tutti i dati in input/output
- Proteggere le aree admin con sessioni e controlli di accesso
- Utilizzare HTTPS in produzione
- Limitare l’accesso diretto ai file sensibili

## 📌 Note

- Il codice è scritto in PHP nativo, senza framework.
- Ottimo per progetti scolastici, prototipi o gestione semplice di ordini su spiaggia o campeggio.
- Può essere facilmente esteso con notifiche, pagamenti, codice QR, ecc.

## ✍️ Autore

Antonio Tomaselli  
📅 Agosto 2025
