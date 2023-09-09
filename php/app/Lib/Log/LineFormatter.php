<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-09-09
 * Time: 14:15
 */

namespace App\Lib\Log;

use Monolog\Formatter\LineFormatter as BaseLineFormatter;
use Monolog\Formatter\NormalizerFormatter;

class LineFormatter extends BaseLineFormatter
{
    public const SIMPLE_FORMAT = "[%datetime%] [" . LOG_ID . "] %channel%.%level_name%: %message% %context% %extra%\n";

    public const DATE_FORMAT = 'Y-m-d H:i:s';

    public function __construct($format = null, $dateFormat = null, $allowInlineLineBreaks = false, $ignoreEmptyContextAndExtra = false, bool $includeStacktraces = false)
    {
        $format = self::SIMPLE_FORMAT;
        $dateFormat = self::DATE_FORMAT;

        parent::__construct($format, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra, $includeStacktraces);
    }


    public function format(array $record): string
    {
        $vars = (new NormalizerFormatter(self::DATE_FORMAT))->format($record);

        $output = self::SIMPLE_FORMAT;

        foreach ($vars['extra'] as $var => $val) {
            if (false !== strpos($output, '%extra.'.$var.'%')) {
                $output = str_replace('%extra.'.$var.'%', $this->stringify($val), $output);
                unset($vars['extra'][$var]);
            }
        }

        foreach ($vars['context'] as $var => $val) {
            if (false !== strpos($output, '%context.'.$var.'%')) {
                $output = str_replace('%context.'.$var.'%', $this->stringify($val), $output);
                unset($vars['context'][$var]);
            }
        }

        if ($this->ignoreEmptyContextAndExtra) {
            if (empty($vars['context'])) {
                unset($vars['context']);
                $output = str_replace('%context%', '', $output);
            }

            if (empty($vars['extra'])) {
                unset($vars['extra']);
                $output = str_replace('%extra%', '', $output);
            }
        }

        foreach ($vars as $var => $val) {
            if (false !== strpos($output, '%'.$var.'%')) {
                $output = str_replace('%'.$var.'%', $this->stringify($val), $output);
            }
        }

        // remove leftover %extra.xxx% and %context.xxx% if any
        if (false !== strpos($output, '%')) {
            $output = preg_replace('/%(?:extra|context)\..+?%/', '', $output);
        }

        return $output;
    }
}
