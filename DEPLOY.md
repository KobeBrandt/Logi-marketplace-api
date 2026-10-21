# Deploying to logiplugin.com

Production runs on an OVHcloud VPS with Docker, using `docker-compose.prod.yml`:

- **app**: PHP/Apache with the code baked into the image (no database, file cache)
- **caddy**: reverse proxy that gets and renews HTTPS certificates for `logiplugin.com` automatically, and redirects `www.logiplugin.com` to it

The regular `docker-compose.yml` is for local development only.

## One-time setup

1. **DNS**: at your domain registrar, create these records pointing to the VPS IP:
   - `A` record for `logiplugin.com` → VPS IPv4
   - `A` record for `www.logiplugin.com` → VPS IPv4
   - (optional) matching `AAAA` records if the VPS has IPv6

   Make sure the records resolve (`dig +short logiplugin.com`) before starting Caddy, otherwise certificate issuance fails.

2. **Firewall**: allow ports 22, 80 and 443 (TCP, plus 443/UDP for HTTP/3), both in `ufw` (if enabled) and in the OVHcloud network firewall (if enabled).

3. **Docker**: install Docker Engine + the compose plugin (https://docs.docker.com/engine/install/).

4. **GitHub access**: the repo is private, so the VPS reads it with a deploy key (read-only, limited to this one repo). On the VPS, as the user that runs deploys:
   ```bash
   ssh-keygen -t ed25519 -C "logiplugin-vps" -f ~/.ssh/logiplugin_deploy -N ""
   cat ~/.ssh/logiplugin_deploy.pub
   ```
   On GitHub, open the repo's **Settings → Deploy keys → Add deploy key**, paste the public key and leave **Allow write access** unchecked. Then tell SSH to use that key for GitHub:
   ```bash
   cat >> ~/.ssh/config <<'EOF'
   Host github.com
       User git
       IdentityFile ~/.ssh/logiplugin_deploy
       IdentitiesOnly yes
   EOF
   chmod 600 ~/.ssh/config
   ssh -T git@github.com   # should greet you with "successfully authenticated"
   ```

5. **Code and config**:
   ```bash
   sudo mkdir -p /opt/logiplugin && sudo chown "$USER": /opt/logiplugin
   git clone git@github.com:KobeBrandt/Logiplugin.git /opt/logiplugin
   cd /opt/logiplugin
   cp .env.production.example .env.production
   echo "APP_KEY=base64:$(openssl rand -base64 32)"   # paste into .env.production
   ```

6. **Start**:
   ```bash
   docker compose -f docker-compose.prod.yml up -d --build
   ```

## Deploying updates

```bash
cd /opt/logiplugin && ./deploy.sh
```

This pulls the latest code, rebuilds the image and restarts the containers. Config, route and view caches are rebuilt when the container starts.

## Useful commands

```bash
docker compose -f docker-compose.prod.yml logs -f app     # application logs
docker compose -f docker-compose.prod.yml logs -f caddy   # certificate / proxy logs
docker compose -f docker-compose.prod.yml exec app php artisan cache:clear
```
