class {'apt': }
apt::ppa { 'ppa:ondrej/php5-5.6': }->
class { 'phpenv': }
class { 'l10n': }->

class { 'eventstore': }


$toolpkg = [
    'htop',
    'screen',
    'tmux',
    'unzip',
    'dos2unix',
]

package { $toolpkg:
  ensure => present
}

class { 'submodules':
}
