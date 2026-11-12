<?php
/**
 * Author: Ivo Toman
 */

namespace Pohoda\Object;


use Pohoda\Validators;

class Identity
{

	/** @var mixed max. 19 chars */
	private $id = null;

	/** @var Address */
	private $address;

	/** @var Address|null */
	private $shippingAddress;


	/**
	 * Identity constructor.
	 * @param mixed|null $id
	 * @param Address $address
	 * @param Address|null $shippingAddress
	 */
	public function __construct($id, Address $address, ?Address $shippingAddress = null)
	{
		$this->setId($id);
		$this->address = $address;
		$this->shippingAddress = $shippingAddress;
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id)
	{
		Validators::assertMaxLength($id, 19);
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return bool
	 */
	public function hasId()
	{
		return !is_null($this->id);
	}


	/**
	 * @return Address
	 */
	public function getAddress()
	{
		return $this->address;
	}


	/**
	 * @return null|Address
	 */
	public function getShippingAddress()
	{
		return $this->shippingAddress;
	}


	/**
	 * @return bool
	 */
	public function hasShippingAddress()
	{
		return !is_null($this->shippingAddress);
	}



}