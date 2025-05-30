<?php
declare(strict_types=1);

namespace Bap\JapanAddress\Setup\Patch\Data;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Directory\Helper\Data;

class AddDataForJapan implements DataPatchInterface
{
    private $moduleDataSetup;
    private $resourceConnection;
    private $data;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        Data $data,
        ResourceConnection $resourceConnection
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->data = $data;
        $this->resourceConnection = $resourceConnection;
    }

    public function apply()
    {
        /** @var AdapterInterface $adapter */
        $adapter = $this->moduleDataSetup->getConnection();
        $regionTable = $this->resourceConnection->getTableName('directory_country_region');
        $regionNameTable = $this->resourceConnection->getTableName('directory_country_region_name');

        $data = $this->getDataForJapan();

        foreach ($data as $row) {
            $select = $adapter->select()
                ->from($regionTable, ['region_id'])
                ->where('country_id = ?', $row[0])
                ->where('code = ?', $row[1]);

            $regionId = $adapter->fetchOne($select);

            if (!$regionId) {
                $adapter->insert($regionTable, [
                    'country_id' => $row[0],
                    'code' => $row[1],
                    'default_name' => $row[1]
                ]);

                $regionId = $adapter->lastInsertId($regionTable);
            }

            $regionNames = [
                ['locale' => 'en_US', 'region_id' => $regionId, 'name' => $row[1]],
                ['locale' => 'ja_JP', 'region_id' => $regionId, 'name' => $row[2]]
            ];

            foreach ($regionNames as $regionName) {
                $adapter->insertOnDuplicate($regionNameTable, $regionName, ['name']);
            }
        }

        $countries = $this->data->getCountryCollection()->getCountriesWithRequiredStates();
        $adapter->update(
            $this->resourceConnection->getTableName('core_config_data'),
            ['value' => implode(',', array_keys($countries))],
            ['scope = ?' => 'default', 'scope_id = ?' => 0, 'path = ?' => Data::XML_PATH_STATES_REQUIRED]
        );
    }
    private function getDataForJapan()
    {
        return [
            ['JP', 'Hokkaido', '北海道'],
            ['JP', 'Aomori', '青森県'],
            ['JP', 'Iwate', '岩手県'],
            ['JP', 'Miyagi', '宮城県'],
            ['JP', 'Akita', '秋田県'],
            ['JP', 'Yamagata', '山形県'],
            ['JP', 'Fukushima', '福島県'],
            ['JP', 'Ibaraki', '茨城県'],
            ['JP', 'Tochigi', '栃木県'],
            ['JP', 'Gunma', '群馬県'],
            ['JP', 'Saitama', '埼玉県'],
            ['JP', 'Chiba', '千葉県'],
            ['JP', 'Tokyo', '東京都'],
            ['JP', 'Kanagawa', '神奈川県'],
            ['JP', 'Niigata', '新潟県'],
            ['JP', 'Toyama', '富山県'],
            ['JP', 'Ishikawa', '石川県'],
            ['JP', 'Fukui', '福井県'],
            ['JP', 'Yamanashi', '山梨県'],
            ['JP', 'Nagano', '長野県'],
            ['JP', 'Gifu', '岐阜県'],
            ['JP', 'Shizuoka', '静岡県'],
            ['JP', 'Aichi', '愛知県'],
            ['JP', 'Mie', '三重県'],
            ['JP', 'Shiga', '滋賀県'],
            ['JP', 'Kyoto', '京都府'],
            ['JP', 'Osaka', '大阪府'],
            ['JP', 'Hyogo', '兵庫県'],
            ['JP', 'Nara', '奈良県'],
            ['JP', 'Wakayama', '和歌山県'],
            ['JP', 'Tottori', '鳥取県'],
            ['JP', 'Shimane', '島根県'],
            ['JP', 'Okayama', '岡山県'],
            ['JP', 'Hiroshima', '広島県'],
            ['JP', 'Yamaguchi', '山口県'],
            ['JP', 'Tokushima', '徳島県'],
            ['JP', 'Kagawa', '香川県'],
            ['JP', 'Ehime', '愛媛県'],
            ['JP', 'Kochi', '高知県'],
            ['JP', 'Fukuoka', '福岡県'],
            ['JP', 'Saga', '佐賀県'],
            ['JP', 'Nagasaki', '長崎県'],
            ['JP', 'Kumamoto', '熊本県'],
            ['JP', 'Oita', '大分県'],
            ['JP', 'Miyazaki', '宮崎県'],
            ['JP', 'Kagoshima', '鹿児島県'],
            ['JP', 'Okinawa', '沖縄県']
        ];
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
