<?php

namespace Infira\Umpy\console\helper;

use Infira\Utils\Dir;

class SqlFileCollector extends FileCollector
{
	public function addFiles(array $files)
	{
		foreach ($files as $file) {
			if (is_dir($file)) {
				$this->addFiles(Dir::getContent($file, ['sql']));
			}
			elseif (is_file($file)) {
				if (in_array($file, $this->voidFiles)) {
					continue;
				}
				$realExtension = pathinfo($file)['extension'];
				if ($realExtension == 'sql') {
					$this->dbFiles[] = $file;
				}
				else {
					$ex  = explode('.', $file);
					$ext = strtolower(join('.', array_slice($ex, -2)));
					if ($ext == 'sql.php') {
						$this->dbFiles[] = $file;
					}
				}
			}
			else {
				$this->cmd->getOutput()->error('File is not file or path(' . $file . ') not found');
			}
		}
	}
}
