class phpenv
{
  include php::params
  package{'php5-json': }
  class {'php::cli': }

  class {['php::composer', 'php::composer::auto_update']: }
  class {'php::extension::curl': }
  class {'php::extension::intl': }

  package {'php5-apcu': }
}
