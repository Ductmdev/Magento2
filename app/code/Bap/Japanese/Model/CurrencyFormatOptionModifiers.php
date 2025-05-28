<?php
declare(strict_types=1);

namespace Bap\Japanese\Model;

use CommunityEngineering\JapaneseYenFormatting\Model\Config\Source\YenFormatting;
use CommunityEngineering\JapaneseYenFormatting\Model\Config\YenFormattingConfig;
use CommunityEngineering\JapaneseYenFormatting\Model\CurrencyFormatOptionModifiers as ModelCurrencyFormatOptionModifiers;

class CurrencyFormatOptionModifiers extends ModelCurrencyFormatOptionModifiers
{
    private $yenFormattingConfigChild;

    public function __construct(YenFormattingConfig $yenFormattingConfig)
    {
        parent::__construct($yenFormattingConfig);
        $this->yenFormattingConfigChild = $yenFormattingConfig;
    }

    public function getOptions(string $currencyCode): array
    {
        if ($currencyCode !== 'JPY') {
            return [];
        }

        switch ($this->yenFormattingConfigChild->getFormat()) {
            case YenFormatting::DEFAULT:
                return [];
            case YenFormatting::KANJI:
                return [
                    'position' => \Magento\Framework\Currency\Data\Currency::RIGHT,
                    'symbol' => (string) __('Yen'),
                ];
            default:
                throw new \InvalidArgumentException('Unsupported format');
        }
    }
}
