<?php
namespace Pyther\Json\Types;

/**
 * Enumeration of enum formats
 */
enum EnumFormat {
    /**
     * (De)serialize the enum by enum value (Backed Enumerations only).
     */
    case Value;

    /**
     * (De)serialize the enum by enum name (Basic and Backed Enumeration).
     */
    case Name;

    /**
     * (De)serialize the enum by enum value and name (Basic and Backed Enumeration).
     */
    case Full;
}