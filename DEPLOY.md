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
2. Create a project. **Pick the region closest to where your Render service runs** — e.g. Render
   **Oregon** ↔ Neon **AWS US West 2 (Oregon)**. Same-region is the single biggest perf win (see
   step 6): a cross-country DB adds ~60–70 ms to *every* query.
3. It gives you a **connection string** like:
   `postgresql://alex:abc123@ep-cool-name-123.us-west-2.aws.neon.tech/neondb?sslmode=require`
4. Copy it — that's your `DB_URL`.

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
| `SESSION_DRIVER` | `cookie` (avoids a DB round-trip per request — see step 6) |
| `QUEUE_CONNECTION` | `sync` |
| `CACHE_STORE` | `file` |
| `LOG_CHANNEL` | `stderr` |
| `MAIL_MAILER` | `log` |
| `GEMINI_API_KEY` | *(optional)* your Google AI Studio key for AI budget tips |
| `CRON_SECRET` | *(optional)* a long random string — see step 5 |

You don't need to set `APP_URL` — the container fills it from Render's URL automatically.

## 4. Deploy

Click **Deploy**. On boot the container runs migrations automatically, then runs
`php artisan optimize` (caches config, events, routes, and compiled views) and starts the
server. Watch the **Logs** tab — when it's up, open the
`https://<your-app>.onrender.com` URL and register an account.

## 5. (Optional) Keep recurring expenses generating

The free tier has no cron, so recurring/biweekly expenses won't auto-create on their own.
To fix that:

1. Set a `CRON_SECRET` env var in Render (any long random string).
2. At **cron-job.org** (free), create a job that does a daily GET request to:
   `https://<your-app>.onrender.com/_cron/recurring/<CRON_SECRET>`

That endpoint returns `404` unless the token matches, so keep the secret private.
(The daily ping also wakes the app and catches up any missed occurrences.)

## 6. Make it fast (region, sessions, cold start)

Three things make the free tier feel slow — in order of impact:

**1. Put the database in the same region as the app (biggest win).** A Render service in Oregon
talking to a Neon DB in US-East pays **~60–70 ms per query** across the country, and the dashboard
alone runs ~12 queries — roughly **1 second of pure network wait on every load**, even when warm.
Same-region drops each query to **~1–3 ms**.
- Check your Neon region from the `DB_URL` host (`…us-east-1.aws.neon.tech`) or the Neon dashboard;
  Render's region is on the service **Settings** page (this repo defaults to `oregon` = AWS
  `us-west-2`).
- Neon can't move an existing project's region, so **create a new Neon project in the matching
  region** and migrate, then point `DB_URL` at it and redeploy:
  ```bash
  pg_dump "postgresql://OLD_URL" | psql "postgresql://NEW_SAME_REGION_URL"
  ```
  (Or instead recreate the Render service in the region nearest your current Neon DB.)

**2. Keep sessions & cache off the remote DB.** With `SESSION_DRIVER=database`, every request does a
session read **+** write across that slow link. This repo now uses `SESSION_DRIVER=cookie`
(stateless, survives redeploys) and `CACHE_STORE=file` (local; still persists login rate-limits). If
you set env vars **manually** in the Render dashboard, change those two there too.

**3. Cold starts.** The free service **sleeps after ~15 min idle**, so the next visitor waits ~50s
for it to wake. The app-level tuning here (config/route/view cache + OPcache JIT) only speeds *warm*
requests. To avoid the cold hit:
- **Keep it warm (free):** at **cron-job.org**, add a job that GETs
  `https://<your-app>.onrender.com/login` every **10 minutes**. One always-on service still fits
  Render's free **750 instance-hours/month** (~730h in a month).
- **Upgrade:** Render's paid **Starter** tier never sleeps and removes cold starts entirely.

---

## Notes

- **Data & backups:** Your data lives in Neon and survives redeploys. For a finance app,
  take occasional backups (`pg_dump` against the Neon URL, or Neon's point-in-time restore).
- **Real emails:** budget-alert emails currently go to the log (`MAIL_MAILER=log`). To send
  them for real, add a provider (Resend/Postmark/Mailgun) and set the `MAIL_*` vars.
- **Custom domain & HTTPS:** Render gives you free HTTPS on the `onrender.com` URL; you can
  attach a custom domain in the dashboard (also free).
- **Redeploys:** push to your default branch and Render rebuilds automatically.
