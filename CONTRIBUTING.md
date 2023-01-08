# Contributing

You can chat with us on [Gitter: jquery/dev](https://gitter.im/jquery/dev) or on [Libera Chat IRC](https://libera.chat/) in `#jquery` ([webchat](https://web.libera.chat/#jquery)).

## Update vendor

* For compatibility with PHP 5.4, use Composer 2.2 LTS and not a later version.
  Run `composer self-update && composer self-update --2.2`
* Run `composer install --no-dev`
* Run `git add vendor/`
