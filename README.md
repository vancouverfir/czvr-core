# Vancouver FIR Core

### Thank you to the team at the [Gander Oceanic OCA](https://github.com/gander-oceanic-fir-vatsim) and [Winnipeg FIR](https://github.com/winnipegfir) for developing the initial core! [Click here to visit the Gander repository](https://github.com/gander-oceanic-fir-vatsim/czqo-core) and [here to visit the CZWG repository](https://github.com/winnipegfir/CZWG-core)
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

1. Clone the repository.
2. Run `composer install`
3. Rename `.env.example` to `.env` and fill required fields. The VATSIM connect demo URI is already placed in there. Get your ID and put your redirect URI into http://auth-dev.vatsim.net.
4. Create a local database and update the `.env` file with your database credentials.
5. Define `MAIL_FROM_ADDRESS` in the `.env` file.
6. Get an API key from [CheckWX API](https://www.checkwxapi.com/) and set it to `AIRPORT_API_KEY` in the `.env` file.
7. Run `php artisan migrate --seed` (runs database migrations and seeds with required rows).
8. Run `php artisan key:generate`.
9. Login with one of the accounts found at http://wiki.vatsim.net/connect.
10. Give that new account in the `users` table a `permissions` value of `5`.


