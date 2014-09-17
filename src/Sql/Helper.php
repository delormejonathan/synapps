<?php

namespace Inneair\Synapps\Sql;

/**
 * Class containing helper functions for SQL language, with multibyte strings support, should this happen. The class may
 * be extended to override default ISO behaviour.
 */
class Helper
{
    /**
     * Wildcard character used to match any string in a LIKE clause.
     * @var string
     */
    const LIKE_ANY_CHAR_WILDCARD = '_';
    /**
     * Wildcard character used to match any character in a LIKE clause.
     * @var string
     */
    const LIKE_ANY_STRING_WILDCARD = '%';
    /**
     * Default escape character used in LIKE pattern.
     * @var string
     */
    const LIKE_DEFAULT_ESCAPE_CHAR = '\\';
    /**
     * Pattern used to capture special characters in LIKE pattern.
     * @var string
     */
    const LIKE_SPECIAL_CHARACTERS_PATTERN = '(_|%|\\\\)';

    /**
     * Empty private constructor to prevent erroneous instanciations.
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Escapes special characters '_' and '%' if used in a pattern for a LIKE clause. The escape character is the anti-
     * slash character '\', which is espaced too if present in the pattern.
     *
     * @param string $pattern Pattern.
     * @return string The escaped pattern. If the pattern is <code>null</code>, an empty string is returned.
     */
    public static function escapeLikePattern($pattern)
    {
        // We use the pattern directly instead of building the pattern with the constants in the class, for performance
        // only.
        return mb_ereg_replace(self::LIKE_SPECIAL_CHARACTERS_PATTERN, '\\\\1', $pattern);
    }
}
