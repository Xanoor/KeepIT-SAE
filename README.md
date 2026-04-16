# sae-web

## 📊 API R (Plumber) – Installation

Cette API R (via **Plumber**) est utilisée pour alimenter le dashboard en données.

---

### 🐧 Ubuntu / Debian

#### 1. Dépendances

```bash
sudo apt update
sudo apt install r-base libsodium-dev libcurl4-openssl-dev libssl-dev -y
```

#### 2. Installer Plumber

```bash
sudo -i R
```

```r
install.packages("plumber", repos="https://cloud.r-project.org")
q()
```

#### 3. Lancer en service

Créer `/etc/systemd/system/r-stats-api.service` :

```ini
[Unit]
Description=API R Plumber
After=network.target

[Service]
ExecStart=/usr/bin/Rscript -e 'library(plumber); pr <- plumb("router.R"); pr$run(host="0.0.0.0", port=8000)'
WorkingDirectory=/var/www/r-api
Restart=always
User=www-data

[Install]
WantedBy=multi-user.target
```

Puis :

```bash
sudo systemctl daemon-reload
sudo systemctl enable r-stats-api.service
sudo systemctl start r-stats-api.service
```

---

### 🪟 Windows (XAMPP)

#### 1. Installer Plumber

```r
install.packages("plumber")
```

#### 2. Configurer le `.bat`

```bat
@echo off
"C:\Program Files\R\R-4.4.3\bin\Rscript.exe" -e "pr <- plumber::pr('D:/sae-web/Conception/Statistiques/router.R'); pr$run(port=8000)"
pause
```

Adapter :

- chemin vers R
- chemin vers `router.R`

#### 3. Lancer

- Exécuter le `.bat`
- ⚠️ laisser la fenêtre ouverte

---

## ⚠️ Important

- L’API doit être lancée pour que le dashboard fonctionne
- Redémarrer après modification :
- Linux → `systemctl restart`
- Windows → relancer le `.bat`
