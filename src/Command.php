<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH;

final class Command
{
	private string $command;
	private string $io;
	private array $options = [];
	private array $chains = [];
	private array $pipes = [];

	public function __construct(string $command, ?string ...$options)
	{
		$this->command = $command;
		$this->setOptions(...$options);
	}


	public function __toString(): string
	{
		return $this->create();
	}


	public function create(): string
	{
		$options = null;
		$chains = null;
		$pipes = null;

		foreach ($this->options as $option) {
			$options .= ' '.(trim($option[0].' '.implode(' ', $option[1])));
		}

		foreach ($this->pipes as $pipe) {
			$pipes .= ' | '.$pipe;
		}

		foreach ($this->chains as $chain) {
			$chains .= '; '.$chain;
		}

		return $this->command.$options.$this->io.$pipes.$chains;
	}


	public function toFile(?string $file, bool $append = false): self
	{
		if (!empty($file)) {
			$method = $append ? ' >> ' : ' > ';
			$file = $method.$file;
		}

		$this->io = $file;
		return $this;
	}


	public function fromFile(?string $file): self
	{
		if (!empty($file)) {
			$file = ' < '.$file;
		}

		$this->io = $file;
		return $this;
	}


	public function setOption(string $key, string ...$values): self
	{
		if (empty($values)) {
			$values = explode(' ', $key);
			$key = array_shift($values);
		}

		$this->options[] = [$key, $values];
		return $this;
	}


	public function setOptions(?string ...$options): self
	{
		foreach (array_filter($options) as $option) {
			$this->setOption($option);
		}

		return $this;
	}


	public function addPipe(self $command): self
	{
		$this->pipes[] = $command;
		return $this;
	}


	public function addChain(self $command): self
	{
		$this->chains[] = $command;
		return $this;
	}
}
