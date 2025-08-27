<?php

namespace Hubleto\App\Community\Documents;

use Dompdf\Dompdf;
use Dompdf\Options;

use Hubleto\App\Community\Documents\Models\Template;

class Generator extends \Hubleto\Framework\Core
{

  /**
   * Creates a document in upload folder with a given content and returns ID of created document.
   *
   * @param string $content Content of the document
   * @param string $outputFilename Name of the output file
   * 
   * @return int ID of generated document
   * 
   */
  public function saveFromString(string $content, string $outputFilename): int
  {
    @file_put_contents($this->getEnv()->uploadFolder . '/' . $outputFilename, $content);
    
    $mDocument = $this->getModel(Models\Document::class);
    $document = $mDocument->record->recordCreate([
      'id_folder' => 0,
      'name' => $outputFilename,
      'file' => $outputFilename,
    ]);

    return (int) $document['id'];
  }

  public function renderTemplate(int $idTemplate, array $vars): string
  {
    $mTemplate = $this->getService(Template::class);
    $template = $mTemplate->record->prepareReadQuery()->where('documents_templates.id', $idTemplate)->first();
    if (!$template) throw new \Exception('Template was not found.');

    $mTemplate = $this->getModel(Models\Template::class);
    $template = $mTemplate->record->prepareReadQuery()->where('id', $idTemplate)->first();

    $vars['defaultStyle'] = "
      <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
      <head>
        <style>
          @font-face {
            font-family: 'Gabarito';
            font-style: normal;
            font-weight: normal;
            src: url('{$this->getEnv()->assetsUrl}/fonts/Gabarito/Gabarito-VariableFont_wght.ttf') format('truetype');
          }

          @font-face {
            font-family: 'Gabarito';
            font-style: normal;
            font-weight: bold;
            src: url('{$this->getEnv()->assetsUrl}/fonts/Gabarito/Gabarito-VariableFont_wght.ttf') format('truetype');
          }

          * { font-family: 'Gabarito'; font-size: '10pt' }
        </style>
      </head>
    ";

    $twigTemplate = $this->getRenderer()->getTwig()->createTemplate($template->content);
    return $twigTemplate->render($vars);
  }

  /**
   * Generates PDF document from template and returns ID of the generated document.
   *
   * @param int $idTemplate ID of template to be used for generating the document
   * @param string $outputFilename Name of the file to be generated.
   * @param array $vars Variable values to be replaced in template.
   * 
   * @return int ID of generated document
   * 
   */
  public function generatePdfFromTemplate(int $idTemplate, string $outputFilename, array $vars): int
  {
    $pdfGeneratorEndpoint = $this->getConfig()->getAsString('pdfGeneratorEndpoint');

    $htmlString = $this->renderTemplate($idTemplate, $vars);
    $pdfString = '';

    if (!empty($pdfGeneratorEndpoint)) {
      try {
        $ch = curl_init($pdfGeneratorEndpoint);
        $payload = json_encode(['html' => $htmlString]);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $pdfString = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if (!empty($error)) throw new \Exception($error);
      } catch (\Throwable $e) {
        throw new \Exception('Error occured while generating PDF. ' . $e->getMessage());
      }
    } else {
      $options = new Options();
      $options->set('isRemoteEnabled', true);

      $dompdf = new Dompdf($options);
      $dompdf->loadHtml($htmlString, 'UTF-8');
      $dompdf->setPaper('A4');
      $dompdf->render();

      $pdfString = $dompdf->output();
    }

    $idDocument = $this->saveFromString($pdfString, $outputFilename);
    return $idDocument;
  }

}