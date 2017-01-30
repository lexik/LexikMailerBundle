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

        if ('utf8mb4' !== strtolower($params['charset'])) {
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
