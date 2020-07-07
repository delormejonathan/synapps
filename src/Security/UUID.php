<?php

namespace Inneair\Synapps\Security;

use Exception;
use Inneair\Synapps\System\OS;

/**
 * This class encapsulates an immutable universally unique identifier (UUID).
 */
class UUID
{
    /**
     * Low part of time.
     * @var string
     */
    private $timeLow;
    /**
     * Middle part of time.
     * @var string
     */
    private $timeMid;
    /**
     * High part of time, and version.
     * @var int
     */
    private $timeHighAndVersion;
    /**
     * Variant and sequence.
     * @var int
     */
    private $variantAndSequence;
    /**
     * Node (IEEE 802 MAC address).
     * @var string
     */
    private $node;

    /**
     * Builds a UUID. Use the randomUuid method to get an instance of this class.
     *
     * @param string $timeLow
     * @param string $timeMid
     * @param int $timeHighAndVersion
     * @param int $variantAndSequence
     * @param string $node
     */
    private function __construct($timeLow, $timeMid, $timeHighAndVersion, $variantAndSequence, $node)
    {
        $this->timeLow = $timeLow;
        $this->timeMid = $timeMid;
        $this->timeHighAndVersion = $timeHighAndVersion;
        $this->variantAndSequence = $variantAndSequence;
        $this->node = $node;
    }

    /**
     * Generates a Universally Unique IDentifier, version 4.
     *
     * This function generates a truly random UUID. The built in CakePHP String::uuid() function
     * is not cryptographically secure. You should use this function instead.
     *
     * @see http://tools.ietf.org/html/rfc4122#section-4.4
     * @see http://en.wikipedia.org/wiki/UUID
     * @throws Exception if something went wrong getting random number from <code>/dev/random</code> command line.
     * @return UUID The UUDI.
     */
    public static function randomUuid()
    {
        $pr_bits = null;
        $fp = false;
        if (OS::getInstance()->isWindows()) {
            // If /dev/urandom isn't available (eg: in non-unix systems), use mt_rand().
            $pr_bits = "";
            for ($cnt = 0; $cnt < 16; $cnt++) {
                $pr_bits .= chr(mt_rand(0, 255));
            }
        } else {
            try {
                $fp = @fopen('/dev/urandom', 'rb');
                $pr_bits .= @fread($fp, 16);
            } finally {
                @fclose($fp);
            }
        }

        $time_low = bin2hex(substr($pr_bits, 0, 4));
        $time_mid = bin2hex(substr($pr_bits, 4, 2));
        $time_hi_and_version = bin2hex(substr($pr_bits, 6, 2));
        $clock_seq_hi_and_reserved = bin2hex(substr($pr_bits, 8, 2));
        $node = bin2hex(substr($pr_bits, 10, 6));

        /**
         * Set the four most significant bits (bits 12 through 15) of the
         * time_hi_and_version field to the 4-bit version number from
         * Section 4.1.3.
         *
         * @see http://tools.ietf.org/html/rfc4122#section-4.1.3
         */
        $time_hi_and_version = hexdec($time_hi_and_version);
        $time_hi_and_version = $time_hi_and_version >> 4;
        $time_hi_and_version = $time_hi_and_version | 0x4000;

        /**
         * Set the two most significant bits (bits 6 and 7) of the
         * clock_seq_hi_and_reserved to zero and one, respectively.
         */
        $clock_seq_hi_and_reserved = hexdec($clock_seq_hi_and_reserved);
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;

        return new UUID($time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node);
    }

    /**
     * {@inheritDoc}
     *
     * @return string String representation of this UUID, made up of 32 hex digits and 4 hyphens:
     * <time_low> '-' <time_mid> '-' <time_high_and_version> '-' <variant_and_sequence> '-' <node>
     */
    public function __toString()
    {
        return sprintf(
            '%08s-%04s-%04x-%04x-%012s',
            $this->timeLow,
            $this->timeMid,
            $this->timeHighAndVersion,
            $this->variantAndSequence,
            $this->node
        );
    }
}
