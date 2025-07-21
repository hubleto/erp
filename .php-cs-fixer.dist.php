<?php

// Example usage:
// php-cs-fixer fix --config=.php-cs-fixer.dist.php .\apps

return (new PhpCsFixer\Config())
  ->setRules([
    '@PSR12' => true,
  ])
  ->setUsingCache(false)
  ->setIndent("  ")
;