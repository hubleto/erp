<?php declare(strict_types=1);

namespace HubletoApp\Help\Customers\Tests;

use PHPUnit\Framework\TestCase;

use HubletoApp\Community\Documents\Models\Template;
use HubletoApp\Community\Documents\Generator;

final class GenerateDocumentTest extends TestCase
{

  public function testCreateTemplate(): void
  {
    $main = \HubletoMain\Loader::getGlobalApp();

    $mTemplate = $main->getService(Template::class);
    $template = $mTemplate->record->recordCreate([
      'name' => 'Test template',
      'content' => '<p>This is a test template. Current time is: {{ time }}</p>',
    ]);

    $generator = $main->getService(Generator::class);
    $generator->generatePdfFromTemplate(
      $template['id'],
      'test-create-template.pdf',
      ['time' => new \DateTimeImmutable()->format('Y-m-d H:I:s')]
    );
  }

}
