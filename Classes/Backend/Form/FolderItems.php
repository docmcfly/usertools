<?php
namespace Cylancer\Usertools\Backend\Form;

use TYPO3\CMS\Core\Core\Environment;

/**
 * This file is part of the "user tools" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2022 Clemens Gogolin <service@cylancer.net>
 */
class FolderItems
{

    /**
     * Adds the fileadmin folders as items to the configuration..
     *
     * @param array $configuration
     *            Current field configuration
     */
    public function addFolderItems(array &$configuration)
    {
        $list = [];
        $this->subfolder(Environment::getPublicPath() . '/fileadmin', '', '', $list);

        foreach ($list as $label => $dir) {
            $configuration['items'][] = [
                $label,
                $dir
            ];
        }
    }

    const NOT_ALLOWED_FOLDER = [
        '..',
        '.',
        '_processed_'
    ];

    private function subfolder($root, $pathPrefix, $path, &$list)
    {
        if ($handle = opendir($root . $pathPrefix . $path)) {
            while (false !== ($entry = readdir($handle))) {
                if (! (in_array($entry, FolderItems::NOT_ALLOWED_FOLDER))) {
                    $tmp = $path . '/' . $entry;
                    if (is_dir($root . $pathPrefix . $tmp)) {
                        $list[$tmp] = $pathPrefix . $tmp;
                        $this->subfolder($root, $pathPrefix, $tmp, $list);
                    }
                }
            }
        }
        closedir($handle);
    }
}