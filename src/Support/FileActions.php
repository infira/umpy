<?php

namespace Infira\Umpy\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\File;

class FileActions
{
	private ?string    $baseFile;
	private Filesystem $filesystem;
	private File       $file;
	
	public function __construct(string $file, Filesystem $filesystem)
	{
		$this->baseFile   = $file;
		$this->filesystem = $filesystem;
	}
	
	private function getPath(string $overwriteName = null): string
	{
		$fileName = $overwriteName ?: $this->baseFile;
		
		return $this->filesystem->path($fileName);
	}
	
	public function path(): string
	{
		return $this->getPath();
	}
	
	
	public function file(): File
	{
		if (!$this->file) {
			$this->file = new File($this->getPath());
		}
		
		return $this->file;
	}
	
	public function remove(): bool
	{
		return $this->filesystem->delete($this->baseFile);
	}
	
	public function getBasename(): string
	{
		return $this->file()->getBasename();
	}
	
	public function getFilename(): string
	{
		return $this->file()->getFilename();
	}
	
	public function getExtension(): string
	{
		return $this->file()->getExtension();
	}
	
	public function exists(): bool
	{
		return $this->filesystem->exists($this->baseFile);
	}
	
	public function rename(string $newName): bool
	{
		return rename($this->getPath(), $this->getPath($newName));
	}
	
	public function copy(string $newName): bool
	{
		return $this->filesystem->copy($this->getPath(), $this->getPath($newName));
	}
}
