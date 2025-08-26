<?php

namespace HubletoMain;

class Renderer extends \Hubleto\Framework\Renderer
{

  public function init(): void
  {
    parent::init();

    $this->twigLoader->addPath(__DIR__ . '/../views', 'hubleto-main');
    $this->twigLoader->addPath(__DIR__ . '/../../apps/src', 'app');

    if (is_dir($this->getEnv()->projectFolder . '/src/views')) {
      $this->twigLoader->addPath($this->getEnv()->projectFolder . '/src/views', 'project');
    }

  }

  /**
   * Callback called before the rendering starts.
   *
   * @return void
   * 
   */
  public function onBeforeRender(): void
  {
    parent::onBeforeRender();
    $this->getAppManager()->onBeforeRender();
  }

}