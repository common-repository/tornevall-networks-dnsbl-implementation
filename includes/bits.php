<?php

/**
 * Copyright 2018 Tomas Tornevall & Tornevall Networks
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Tornevall Networks netCurl library - Yet another http- and network communicator library
 * Each class in this library has its own version numbering to keep track of where the changes are. However, there is a
 * major version too.
 *
 * @package    TorneLIB
 * @version    6.0.1
 * @deprecated Use composerized tornevall/tornelib-php-bitmask instead
 */

namespace Tornevall_WP_DNSBL;

if (!defined('NETCURL_NETBITS_RELEASE')) {
    define('NETCURL_NETBITS_RELEASE', '6.0.1');
}
if (!defined('NETCURL_NETBITS_MODIFY')) {
    define('NETCURL_NETBITS_MODIFY', '20180320');
}

// Check if there is a packagist release already loaded, since this network standalone release is deprecated as of 20180320.
if (!class_exists('MODULE_NETBITS') && !class_exists('Tornevall_WP_DNSBL\MODULE_NETBITS')) {
    /**
     * Class TorneLIB_NetBits Netbits Library for calculations with bitmasks
     *
     * @package TorneLIB
     * @version 6.0.1
     */
    class MODULE_NETBITS
    {
        /** @var array Standard bitmask setup */
        private $BIT_SETUP;
        private $maxBits = 8;

        public function __construct($bitStructure = array())
        {
            $this->BIT_SETUP = array(
                'OFF' => 0,
                'BIT_1' => 1,
                'BIT_2' => 2,
                'BIT_4' => 4,
                'BIT_8' => 8,
                'BIT_16' => 16,
                'BIT_32' => 32,
                'BIT_64' => 64,
                'BIT_128' => 128
            );
            if (is_array($bitStructure) && count($bitStructure)) {
                $this->BIT_SETUP = $this->validateBitStructure($bitStructure);
            }
        }

        public function setMaxBits($maxBits = 8)
        {
            $this->maxBits = $maxBits;
            $this->validateBitStructure($maxBits);
        }

        public function getMaxBits()
        {
            return $this->maxBits;
        }

        private function getRequiredBits($maxBits = 8)
        {
            $requireArray = array();
            if ($this->maxBits != $maxBits) {
                $maxBits = $this->maxBits;
            }
            for ($curBit = 0; $curBit <= $maxBits; $curBit++) {
                $requireArray[] = (int)pow(2, $curBit);
            }

            return $requireArray;
        }

        private function validateBitStructure($bitStructure = array())
        {
            if (is_numeric($bitStructure)) {
                $newBitStructure = array(
                    'OFF' => 0
                );
                for ($bitIndex = 0; $bitIndex <= $bitStructure; $bitIndex++) {
                    $powIndex = pow(2, $bitIndex);
                    $newBitStructure["BIT_" . $powIndex] = $powIndex;
                }
                $bitStructure = $newBitStructure;
                $this->BIT_SETUP = $bitStructure;
            }
            $require = $this->getRequiredBits(count($bitStructure));
            $validated = array();
            $newValidatedBitStructure = array();
            $valueKeys = array();
            foreach ($bitStructure as $key => $value) {
                if (in_array($value, $require)) {
                    $newValidatedBitStructure[$key] = $value;
                    $valueKeys[$value] = $key;
                    $validated[] = $value;
                }
            }
            foreach ($require as $bitIndex) {
                if (!in_array($bitIndex, $validated)) {
                    if ($bitIndex == "0") {
                        $newValidatedBitStructure["OFF"] = $bitIndex;
                    } else {
                        $bitIdentificationName = "BIT_" . $bitIndex;
                        $newValidatedBitStructure[$bitIdentificationName] = $bitIndex;
                    }
                } else {
                    if (isset($valueKeys[$bitIndex]) && !empty($valueKeys[$bitIndex])) {
                        $bitIdentificationName = $valueKeys[$bitIndex];
                        $newValidatedBitStructure[$bitIdentificationName] = $bitIndex;
                    }
                }
            }
            asort($newValidatedBitStructure);
            $this->BIT_SETUP = $newValidatedBitStructure;

            return $newValidatedBitStructure;
        }

        public function setBitStructure($bitStructure = array())
        {
            $this->validateBitStructure($bitStructure);
        }

        public function getBitStructure()
        {
            return $this->BIT_SETUP;
        }

        /**
         * Finds out if a bitmasked value is located in a bitarray
         *
         * @param int $requestedExistingBit
         * @param int $requestedBitSum
         *
         * @return bool
         */
        public function isBit($requestedExistingBit = 0, $requestedBitSum = 0)
        {
            $return = false;
            if (is_array($requestedExistingBit)) {
                foreach ($requestedExistingBit as $bitKey) {
                    if (!$this->isBit($bitKey, $requestedBitSum)) {
                        return false;
                    }
                }

                return true;
            }

            // Solution that works with unlimited bits
            for ($bitCount = 0; $bitCount < count($this->getBitStructure()); $bitCount++) {
                if ($requestedBitSum & pow(2, $bitCount)) {
                    if ($requestedExistingBit == pow(2, $bitCount)) {
                        $return = true;
                    }
                }
            }

            return $return;
        }

        /**
         * Get active bits in an array
         *
         * @param int $bitValue
         *
         * @return array
         */
        public function getBitArray($bitValue = 0)
        {
            $returnBitList = array();
            foreach ($this->BIT_SETUP as $key => $value) {
                if ($this->isBit($value, $bitValue)) {
                    $returnBitList[] = $key;
                }
            }

            return $returnBitList;
        }
    }
}

if (!class_exists('TorneLIB_NetBits') && !class_exists('TorneLIB\TorneLIB_NetBits')) {
    /**
     * Class TorneLIB_NetBits
     *
     * @package    TorneLIB
     * @deprecated Use MODULE_NETBITS
     */
    class TorneLIB_NetBits extends MODULE_NETBITS
    {
        function __construct(array $bitStructure = array())
        {
            parent::__construct($bitStructure);
        }
    }
}
