FROM node:20.18.1-bookworm-slim

# Bot víctima compartido de los labs de XSS y CSRF (entorno local controlado).
# Chromium headless que "visita" lo que le mandan los niveles, con la sesión
# del admin del nivel. No se publica en ningún puerto: solo lo ven los niveles
# por la red interna labs-bot.

RUN apt-get update \
    && apt-get install -y --no-install-recommends chromium fonts-liberation ca-certificates \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /bot
COPY bot/package.json bot/bot.js ./
RUN npm install --omit=dev --no-audit --no-fund && chown -R node:node /bot

USER node
ENV CHROME_PATH=/usr/bin/chromium
EXPOSE 3000
CMD ["node", "bot.js"]
