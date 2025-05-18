<?php
namespace Cylancer\Usertools\ViewHelpers;


use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This file is part of the "user tools" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 C. Gogolin <service@cylancer.net>
 * 
 */
class InArrayViewHelper extends AbstractViewHelper
{

    public function initializeArguments(): void
    {
        $this->registerArgument('value', '*', 'The value ', true);
        $this->registerArgument('array', 'string,array', 'The array', true);
    }

    public function render(): string
    {
        
        $array = is_array($this->arguments['array']) ? $this->arguments['array'] : explode(',', $this->arguments['array']);
        return in_array($this->arguments['value'], $array);
        
    }
}
