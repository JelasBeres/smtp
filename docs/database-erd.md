MailFlow Database ERD
=====================

Primary Relationships
---------------------

- `users` creates `contact_lists`, `segments`, `email_templates`, `campaigns`, and `contact_imports`.
- `contacts` belongs to many `contact_lists` through `contact_list_members`.
- `campaigns` belongs to an `email_template` and creator `user`.
- `campaign_recipients` belongs to one `campaign` and one `contact`.
- `email_suppressions` blocks email addresses globally.
- `webhook_events` records provider events idempotently by `provider + provider_event_id`.
- `audit_logs` optionally belongs to a `user` and polymorphically references changed resources.

Core Tables
-----------

- `users`: admin identity and role.
- `contacts`: normalized consented contact records.
- `contact_lists`: static named lists.
- `contact_list_members`: list membership, unique by list/contact.
- `segments`: dynamic JSON rules.
- `email_templates`: reusable HTML/text templates.
- `campaigns`: campaign metadata and aggregate counters.
- `campaign_recipients`: per-contact delivery state, unique by campaign/contact.
- `email_provider_settings`: encrypted provider settings and rate limits.
- `sending_domains`: DNS verification status.
- `email_suppressions`: global suppression list.
- `webhook_events`: raw provider webhook payloads and processing state.
- `contact_imports`: import job state and counts.
- `audit_logs`: security and administrative audit trail.

Important Constraints
---------------------

- `contacts.email` unique.
- `email_suppressions.email` unique.
- `sending_domains.domain` unique.
- `campaign_recipients(campaign_id, contact_id)` unique.
- `webhook_events(provider, provider_event_id)` unique.
- Foreign keys restrict deletion for author-owned objects and cascade where membership/child rows are not meaningful independently.
