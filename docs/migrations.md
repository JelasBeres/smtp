Migration Plan
==============

Existing Migrations
-------------------

- `0001_01_01_000000_create_users_table.php`
- `0001_01_01_000001_create_cache_table.php`
- `0001_01_01_000002_create_jobs_table.php`
- `2026_07_05_000001_create_mailflow_core_tables.php`

Follow-up Migrations
--------------------

- Add campaign selection tables for selected contact lists and segments.
- Add secure unsubscribe token table if token rotation/revocation is needed beyond signed URLs.
- Add tracking event tables only when open/click tracking is enabled.
- Add provider-specific metadata columns only when required by a concrete adapter.

Notes
-----

- Use string constants or enums in application code rather than database enum columns to keep migrations portable.
- Keep credentials encrypted using model mutators/accessors and Laravel Crypt.
- Never store real credentials in migrations, seeders, tests, or `.env.example`.
