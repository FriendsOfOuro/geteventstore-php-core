class l10n
{
    $localeit = '/var/lib/locales/supported.d/it'
    file { $localeit:
        content => "it_IT.UTF-8 UTF-8\n"
    }

    $localeen = '/var/lib/locales/supported.d/en'
    file { $localeen:
        content => "en_US.UTF-8 UTF-8\n"
    }

    class { 'locales' :
        default_value  => "it_IT.UTF-8",
        available      => ["it_IT.UTF-8 UTF-8", "en_US.UTF-8 UTF-8"],
        require        => File[$localeit, $localeen]
    }
    class { 'timezone':
        timezone => 'Europe/Rome',
    }
}
