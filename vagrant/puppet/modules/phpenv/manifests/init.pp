class phpenv
{
    package{'php5-json': }
    class {'php::cli': }

    class {['php::composer', 'php::composer::auto_update']: }
    class {'php::extension::curl': }
    class {'php::extension::intl': }

    package {'php5-apcu': }
    package{'php5-dev': } ->
    class {'php::pear': } ->
    php::cli::config {'CLI TimeZone':
        config => [
            'set Date/date.timezone UTC'
        ]
    }->
    php::fpm::config {'FPM TimeZone':
        config => [
            'set Date/date.timezone UTC'
        ]
    }
}
