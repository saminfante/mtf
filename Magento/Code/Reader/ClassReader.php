<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Code\Reader;

class ClassReader
{
    /**
     * Read class constructor signature
     *
     * @param string $className
     * @return array|null
     * @throws \ReflectionException
     */
    public function getConstructor($className)
    {
        $class = new \ReflectionClass($className);
        $result = null;
        $constructor = $class->getConstructor();
        if ($constructor) {
            $result = array();
            /** @var $parameter \ReflectionParameter */
            foreach ($constructor->getParameters() as $parameter) {
                try {
                    $result[] = array(
                        $parameter->getName(),
                        $parameter->getClass() !== null ? $parameter->getClass()->getName() : null,
                        !$parameter->isOptional(),
                        $parameter->isOptional() ? $parameter
                            ->isDefaultValueAvailable() ? $parameter
                            ->getDefaultValue() : null : null
                    );
                } catch (\ReflectionException $e) {
                    $message = $e->getMessage();
                    throw new \ReflectionException($message, 0, $e);
                }
            }
        }

        return $result;
    }

    /**
     * Retrieve parent relation information for type in a following format
     * array(
     *     'Parent_Class_Name',
     *     'Interface_1',
     *     'Interface_2',
     *     ...
     * )
     *
     * @param string $className
     * @return string[]
     */
    public function getParents($className)
    {
        $parentClass = get_parent_class($className);
        if ($parentClass) {
            $result = array();
            $interfaces = class_implements($className);
            if ($interfaces) {
                $parentInterfaces = class_implements($parentClass);
                if ($parentInterfaces) {
                    $result = array_values(array_diff($interfaces, $parentInterfaces));
                } else {
                    $result = array_values($interfaces);
                }
            }
            array_unshift($result, $parentClass);
        } else {
            $result = array_values(class_implements($className));
            if ($result) {
                array_unshift($result, null);
            } else {
                $result = array();
            }
        }
        return $result;
    }
}
