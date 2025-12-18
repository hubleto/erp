<?php declare(strict_types=1);

namespace Hubleto\App\Help\Customers\Tests;

use PHPUnit\Framework\TestCase;

use Hubleto\App\Community\Documents\Models\Template;
use Hubleto\App\Community\Documents\Generator;

final class GenerateDocumentTest extends TestCase
{

  public function testCreateTemplate(): void
  {
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    $mTemplate = $hubleto->getService(Template::class);
    $template = $mTemplate->record->recordCreate([
      'name' => 'Test template',
      'content' => '<p>This is a test template. Current time is: {{ time }}</p>',
    ]);

    $generator = $hubleto->getService(Generator::class);
    $generator->createPdfFromTemplate(
      $template['id'],
      'test-create-template.pdf',
      ['time' => new \DateTimeImmutable()->format('Y-m-d H:I:s')]
    );
  }

}
