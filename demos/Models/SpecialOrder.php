<?php
namespace Demo\Models;

class SpecialOrder extends Order {

    public string $special = "Special";

    public \DateTime $dateTime;
}