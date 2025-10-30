# Common snippets and canonical paragraphs

This file collects canonical paragraphs and small examples that are intentionally shared across the documentation to avoid repetition.

## Index links (canonical navigation)
<a id="index-links"></a>

El conjunto de enlaces que se usan como navegación rápida entre los documentos principales:

**[📖 Índice General](docs/INDEX.md)** | **[🏠 README](README.md)** | **[🏗️ Arquitectura](ARCHITECTURE.md)** | **[🗄️ Database](DATABASE.md)**

---

## .env (example) <a id="env-example"></a>

Ejemplo compacto de variables de entorno para un entorno de desarrollo local:

```bash
# .env.development
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000
# DB settings (example)
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=snackshop
DB_USER=snackshop
DB_PASS=secret
```

---

Nota: si necesitas editar los snippets canónicos, actualiza este único archivo para propagar el cambio en la documentación.



