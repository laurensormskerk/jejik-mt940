<?php

declare(strict_types=1);

/*
 * This file is part of the Jejik\MT940 library
 *
 * Copyright (c) 2012 Sander Marechal <s.marechal@jejik.com>
 * Licensed under the MIT license
 *
 * For the full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 */

namespace Jejik\MT940\Parser;

/**
 * Parser for ABN-AMRO documents
 *
 * @author Sander Marechal <s.marechal@jejik.com>
 */
class AbnAmroNew extends AbstractParser
{
    /**
     * Test if the document is an ABN-AMRO document
     */
    public function accept(string $text): bool
    {
        if (empty($text)) {
            return false;
        }
        return substr($text, 0, 6) === 'ABNANL';
    }

    /**
     * Get the contra account from a transaction
     *
     * @param array $lines The transaction text at offset 0 and the description at offset 1
     */
    protected function contraAccountNumber(array $lines): ?string
    {
        if (!isset($lines[1])) {
            return null;
        }

		$number = null;

		if (preg_match('/iban: (.*?)\s*\w*: /im', $lines[1], $match)) {
			$number = $match[1];
		}

		if ($number) {
			return $number;
		}

		return null;
    }

    /**
     * Get the contra account holder name from a transaction
     *
     * There is only a countra account name if there is a contra account number
     * The name immediately follows the number in the first 32 characters of the first line
     * If the charaters up to the 32nd after the number are blank, the name is found in
     * the rest of the line.
     *
     * @param array $lines The transaction text at offset 0 and the description at offset 1
     */
    protected function contraAccountName(array $lines): ?string
    {
        if (!isset($lines[1])) {
            return null;
        }

        $name = null;

		if (preg_match('/naam: (.*?)\s*\w*: /im', $lines[1], $match)) {
			$name = $match[1];
		}

		if ($name) {
			return $name;
		}

        return null;
    }

    /**
     * Get an array of allowed BLZ for this bank
     */
    public function getAllowedBLZ(): array
    {
        return [];
    }
}
