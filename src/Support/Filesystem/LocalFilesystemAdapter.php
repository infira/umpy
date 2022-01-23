<?php

namespace Infira\Umpy\Support\Filesystem;

use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Adapter\Local as LocalAdapter;
use Illuminate\Support\Facades\File;

class LocalFilesystemAdapter extends FilesystemAdapter
{
	public function __construct(array $config)
	{
		if (!isset($config['root'])) {
			return throw new \Exception('Missing root');
		}
		$permissions = $config['permissions'] ?? [];
		
		$links = ($config['links'] ?? null) === 'skip'
			? LocalAdapter::SKIP_LINKS
			: LocalAdapter::DISALLOW_LINKS;
		
		$adapter = new LocalAdapter(
			$config['root'], $config['lock'] ?? LOCK_EX, $links, $permissions
		);
		parent::__construct(new \League\Flysystem\Filesystem($adapter));
	}
	
	/**
	 * Build new disk from path
	 *
	 * @param string $path
	 * @throws \Exception
	 * @return LocalFilesystemAdapter
	 */
	public function build($path): LocalFilesystemAdapter
	{
		return new LocalFilesystemAdapter([
			'root' => $path,
		]);
	}
	
	/**
	 * get parent folder as disk
	 *
	 * @throws \Exception
	 * @return \Infira\Umpy\Support\Filesystem\LocalFilesystemAdapter
	 */
	public function parent(): LocalFilesystemAdapter
	{
		return $this->build(realpath($this->path('../')));
	}
	
	/**
	 * get current working path
	 *
	 * @return string
	 */
	public function cwd()
	{
		return $this->driver->getAdapter()->getPathPrefix();
	}
	
	/**
	 * Extract the trailing name component from a file path.
	 *
	 * @param string $path
	 * @return string
	 */
	public function basename(string $path = ''): string
	{
		return File::basename($this->path($path));
	}
	
	/**
	 * Extract the parent directory from a file path.
	 *
	 * @param string $path
	 * @return string
	 */
	public function dirname(string $path = ''): string
	{
		return File::dirname($this->path($path));
	}
	
	/**
	 * Flush current working directory
	 *
	 * @throws \Exception
	 * @return bool
	 */
	public function flush(): bool
	{
		return $this->parent()->deleteDirectory($this->basename());
	}
	
	
	/**
	 * Flush current working directory
	 *
	 * @param string $path
	 * @throws \Exception
	 * @return bool
	 */
	public function flushDirectory(string $path): bool
	{
		return $this->build($path)->flush();
	}
	
}