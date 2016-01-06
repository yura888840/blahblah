<?php
/**
 * Created by PhpStorm.
 * User: yuri
 * Date: 15.07.15
 * Time: 14:19
 */

use Phalcon\Annotations\Adapter\Memory as MemoryAdapter;
use Phalcon\Annotations\Reader;
use Phalcon\Annotations\Reflection;

class RebuildaclTask extends \Phalcon\CLI\Task
{
    public function mainAction()
    {

        echo "USAGE: php cli.php rebuildacl {start|clear}" . PHP_EOL;
    }

    public function startAction()
    {
        // правило, для скипа, значения acl

        $className = 'TestaController';
        $headerData = $this->parseClassHeaderAnnotaions($className);
        var_dump($headerData);
        $methodsData = $this->parseClassMethodsAnnotations($className);
        print_r($methodsData);
        $params = [
            'headerData' => $headerData,
            'methodsData' => $methodsData,
            'className' => $className
        ];
        $this->buildAclOnProcessedAnnotaions($params);
    }

    /**
     *
     * @var array <varName> => Index In array for value
     */
    private $namedHeaderArgs = [
        'module' => 0,

    ];

    private function parseClassHeaderAnnotaions($className)
    {
        $reader = new MemoryAdapter();

        // Reflect the annotations in the class Example
        $reflector = $reader->get($className);

        // Read the annotations in the class' docblock
        $annotations = $reflector->getClassAnnotations();

        $transformed = [];
        // Traverse the annotations
        foreach ($annotations as $annotation) {

            $annName = $annotation->getName();
            if (array_key_exists($annName, $this->namedHeaderArgs)) {
                $args = $annotation->getArguments();

                $transformed[$annName] = $args[$this->namedHeaderArgs[$annName]];
            }
        }

        return $transformed;
    }

    private $methodsArgs = [
        'permission' => 0,
    ];

    private function parseClassMethodsAnnotations($className)
    {
        $reader = new Reader();
        $parsing = $reader->parse($className);
        $reflection = new Reflection($parsing);
        $class = new ReflectionClass($className);

        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

        $classMethods = [];

        foreach ($methods as $method) {
            if ($method->class == $className) {
                $classMethods[] = $method->name;
            }
        }

        $methodsAnnotations = $reflection->getMethodsAnnotations();
        $processed = [];

        $methodAnnotationsValues = [];
        foreach ($methodsAnnotations as $method => $annotations) {
            if (!in_array($method, $classMethods)) continue;

            $processed[] = $method;
            $methodAnnotationsValues[$method] = [];

            foreach ($annotations as $annotation) {
                $name = $annotation->getName();

                if (!array_key_exists($name, $this->methodsArgs))
                    continue;

                $args = $annotation->getArguments();
                $methodAnnotationsValues[$method][$name] = $args[$this->methodsArgs[$name]];
            }
        }

        if (array_intersect($processed, $classMethods) != $classMethods) {
            echo "Error: Not all methods contains neccessary annotaions";
        }

        return $methodAnnotationsValues;
    }

    /*
     *
            $params = [
                'headerData' => $headerData,
                'methodsData' => $methodsData,
                'className' => $className
            ];
     *
     */

    private function buildAclOnProcessedAnnotaions($params)
    {
        $headerData = $params['headerData'];
        $methodsData = $params['methodsData'];
        $className = $params['className'];

        // rules for building
        //@todo build for model

        foreach ($headerData as $k => $v) {

        }

        $resource = $headerData['module'];


        // transforming data
        $transformed = [];

        foreach ($methodsData as $k => $v) {

        }

        $data = [];

        $models = ['Resources', 'PrivateResources'];

        foreach ($models as $model) {
            // формирование данных, дата

            $m = \Crm\Models\CollectionFactory::getNewInstanceOf($model, $data);

            if (!$m->save()) {
                throw new \Exception('Error saving');
            }
        }
        /*
         * Сценарии :
         *  1. Уже, есть этот контроллер- action, и на него, выставлены уже права, для каких- то групп
         *      В таком случае - ничего не трогаем, не - добавляем, т.д.
         *
         *  2. Просто, появился action, на группу, где уже выставлены, некие права
         *       Тогда, он автоматически "подхватывает", права группы , - алиаса
         *
         *  3. Если новый, абсолютно новый action (т.е., группа action - ов ) - просто, добавляем его
         *
         *  4. Если абсолюно новый контроллер - action - добавляем, опять же, Ж
         *
         *  5. Если - удалили, убрали, наПример, action , либо - переместили - другая группа action- ов.
         *      Другой пермишен- слот
         *
         *  6. Если, удалили контроллер, а permissions - actions, на него остались
         *     Тогда, делаем clean- up
         *
         */

    }

    public function testAction()
    {
        print_r(defined('IS_CONSOLE'));
    }

}