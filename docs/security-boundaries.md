Security Boundaries
===================

Allowed
-------

- Consent-based contact management.
- CSV import from user-owned or permissioned sources.
- Safe email syntax, domain, MX, disposable-domain, and role-address checks.
- Sending via authenticated SMTP or official provider APIs.
- Provider webhook handling with signature verification when supported.
- Global suppression enforcement for unsubscribe, bounce, complaint, and manual blocks.

Forbidden
---------

- Email scraping.
- Mailbox enumeration.
- SMTP `RCPT TO` probing.
- Provider account probing for Gmail, Yahoo, Outlook, or similar services.
- Spam-filter bypass logic.
- Domain rotation to avoid provider enforcement.
- Sender spoofing or unauthorized From domains.
- Hidden tracking or fingerprinting.

Operational Requirements
------------------------

- Default development admin password is `password` and must be changed immediately.
- Webhooks must be idempotent.
- Tests must use fake mail, fake queue, and mock providers.
- CSV upload size and MIME type must be validated.
- Audit logs must exclude passwords, API keys, SMTP credentials, and secrets.
