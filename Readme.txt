=== Custom Admin URL ===
Contributors: abhisheksharma
Tags: custom login, hide admin, brute force, security, protect login
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Protect your WordPress site by renaming the login URL and blocking brute-force attacks with IP lockout.

== Description ==

**Custom Admin URL** is a lightweight yet powerful security tool designed to protect your WordPress website from unauthorized access and brute-force attacks.

By default, every WordPress site uses `wp-admin` and `wp-login.php` for access, making them easy targets for hackers. This plugin allows you to rename your login URL to anything you want and provides an extra layer of security by limiting failed login attempts.

### Key Features:
* **Custom Login URL:** Rename `wp-login.php` to a custom slug.
* **Hidden Admin Area:** Redirects unauthorized users away from the default folders.
* **Brute Force Protection:** Automatically blocks IP addresses after failed attempts.
* **Custom Lockout Duration:** You decide how long a suspicious IP should stay blocked.
* **Clean & Native UI:** A professional settings dashboard.

== Installation ==

1. Upload the `custom-admin-url` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **🛡️ Admin Shield** in your dashboard.
4. Set your custom login slug and save changes.
5. **Important:** Bookmark your new login URL immediately.

== Frequently Asked Questions ==

= What happens if I forget my custom login URL? =
If you lose your custom URL, access your site via FTP, and rename the plugin folder to restore the default login.

== Screenshots ==

1. The main dashboard where you can configure the custom URL and security settings.
2. Brute-force protection in action with custom lockout messages.

== Changelog ==

= 1.0.1 =
* Fixed Text Domain and I18n standards.
* Updated "Tested up to" version compatibility.
* Improved security for direct file access.

= 1.0.0 =
* Initial release.