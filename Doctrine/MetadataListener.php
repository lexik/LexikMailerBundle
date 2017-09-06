<?php

namespace Lexik\Bundle\MailerBundle\Doctrine;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class MetadataListener
{
    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $params = $eventArgs->getEntityManager()->getConnection()->getParams();

        $charset = '';
        if (isset($params['charset'])) {
            $charset = $params['charset'];
        } elseif (isset($params['master']['charset'])) {
            $charset = $params['master']['charset'];
        }

        if ('utf8mb4' !== mb_strtolower($charset)) {
            return;
        }

        /** @var ClassMetadataInfo $metadata */
        $metadata = $eventArgs->getClassMetadata();

        if (false === strpos($metadata->getName(), 'MailerBundle')) {
            return;
        }

        foreach ($metadata->getFieldNames() as $name) {
            $fieldMapping = $metadata->getFieldMapping($name);

            if (isset($fieldMapping['type']) && 'string' === $fieldMapping['type']) {
                $fieldMapping['length'] = 191;
                $metadata->fieldMappings[$name] = $fieldMapping;
            }
        }
    }
}
