# Soda Analytics
A sweet integration with Google Analytics into the Soda Framework

##Installation
1) Firstly follow the instructions to install Soda CMS at:
https://github.com/soda-framework/cms


2) Install Soda Analytics with composer
```
#!bash
composer require soda-framework/analytics
```

3) Add `Soda\Analytics\Providers\AnalyticsServiceProvider::class`

4) Run `php artisan vendor:publish`

5) Run `php artisan migrate`

6) Modify `config/soda/analytics.php` according to your needs:
* `apis` - Google Console API's Soda Analytics requires
* `service-account-name` - The name of the Service Account created for [Using OAuth 2.0 for Server to Server Applications](https://developers.google.com/identity/protocols/OAuth2ServiceAccount)
* `scheduler` - available cron job intervals for the Analytics scheduler

7) Add the following Laravel Blade code to your `<head>` to initialize Google Analytics:
```#!php
@include('soda-analytics::analytics')
```

##Configuration
* Log into the CMS
* Go to Analytics > Configure
* Complete all the steps, in order, to enable and create the relevant apis and access keys.
* You're ready. Start using Analytics > Audience, Events, Schedules.

##Usage

###Sending Events
Send events as normal with Google Analytics:
```!#javascript
ga('send', 'event', [eventCategory], [eventAction], [eventLabel], [eventValue], [fieldsObject]);
```
Or use our helper function:
```!#javascript
send_event([eventCategory], [eventAction], [eventLabel] (optional), [eventValue] (optional));
```
For best results, try to use all the parameters.

###Analyzing Events
* Log into the CMS
* Go to Analytics > Events

###Analyzing Audience
* Log into the CMS
* Go to Analytics > Audience

###Creating Schedules
* Log into the CMS
* Go to Analytics > Schedules
* Choose your desired schedule frequency (the same frequency is used for all schedules)
* Enter the displayed `cron` command onto your server (using the `crontab -e` command)
* Create a new schedule
* Choose the type. Event will send event data, Audience, audience data, Events and Audience will send both.
* Enter at least one email to send the report to.
* Click save.
* You can test your schedule by clicking `Run Schedule`

