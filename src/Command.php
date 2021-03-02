<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2020
 * @license   MIT License
 */

namespace JuniWalk\SSH;

final class Command
{
	/** @var string */
	private $command;

	/** @var string */
	private $io;

	/** @var string[] */
	private $options = [];

	/** @var static[] */
	private $pipes = [];


	/**
	 * @param string  $command
	 * @param string|null  $options ...
	 */
	public function __construct(string $command, ?string ... $options)
	{
		$this->command = $command;

		$this->setOptions(... $options);
	}


	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->create();
	}


	/**
	 * @return string
	 */
	public function create(): string
	{
		$options = null;
		$pipes = null;

		foreach ($this->options as $option) {
			$options .= ' '.(trim($option[0].' '.implode(' ', $option[1])));
		}

		foreach ($this->pipes as $pipe) {
			$pipes .= ' | '.$pipe;
		}

		return $this->command.$options.$this->io.$pipes;
	}


	/**
	 * @param  string|null  $file
	 * @return static
	 */
	public function toFile(?string $file): self
	{
		if (!empty($file)) {
			$file = ' > '.$file;
		}

		$this->io = $file;
	}


	/**
	 * @param  string|null  $file
	 * @return static
	 */
	public function fromFile(?string $file): self
	{
		if (!empty($file)) {
			$file = ' < '.$file;
		}

		$this->io = $file;
	}


	/**
	 * @param  string  $key
	 * @param  string[]  $values ...
	 * @return static
	 */
	public function setOption(string $key, string ... $values): self
	{
		if (empty($values)) {
			$values = explode(' ', $key);
			$key = array_shift($values);
		}

		$this->options[] = [$key, $values];
		return $this;
	}


	/**
	 * @param  string|null  $params ...
	 * @return static
	 */
	public function setOptions(?string ... $options): self
	{
		foreach (array_filter($options) as $option) {
			$this->setOption($option);
		}

		return $this;
	}


	/**
	 * @param  static  $command
	 * @return static
	 */
	public function addPipe(self $command): self
	{
		$this->pipes[] = $command;
		return $this;
	}
}
