class {'apt': }
apt::ppa { 'ppa:ondrej/php5-5.6': }->
class { 'phpenv': }
class { 'l10n': }->

packagecloud::repo {'EventStore/EventStore-OSS':
  type => 'deb'
}->
package {'eventstore-oss':
  ensure => latest
}->
service {'eventstore':
  enable => true
}

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
