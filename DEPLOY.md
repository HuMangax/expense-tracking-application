# Deploying for free (Render + Neon)

This app ships with a `Dockerfile` so it can run on **Render's free tier**, using a
free **Neon** Postgres database so your data persists across deploys.

> Heads-up about the free tier: the web service **sleeps after ~15 min idle**, so the
> first visit after that takes ~50s to wake. Your data is safe in Neon regardless.

---

## 0. Push the code to GitHub

Render deploys from a Git repo, so commit everything and push to GitHub first:

```bash
git add .
git commit -m "Add deployment config (Docker + Render + Neon)"
git push
```

## 1. Create a free Postgres database (Neon)

1. Sign up at **neon.tech** (no credit card).
2. Create a project → it gives you a **connection string** like:
   `postgresql://alex:abc123@ep-cool-name-123.us-east-2.aws.neon.tech/neondb?sslmode=require`
3. Copy it — that's your `DB_URL`.

## 2. Create the web service (Render)

**Option A — Dashboard (simplest):**
1. Sign up at **render.com** (no card for the free tier).
2. **New + → Web Service →** connect your GitHub repo.
3. Render detects the `Dockerfile` automatically. Choose the **Free** plan and create.

**Option B — Blueprint:** New + → **Blueprint** → pick this repo (`render.yaml` is included).

## 3. Set the environment variables

In the service's **Environment** tab, add:

| Key | Value |
|---|---|
| `APP_KEY` | `base64:...` (run `php artisan key:generate --show`, or use the one I gave you) |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `DB_CONNECTION` | `pgsql` |
| `DB_URL` | your Neon connection string from step 1 |
| `DB_SSLMODE` | `require` |
| `SESSION_DRIVER` | `database` |
| `QUEUE_CONNECTION` | `sync` |
| `CACHE_STORE` | `database` |
| `LOG_CHANNEL` | `stderr` |
| `MAIL_MAILER` | `log` |
| `GEMINI_API_KEY` | *(optional)* your Google AI Studio key for AI budget tips |
| `CRON_SECRET` | *(optional)* a long random string — see step 5 |

You don't need to set `APP_URL` — the container fills it from Render's URL automatically.

## 4. Deploy

Click **Deploy**. On boot the container runs migrations automatically, caches config/views,
then starts the server. Watch the **Logs** tab — when it's up, open the
`https://<your-app>.onrender.com` URL and register an account.

## 5. (Optional) Keep recurring expenses generating

The free tier has no cron, so recurring/biweekly expenses won't auto-create on their own.
To fix that:

1. Set a `CRON_SECRET` env var in Render (any long random string).
2. At **cron-job.org** (free), create a job that does a daily GET request to:
   `https://<your-app>.onrender.com/_cron/recurring/<CRON_SECRET>`

That endpoint returns `404` unless the token matches, so keep the secret private.
(The daily ping also wakes the app and catches up any missed occurrences.)

---

## Notes

- **Data & backups:** Your data lives in Neon and survives redeploys. For a finance app,
  take occasional backups (`pg_dump` against the Neon URL, or Neon's point-in-time restore).
- **Real emails:** budget-alert emails currently go to the log (`MAIL_MAILER=log`). To send
  them for real, add a provider (Resend/Postmark/Mailgun) and set the `MAIL_*` vars.
- **Custom domain & HTTPS:** Render gives you free HTTPS on the `onrender.com` URL; you can
  attach a custom domain in the dashboard (also free).
- **Redeploys:** push to your default branch and Render rebuilds automatically.
