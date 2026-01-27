#!/bin/bash
# Skrypt do generowania self-signed SSL certificate dla BookRoom

openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout nginx-selfsigned.key \
  -out nginx-selfsigned.crt \
  -subj "/C=PL/ST=Poland/L=Warsaw/O=BookRoom/CN=localhost"

echo "SSL certificates generated successfully!"
echo "Files created:"
echo "  - nginx-selfsigned.key"
echo "  - nginx-selfsigned.crt"
