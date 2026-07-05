MailFlow Architecture
=====================

MailFlow is a consent-based email campaign manager for first-party contact lists and official SMTP or email API providers.

Core Rules
----------

- Campaigns only target contacts with explicit consent.
- Suppressed contacts are excluded globally.
- Email validation never performs SMTP RCPT probing, mailbox enumeration, provider login, or unsolicited test delivery.
- Sending is asynchronous through Laravel queues.
- Controllers handle HTTP only; business rules live in services, jobs, commands, policies, and provider adapters.
- Provider credentials are encrypted and never displayed in full.

Application Layers
------------------

- HTTP: controllers, form requests, middleware, policies.
- Domain: Eloquent models, enums/constants, services.
- Async: queue jobs for imports, validation, campaigns, emails, and webhooks.
- Provider: `App\Contracts\EmailProviderInterface` with SMTP and optional SES adapters.
- Operations: Artisan commands and scheduler.

Queues
------

- `imports`: CSV imports.
- `validation`: contact and domain validation batches.
- `campaigns`: recipient preparation and dispatch orchestration.
- `emails`: individual email sends.
- `webhooks`: provider event processing.

Safety Gates
------------

Campaign preflight blocks sending unless:

- Sender email is syntactically valid.
- Sending domain exists and is verified.
- SPF, DKIM, DMARC, and MX statuses are valid or acceptable for configured provider.
- Provider is active.
- Template exists.
- Unsubscribe URL can be generated.
- Recipients are active, consented, validated safely, and not suppressed.
