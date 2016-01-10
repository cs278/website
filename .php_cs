
<?php

return (new Cs278\CsFixer\ConfigBuilder)
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->exclude([
                'bin',
                'var',
                'web',
            ])
            ->in(__DIR__)
    )
    ->build()
;
