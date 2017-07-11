# Soda Voting
A sweet suite for sweet developers.

###Installation
1) Firstly follow the instructions to install Soda CMS at:
https://github.com/soda-framework/cms


2) Apply these changes to your `composer.json` file and running a composer update
```
#!json

"repositories": [
    {
        "type": "vcs",
        "url": "git@bitbucket.org:made-in-katana/mik-site.git"
    }
],
"require": { 
    "soda-framework/cms": "^0.5.7",
    "mik/site": "^0.2.0",
},
```

3) Integrate into laravel by adding `Soda\Site\Providers\SiteServiceProvider::class`
in the providers array in `/config/app.php`
```
    'providers' => [
        Soda\Providers\SodaServiceProvider::class,
        Soda\Site\Providers\SiteServiceProvider::class,
    ]
```

4) Run the database migrations `php artisan migrate` to generate the necessary tables

##Usage

###Social Sharing Meta Data
Include this code in your <head>
```
#!php
@include('mik-site::head')
```
This will include Facebook and Twitter meta data used when sharing any links on this sites domain.
They can be altered in the CMS, Site > Socials

To include the SS Standard/Circle fonts, include this in your footer

```
#!php
@include('mik-site::ss-fonts')
```


###Styling Helpers
In this suite is a set of sweet CSS/SASS helper classes/functions.
They must be manually imported into your SCSS.
```
#!scss
@import "../../../../../vendor/mik/site/resources/scss/styles";
```

There is also a set of colour classes defined in this suite as defined below:
```
#!scss
$colours: (
    "clear" : transparent,
    "black" : #000000,
    "white" : #FFFFFF,
);
```
which **NEEDS** to be defined before including the main SASS file above.
These will create a set of classes that will apply colour changes to their elements.
```
.text-COLOUR
.text-hover-COLOUR
.bg-COLOUR
.bg-hover-COLOUR
.border-COLOUR
.border-hover-COLOUR
```
All of these classes can also be applied for different the different **bootstrap** screen sizes:
```
.text-COLOUR-xs-up
.text-COLOUR-sm-up
.text-COLOUR-md-up
.text-COLOUR-lg-up
```
*they are only UP to enforce mobile first development*


###JavaScript Helpers
In this suite is a set of sweet JS helper functions/commands.
Only way to use this is to include this file inside another using WebPack.

TODO: publish js to public folder
