# LinkedIn messages and followups (Hubleto custom app)

Namespace `Hubleto\App\Custom\LinkedinMessages`, url slug `linkedin-messages`.

## Install
1. Unpack this archive into `src/apps` of your project, so that `src/apps/LinkedinMessages/Loader.php` exists.
2. Run `php hubleto app install Hubleto\App\Custom\LinkedinMessages` (requires the Workflow and Calendar apps).
3. Rebuild your React bundle so that `src/apps/LinkedinMessages/Loader.tsx` is included.

## Connect LinkedIn
1. In the LinkedIn Developer Portal create an app, add the products you have access to
   ("Sign In with LinkedIn using OpenID Connect" and "Member Data Portability") and add the redirect URL shown in
   *LinkedIn messages > Settings* (`<project-url>/linkedin-messages/api/oauth-callback`).
2. Enter Client ID / Client secret in *Settings*, then open *Accounts > Add account > Connect to LinkedIn*.

## What LinkedIn allows (important)
* Reading messages works only through LinkedIn's Member Data Portability API (read-only; new events for the last
  28 days via the changelog, older history via the INBOX snapshot). The cron job `SyncMessages` runs every 15 minutes;
  *Synchronize now* on the account form runs it immediately.
* Sending messages (`POST /v2/messages`, scope `w_messages`) is restricted to LinkedIn-approved partners. Without that
  access the reply is stored with status "Waiting for manual sending"; use *Copy reply*, *Open LinkedIn*, then *I sent it*.
* After every reply the user is prompted to plan a follow-up (calendar activity linked to the message).
* Access tokens are stored in the `linkedin_accounts` table and the client secret in the app config, both unencrypted.
