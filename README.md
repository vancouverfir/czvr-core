# Vancouver FIR Core

### Thank you to the team at the [Gander Oceanic OCA](https://github.com/czqoocavatsim) and [Winnipeg FIR](https://github.com/winnipegfir) for developing the initial core! [Click here to visit the Gander repository](https://github.com/gander-oceanic-fir-vatsim/czqo-core) and [here to visit the CZWG repository](https://github.com/winnipegfir/CZWG-core)
---
### Contributing

We would love to have your ideas put into the website. Anything can be put into either a pull request or an issue for our review!

#### Submitting an Issue or Pull Request
Guidelines for submitting an **issue**:

- Be sensible, and don't spam with unnecessary issues.
- Tell us:
  - What is the issue/feature?
  - Why does it need to be fixed/why is it important to add?
  - How can we reproduce the issue? (if it is a bug)
  - What have you already tried? (if it is a bug)

Guidelines for submitting a **pull request**:
- Be sensible as stated above.
- Tell us:
  - What you have fixed/added and where you fixed it
  - Why it was a problem, or why it was neccessary/nice to add
- Document/comment your code. This is important for us and future developers so they can understand what you have written.

### Initial setup process

Install PHP dependencies, create `.env`, generate the application key, migrate and seed the database, and create the public storage link using the command below

```bash
composer run setup
```

Give the test account 10000001 the admin role

```bash
php artisan tinker --execute="App\Models\Users\User::findOrFail(10000001)->assignRole(App\Support\Authorization\SystemRoleCatalog::ADMINISTRATOR);"
```

Lastly, fill in your `.env` file!

### Deployment settings

- Set `APP_URL` to the main site URL. Booking and training default to the `booking.` and `training.` subdomains of that URL's host. Use `BOOKING_DOMAIN` and `TRAINING_DOMAIN` to override them.
- Set `DISCORD_GUILD_ID` and `DISCORD_AUDIT_CHANNEL_ID` for deployments using a different Discord server.
- Set `BACKUP_ARCHIVE_PASSWORD` through your deployment secrets to encrypt new archives. Retention keeps daily, weekly, and monthly copies subject to `BACKUP_STORAGE_LIMIT_MB`. Size the limit for your archive sizes and verify a restore before relying on the retention schedule.
- The legacy fetcher imports events and roster entries. Roster removals and event deletions require explicit review. Laravel handles ratings for existing users and quarterly currency.
- Apply the migrations and restart queue workers with the release. Review existing role assignments and direct grants if the retired `admin` slug was used. The migration revokes permissions inherited from that slug. Existing students whose only stage label was Training Paused can have their previous stage restored before the pause label is removed.
