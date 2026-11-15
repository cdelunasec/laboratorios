// Bot víctima de los labs (local y controlado).
//
// POST /visit  {"url": "...", "cookies": [{name, value, domain, httpOnly}], "click": "#selector"}
//   Abre la URL en un contexto limpio de Chromium con esas cookies (la sesión
//   del admin del nivel), opcionalmente hace clic en un elemento, espera unos
//   segundos y cierra. Responde cuando termina.
//
// Solo acepta URLs de *.labs.test (los niveles y el servidor de exploits).

const http = require('http');
const puppeteer = require('puppeteer-core');

const PORT = 3000;
const DOMINIO = /^https?:\/\/[a-z0-9-]+\.labs\.test(:\d+)?(\/|$)/i;
const ESPERA_MS = 4000;

let browser = null;

async function navegador() {
  if (!browser || !browser.connected) {
    browser = await puppeteer.launch({
      executablePath: process.env.CHROME_PATH || '/usr/bin/chromium',
      headless: true,
      args: ['--no-sandbox', '--disable-gpu', '--disable-dev-shm-usage', '--no-first-run'],
    });
  }
  return browser;
}

async function visitar({ url, cookies = [], click = null }) {
  const b = await navegador();
  const ctx = await b.createBrowserContext();
  const page = await ctx.newPage();
  page.on('dialog', (d) => d.dismiss().catch(() => {}));
  try {
    if (cookies.length) {
      await page.setCookie(...cookies.map((c) => ({
        name: c.name,
        value: c.value,
        domain: c.domain,
        path: c.path || '/',
        httpOnly: !!c.httpOnly,
        sameSite: c.sameSite || 'Lax',
      })));
    }
    await page.goto(url, { timeout: 8000, waitUntil: 'load' }).catch(() => {});
    if (click) {
      await page.click(click).catch(() => {});
    }
    await new Promise((r) => setTimeout(r, ESPERA_MS));
  } finally {
    await ctx.close().catch(() => {});
  }
}

// Una visita a la vez: el admin es una sola persona.
let cola = Promise.resolve();

http.createServer((req, res) => {
  if (req.method !== 'POST' || req.url !== '/visit') {
    res.writeHead(404).end('{"error":"usa POST /visit"}\n');
    return;
  }
  let body = '';
  req.on('data', (c) => { body += c; if (body.length > 65536) req.destroy(); });
  req.on('end', () => {
    let job;
    try {
      job = JSON.parse(body);
    } catch {
      res.writeHead(400).end('{"error":"JSON inválido"}\n');
      return;
    }
    if (typeof job.url !== 'string' || !DOMINIO.test(job.url)) {
      res.writeHead(400).end('{"error":"solo visito URLs de *.labs.test"}\n');
      return;
    }
    cola = cola.then(() => visitar(job)).then(
      () => res.writeHead(200).end('{"ok":true}\n'),
      (e) => { console.error(e); res.writeHead(500).end('{"error":"falló la visita"}\n'); },
    );
  });
}).listen(PORT, () => console.log(`bot escuchando en :${PORT}`));
