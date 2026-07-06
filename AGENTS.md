# aCeler Project — Guía para Agentes de IA

## ⚠️ Workarounds y Limitaciones Conocidas

### Login con Azure AD (Entorno Local)

**Problema:** Microsoft Azure Portal tiene registrado el siguiente redirect URI:
```
https://localhost/project/login/azure/callback
```

Pero el proyecto real está en:
```
http://localhost/alsinaProject/public/
```

**Resultado:** Después de autenticarse en Microsoft, el navegador es redirigido a una URL inexistente (`/project/` en vez de `/alsinaProject/public/`).

**Workaround manual:**
1. Después de que Microsoft redirige a `https://localhost/project/login/azure/callback?code=...`
2. Manualmente editar la URLMaximum URL length for Azure AD is **256 characters**.
