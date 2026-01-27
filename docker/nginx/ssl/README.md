# Generowanie certyfikatu SSL dla BookRoom (development)

## Opcja 1: Użycie Docker (zalecane)

```bash
docker run --rm -v ${PWD}/docker/nginx/ssl:/certs alpine/openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /certs/nginx-selfsigned.key -out /certs/nginx-selfsigned.crt -subj "/C=PL/ST=Poland/L=Warsaw/O=BookRoom/CN=localhost"
```

## Opcja 2: Użycie OpenSSL lokalnie

```bash
cd docker/nginx/ssl
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout nginx-selfsigned.key -out nginx-selfsigned.crt -subj "/C=PL/ST=Poland/L=Warsaw/O=BookRoom/CN=localhost"
```

## Opcja 3: Użycie skryptu generate-cert.sh

```bash
cd docker/nginx/ssl
bash generate-cert.sh
```

## Po wygenerowaniu certyfikatu:

1. Uruchom kontenery: `docker-compose up -d`
2. Aplikacja będzie dostępna pod:
   - HTTP: http://localhost:8080 (przekierowanie na HTTPS)
   - HTTPS: https://localhost:8443

**Uwaga**: Self-signed certificate wywoła ostrzeżenie w przeglądarce - to normalne dla środowiska deweloperskiego.
