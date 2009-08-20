<?php

/**
 * Deletes all the files (models, tables, filters, forms) related to a model.
 *
 * @package       uvmcDoctrineTaskExtra
 * @subpackage    task
 * @author        Marc Weistroff <mweistroff@uneviemoinschere.com>
 * @version       $Id$
 */
class deletefilesofmodelTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
     $this->addArguments(array(
       new sfCommandArgument('model_name', sfCommandArgument::REQUIRED, 'model name'),
     ));

    $this->namespace        = 'doctrine';
    $this->name             = 'delete-files-of-model';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [delete-files-of-model|INFO] deletes all the files (model, tables, filters, forms) related to a model.

  [php symfony delete-files-of-model|INFO model_name]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $modelName = $arguments['model_name'];
    // add your code here
    $confirm = $this->askConfirmation(array(sprintf('This will delete all the files related to model %s', $modelName),
                                            sprintf('Are you sure you want to proceed (y/N)')),
                                      'QUESTION',
                                      false);

    if(!$confirm)
    {
      $this->log('Operation aborted.', 'INFO');
      return 1;
    }

    
    $rootDir = sfConfig::get('sf_root_dir');
    foreach($derivatedNames as $toEradicate)
    {
      $files = sfFinder::type('file')->name($toEradicate.'.class.php')->in($rootDir);
      foreach($files as $file)
      {
        unlink($file);
        $this->logSection('file-', $file);
      }
    }
  }

  /**
   * Generate all the file names we could encounter...
   * @param string $modelName
   * @return array
   */
  protected function generateDerivatedNames($modelName)
  {
    $prefixes = array('', 'Plugin', 'Base');
    $suffixes = array('', 'Table', 'Form', 'FormFilter');

    $derivated = array();
    foreach($prefixes as $prefixe)
    {
      foreach($suffixes as $suffixe)
      {
        $derivated[] = $prefixe.$modelName.$suffixe;
      }
    }

    return $derivated;
  }
}
