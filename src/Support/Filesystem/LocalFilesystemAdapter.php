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
	 * @return $this
	 */
	public function build(string $path = ''): static
	{
		$fullPath = $path;
		if ($path[0] != '/') {
			$fullPath = $this->path($path);
		}
		
		return new static([
			'root' => $fullPath,
		]);
	}
	
	/**
	 * get parent folder as disk
	 *
	 * @throws \Exception
	 * @return $this
	 */
	public function parent(): static
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
	public function selfFlush(): bool
	{
		return $this->parent()->flush($this->basename());
	}
	
	/**
	 * Delete current working directory
	 *
	 * @throws \Exception
	 * @return bool
	 */
	public function selfDelete(): bool
	{
		return $this->parent()->deleteDirectory($this->basename());
	}
	
	public function flush(string $path): bool
	{
		return File::cleanDirectory($this->path($path));
	}
	
}