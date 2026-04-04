<?php

namespace Hubleto\App\Community\Documents;

use Dompdf\Dompdf;
use Dompdf\Options;

use Hubleto\App\Community\Documents\Models\Template;

class Generator extends \Hubleto\Erp\Core
{

  /**
   * [Description for renderTemplate]
   *
   * @param int $idTemplate
   * @param array $vars
   * 
   * @return string
   * 
   */
  public function renderTemplate(int $idTemplate, array $vars): string
  {
    /** @var Template */
    $mTemplate = $this->getService(Template::class);
    $template = $mTemplate->record->prepareReadQuery()->where('documents_templates.id', $idTemplate)->first();
    if (!$template) throw new \Exception('Template was not found.');

    /** @var Models\Template */
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
            src: url('{$this->env()->assetsUrl}/fonts/Gabarito/Gabarito-VariableFont_wght.ttf') format('truetype');
          }

          @font-face {
            font-family: 'Gabarito';
            font-style: normal;
            font-weight: bold;
            src: url('{$this->env()->assetsUrl}/fonts/Gabarito/Gabarito-VariableFont_wght.ttf') format('truetype');
          }

          * { font-family: 'Gabarito'; font-size: '10pt' }
        </style>
      </head>
    ";

    $twigTemplate = $this->renderer()->getTwig()->createTemplate($template->content);
    return $twigTemplate->render($vars);
  }

  /**
   * Generates PDF document from template and returns ID of the generated document.
   *
   * @param string $model Model (full class name) which the document is related to.
   * @param int $recordId ID of the record in the model.
   * @param int $idTemplate ID of template to be used for generating the document.
   * @param string $outputFilename Name of the file to be generated.
   * @param array $vars Variable values to be replaced in template.
   * 
   * @return int ID of generated document (0 if $createDocumentEntry == false)
   * 
   */
  public function generatePdfDocumentFromTemplate(
    string $model,
    int $recordId,
    int $idTemplate,
    string $outputFilename,
    array $vars,
    bool $createDocumentEntry = false
  ): int
  {
    $pdfGeneratorEndpoint = $this->config()->getAsString('pdfGeneratorEndpoint');

    $vars['PDF_EXPORT'] = true;
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
        // curl_close($ch);

        if (!empty($error)) throw new \Exception($error);
      } catch (\Throwable $e) {
        throw new \Exception('Error occured while generating PDF. ' . $e->getMessage());
      }
    } else {
      $options = new Options();
      // $options->setDebugPng(true);
      $options->setChroot([$this->env()->uploadFolder]);

      // replace all remote URLs to file URLs (to load images from uploads folder)
      $htmlString = preg_replace_callback('/(src|href)=["\'](' . preg_quote($this->env()->uploadUrl, '/') . '\/(.*?)["\'])/i', function($matches) {
        $filePath = $this->env()->uploadFolder . '/' . $matches[3];
        if (file_exists($filePath)) {
          return $matches[1] . '="file://' . str_replace('\\', '/', $filePath) . '"';
        } else {
          return $matches[0];
        }
      }, $htmlString);

      $dompdf = new Dompdf($options);
      $dompdf->loadHtml($htmlString, 'UTF-8');
      $dompdf->setPaper('A4');
      $dompdf->render();

      $pdfString = $dompdf->output();
    }

    @file_put_contents(
      $this->env()->uploadFolder . '/' . $outputFilename,
      $pdfString
    );

    if ($createDocumentEntry) {
      /** @var Document */
      $mDocument = $this->getModel(Models\Document::class);

      /** @var DocumentVersion */
      $mDocumentVersion = $this->getModel(Models\DocumentVersion::class);

      $document = $mDocument->record
        ->where('model', $model)
        ->where('record_id', $recordId)
        ->first();
      
      if ($document) {
        $idDocument = $document->id;
      } else {
        $idDocument = $mDocument->record->recordCreate([
          'model' => $model,
          'record_id' => $recordId,
          'name' => $outputFilename,
        ])['id'] ?? 0;
      }

      $version = $mDocumentVersion->record->recordCreate([
        'id_document' => $idDocument,
        'name' => $outputFilename,
        'file' => $outputFilename,
      ]);

      return (int) $version['id'];
    } else {
      return 0;
    }
  }

  /**
   * Shorthand for generatePdfDocumentFromTemplate with $createDocumentEntry set to true.
   *
   * @param string $model
   * @param int $recordId
   * @param int $idTemplate
   * @param string $outputFilename
   * @param array $vars
   * 
   * @return int
   * 
   */
  public function createPdfDocumentFromTemplate(
    string $model,
    int $recordId,
    int $idTemplate,
    string $outputFilename,
    array $vars
  ): int
  {
    return $this->generatePdfDocumentFromTemplate($model, $recordId, $idTemplate, $outputFilename, $vars, true);
  }
}