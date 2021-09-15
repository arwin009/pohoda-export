<?php
/**
 * Author: Ivo Toman
 */

namespace Pohoda;


class InvoiceItem
{
	const VAT_NONE = "none";
	const VAT_HIGH = "high";
	const VAT_LOW = "low";
    const VAT_THIRD = "third";
    const VAT_HISTORY_HIGH = "historyHigh";
    const VAT_HISTORY_LOW = "historyLow";

	private $text;
	private $quantity = '1.0';
	private $unit;
	private $coefficient = '1.0';
	private $payVAT = false; //unitPrice is with (true) or without (false) VAT
	private $rateVAT;
	private $percentVAT;
	private $discountPercentage;
	private $homeCurrency = [
		"unitPrice" => null, //mandatory
		"price" => null, //optional
		"priceVAT" => null, //optional
		"priceSum" => null, //only for export from pohoda
	];
	private $foreignCurrency = [
		"unitPrice" => null, //mandatory
		"price" => null, //optional
		"priceVAT" => null, //optional
		"priceSum" => null, //only for export from pohoda
	];
	private $note;
	private $code;
	private $guarantee = 48;
	private $guaranteeType = "month";

	private $stockItem; //odkaz na skladovou zasobu


	/**
	 * @return string $text
	 */
	public function getText()
	{
		return $this->text;
	}


	public function setText($text)
	{
		if (Validators::isMaxLength($text, 90) === false)
			$text = mb_substr($text, 0, 90);
		$this->text = $text;
	}

	/**
	 * @return float
	 */
	public function getQuantity()
	{
		return $this->quantity;
	}


	/**
	 * @param float $quantity
	 */
	public function setQuantity($quantity)
	{
		Validators::assertNumeric($quantity);
		$this->quantity = $quantity;
	}


	/**
	 * @return string $unit
	 */
	public function getUnit()
	{
		return $this->unit;
	}

	public function setUnit($unit)
	{
		Validators::assertMaxLength($unit, 10);
		$this->unit = $unit;
	}

	/**
	 * @return float
	 */
	public function getCoefficient()
	{
		return $this->coefficient;
	}

	/**
	 * @param float $coefficient
	 */
	public function setCoefficient($coefficient)
	{
		Validators::assertNumeric($coefficient);
		$this->coefficient = $coefficient;
	}

	/**
	 * @return boolean
	 */
	public function isPayVAT()
	{
		return $this->payVAT;
	}

	/**
	 * @param boolean $payVAT
	 */
	public function setPayVAT($payVAT)
	{
		Validators::assertBoolean($payVAT);
		$this->payVAT = $payVAT;
	}

	/**
	 * @param bool $bool
	 */
	public function setWithVAT($bool = true)
	{
		$this->setPayVAT($bool);
	}

	/**
	 * @return bool
	 */
	public function isWithVAT()
	{
		return $this->isPayVAT();
	}

	/**
	 * @return
	 */
	public function getRateVAT()
	{
		return $this->rateVAT;
	}

	/**
	 * @param mixed $rateVAT
	 */
	public function setRateVAT($rateVAT)
	{
		$rates = [
			self::VAT_NONE => "bez DPH",
			self::VAT_HIGH => "Základní sazba",
			self::VAT_LOW => "Snížena sazba",
            self::VAT_THIRD => "2. snížená sazba",
            self::VAT_HISTORY_HIGH => "Historická základní sazba",
            self::VAT_HISTORY_LOW => "Historická snížená sazba",
		];

		Validators::assertKeyInList($rateVAT, $rates);
		$this->rateVAT = $rateVAT;
	}

	/**
	 * @return float
	 */
	public function getPercentVAT()
	{
		return $this->percentVAT;
	}

	/**
	 * @param float $percentVAT
	 */
	public function setPercentVAT($percentVAT)
	{
		Validators::assertNumeric($percentVAT);
		$this->percentVAT = $percentVAT;
	}

	/**
	 * @return float
	 */
	public function getDiscountPercentage()
	{
		return $this->discountPercentage;
	}

	/**
	 * @param float $discountPercentage
	 */
	public function setDiscountPercentage($discountPercentage)
	{
		Validators::assertNumeric($discountPercentage);
		if ($discountPercentage < -999 || $discountPercentage > 999)
			throw new \InvalidArgumentException($discountPercentage . " must be betweeen -999 and 999");
		$this->discountPercentage = $discountPercentage;
	}

