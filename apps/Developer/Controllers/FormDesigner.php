<?php

namespace HubletoApp\Community\Developer\Controllers;

class FormDesigner extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'developer', 'content' => $this->translate('Developer') ],
      [ 'url' => 'form-designer', 'content' => $this->translate('Form designer') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $template = $this->main->urlParamAsString('template');

    $renderContentCode = '';

    switch ($template) {
      case 'one-column':
        $renderContentCode = <<<EOL
  renderContent(): JSX.Element {
    return <>
      {this.divider(this.translate('Divider #1'))}
      {this.inputWrapper('input_for_column_1')}
      {this.inputWrapper('input_for_column_2')}
      {this.inputWrapper('input_for_column_3')}
      {this.divider(this.translate('Divider #2'))}
      {this.inputWrapper('input_for_column_4')}
      {this.inputWrapper('input_for_column_5')}
      {this.inputWrapper('input_for_column_6')}
    </>;
  }
EOL;
        break;
      case 'two-columns':
        $renderContentCode = <<<EOL
  renderContent(): JSX.Element {
    return <>
      <div className='w-full flex gap-2'>
        <div className='flex-1 border-r border-gray-100'>
          {this.divider(this.translate('Divider #1.1'))}
          {this.inputWrapper('input_for_column_1')}
          {this.inputWrapper('input_for_column_2')}
          {this.inputWrapper('input_for_column_3')}
          {this.divider(this.translate('Divider #1.2'))}
          {this.inputWrapper('input_for_column_4')}
          <div className='flex gap-2'>
            <div className='w-full'>{this.inputWrapper('input_for_column_5')}</div>
            <div className='w-full'>{this.inputWrapper('input_for_column_6')}</div>
          </div>
        </div>
        <div className='flex-1'>
          {this.divider(this.translate('Divider #2.1'))}
          {this.inputWrapper('input_for_column_1')}
          {this.inputWrapper('input_for_column_2')}
          {this.inputWrapper('input_for_column_3')}
          {this.divider(this.translate('Divider #2.2'))}
          {this.inputWrapper('input_for_column_1')}
          {this.inputWrapper('input_for_column_2')}
          {this.inputWrapper('input_for_column_3')}
        </div>
      </div>
    </>;
  }
EOL;
        break;
    }

    $this->viewParams['renderContentCode'] = $renderContentCode;

    $this->setView('@HubletoApp:Community:Developer/FormDesigner.twig');
  }

}
