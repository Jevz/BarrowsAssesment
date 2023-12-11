# Other questions

## 1. Justification of Framework:
Laravel is a modern PHP framework which has a fluent and easy to understand API. It includes many features straight out of the box with minimal setup required. Some features are:
- Routing and Middleware
- Easy to use task scheduling
- Out of the box authentication scaffolding
- Database integration with migration and mock data creation
- Includes a powerful Object Relation Manager (ORM) for simplified DB interactions
- Security Features such as protection against SQL injection, cross-site request forgery, and cross-site scripting

I also have a decent amount of experience in Laravel and with all the included features it makes getting a simple PoC up and running quick and easy.

## 2. Justification of Datastore:
MySQL was used as the chosen datastore, since it integrates out of the box with Laravel and also because it is the database I'm most familiar with.

## 3. Suggested Security Enhancements
- User Registration: Introduce more layers of user verification such as email verification. Enabling two-factor authentication would also deter the creation of bot/spam users. A captcha verification system can also be considered.
- Rate Limiting/Throttling: Introduce limits on the frequency of posts and/or comments from individual users. This can also be limited to a specific IP address to prevent spamming from a certain domain.
- Ban functionality: Introduce logic to be able to block certain users from accessing the platform.
- Hidden Fields (Honeypots): Add hidden fields onto form elements to "trick" bot accounts into filling these in. If they are filled, it's highly likely the request was made by a bot. 
- HTTP Encryption. Ensure the web-app/website uses HTTPS encryption to prevent data-jacking.