	/**
	 * @return array
	 */
	public function getHomeCurrency()
	{
		return $this->homeCurrency;
	}

	public function getUnitPrice()
	{
		return $this->homeCurrency["unitPrice"];
	}

	/**
	 * @param float $price
	 */
	public function setUnitPrice($price)
	{
		Validators::assertNumeric($price);
		$this->homeCurrency["unitPrice"] = $price;
	}

	public function getPrice()
	{
		return $this->homeCurrency["price"];
	}

	/**
	 * @param float $price
	 */
	public function setPrice($price)
	{
		//without tax (vat)
		Validators::assertNumeric($price);
		$this->homeCurrency["price"] = $price;
	}

	public function getPriceVAT()
	{
		return $this->homeCurrency["priceVAT"];
	}

	/**
	 * @param float $price
	 */
	public function setPriceVAT($price)
	{
		//only vat itself
		Validators::assertNumeric($price);
		$this->homeCurrency["priceVAT"] = $price;
	}


	public function getPriceSum()
	{
		return $this->homeCurrency["priceSum"];
	}


	public function setPriceSum($price)
	{
		//price with vat
		trigger_error("PriceSUM is only for export from POHODA");
		Validators::assertNumeric($price);
		$this->homeCurrency["priceSum"] = $price;
	}


	/**
	 * @return array
	 */
	public function getForeignCurrency()
	{
		return $this->foreignCurrency;
	}


	public function getForeignUnitPrice()
	{
		return $this->foreignCurrency["unitPrice"];
	}

	/**
	 * @param float $price
	 */
	public function setForeignUnitPrice($price)
	{
		Validators::assertNumeric($price);
		$this->foreignCurrency["unitPrice"] = $price;
	}

	public function getForeignPrice()
	{
		return $this->foreignCurrency["price"];
	}

	/**
	 * @param float $price
	 */
	public function setForeignPrice($price)
	{
		//without tax (vat)
		Validators::assertNumeric($price);
		$this->foreignCurrency["price"] = $price;
	}

	public function getForeignPriceVAT()
	{
		return $this->foreignCurrency["priceVAT"];
	}

	/**
	 * @param float $price
	 */
	public function setForeignPriceVAT($price)
	{
		//only vat itself
		Validators::assertNumeric($price);
		$this->foreignCurrency["priceVAT"] = $price;
	}


	public function getForeignPriceSum()
	{
		return $this->foreignCurrency["priceSum"];
	}


	public function setForeignPriceSum($price)
	{
		//price with vat
		trigger_error("PriceSUM is only for export from POHODA");
		Validators::assertNumeric($price);
		$this->foreignCurrency["priceSum"] = $price;
	}

	/**
	 * @return string
	 */
	public function getNote()
	{
		return $this->note;
	}

	/**
	 * @param string $note
	 */
	public function setNote($note)
	{
		if (Validators::isMaxLength($note, 90) === false)
			$note = mb_substr($note, 0, 90);
		$this->note = $note;
	}

	/**
	 * @return null|string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param string $code
	 */
	public function setCode($code)
	{
		Validators::assertMaxLength($code, 64);
		$this->code = $code;
	}

	/**
	 * @return int
	 */
	public function getGuarantee()
	{
		return $this->guarantee;
	}

	/**
	 * @param int $guarantee
	 */
	public function setGuarantee($guarantee)
	{
		Validators::assertNumeric($guarantee);
		$this->guarantee = $guarantee;
	}

	/**
	 * @return string
	 */
	public function getGuaranteeType()
	{
		return $this->guaranteeType;
	}

	/**
	 * @param string $guaranteeType
	 */
	public function setGuaranteeType($guaranteeType)
	{
		$types = [
			"none" => "bez záruky",
			"hour" => "v hodinách",
			"day" => "ve dnech",
			"month" => "v měsících",
			"year" => "v letech",
			"life" => "doživotní záruka",
		];

		Validators::assertKeyInList($guaranteeType, $types);
		$this->guaranteeType = $guaranteeType;
	}

	/**
	 * @return mixed
	 */
	public function getStockItem()
	{
		return $this->stockItem;
	}

	/**
	 * @param mixed $stockItem
	 */
	public function setStockItem($stockItem)
	{
		$this->stockItem = $stockItem;
	}


}
