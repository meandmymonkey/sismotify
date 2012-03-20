# Sismotify

A couple of notifiers for [Sismo](http://sismo.sensiolabs.org).

## Install

Create a composer.json and install

```json
{
    "require": {
        "duochrome/sismotify": "*"
    }
}
```

```sh
$ php composer.phar install
```

## HoustonNotifier

Calls mission control if there is a problem. This Notifier is **Mac only** for now, sorry.

```php
$notfier = new Duochrome\Sismo\HoustonNotifier();
```

After the first couple of hundred failed builds, the audio message might start getting on your nerves a bit. For these cases, you can of course customize the failure sound, add a sound for successful builds, and adjust the volume:

```php
$notfier = new Duochrome\Sismo\HoustonNotifier(
    '/path/to/failsound',
    '/path/to/successsound',
    50
);
```

All audio formats that can be played by quicktime should work fine.
